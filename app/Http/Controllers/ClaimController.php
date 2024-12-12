<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Jobs\BatchClaimsJob;
use App\Models\Claim;
use App\Models\ClaimItem;
use App\Models\Insurer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClaimController extends Controller
{
    public function submitClaim(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'insurerCode' => 'required|string',
            'providerName' => 'required|string',
            'encounterDate' => 'required|date',
            'specialty' => 'required|string',
            'priorityLevel' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error(422, $validator->errors());
        }

        DB::beginTransaction();

        try {
            $insurer = Insurer::query()->where('code', $request->insurerCode)->first();
            if (!$insurer) {
                return ResponseHelper::error('422', 'Insurer not found.');
            }

            $insurerClaimsCount = Claim::query()
                ->where('insurer_code', $request->insurerCode)
                ->whereDate('submission_date', Carbon::today())
                ->count();

            if ($insurerClaimsCount >= $insurer->daily_capacity_limit) {
                return ResponseHelper::error('422', $insurer->name . ' claims limit exceeded for the day');
            }

            $claim = Claim::create([
                'insurer_code' => $request->insurerCode,
                'provider_name' => $request->providerName,
                'submission_date' => Carbon::today(),
                'processed_date' => Carbon::today()->addDay(),
                'encounter_date' => $request->encounterDate,
                'specialty' => $request->specialty,
                'priority_level' => $request->priorityLevel,
            ]);

            $totalClaimValue = 0;
            $claimItems = [];

            foreach ($request->items as $item) {
                $claimItems[] = new ClaimItem([
                    'name' => $item['name'],
                    'unit_price' => $item['unitPrice'],
                    'quantity' => $item['quantity'],
                ]);
                $totalClaimValue += $item['unitPrice'] * $item['quantity'];
            }

            $claim->total_claim_value = $totalClaimValue;

            $this->calculateClaimCosts($claim, $request, $insurer);

            $claim->save();
            $claim->items()->saveMany($claimItems);

            DB::commit();
            return ResponseHelper::success([], 'Claim submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error submitting claim: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);

            return ResponseHelper::error(500, 'Failed to submit claim. Please try again.');
        }
    }

    private function calculateClaimCosts($claim, $request, $insurer)
    {
        // Calculate time of month cost value
        if ($insurer->batch_preference === 'submission_date') {
            $time_of_month_cost_perc = 20 + ((30 / 29) * ((Carbon::parse($claim->submission_date)->day) - 1));
        } else {
            $time_of_month_cost_perc = 20 + ((30 / 29) * ((Carbon::parse($claim->encounter_date)->day) - 1));
        }
        $claim->time_of_month_cost_value = ($time_of_month_cost_perc / 100) * $claim->total_claim_value;

        // Calculate specialty type cost value
        $specialtyEfficiency = json_decode($insurer->specialty_efficiency, true);
        $efficiencyPercentage = $specialtyEfficiency[$request->specialty] ?? 0;
        $claim->specialty_type_cost_value = ($efficiencyPercentage / 100) * $claim->total_claim_value;

        // Calculate priority level cost value
        $priority_level_cost_perc = ($request->priorityLevel / 5) * 100;
        $claim->priority_level_cost_value = ($priority_level_cost_perc / 100) * $claim->total_claim_value;

        // Calculate monetary cost value
        if ($claim->total_claim_value > $insurer->high_monetary_value) {
            $monetary_cost_value = ($insurer->max_processing_cost_percentage / 100) * $claim->total_claim_value;
        } else {
            $monetary_cost_value = ($insurer->min_processing_cost_percentage / 100) * $claim->total_claim_value;
        }
        $claim->monetary_cost_value = $monetary_cost_value;

        // Calculate total processing cost value
        $claim->total_processing_cost_value = $claim->time_of_month_cost_value +
            $claim->specialty_type_cost_value +
            $claim->priority_level_cost_value +
            $claim->monetary_cost_value;
    }

}

