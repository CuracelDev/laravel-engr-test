<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsurerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:insurers'],
            'capacity_limit' => ['required', 'numeric'],
            'min_batch_size' => ['required', 'numeric'],
            'max_batch_size' => ['required', 'numeric'],
            'date_preffered_type' => ['required', 'string', 'max:50'],
        ];
    }
}
