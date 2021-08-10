<?php

namespace Tests;

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
        $user['client_id'] = DB::table('oauth_clients')->find(2)->id;
        $user['client_secret'] = DB::table('oauth_clients')->find(2)->secret;
        $user['password_confirmation'] = 'password';
        $user['password'] = 'password';

        return $user;
    }
}
