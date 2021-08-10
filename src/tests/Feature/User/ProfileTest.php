<?php

namespace Tests\Feature\User;

use App\Events\UserRegisteredEvent;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ProfileTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus302OnRequestingProfile()
    {
        $this->withoutExceptionHandling();
        $user = $this->getUser();
        $response = $this->postJson(\route('user.register'), $user);
        $accessToken = $response['data']['access_token'];
        $response = $this->getJson(\route('user.profile'), ['Authorization' => 'Bearer '.$accessToken]);
        $response->assertStatus(200);
    }

    // @test
    public function testUserProfileEventFiredWhenRegister()
    {
        Event::fake();
        $this->withoutExceptionHandling();
        $user = $this->getUser();
        $this->postJson(\route('user.register'), $user);
        Event::assertDispatched(UserRegisteredEvent::class);
    }

    // @test
    public function testUserProfileSavedInDBWhenRegister()
    {
        $this->withoutExceptionHandling();
        $user = $this->getUser();
        $this->assertDatabaseCount('profiles', 0);

        $this->postJson(\route('user.register'), $user);
        $this->assertDatabaseCount('profiles', 1);
    }

    // @test
    public function testUpdateProfileBio()
    {
        $this->withoutExceptionHandling();
        $user = $this->getUser();
        $response = $this->postJson(\route('user.register'), $user);
        $accessToken = $response['data']['access_token'];
        $data = ['bio' => 'test bio'];
        $response = $this->putJson(\route('user.profile'), $data, ['Authorization' => 'Bearer '.$accessToken]);
        $response->assertJsonFragment(['bio' => 'test bio']);
    }

    // @test
    public function testUpdateProfile()
    {
        $this->withoutExceptionHandling();
        $user = $this->getUser();
        $response = $this->postJson(\route('user.register'), $user);
        $accessToken = $response['data']['access_token'];
        $profile = Profile::factory()->raw();
        $response = $this->putJson(\route('user.profile'), $profile, ['Authorization' => 'Bearer '.$accessToken]);
        $response->assertJsonFragment($profile);
    }
}
