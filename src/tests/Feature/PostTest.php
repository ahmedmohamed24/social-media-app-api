<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PostTest extends TestCase
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
    public function test201OnCreatingNewPost()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->raw();
        $response = $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertStatus(201);
    }

    // @test
    public function testPostSaveIntoDB()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->raw();
        $this->assertDatabaseCount('posts', 0);
        $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertDatabaseCount('posts', 1);
    }

    // @test
    public function testPostContentIsRequired()
    {
        $post = Post::factory()->raw(['content' => '']);
        $response = $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertJsonFragment(['content' => ['The content field is required.']]);
    }

    // @test
    public function testOnlyAuthCanCreatePost()
    {
        $post = Post::factory()->raw();
        $response = $this->postJson(\route('post.store'), $post);
        $response->assertJsonFragment(['errors' => ['Unauthenticated']]);
    }

    // @test
    public function testUserCanRetrieveHisPosts()
    {
        $post = Post::factory()->raw();
        $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);
        $post = Post::factory()->raw();
        $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response = $this->getJson(\route('user.posts'), ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertCount(2, $response['data']['posts']['data']);
    }

    // @test
    public function test200OnUpdatePost()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->raw();
        $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);
        $newData = ['content' => $this->faker->sentence()];
        $response = $this->putJson(\route('post.update', Post::latest()->first()->id), $newData, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response->assertStatus(200);
    }

    // @test
    public function testUpdatedInDB()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->raw();
        $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);
        $newData = ['content' => $this->faker->sentence()];
        $this->putJson(\route('post.update', Post::latest()->first()->id), $newData, ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertDatabaseHas('posts', $newData);
    }

    // @test
    public function testDeletePost()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->raw();
        $this->postJson(\route('post.store'), $post, ['Authorization' => 'Bearer '.$this->accessToken]);
        $response = $this->getJson(\route('user.posts'), ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertCount(1, $response['data']['posts']['data']);
        $this->deleteJson(\route('post.delete', Post::latest()->first()->id), ['Authorization' => 'Bearer '.$this->accessToken]);
        $this->assertNotNull(Post::withTrashed()->first()->deleted_at);
    }
}
