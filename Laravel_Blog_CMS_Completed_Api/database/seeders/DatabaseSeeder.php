<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Admin user with company details
        User::create([
            'name' => 'Admin User',
            'email' => 'adminn@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company_name' => 'Example Company Pvt Ltd',
            'company_address' => '123, Business Street, City',
            'company_phone' => '+911234567890'
        ]);

        // Create two editor accounts
        $editors = User::factory()->count(2)->create();

        // Create categories
        $categories = collect([
            ['name' => 'Travel', 'slug' => 'Travel', 'description' => 'Travelling'],
            ['name' => 'Tech', 'slug' => 'tech', 'description' => 'Technology and coding'],
            ['name' => 'Life', 'slug' => 'life', 'description' => 'Lifestyle content'],
            ['name' => 'Beauty', 'slug' => 'Beauty', 'description' => 'World of Beauty'],
            ['name' => 'Health', 'slug' => 'Health', 'description' => 'Health is Wealth'],
            ['name' => 'Business', 'slug' => 'Business', 'description' => 'Business around the world'],
            ['name' => 'Music', 'slug' => 'Music', 'description' => 'The Musical Theropy'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Supper Sports'],
            ['name' => 'Agriculture', 'slug' => 'Agriculture', 'description' => 'Grow With Agro'],
            ['name' => 'Self-improvement', 'slug' => 'Self-improvement', 'description' => 'Sometime-Selftime'],
        ])->map(function ($c){ return Category::create($c); });

        // Tags
        $tags = ['Laravel','PHP','Tips','DevOps','Career'];
        foreach($tags as $t) {
            Tag::create(['name'=>$t,'slug'=>\Illuminate\Support\Str::slug($t)]);
        }

        // Create dummy posts: 5 posts per category
        foreach($categories as $category) {
            for ($i=1;$i<=5;$i++) {
                $author = $editors->random();
                $post = Post::create([
                    'user_id' => $author->id,
                    'category_id' => $category->id,
                    'title' => "{$category->name} Post {$i}",
                    'excerpt' => "Excerpt for {$category->name} Post {$i}",
                    'body' => "This is the body of {$category->name} Post {$i}. " . fake()->paragraphs(5, true),
                    'published' => true,
                ]);
                // attach one or two tags randomly
                $tagIds = \App\Models\Tag::inRandomOrder()->take(rand(1,2))->pluck('id')->toArray();
                $post->tags()->sync($tagIds);
            }
        }
    }
}
