<?php

namespace Tests\Feature\Auth\User;

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

    // @test
    public function testUserReceive201WhenRegister()
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson(\route('user.register'), $this->getUser());
        $response->assertStatus(201);
    }

    // @test
    public function testInvalidClientMessage()
    {
        $user = $this->getUser();
        $user['client_id'] = '1238717';
        $response = $this->postJson(\route('user.register'), $user);
        $response->assertJsonFragment(['client_id' => ['Invalid client']]);
    }

    // @test
    public function testUserStoredInDBWhenRegister()
    {
        $this->withoutExceptionHandling();
        $this->assertDatabaseCount('users', 0);
        $this->postJson(\route('user.register'), $this->getUser());
        $this->assertDatabaseCount('users', 1);
    }
}
