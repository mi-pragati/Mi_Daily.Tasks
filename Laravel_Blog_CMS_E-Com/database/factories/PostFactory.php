<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition()
    {
        $title = fake()->sentence(6);
        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(7, true),
            'published' => true,
        ];
    }
}
