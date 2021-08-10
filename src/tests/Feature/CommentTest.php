<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class CommentTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    private $accessToken;

    protected function setUp(): void
    {
        parent::setUp();
        //get auth token
        $user = $this->getUser();
        $response = $this->postJson(\route('user.register'), $user);
        $this->accessToken = $response['data']['access_token'];
    }

    // @test
    public function testStatus201OnCommenting()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $response = $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertStatus(201);
    }

    // @test
    public function testCommentSavedInDB()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->assertDatabaseCount('comments', 0);
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertDatabaseCount('comments', 1);
    }

    // @test
    public function testCommentRetrievedWithPost()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response = $this->getJson(\route('post.show', $post['id']));
        $this->assertCount(2, $response['data']['post']['comments']);
    }

    // @test
    public function testStatus200OnUpdateComment()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $newData = Comment::factory()->raw();
        $response = $this->putJson(\route('comment.update', [$post['id'], Comment::first()->id]), $newData);
        $response->assertStatus(200);
    }

    // @test
    public function testUpdateCommentUpdatesDB()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $newData = Comment::factory()->raw();
        $this->putJson(\route('comment.update', [$post['id'], Comment::first()->id]), $newData);
        $this->assertDatabaseHas('comments', $newData);
    }

    // @test
    public function testDeletingCommentReturnStatus200()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response = $this->deleteJson(\route('comment.delete', [$post['id'], Comment::first()->id]));
        $response->assertStatus(200);
    }

    // @test
    public function testDeletingCommentFromPostInfo()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->deleteJson(\route('comment.delete', [$post['id'], Comment::first()->id]));
        $response = $this->getJson(\route('post.show', $post['id']));
        $this->assertCount(0, $response['data']['post']['comments']);
    }

    // @test
    public function testRestoringCommentReturnStatus200()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $commentId = Comment::first()->id;
        $this->deleteJson(\route('comment.delete', [$post['id'], $commentId]));
        $response = $this->getJson(\route('comment.restore', [$post['id'], $commentId]));
        $response->assertStatus(200);
    }

    // @test
    public function testRestoringElementReturnedWithPostInfo()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $commentId = Comment::first()->id;
        $this->deleteJson(\route('comment.delete', [$post['id'], $commentId]));
        $this->getJson(\route('comment.restore', [$post['id'], $commentId]));
        $response = $this->getJson(\route('post.show', $post['id']));
        $this->assertCount(1, $response['data']['post']['comments']);
    }

    // @test
    public function testForceDeleteCommentReturnsStatus200()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $commentId = Comment::first()->id;
        $response = $this->deleteJson(\route('comment.forceDelete', [$post['id'], $commentId]));
        $response->assertStatus(200);
    }

    // @test
    public function testForceDeleteCommentDeletesFromDB()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $comment = Comment::factory()->raw();
        $this->postJson(\route('comment.store', $post['id']), $comment, ['Authorization' => 'Bearer '.$this->accessToken]);
        $commentId = Comment::first()->id;
        $this->assertDatabaseCount('comments', 1);
        $this->deleteJson(\route('comment.forceDelete', [$post['id'], $commentId]), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertDatabaseCount('comments', 0);
    }

    protected function createPost()
    {
        $post = Post::factory()->raw();

        $response = $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);

        return $response['data']['post'];
    }
}
