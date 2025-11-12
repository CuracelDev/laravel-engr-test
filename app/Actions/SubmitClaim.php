<?php

namespace App\Actions;

use App\Models\Claim;
use App\Models\Insurer;
use Illuminate\Support\Facades\Validator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Submit Claim Action
 * 
 * Validates and creates a new healthcare claim.
 * Calculates item subtotals and total claim amount.
 * 
 * This action can be called from:
 * - Controllers
 * - Console commands
 * - Jobs
 * - Other actions
 */
class SubmitClaim
{
    use AsAction;

    /**
     * Create a new claim with validated data
     * 
     * @param array $data Claim data including insurer_code, provider_name, items, etc.
     * @return Claim The created claim
     */
    public function handle(array $data): Claim
    {
        $insurer = Insurer::where('code', $data['insurer_code'])->firstOrFail();
        
        $items = collect($data['items'])->map(fn($item) => array_merge($item, [
            'subtotal' => $item['unit_price'] * $item['quantity']
        ]));

        return Claim::create([
            'provider_name' => $data['provider_name'],
            'insurer_id' => $insurer->id,
            'encounter_date' => $data['encounter_date'],
            'submission_date' => $data['submission_date'] ?? now()->toDateString(),
            'priority_level' => $data['priority_level'],
            'specialty' => $data['specialty'],
            'items' => $items->toArray(),
            'total_amount' => $items->sum('subtotal'),
        ]);
    }

    /**
     * Validation rules for claim submission
     * 
     * @return array Validation rules
     */
    public function rules(): array
    {
        return [
            'insurer_code' => 'required|exists:insurers,code',
            'provider_name' => 'required|string|max:255',
            'encounter_date' => 'required|date',
            'specialty' => 'required|in:cardiology,orthopedics,neurology,general',
            'priority_level' => 'required|integer|between:1,5',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Handle action when called as controller
     * 
     * @param ActionRequest $request Validated request
     * @return Claim The created claim
     */
    public function asController(ActionRequest $request)
    {
        return $this->handle($request->validated());
    }
    
    public function jsonResponse($result, $request)
    {
        return response()->json([
            'message' => 'Claim submitted successfully',
            'claim_id' => $result->id,
            'batch_id' => $result->batch_id
        ]);
    }
}