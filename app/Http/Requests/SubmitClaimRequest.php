<?php

namespace App\Http\Requests;

use App\Enums\PriorityLevel;
use App\Enums\Specialty;
use Illuminate\Validation\Rule;

class SubmitClaimRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'insurer_code' => ['required', 'string', Rule::exists('insurers', 'code')],
            'provider_name' => ['required', 'string', 'max:255'],
            'specialty' => ['required',  Rule::enum(Specialty::class)],
            'priority_level' => ['required', Rule::enum(PriorityLevel::class)],
            'encounter_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'insurer_code.exists' => 'The selected insurer code is invalid.',
            'items.min' => 'At least one claim item is required.',
            'items.*.unit_price.min' => 'Unit price must be greater than 0.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
