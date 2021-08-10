<?php

namespace App\Http\Controllers\API\V1\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        try {
            //use transaction to prevent creating user without issuing a Token
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            //get client name
            $client = DB::table('oauth_clients')->where('id', $request->client_id)->where('secret', $request->client_secret)->first();
            if (!$client) {
                throw new ValidationException($client);
            }
            $accessToken = $user->createToken($client->name)->accessToken;

            DB::commit();

            return $this->response(201, 'success', \null, ['user' => $user, 'access_token' => $accessToken]);
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($e instanceof ValidationException) {
                return $this->response(406, 'Invalid client.', ['client' => 'Invalid client credentials'], \null);
            }
            Log::alert($e->getMessage());

            return $this->response(500, 'internal error occurred!', \null, \null);
        }
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return $this->response(406, 'The Given data was invalid', 'Invalid Credentials!', \null);
        }
        //get the client name used in register process
        $clientName = DB::table('oauth_access_tokens')->where('user_id', \auth()->id())->latest()->first()->name ?? 'authToken';
        $accessToken = \auth()->user()->createToken($clientName)->accessToken;

        return $this->response(302, 'success', null, ['access_token' => $accessToken]);
    }

    public function getUser()
    {
        return $this->response(200, 'success', \null, \auth()->user());
    }

    public function logout(Request $request)
    {
        auth()->user()->token()->revoke();

        return $this->response(302, 'success', \null, \null);
    }
}
