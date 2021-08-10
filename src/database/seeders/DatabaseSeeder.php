<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        Artisan::call('passport:install');
        Admin::factory()->count(10)->create();
        User::factory()->count(101)->create();
        for ($i = 1; $i < 100; ++$i) {
            Post::factory()->count(1)->create(['owner' => $i]);
            Comment::factory()->count(10)->create(['commented_by' => $i, 'post_id' => $i]);
            Reply::factory()->count(15)->create(['replied_by' => $i, 'comment_id' => $i]);
        }
    }
}
