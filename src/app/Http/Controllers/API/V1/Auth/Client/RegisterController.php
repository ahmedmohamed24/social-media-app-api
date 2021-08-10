<?php

namespace App\Http\Controllers\API\V1\Auth\Client;

use App\Http\Requests\Client\RegisterRequest;
use Laravel\Passport\Http\Controllers\ClientController;
use Laravel\Passport\Passport;

class RegisterController extends ClientController
{
    /**
     * Store a new client.
     *
     * @return array|\Laravel\Passport\Client
     */
    public function storeClient(RegisterRequest $request)
    {
        $client = $this->clients->create(
            $request->user()->getAuthIdentifier(),
            $request->name,
            $request->redirect,
            null,
            false,
            false,
            (bool) $request->input('confidential', true)
        );
        if (Passport::$hashesClientSecrets) {
            return ['plainSecret' => $client->plainSecret] + $client->toArray();
        }

        return $client->makeVisible('secret');
    }
}
