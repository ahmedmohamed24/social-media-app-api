<?php

namespace App\Providers;

use App\Repository\Comment\CommentRepository;
use App\Repository\Comment\ICommentRepository;
use App\Repository\Post\IPostRepository;
use App\Repository\Post\PostRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(IPostRepository::class, PostRepository::class);
        $this->app->bind(ICommentRepository::class, CommentRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
    }
}
