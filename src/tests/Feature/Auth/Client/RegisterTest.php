<?php

namespace Tests\Feature\Auth\Client;

use App\Models\User;
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

    // @test
    public function test201StatusReceivedOnRegister()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs(User::factory()->create())->postJson(\route('clients.store'), $this->client);
        $response->assertStatus(201);
    }

    // @test
    public function testClientSavedInDBAfterRegister()
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs(User::factory()->create())->postJson(\route('clients.store'), $this->client);
        $this->assertDatabaseHas('oauth_clients', ['name' => $response->getData()->name]);
    }

    // @test
    public function testClientNameIsRequired()
    {
        $this->withoutExceptionHandling();
        $this->client['name'] = '';
        $response = $this->actingAs(User::factory()->create())->postJson(\route('clients.store'), $this->client);
        $response->assertJsonValidationErrors('name');
    }

    // @test
    public function testClientRedirectUrlIsValid()
    {
        $this->withoutExceptionHandling();
        $this->client['redirect'] = 'test';
        $response = $this->actingAs(User::factory()->create())->postJson(\route('clients.store'), $this->client);
        $response->assertJsonValidationErrors('redirect');
    }

    // @test
    public function testOnlyAuthCanRegisterAsClients()
    {
        $response = $this->postJson(\route('clients.store'), $this->client);
        $response->assertStatus(401);
    }
}
