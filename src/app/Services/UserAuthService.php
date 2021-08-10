<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class UserAuthService extends AccessTokenController
{
    public function validateLoginRequest(array $data)
    {
        return Validator::make($data, [
            'client_id' => ['required', 'numeric', 'exists:oauth_clients,id'],
            'client_secret' => ['required', 'string', 'max:255'],
            'username' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'client_id.*' => 'Invalid client: client_id is require and numeric and client_secret is required and string',
            'client_secret.*' => 'Invalid client: client_id is require and numeric and client_secret is required and string',
        ]);
    }

    public function validateRegisterRequest(array $data)
    {
        return Validator::make($data, [
            'grant_type' => ['required', 'string'],
            'client_id' => ['required', 'numeric', 'exists:oauth_clients,id'],
            'client_secret' => ['required', 'string', 'max:255'],
            'username' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'client_id.*' => 'Invalid client: client_id is require and numeric and client_secret is required and string',
            'client_secret.*' => 'Invalid client: client_id is require and numeric and client_secret is required and string',
        ]);
    }
}
