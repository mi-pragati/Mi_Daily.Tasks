<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        if (!$user) {
            // Create a dummy admin user if no user is found
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        // Get all product categories
        $categories = ProductCategory::all();

        foreach ($categories as $category) {
            // Create 5 dummy products for each category
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'product_category_id' => $category->id,
                    'user_id' => $user->id,
                    'title' => $category->name . ' Product ' . $i,
                    'slug' => \Str::slug($category->name . ' Product ' . $i).'-'.uniqid(),
                    'description' => 'This is a dummy description for ' . $category->name . ' product ' . $i,
                    'price' => rand(100, 10000),
                    'stock' => rand(1, 100),
                    'status' => 'published',
                    'image' => $this->getDummyImage($category->name), // Get matching image based on category
                ]);
            }
        }
    }

    // Function to return different image URLs based on the category name
    private function getDummyImage($categoryName)
    {
        switch (strtolower($categoryName)) {
            case 'electronics':
                return 'https://picsum.photos/200/200?electronics';
            case 'men\'s fashion':
                return 'https://picsum.photos/200/200?mens-fashion';
            case 'cutlery':
                return 'https://picsum.photos/200/200?cutlery';
            case 'home decor':
                return 'https://picsum.photos/200/200?home-decor';
            case 'beauty & personal care':
                return 'https://picsum.photos/200/200?beauty';
            case 'sports & outdoors':
                return 'https://picsum.photos/200/200?sports';
            case 'books':
                return 'https://picsum.photos/200/200?books';
            default:
                return 'https://picsum.photos/200/200';
        }
    }
}
