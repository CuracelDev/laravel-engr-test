<?php

namespace App\Http\Requests\ProviderClaims;

use App\Models\Insurer;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'insurer_code' => ['required', 'string', 'exists:insurers,code'],
            'encounter_date' => ['required', 'date', 'date_format:Y-m-d'],
            'specialty' => ['required', 'string'],
            'priority_level' => ['required', 'integer', 'min:1', 'max:5'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.unit_price' => ['required', 'numeric', 'min:1'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'items.*.quantity.min' => 'The quantity must be at least 1.',
            'items.*.unit_price.min' => 'The unit price must be at least 1.',
            'items.*.name.required' => 'The item name is required.',
            'items.*.unit_price.required' => 'The item price is required.',
            'items.*.quantity.required' => 'The quantity is required.',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        
        $data['items'] = array_map(function($item) {
            $item['sub_total'] = $item['unit_price'] * $item['quantity'];
            return $item;
        }, $data['items']);
        
        $data['total_amount'] = array_sum(array_column($data['items'], 'sub_total'));
        $data['insurer_id'] = Insurer::where('code', $data['insurer_code'])->first()->id;
        $data['submission_date'] = now();

        
        unset($data['insurer_code']);
        return $data;
    }
}
