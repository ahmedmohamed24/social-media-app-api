<?php

namespace App\Repository\Comment;

use Illuminate\Database\Eloquent\Model;

interface ICommentRepository
{
    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function find(int $id): ?Model;

    public function delete(Model $model): bool;

    public function findWithDeleted(int $id);

    public function restore(int $id): ?Model;

    public function forceDelete(int $id): ?bool;

}
