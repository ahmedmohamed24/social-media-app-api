<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\CreationRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Post;
use App\Repository\Post\IPostRepository;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ApiResponse;
    protected $model;

    public function __construct(IPostRepository $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        $perPage = \request('perPage', null);
        $columns = \request('perPage', ['*']);
        $pageName = \request('perPage', 'page');
        $page = \request('perPage', null);
        $posts = $this->model->paginate($perPage, $columns, $pageName, $page);

        return $this->response(200, 'Success', \null, ['posts', $posts]);
    }

    public function show(int $post)
    {
        $post = $this->model->findOrFail($post);

        return $this->response(200, 'success', \null, ['post' => $post]);
    }

    public function store(CreationRequest $request)
    {
        $post = ['content' => $request->content, 'owner' => \auth()->id()];
        $post = Post::create($post);

        return $this->response(201, 'created', null, ['post' => $post]);
    }

    public function getUserPosts(Request $request)
    {
        $posts = $this->model->getUserPosts();

        return $this->response(200, 'success', \null, ['posts' => $posts]);
    }

    public function update(Post $post, UpdateRequest $request)
    {
        $this->authorize('update', $post);
        $post->update($request->validated());

        return $this->response(200, 'updated', \null, ['post' => $post]);
    }

    public function delete(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return $this->response(200, 'deleted', \null, \null);
    }

    public function forceDelete(int $id)
    {
        $post = $this->model->findWithDeleted($id);
        $this->authorize('forceDelete', $post);
        $post->forceDelete();

        return $this->response(200, 'permanently deleted', \null, \null);
    }

    public function restore(int $id)
    {
        $post = $this->model->findWithDeleted($id);
        $this->authorize('restore', $post);
        $post->restore();

        return $this->response(200, 'restored', \null, ['post' => $post]);
    }
}
