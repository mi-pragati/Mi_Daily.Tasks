<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
use App\Models\ProductCategory;

class UpdateProductImagesBasedOnCategory extends Migration
{
    public function up()
    {
        // Loop through all products and update image based on category
        $products = Product::all();

        foreach ($products as $product) {
            $categoryName = $product->category->name;  // Get category name
            $slug = strtolower(str_replace(' ', '-', $categoryName)); // Generate slug
            $imageUrl = 'https://picsum.photos/200/200?random=' . crc32($slug); // Create Picsum URL

            // Update product's image with the generated URL
            $product->update(['image' => $imageUrl]);
        }
    }

    public function down()
    {
        // Optionally, you can revert the images back to null or default values
        Product::update(['image' => null]);
    }
}
