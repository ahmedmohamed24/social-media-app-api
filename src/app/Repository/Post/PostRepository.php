<?php

namespace App\Repository\Post;

use App\Models\Post;
use App\Repository\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class PostRepository extends BaseRepository implements IPostRepository
{
    protected static $model;

    public function __construct(Post $model)
    {
        self::$model = $model;
    }

    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return self::$model->withCount('likes')->with('owner')->with('comments')->paginate($perPage, $columns, $pageName, $page);
    }

    public function find(int $id): ?Model
    {
        return self::$model->with('likes')->with('owner')->with('comments')->find($id);
    }

    public function findOrFail(int $id): ?Model
    {
        return self::$model->with('likes')->with('owner')->with('comments')->findOrFail($id);
    }

    public function getUser(): Model
    {
        return self::$model->owner;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function getUserPosts(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return auth()->user()->posts()->withCount('likes')->with('owner')->with('comments')->paginate($perPage, $columns, $pageName, $page);
    }

    public function findWithDeleted(int $id)
    {
        return self::$model->withTrashed()->findOrFail($id);
    }
}
