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
class LikeReplyTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus201OnLikingAReply()
    {
        $this->withoutExceptionHandling();
        [$users,$comment,$post,$replyResponse] = $this->createPostWithCommentAndReply();
        $response = $this->postJson(\route('like.reply.save', $replyResponse['data']['reply']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(201);
    }

    // @test
    public function testLikeSavedInDB()
    {
        $this->withoutExceptionHandling();
        [$users,$comment,$post,$replyResponse] = $this->createPostWithCommentAndReply();
        $this->assertDatabaseCount('likes', 0);
        $this->postJson(\route('like.reply.save', $replyResponse['data']['reply']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $this->assertDatabaseCount('likes', 1);
    }

    // @test
    public function testCanLikeOnlyOnce()
    {
        $this->withoutExceptionHandling();
        [$users,$comment,$post,$replyResponse] = $this->createPostWithCommentAndReply();
        $this->postJson(\route('like.reply.save', $replyResponse['data']['reply']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response = $this->postJson(\route('like.reply.save', $replyResponse['data']['reply']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(403);
    }

    // @test
    public function testDislikeReplyReturns200()
    {
        $this->withoutExceptionHandling();
        [$users,$comment,$post,$replyResponse] = $this->createPostWithCommentAndReply();
        $this->postJson(\route('like.reply.save', $replyResponse['data']['reply']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response = $this->deleteJson(\route('like.reply.remove', $replyResponse['data']['reply']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(200);
    }

    // @test
    public function testLikeRemovedFromDBWhenDislike()
    {
        $this->withoutExceptionHandling();
        [$users,$comment,$post,$replyResponse] = $this->createPostWithCommentAndReply();
        $this->postJson(\route('like.reply.save', $replyResponse['data']['reply']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $this->assertDatabaseCount('likes', 1);
        $this->deleteJson(\route('like.reply.remove', $replyResponse['data']['reply']['id']), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $this->assertDatabaseCount('likes', 0);
    }

    private function createPostWithCommentAndReply()
    {
        $users = $this->createTwoUsers();
        //create post with user and add comment
        $post = Post::factory()->create(['owner' => $users['firstUser']['id']]);
        $comment = Comment::factory()->raw(['commented_by' => $users['firstUser']['id']]);
        $post->comments()->create($comment);
        $reply = Reply::factory()->raw();
        $response = $this->postJson(\route('reply.store', $post->comments()->first()->id), $reply, ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);

        return [$users, $comment, $post, $response];
    }
}
