<?php

namespace App\Repository\Post;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface IPostRepository
{
    public function paginate(?int $perPage = null, array $columns = ['*'], string $pageName = 'page', ?int $page = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function find(int $id): ?Model;

    public function findOrFail(int $id): ?Model;

    public function getUser(): Model;

    public function getUserPosts(): Collection;

    public function delete(Model $model): bool;
}
