<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;
    private string $adminClient;

    public function __construct()
    {
        $this->adminClient = \env('ADMIN_CLIENT_NAME', 'adminAuthToken');
    }

    public function login(LoginRequest $request)
    {
        $isValid = Auth::guard('admin-web')->attempt($request->validated());
        if (!$isValid) {
            return $this->response(406, 'The Given data was invalid', 'Invalid Credentials!', \null);
        }
        $accessToken = Auth::guard('admin-web')->user()->createToken($this->adminClient)->accessToken;

        return $this->response(200, 'success', \null, ['access_token' => $accessToken]);
    }

    public function getAdmin()
    {
        $admin = Auth::guard('admin')->user();

        return $this->response(200, 'success', null, ['admin' => $admin]);
    }

    public function logout()
    {
        auth()->user()->token()->revoke();

        return $this->response(200, 'success', \null, \null);
    }
}
