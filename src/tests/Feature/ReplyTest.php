<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ReplyTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus201OnSendingReply()
    {
        $this->withoutExceptionHandling();
        [$users,$comment,$post] = $this->createPostWithComment();
        //Reply with the other user
        $reply = Reply::factory()->raw();
        $response = $this->postJson(\route('reply.store', $post->comments()->first()->id), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(201);
    }

    public function testReplySavedInDB()
    {
        $this->withoutExceptionHandling();
        [$users,$comment,$post] = $this->createPostWithComment();
        //Reply with the other user
        $reply = Reply::factory()->raw();
        $this->assertDatabaseCount('replies', 0);
        $this->postJson(\route('reply.store', $post->comments()->first()->id), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $this->assertDatabaseCount('replies', 1);
    }

    public function testReplyContentIsRequired()
    {
        [$users,$comment,$post] = $this->createPostWithComment();
        //Reply with the other user
        $reply = Reply::factory()->raw(['content' => '']);
        $response = $this->postJson(\route('reply.store', $post->comments()->first()->id), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(401);
    }

    public function testReplyCommentIdMustBeValid()
    {
        [$users,$comment,$post] = $this->createPostWithComment();
        //Reply with the other user
        $reply = Reply::factory()->raw();
        $response = $this->postJson(\route('reply.store', \rand(100, 200)), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(404);
    }

    public function testAddingMultipleReplies()
    {
        [$users,$comment,$post] = $this->createPostWithComment();
        //Reply with the other user
        $reply = Reply::factory()->raw();
        $this->postJson(\route('reply.store', $post->comments()->first()->id), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']])->assertStatus(201);
        $this->postJson(\route('reply.store', $post->comments()->first()->id), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']])->assertStatus(201);
        $this->postJson(\route('reply.store', $post->comments()->first()->id), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']])->assertStatus(201);
        $this->postJson(\route('reply.store', $post->comments()->first()->id), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']])->assertStatus(201);
        $this->assertCount(4, $post->comments()->first()->replies);
    }

    private function createPostWithComment()
    {
        $users = $this->createTwoUsers();
        //create post with user and add comment
        $post = Post::factory()->create(['owner' => $users['firstUser']['id']]);
        $comment = Comment::factory()->raw(['commented_by' => $users['firstUser']['id']]);
        $post->comments()->create($comment);

        return [$users, $comment, $post];
    }
}
