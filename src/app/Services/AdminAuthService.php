<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class AdminAuthService extends AccessTokenController
{
    public function validateLoginRequest(array $data)
    {
        return Validator::make($data, [
            'client_id' => ['required', 'numeric', 'exists:oauth_clients,id'],
            'client_secret' => ['required', 'string', 'max:255'],
            'username' => ['required', 'email', 'exists:admins,email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'client_id.*' => 'Invalid client: client_id is require and numeric and client_secret is required and string',
            'client_secret.*' => 'Invalid client: client_id is require and numeric and client_secret is required and string',
            'username.*' => 'Invalid credentials!',
            'password.*' => 'Invalid credentials!',
        ]);
    }
}
