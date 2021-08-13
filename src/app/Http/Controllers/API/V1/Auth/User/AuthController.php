<?php

namespace App\Http\Controllers\API\V1\Auth\User;

use App\Events\UserRegisteredEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use App\Services\UserAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    use ApiResponse;
    private $userAuthService;

    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function login(ServerRequestInterface $request)
    {
        //parse body
        $data = $request->getParsedBody();
        //validate data
        $validator = $this->userAuthService->validateLoginRequest($data);
        if ($validator->fails()) {
            return $this->response(401, 'Invalid data', $validator->getMessageBag(), \null);
        }
        //set grant_type and cope statically
        $request->grant_type = 'password';
        $request->scope = '';

        $response = $this->userAuthService->issueToken($request);
        if (200 === $response->status()) {
            //success (302 to indicate user to redirect)
            return $this->response(302, 'success', \null, \json_decode($response->getContent(), \true));
        }

        return $this->response($response->status(), 'authentication Error!', [$response->getContent()], \null);
    }

    public function register(ServerRequestInterface $request)
    {
        $data = $request->getParsedBody();
        $validator = $this->userAuthService->validateRegisterRequest($data);
        if ($validator->fails()) {
            return $this->response(401, 'Invalid data', $validator->getMessageBag(), \null);
        }

        $data = $validator->validated();
        DB::beginTransaction();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['username'],
            'password' => \bcrypt($data['password']),
        ]);

        $response = $this->userAuthService->issueToken($request);
        if (200 === $response->status()) {
            \event(new UserRegisteredEvent($user->id, $user->email));
            DB::commit();

            return $this->response(201, 'success', \null, \json_decode($response->getContent(), \true));
        }
        DB::rollBack();

        return $this->response($response->status(), 'authentication Error!', [$response->getContent()], \null);
    }

    public function getUser()
    {
        return $this->response(200, 'success', \null, new UserResource(User::findOrFail(\auth()->id())));
    }

    public function logout(Request $request)
    {
        auth()->user()->token()->revoke();

        return $this->response(302, 'success', \null, \null);
    }
}
