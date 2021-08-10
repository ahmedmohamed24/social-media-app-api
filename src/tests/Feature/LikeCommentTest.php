<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LikeCommentTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    // @test
    public function testStatus201OnLikingAComment()
    {
        $this->withoutExceptionHandling();
        [$users] = $this->createPostWithComment();
        $response = $this->postJson(\route('like.comment.save', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(201);
    }

    // @test
    public function testLikeSavedInDB()
    {
        $this->withoutExceptionHandling();
        [$users] = $this->createPostWithComment();
        $this->assertDatabaseCount('likes', 0);
        $this->postJson(\route('like.comment.save', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $this->assertDatabaseCount('likes', 1);
    }

    // @test
    public function testCanLikeOnlyOnce()
    {
        $this->withoutExceptionHandling();
        [$users] = $this->createPostWithComment();
        $this->postJson(\route('like.comment.save', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response = $this->postJson(\route('like.comment.save', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(403);
    }

    // @test
    public function testDislikeCommentReturns200()
    {
        $this->withoutExceptionHandling();
        [$users] = $this->createPostWithComment();
        $this->postJson(\route('like.comment.save', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response = $this->deleteJson(\route('like.comment.remove', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(200);
    }

    // @test
    public function testCanDislikeOnlyLikedComments()
    {
        $this->withoutExceptionHandling();
        [$users] = $this->createPostWithComment();
        $response = $this->deleteJson(\route('like.comment.remove', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $response->assertStatus(403);
    }

    // @test
    public function testLikeRemovedFromDBWhenDislike()
    {
        $this->withoutExceptionHandling();
        [$users] = $this->createPostWithComment();
        $this->postJson(\route('like.comment.save', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $this->assertDatabaseCount('likes', 1);
        $this->deleteJson(\route('like.comment.remove', Comment::first()->id), [], ['Authorization' => 'Bearer '.$users['secondUser']['accessToken']]);
        $this->assertDatabaseCount('likes', 0);
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
