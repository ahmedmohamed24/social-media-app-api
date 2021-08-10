<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Reply;
use Illuminate\Http\Response;

class LikeReplyController extends Controller
{
    use ApiResponse;

    public function storeLike(Reply $reply)
    {
        if (0 !== $reply->likes()->where('liked_by', auth()->id())->count()) {
            return $this->response(Response::HTTP_FORBIDDEN, 'already liked', ['This Reply  is already liked by You!'], \null);
        }
        $isValid = $reply->likes()->create(['liked_by' => \auth()->id()]);

        return $this->response(201, 'liked', \null, [$isValid]);
    }

    public function removeLike(Reply $reply)
    {
        if (0 === $reply->likes()->where('liked_by', auth()->id())->count()) {
            return $this->response(Response::HTTP_FORBIDDEN, 'not liked', ['This Reply is not liked by You!'], \null);
        }
        $isDeleted = $reply->likes()->where('liked_by', auth()->id())->delete();

        return $this->response(200, 'disliked', \null, [$isDeleted]);
    }
}
