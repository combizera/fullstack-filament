<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reply;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(20)->create();
        $tags = Tag::factory(10)->create();
        $categories = Category::factory(10)->create();
        $posts = Post::factory(10)
            ->recycle($users)
            ->recycle($categories)
            //->recycle($tags)
            ->create();
        $comments = Comment::factory(10)
            ->recycle($users)
            ->recycle($posts)
            ->create();
        Reply::factory(10)
            ->recycle($users)
            ->recycle($comments)
            ->create();

        User::factory()->create([
            'name' => 'Ygor Combi',
            'is_admin' => true,
            'email' => 'test@example.com',
            'password' => 'test@example.com',
        ]);
    }
}
