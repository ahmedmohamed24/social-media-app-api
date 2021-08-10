<?php

namespace Tests\Feature\Auth\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AuthTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function test200OnGettingUserInfo()
    {
        $user = $this->getUser();
        $response = $this->postJson(\route('user.register'), $user);
        $response = $this->getJson(\route('user.info'), ['Authorization' => 'Bearer '.$response['data']['access_token']]);
        $response->assertStatus(200);
    }

    // @test
    public function testDataRetrievedOnRequest()
    {
        $this->withoutExceptionHandling();
        $user = $this->getUser(1);
        $response = $this->postJson(\route('user.register'), $user);
        $response = $this->getJson(\route('user.info'), ['Authorization' => 'Bearer '.$response['data']['access_token']]);
        $this->assertEquals($user['name'], $response['data']['name']);
    }

    // @test
    public function testLogoutReturns302Status()
    {
        $this->withoutExceptionHandling();
        $user = $this->getUser();
        $response = $this->postJson(\route('user.register'), $user);
        $response = $this->postJson(\route('user.logout'), [], ['Authorization' => 'Bearer '.$response['data']['access_token']]);
        $response->assertStatus(302);
    }
}
