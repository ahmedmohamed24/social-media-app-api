<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LikePostTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
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
    public function testLikeToPostReturn201Status()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $response = $this->postJson(\route('like.post.save', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertStatus(201);
    }

    // @test
    public function testLikeToPostSavedToDB()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $this->assertDatabaseCount('likes', 0);
        $this->postJson(\route('like.post.save', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertDatabaseCount('likes', 1);
    }

    // @test
    public function testCanLikePostOnlyOnce()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $this->assertDatabaseCount('likes', 0);
        $this->postJson(\route('like.post.save', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $response = $this->postJson(\route('like.post.save', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertNotEmpty($response['errors']);
    }

    // @test
    public function test200StatusOnUnlike()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $this->postJson(\route('like.post.save', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $response = $this->deleteJson(\route('like.post.remove', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertStatus(200);
    }

    // @test
    public function testUnlikePostDeletesIt()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $this->assertDatabaseCount('likes', 0);
        $this->postJson(\route('like.post.save', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertDatabaseCount('likes', 1);
        $this->deleteJson(\route('like.post.remove', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertDatabaseCount('likes', 0);
    }

    // @test
    public function testCannotUnlikeUnLikedPost()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $response = $this->deleteJson(\route('like.post.remove', $post['id']), [], ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    // @test
    public function testOnlyAuthCanLikePost()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['owner' => $user->id]);
        $response = $this->postJson(\route('like.post.save', $post->id), [], []);
        $response->assertStatus(401);
    }

    // @test
    public function testRetrievePostLikes()
    {
        $this->withoutExceptionHandling();
        $post = $this->createPost();
        $this->actingAs(User::factory()->create())->postJson(\route('like.post.save', $post['id']), [])->assertStatus(201);
        $response = $this->getJson(\route('post.show', $post['id']));
        $this->assertCount(1, $response['data']['post']['likes']);
    }

    protected function createPost()
    {
        $post = Post::factory()->raw();

        $response = $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);

        return $response['data']['post'];
    }
}
