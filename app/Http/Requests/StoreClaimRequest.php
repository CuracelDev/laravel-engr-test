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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'insurer' => 'required|exists:insurers,id',
            'priority' => 'required|in:' . implode(',', array_values(\App\Models\Claim::PRIORITES)),
            'specialty' => 'required|in:' . implode(',', array_values(\App\Models\Claim::SPECIALTIES)),
            'name' => 'required|string|max:255',
            'date' => 'required|date_format:Y-m-d|before:tomorrow',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unitPrice' => 'required|numeric|min:1',
        ];
    }
}
