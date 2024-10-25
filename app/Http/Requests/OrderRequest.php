<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // You can modify this based on your authorization logic
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'encounter_date' => 'required|date|date_format:Y-m-d',
            'provider_id' => 'required',
            'hmo_code' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.item' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|numeric|min:0', 
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'encounter_date.required' => 'Encounter date is required.',
            'provider_id.required' => 'Provider ID is required.',
            'hmo_code.required' => 'HMO code is required.',
            'total.required' => 'Total amount is required.',
            'items.required' => 'At least one item is required.',
             'items.*.item.required' => 'Item name is required.',
            'items.*.unit_price.required' => 'Unit price is required for each item.',
            'items.*.qty.required' => 'Quantity is required for each item.',
            'items.*.subtotal.required' => 'Subtotal is required for each item.', 
        ];
    }
}
