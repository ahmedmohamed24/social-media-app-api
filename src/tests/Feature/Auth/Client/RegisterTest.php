<?php

namespace Tests\Feature\Auth\Client;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RegisterTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    private $client = ['name' => 'ahmed',
        'redirect' => 'https://google.com',
        'confidential' => \true,
    ];
    private $accessToken;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = $this->getAdmin();
        $this->accessToken = $admin['data']['access_token'];
    }

    // @test
    public function test201StatusReceivedOnRegister()
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson(\route('clients.store'), $this->client, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertStatus(201);
    }

    // @test
    public function testClientSavedInDBAfterRegister()
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson(\route('clients.store'), $this->client, ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertDatabaseHas('oauth_clients', ['name' => $response->getData()->name]);
    }

    // @test
    public function testClientNameIsRequired()
    {
        $this->client['name'] = '';
        $response = $this->postJson(\route('clients.store'), $this->client, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertJsonFragment(['name' => ['The name field is required.']]);
    }

    // @test
    public function testClientRedirectUrlIsValid()
    {
        $this->client['redirect'] = 'test';
        $response = $this->postJson(\route('clients.store'), $this->client, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertJsonFragment(['redirect' => ['One or more redirects have an invalid url format.']]);
    }

    // @test
    public function testOnlyAuthCanRegisterAsClients()
    {
        $response = $this->postJson(\route('clients.store'), $this->client);
        $response->assertStatus(401);
    }
}
