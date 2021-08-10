<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reply\CreationRequest;
use App\Http\Requests\Reply\UpdateRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Comment;
use App\Models\Reply;
use App\Repository\Comment\ICommentRepository;

class ReplyController extends Controller
{
    use ApiResponse;
    protected $model;

    public function __construct(ICommentRepository $model)
    {
        $this->model = $model;
    }

    public function paginate()
    {
    }

    public function store(Comment $comment, CreationRequest $request)
    {
        $reply = $comment->replies()->create(['content' => $request->content, 'replied_by' => \auth()->id()]);

        return $this->response(201, 'created', \null, ['reply' => $reply]);
    }

    public function update(Comment $comment, Reply $reply, UpdateRequest $request)
    {
        $this->authorize('update', $reply);
        $reply = $comment->replies()->where('id', $reply->id)->firstOrFail()->update(['content' => $request->content]);

        return $this->response(200, 'updated', \null, $reply);
    }

    public function delete(Comment $comment, Reply $reply)
    {
        $this->authorize('delete', $reply);
        $isDeleted = $comment->replies()->where('id', $reply->id)->firstOrFail()->delete();
        if (!$isDeleted) {
            return $this->response(500, 'Internal Error occurred!', ['Unexpected Error happened, please try again!'], null);
        }

        return $this->response(200, 'deleted', \null, \null);
    }

    public function forceDelete(Comment $comment, int $reply)
    {
        $reply = $comment->replies()->withTrashed()->where('id', $reply)->firstOrFail();
        $this->authorize('forceDelete', $reply);
        $isDeleted = $reply->forceDelete();
        if (!$isDeleted) {
            return $this->response(500, 'Internal Error occurred!', ['Unexpected Error happened, please try again!'], null);
        }

        return $this->response(200, 'permanent deleted', \null, \null);
    }

    public function restore(Comment $comment, int $reply)
    {
        $reply = $comment->replies()->withTrashed()->where('id', $reply)->firstOrFail();
        $this->authorize('restore', $reply);
        $reply = $reply->restore();

        return $this->response(200, 'restored', \null, ['reply' => $reply]);
    }
}
