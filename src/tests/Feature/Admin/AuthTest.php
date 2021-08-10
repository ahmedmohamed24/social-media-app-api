<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('passport:install');
    }

    // @test
    public function testStatus200OnAdminLogin()
    {
        $this->withoutExceptionHandling();
        $admin = Admin::factory()->create();
        $credentials = ['email' => $admin->email, 'password' => 'password'];
        $response = $this->postJson(\route('admin.login'), $credentials);
        $response->assertStatus(200);
    }

    // @test
    public function testTokenReturnedInResponseAfterLogin()
    {
        $this->withoutExceptionHandling();
        $admin = Admin::factory()->create();
        $credentials = ['email' => $admin->email, 'password' => 'password'];
        $response = $this->postJson(\route('admin.login'), $credentials);
        $this->assertNotNull($response['data']['access_token']);
    }

    public function testAdminLogoutReturns200Status()
    {
        $this->withoutExceptionHandling();
        $admin = Admin::factory()->create();
        $credentials = ['email' => $admin->email, 'password' => 'password'];
        $response = $this->postJson(\route('admin.login'), $credentials);
        $response = $this->postJson(\route('admin.logout'), [], ['Authorization' => 'Bearer '.$response['data']['access_token']]);
        $response->assertStatus(200);
    }
}
