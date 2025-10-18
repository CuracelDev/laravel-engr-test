<?php

namespace App\Http\Requests;

use App\Enums\DatePreference;
use App\Enums\MedicalSpecialty;
use App\Enums\PriorityLevel;
use Illuminate\Foundation\Http\FormRequest;

class SubmitClaimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'insurer_code' => 'required|string|exists:insurers,code',
            'provider_name' => 'required|string|max:255',
            'encounter_date' => 'required|date',
            'priority_level' => 'required|integer|min:1|max:5',
            'specialty' => 'required|string|in:' . implode(',', MedicalSpecialty::all()),
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'insurer_code.required' => 'Insurer code is required',
            'insurer_code.exists' => 'Invalid insurer code',
            'provider_name.required' => 'Provider name is required',
            'encounter_date.required' => 'Encounter date is required',
            'encounter_date.date' => 'Encounter date must be a valid date',
            'priority_level.required' => 'Priority level is required',
            'priority_level.min' => 'Priority level must be between 1 and 5',
            'priority_level.max' => 'Priority level must be between 1 and 5',
            'specialty.required' => 'Specialty is required',
            'specialty.in' => 'Invalid specialty selected',
            'items.required' => 'At least one item is required',
            'items.min' => 'At least one item is required',
            'items.*.name.required' => 'Item name is required',
            'items.*.unit_price.required' => 'Item unit price is required',
            'items.*.unit_price.min' => 'Item unit price must be greater than or equal to 0',
            'items.*.quantity.required' => 'Item quantity is required',
            'items.*.quantity.min' => 'Item quantity must be at least 1',
        ];
    }
}

