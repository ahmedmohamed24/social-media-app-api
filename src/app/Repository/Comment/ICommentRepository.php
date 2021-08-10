<?php

namespace App\Repository\Comment;

use Illuminate\Database\Eloquent\Model;

interface ICommentRepository
{
    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function find(int $id): ?Model;

    public function findOrFail(int $id): ?Model;

    public function getOwner(): Model;

    public function getModel(): Model;

    public function delete(Model $model): bool;

    public function restore(Model $model): bool;

    public function forceDelete(Model $model): bool;
}
