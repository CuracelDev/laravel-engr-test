<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('insurer_code')) {
            $this->merge([
                'insurer_code' => strtoupper(trim($this->insurer_code)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'insurer_code' => 'required|string|exists:insurers,code',
            'provider_name' => 'required|string|max:255',
            'encounter_date' => 'required|date|before_or_equal:today',
            'specialty' => 'required|string|max:255',
            'priority_level' => 'nullable|integer|min:1|max:5',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'insurer_code.exists' => 'The specified insurer code does not exist.',
            'encounter_date.before_or_equal' => 'The encounter date cannot be in the future.',
            'items.required' => 'At least one claim item is required.',
            'items.min' => 'At least one claim item is required.',
        ];
    }
}
