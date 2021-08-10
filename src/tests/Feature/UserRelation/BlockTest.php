<?php

namespace Tests\Feature\UserRelation;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BlockTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus201WhenBlockUser()
    {
        $this->withoutExceptionHandling();
        $users = $this->createTwoUsers();
        $response = $this->postJson(route('user.block', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        $response->assertStatus(201);
    }

    // @test
    public function testBlockSavedInDB()
    {
        $this->withoutExceptionHandling();
        $users = $this->createTwoUsers();
        $this->assertDatabaseCount('user_relations', 0);
        $this->postJson(route('user.block', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        $this->assertDatabaseCount('user_relations', 1);
    }

    // @test
    public function testCannotBlockUserTwice()
    {
        $this->withoutExceptionHandling();
        $users = $this->createTwoUsers();
        $this->postJson(route('user.block', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        $response = $this->postJson(route('user.block', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        $response->assertStatus(406);
    }

    // @test
    public function testCannotReverseABlockToUser()
    {
        $this->withoutExceptionHandling();
        $users = $this->createTwoUsers();
        //from user 1 block user 2
        $this->postJson(route('user.block', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        Passport::actingAs(User::find(2), [], 'api');
        Passport::actingAs(User::find(2), [], 'web');
        //user 2 try to block user 1
        $response = $this->postJson(route('user.block', $users['firstUser']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(406);
    }

    // @test
    public function testCanReturnBlockList()
    {
        $this->withoutExceptionHandling();
        $users = $this->createTwoUsers();
        //from user 1 block user 2
        $this->postJson(route('user.block', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        //get blocks list
        $response = $this->getJson(\route('user.blockList'), ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        $response->assertStatus(200);
        $this->assertCount(1, $response['data']['block-list']);
    }

    // @test
    public function testCanUnblockBlockedUser()
    {
        $this->withoutExceptionHandling();
        $users = $this->createTwoUsers();
        $this->postJson(route('user.block', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        $response = $this->deleteJson(\route('user.unblock', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        $response->assertStatus(200);
        $this->assertEquals('unblocked', $response['meta']['message']);
    }

    // @test
    public function testCanUnblockOnlyBlockedUsers()
    {
        $users = $this->createTwoUsers();
        $response = $this->deleteJson(\route('user.unblock', $users['secondUser']['id']), [], ['Authorization' => 'Bearer '.$users['firstUser']['accessToken']]);
        $response->assertStatus(404);
    }
}
