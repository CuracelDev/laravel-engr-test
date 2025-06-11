<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreClaimRequest
 *
 * @property string $insurer_code
 * @property string $provider_name
 * @property string $encounter_date
 * @property string $specialty
 * @property int $priority_level
 * @property array<int, array{
 *     name: string,
 *     quantity: int,
 *     unit_price: float
 * }> $items
 */
class StoreClaimRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'insurer_code'     => 'required|exists:insurers,code',
            'provider_name'    => 'required|string|max:255',
            'encounter_date'   => 'required|date',
            'specialty'        => 'required|string|max:255',
            'priority_level'   => 'required|integer|min:1|max:5',
            'items'            => 'required|array|min:1',
            'items.*.name'     => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}
