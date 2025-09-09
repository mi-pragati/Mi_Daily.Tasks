<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        // Seeding the product categories table with dummy data
        $categories = [
            'Electronics',
            'Men\'s Fashion',
            'Cutlery',
            'Home Decor',
            'Beauty & Personal Care',
            'Sports & Outdoors',
            'Books',
            'Childrens'
        ];

        foreach ($categories as $category) {
            ProductCategory::create([
                'name' => $category,
                'slug' => \Str::slug($category),
            ]);
        }
    }
}
