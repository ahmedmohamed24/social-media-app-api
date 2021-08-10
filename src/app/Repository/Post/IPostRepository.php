<?php

namespace App\Repository\Post;

use Illuminate\Database\Eloquent\Model;

interface IPostRepository
{
    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function find(int $id): ?Model;

    public function findOrFail(int $id): Model;

    public function getUser(): Model;

    public function delete(Model $model): bool;

    public function getUserPosts(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function findWithDeleted(int $id);

    public function restore(int $id): ?Model;

    public function forceDelete(int $id): ?bool;
}
