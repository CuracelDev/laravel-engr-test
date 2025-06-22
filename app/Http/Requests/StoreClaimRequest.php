<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_name' => 'required|string',
            'insurer_code' => 'required|string|exists:insurers,code',
            'encounter_date' => 'required|date',
            'specialty' => 'required|string',
            'priority_level' => 'required|integer|min:1|max:5',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
