<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetOrdersByBatchRequest extends FormRequest
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
    public function rules()
    {
        return [
            'hmo_code' => 'required|string',
            'provider_id' => 'required|exists:providers,id',
            'search_date' => 'required|date|date_format:Y-m-d',
        ];
    }


    public function messages()
    {
        return [
            'hmo_code.required' => 'HMO code is required.',
            'provider_id.required' => 'Provider ID is required.',
            'provider_id.exists' => 'The selected provider ID does not exist.',
            'search_date.required' => 'Search date is required.',
            'search_date.date' => 'Search date must be a valid date.',
            'search_date.date_format' => 'Search date must be in the format YYYY-MM-DD.',
        ];
    }
}
