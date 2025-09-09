<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Arr;

class MyPostsSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'pk@gmail.com';
        $user = User::firstWhere('email', $email);

        if (!$user) {
            $this->command->warn("No user found for {$email}");
            return;
        }

        $catIds = Category::pluck('id')->all();
        Post::factory()
            ->count(5)
            ->state(fn() => [
                'user_id'     => $user->id,
                'category_id' => Arr::random($catIds),
            ])
            ->create();
    }
}
