<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'insurer_code'     => ['required','string','exists:insurers,code'],
            'provider_name'    => ['required','string','max:255'],
            'encounter_date'   => ['required','date'],
            'submission_date'  => ['required','date'],
            'specialty'        => ['required','string','max:255'],
            'priority_level'   => ['required','integer','min:1','max:5'],

            'items'            => ['required','array','min:1'],
            'items.*.name'     => ['required','string','max:255'],
            'items.*.unit_price' => ['required','numeric','min:0'],
            'items.*.quantity' => ['required','integer','min:1'],
        ];
    }
}