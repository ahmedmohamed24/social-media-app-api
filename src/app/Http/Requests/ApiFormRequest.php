<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ApiFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['data' => ['message' => 'failed'], 'errors' => $validator->errors(), 'meta' => []], Response::HTTP_NOT_ACCEPTABLE));
    }
}
