<?php

namespace App\Http\Controllers\API\V1\Auth\Client;

use App\Http\Requests\Client\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    /**
     * Update the given client.
     *
     * @param string $clientId
     *
     * @return \Illuminate\Http\Response|\Laravel\Passport\Client
     */
    public function update(Request $request, $clientId)
    {
        $client = $this->clients->findForUser($clientId, $request->user()->getAuthIdentifier());

        if (!$client) {
            return new Response('', 404);
        }

        $this->validation->make($request->all(), [
            'name' => 'required|max:191',
            'redirect' => ['required', $this->redirectRule],
        ])->validate();

        return $this->clients->update(
            $client,
            $request->name,
            $request->redirect
        );
    }

    /**
     * Delete the given client.
     *
     * @param string $clientId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $clientId)
    {
        $client = $this->clients->findForUser($clientId, $request->user()->getAuthIdentifier());

        if (!$client) {
            return new Response('', 404);
        }
        $this->clients->delete($client);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Get all of the clients for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser(Request $request)
    {
        $userId = $request->user()->getAuthIdentifier();

        $clients = $this->clients->activeForUser($userId);

        if (Passport::$hashesClientSecrets) {
            return $clients;
        }

        return $clients->makeVisible('secret');
    }
}
