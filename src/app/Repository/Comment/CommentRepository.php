<?php

namespace App\Repository\Comment;

use App\Models\Comment;
use App\Repository\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class CommentRepository extends BaseRepository implements ICommentRepository
{
    protected static $model;

    public function __construct(Comment $model)
    {
        self::$model = $model;
    }

    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return self::$model->withCount('likes')->with('likes')->with('owner')->with('post')->paginate($perPage, $columns, $pageName, $page);
    }

    public function find(int $id): ?Model
    {
        return \null;
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function findWithDeleted(int $id)
    {
        return self::$model->withTrashed()->findOrFail($id);
    }

    public function restore(int $id): ?Model
    {
        return self::$model->withTrashed()->where('id', $id)->firstOrFail()->restore();
    }

    public function forceDelete(int $id): ?bool
    {
        return self::$model->withTrashed()->where('id', $id)->firstOrFail()->forceDelete();
    }
}
