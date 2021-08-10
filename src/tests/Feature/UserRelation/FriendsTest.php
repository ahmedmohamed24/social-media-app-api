<?php

namespace Tests\Feature\User;

use App\Models\User;
use App\Models\UserRelation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class FriendsTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus201WhenSendingFriendRequest()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        $response = $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $response->assertStatus(201);
    }

    // @test
    public function testFriendRequestIsSavedInDB()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->assertCount(1, DB::table('user_relations')->get());
    }

    // @test
    public function testUserCannotAddHimSelf()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        $response = $this->postJson(\route('user.addFriend', $data['firstUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $response->assertStatus(406);
    }

    // @test
    public function testUserGetFriendList()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        UserRelation::first()->update(['relation' => 'friend']);
        $response = $this->getJson(\route('user.getFriendsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->assertCount(1, $response['data']['friends']);
    }

    // @test
    public function testUserGetFriendRequestsList()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $response = $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->assertCount(1, $response['data']['requests-list']['sent']);
        //change current user
        Passport::actingAs(User::find(2), [], 'web');
        Passport::actingAs(User::find(2), [], 'api');
        $response = $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        $this->assertCount(1, $response['data']['requests-list']['received']);
    }

    // @test
    public function testUserRejectOthersFriendRequests()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        //change current user
        Passport::actingAs(User::find(2), [], 'web');
        Passport::actingAs(User::find(2), [], 'api');
        //get requests number before reject
        $response = $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        $this->assertCount(1, $response['data']['requests-list']['received']);
        //reject
        $this->postJson(\route('user.rejectFriend', 1), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']])->assertStatus(200);
        //get requests number after reject
        $response = $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        $this->assertCount(0, $response['data']['requests-list']['received']);
    }

    // @test
    public function testUserCannotSendRequestAgainIfRejected()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        //change current user
        Passport::actingAs(User::find(2), [], 'web');
        Passport::actingAs(User::find(2), [], 'api');
        //reject request
        $this->postJson(\route('user.rejectFriend', 1), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        //change to user 1
        Passport::actingAs(User::find(1), [], 'web');
        Passport::actingAs(User::find(1), [], 'api');
        //send another request to user 2
        $response = $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $response->assertStatus(406);
    }

    // @test
    public function testUserCanAcceptRequestAfterRejectingIt()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        //send friend request from user 1 to user 2
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        //change current user
        Passport::actingAs(User::find(2), [], 'web');
        Passport::actingAs(User::find(2), [], 'api');
        //reject request
        $this->postJson(\route('user.rejectFriend', 1), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        //check friend list is 0
        $response = $this->getJson(\route('user.getFriendsList'), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        $this->assertCount(0, $response['data']['friends']);
        //accept it
        $this->postJson(\route('user.approveFriendRequest', 1), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        //check friend list is 1
        $response = $this->getJson(\route('user.getFriendsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->assertCount(1, $response['data']['friends']);
    }

    // @test
    public function testUserCannotApproveHisFriendRequest()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        //send friend request from user 1 to user 2
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        //change current user
        Passport::actingAs(User::find(2), [], 'web');
        Passport::actingAs(User::find(2), [], 'api');
        //accept it
        $this->postJson(\route('user.approveFriendRequest', 1), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        //remove user 1
        $this->postJson(\route('user.removeFriend', 1), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $response = $this->getJson(\route('user.getFriendsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->assertCount(0, $response['data']['friends']);
    }

    // @test
    public function testRemoveUserAfterAcceptingFriendRequest()
    {
        $this->withoutExceptionHandling();
        $data = $this->createTwoUsers();
        //send friend request from user 1 to user 2
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        //change current user
        Passport::actingAs(User::find(2), [], 'web');
        Passport::actingAs(User::find(2), [], 'api');
        //accept it
        $this->postJson(\route('user.approveFriendRequest', 1), [], ['Authorization' => 'Bearer '.$data['secondUser']['accessToken']]);
        //change to user 1
        Passport::actingAs(User::find(1), [], 'web');
        Passport::actingAs(User::find(1), [], 'api');
        //check friends list = 1
        $response = $this->getJson(\route('user.getFriendsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->assertCount(1, $response['data']['friends']);
        //remove user 2
        $this->postJson(\route('user.removeFriend', 2), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $response = $this->getJson(\route('user.getFriendsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->assertCount(0, $response['data']['friends']);
    }

    // @test
    public function testUserRemoveAFriend()
    {
        $data = $this->createTwoUsers();
        //send friend request from user 1 to user 2
        $this->postJson(\route('user.addFriend', $data['secondUser']['id']), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->getJson(\route('user.getFriendsRequestsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        //try to accept it
        $response = $this->postJson(\route('user.approveFriendRequest', 1), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $response->assertStatus(404);
        //check friend list is 0
        $response = $this->getJson(\route('user.getFriendsList'), [], ['Authorization' => 'Bearer '.$data['firstUser']['accessToken']]);
        $this->assertCount(0, $response['data']['friends']);
    }
}
