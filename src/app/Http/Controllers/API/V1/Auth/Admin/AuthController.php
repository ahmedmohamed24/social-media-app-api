<?php

namespace App\Http\Controllers\API\V1\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Services\AdminAuthService;
use Illuminate\Support\Facades\Auth;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    use ApiResponse;

    protected $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    public function login(ServerRequestInterface $request)
    {
        //parse body
        $data = \json_decode($request->getBody(), true);
        //validate data
        $validator = $this->adminAuthService->validateLoginRequest($data);
        if ($validator->fails()) {
            return $this->response(401, 'Invalid data', $validator->getMessageBag(), \null);
        }
        //set grant_type and cope statically
        // $request->grant_type = 'password';
        // $request->scope = '';
        $response = $this->adminAuthService->issueToken($request);
        if (200 === $response->status()) {
            //success (302 to indicate user to redirect)
            return $this->response(302, 'success', \null, \json_decode($response->getContent(), \true));
        }

        return $this->response($response->status(), 'authentication Error!', [$response->getContent()], \null);
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
