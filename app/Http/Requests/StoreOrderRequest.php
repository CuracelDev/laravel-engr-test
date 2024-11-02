<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'hmo_code' => ['required', 'exists:hmos,code', 'string'],
            'provider_name' => ['required', 'string', 'max:255'],
            'encounter_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.sub_total' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'hmo_code.required' => 'The HMO ID is required.',
            'provider_name.required' => 'The provider name is required.',
            'encounter_date.required' => 'The encounter date is required.',
            'amount.required' => 'The amount is required.',
            'status.required' => 'The status is required.',
            'items.required' => 'At least one item is required.',
            'items.array' => 'Items must be an array.',
            'items.*.title.required' => 'The item title is required.',
            'items.*.unit_price.required' => 'The item unit_price is required.',
            'items.*.quantity.required' => 'The item quantity is required.',
            'items.*.sub_total.required' => 'The item sub total is required.',
        ];
    }
}
