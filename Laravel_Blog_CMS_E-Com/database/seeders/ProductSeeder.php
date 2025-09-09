<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\Product;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get all categories
        $categories = ProductCategory::all();

        foreach ($categories as $category) {
            // Create 6-7 products for each category
            for ($i = 1; $i <= 7; $i++) {
            $imageUrl = $this->generateDummyImageUrl($category->slug, $i);
                Product::create([
                    'user_id' => 1,
                    'product_category_id' => $category->id,
                    'title' => $category->name . ' Product ' . $i,
                    'slug' => strtolower(str_replace(' ', '-', $category->name)) . '-product-' . $i . '-' . uniqid(),
                    'description' => $faker->sentence, // Random description from Faker
                    'price' => $faker->randomFloat(2, 10, 5000), // Random price between 10 and 5000
                    'status' => 'published',
                    'stock' => $faker->numberBetween(5, 50), // Random stock between 5 and 50
                ]);
            }
        }
    }
    private function generateDummyImageUrl($categorySlug, $productIndex)
    {
        // Used Unsplash for category-based images
        return "https://source.unsplash.com/400x300/?{$categorySlug},product,{$productIndex}";
    }
}
