<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Comment;
use Illuminate\Http\Response;

class LikeCommentController extends Controller
{
    use ApiResponse;

    public function storeLike(Comment $comment)
    {
        if (0 !== $comment->likes()->where('liked_by', auth()->id())->count()) {
            return $this->response(Response::HTTP_FORBIDDEN, 'already liked', ['This Comment  is already liked by You!'], \null);
        }
        $isValid = $comment->likes()->create(['liked_by' => \auth()->id()]);

        return $this->response(201, 'liked', \null, [$isValid]);
    }

    public function removeLike(Comment $comment)
    {
        if (0 === $comment->likes()->where('liked_by', auth()->id())->count()) {
            return $this->response(Response::HTTP_FORBIDDEN, 'not liked', ['This Comment  is not liked by You!'], \null);
        }
        $isDeleted = $comment->likes()->where('liked_by', auth()->id())->delete();

        return $this->response(200, 'disliked', \null, [$isDeleted]);
    }
}
