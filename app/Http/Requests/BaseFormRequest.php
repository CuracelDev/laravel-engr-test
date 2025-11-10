<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseFormRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'status' => __('error'),
            'message' => $validator->getMessageBag()->first(),
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response([
            'status' => __('error'),
            'message' => __('You are not authorized to perform this action.'),
        ], Response::HTTP_FORBIDDEN));
    }
}
