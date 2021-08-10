<?php

namespace Tests;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getUser()
    {
        $this->artisan('passport:install');
        $user = User::factory()->raw();
        $user['username'] = $user['email'];
        $user['client_id'] = DB::table('oauth_clients')->find(2)->id;
        $user['client_secret'] = DB::table('oauth_clients')->find(2)->secret;
        $user['password'] = 'password';
        $user['password_confirmation'] = 'password';
        $user['grant_type'] = 'password';
        $user['scope'] = '';

        return $user;
    }

    protected function getAdmin()
    {
        $this->artisan('passport:install');
        $admin = Admin::factory()->create();

        return $this->postJson(\route('admin.login'), ['email' => $admin->email, 'password' => 'password']);
    }

    protected function createTwoUsers()
    {
        $response = $this->postJson(\route('user.register'), $this->getUser());
        $firstUserAccessToken = $response['data']['access_token'];
        $user = $this->getUser();
        $response = $this->postJson(\route('user.register'), $user);
        $secondUserAccessToken = $response['data']['access_token'];
        $firstUserID = 1;
        $secondUserID = 2;

        return [
            'firstUser' => [
                'id' => $firstUserID,
                'accessToken' => $firstUserAccessToken,
            ],
            'secondUser' => [
                'id' => $secondUserID,
                'accessToken' => $secondUserAccessToken,
            ],
        ];
    }
}
