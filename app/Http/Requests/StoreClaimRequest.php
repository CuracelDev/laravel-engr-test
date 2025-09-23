<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider_name' => 'required|string|max:255',
            'insurer_code' => 'required|exists:insurers,code',
            'encounter_date' => 'required|date',
            'priority_level' => 'required|integer|min:1|max:5',
            'specialty' => 'required|string|in:cardiology,orthopedics,neurology,general,pediatrics',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
