<?php

namespace App\Http\Requests\User;

use App\Http\Requests\ApiFormRequest;

class RegisterRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8', 'max:255'],
            'client_id' => ['required', 'numeric', 'exists:oauth_clients,id'],
            'client_secret' => ['required', 'exists:oauth_clients,secret'],
        ];
    }

    public function messages()
    {
        return [
            'client_id.exists' => 'Invalid client',
            'client_secret.exists' => 'Invalid client',
        ];
    }
}
