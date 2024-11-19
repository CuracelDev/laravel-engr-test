<?php

namespace App\Actions;

use App\Jobs\ComputeClaimJob;
use App\Models\Claim;
use App\Models\Insurer;
use App\Models\InsurerSpecialtyEfficiency;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Validation\ValidationException;

class ClaimAction {
    use AsAction;

    public function handle(Claim $claim, array $data)
    {
        $result = InsurerSpecialtyEfficiency::where('insurers_id', $data['insurer_code'])->where('specialty_id', $data['specialty'])->first()->id;

        $claim->provider_name =  $data['provider_name'];
        $claim->insurer_code = $data['insurer_code'];
        $claim->encounter_date = $data['encounter_date'];
        $claim->specialty_id = $data['specialty'];
        $claim->priority = $data['priority_level'];
        $claim->total_value = $this->CalculateTotalValue($data['items']);
        $claim->efficiency_id =  $result;
        $claim->save();

        $claim->items()->createMany($data['items']);

        ComputeClaimJob::dispatch($claim);
        // ComputeClaimJob::dispatch($claim)->onQueue('low-priority');
    }

    public function rules(): array
    {
        return [
            'provider_name' => ['required','string'],
            'insurer_code' => ['required', 'string', 'exists:insurers,code'],
            'encounter_date' => ['required', 'date'],
            'priority_level' => ['required', 'string','in:0,1,2,3,4,5'],
            'specialty' => ['required', 'exists:specialties,id'],
            'items' => ['required', 'array'],
            'items.*.name' => ['required', 'string'],
            'items.*.unit_price' => ['required', 'numeric'],
            'items.*.qty' => ['required', 'integer'],
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, response()->json($validator->errors(), 422));
    }

    public function asController(ActionRequest $request): JsonResponse
    {
        try {
            
            $data = $request->validated();
            $insurer = Insurer::where('code', $data['insurer_code'])->first();
            $data['insurer_code'] = $insurer->id;
            $data['insurer_name'] = $insurer->name;
            $this->handle(new Claim, $data);
        } catch (ValidationException $e) {
            return response()->json($e->getMessage(), 422);
        }

        return response()->json(['message' => 'Claim submitted successfully!'], 200);
    }

    public function CalculateTotalValue(array $items): float
    {
        return array_reduce($items, function ($total, $item) {
            return $total + ($item['unit_price'] * $item['qty']);
        }, 0);
    }
}