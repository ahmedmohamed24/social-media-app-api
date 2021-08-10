<?php

namespace App\Repository\Post;

use App\Models\Post;
use App\Repository\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PostRepository extends BaseRepository implements IPostRepository
{
    protected static $model;

    public function __construct(Post $model)
    {
        self::$model = $model;
    }

    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return self::$model->withCount('likes')->with('owner')->with('commentsLimit')->withCount('comments')->paginate($perPage, $columns, $pageName, $page);
    }

    public function find(int $id): ?Model
    {
        return self::$model->with('likes')->with('owner')->with('comments')->find($id);
    }

    public function findOrFail(int $id)
    {
        return Cache::remember(
            'post'.$id,
            '360000',
            fn () => self::$model->with('likes')->with('likes.owner')->with('owner')
                ->with('comments')->withCount('likes')
                ->withCount('comments')->with('comments.likes')->with(['comments' => function ($query) {
                    $query->withCount('likes');
                    $query->with('likes');
                    $query->with(['likes' => function ($q) {
                        $q->with('owner');
                    }]);
                    $query->withCount('replies');
                    $query->with('replies');
                    $query->with(['replies' => function ($q) {
                        $q->with('owner');
                        $q->with('likes');
                        $q->with(['likes' => function ($qu) {
                            $qu->with('owner');
                        }]);
                        $q->withCount('likes');
                    }]);
                }])->findOrFail($id)
        );
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

    public function restore(int $id): ?Model
    {
        return self::$model->withTrashed()->where('id', $id)->firstOrFail()->restore();
    }

    public function forceDelete(int $id): ?bool
    {
        return self::$model->withTrashed()->where('id', $id)->firstOrFail()->forceDelete();
    }
}
