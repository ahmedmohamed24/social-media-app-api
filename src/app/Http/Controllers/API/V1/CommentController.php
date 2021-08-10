<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CreationRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Comment;
use App\Models\Post;
use App\Repository\Comment\ICommentRepository;

class CommentController extends Controller
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

    public function store(Post $post, CreationRequest $request)
    {
        $comment = $post->comments()->create(['content' => $request->content, 'commented_by' => \auth()->id()]);

        return $this->response(201, 'created', \null, ['comment' => $comment]);
    }

    public function update(Post $post, Comment $comment, UpdateRequest $request)
    {
        $this->authorize('update', $comment);
        $comment = $post->comments()->where('id', $comment->id)->firstOrFail()->update(['content' => $request->content]);

        return $this->response(200, 'updated', \null, $comment);
    }

    public function delete(Post $post, Comment $comment)
    {
        $this->authorize('delete', $comment);
        $isDeleted = $post->comments()->where('id', $comment->id)->firstOrFail()->delete();
        if (!$isDeleted) {
            return $this->response(500, 'Internal Error occurred!', ['Unexpected Error happened, please try again!'], null);
        }

        return $this->response(200, 'deleted', \null, \null);
    }

    public function forceDelete(Post $post, int $comment)
    {
        $comment = $post->comments()->where('id', $comment)->firstOrFail();
        $this->authorize('forceDelete', $comment);
        $isDeleted = $comment->forceDelete();
        if (!$isDeleted) {
            return $this->response(500, 'Internal Error occurred!', ['Unexpected Error happened, please try again!'], null);
        }

        return $this->response(200, 'permanent deleted', \null, \null);
    }

    public function restore(Post $post, int $comment)
    {
        $comment = $post->comments()->withTrashed()->where('id', $comment)->firstOrFail();
        $this->authorize('restore', $comment);
        $comment = $comment->restore();

        return $this->response(200, 'restored', \null, ['comment' => $comment]);
    }
}
