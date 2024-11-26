<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimRequest extends FormRequest
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
            'provider_id' => 'required',
            'insurer_code' => 'required',
            'encounter_date' => 'required|date',
            'specialty_id' => 'required',
            'total_amount' => 'required',
            'priority_level' => 'required|integer|min:1|max:5',
            'claim_items' => 'required|array',
            'claim_items.*.item_name' => 'required',
            'claim_items.*.unit_price' => 'required|numeric|min:1',
            'claim_items.*.quantity' => 'required|numeric|min:1',
        ];
    }
}