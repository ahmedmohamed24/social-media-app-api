<?php

namespace Tests\Feature\Auth\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LoginTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus302OnLogin()
    {
        $this->withoutExceptionHandling();
        $user = $this->getUser();
        $this->postJson(\route('user.register'), $user);
        $response = $this->postJson(\route('user.login'), $user);
        $response->assertStatus(302);
    }

    // @test
    public function testAccessTokenRetrieved()
    {
        $this->withoutExceptionHandling();
        $user = $this->getUser();
        $this->postJson(\route('user.register'), $user);
        $response = $this->postJson(\route('user.login'), $user);
        $this->assertNotEmpty($response['data']['access_token']);
    }

    // @test
    public function testEmailMustBeValid()
    {
        $user = $this->getUser();
        $this->postJson(\route('user.register'), $user);
        $user['email'] = 'email';
        $response = $this->postJson(\route('user.login'), $user);
        $response->assertJsonFragment(['email' => ['The email must be a valid email address.']]);
    }

    // @test
    public function testPasswordIsRequired()
    {
        $user = $this->getUser();
        $this->postJson(\route('user.register'), $user);
        $user['password'] = '';
        $response = $this->postJson(\route('user.login'), $user);
        $response->assertJsonFragment(['password' => ['The password field is required.']]);
    }

    // @test
    public function testPasswordMustBeCorrect()
    {
        $user = $this->getUser();
        $this->postJson(\route('user.register'), $user);
        $user['password'] = 'password1'; //password is the correct
        $response = $this->postJson(\route('user.login'), $user);
        $response->assertJsonFragment(['errors' => 'Invalid Credentials!']);
    }
}
