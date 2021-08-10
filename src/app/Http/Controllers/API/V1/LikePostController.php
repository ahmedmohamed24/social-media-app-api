<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LikePostController extends Controller
{
    use ApiResponse;

    public function storeLike(Post $post, Request $request)
    {
        if (0 !== $post->likes()->where('liked_by', auth()->id())->count()) {
            return $this->response(Response::HTTP_FORBIDDEN, 'Already Liked', ['This post is already liked!'], \null);
        }
        $data = $post->likes()->create(['liked_by' => \auth()->id()]);

        return $this->response(201, 'liked', null, $data);
    }

    public function removeLike(Post $post)
    {
        if (0 === $post->likes()->where('liked_by', auth()->id())->count()) {
            return $this->response(Response::HTTP_FORBIDDEN, 'Already not liked', ['This post is already not liked!'], \null);
        }
        $data = $post->likes()->where('liked_by', auth()->id())->delete();

        return $this->response(200, 'liked', null, $data);
    }
}
