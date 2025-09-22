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
             if ($product->category) {
        $categoryName = $product->category->name;
        $slug = strtolower(str_replace(' ', '-', $categoryName));
        $imageUrl = 'https://picsum.photos/200/200?random=' . crc32($slug);

        $product->update(['image' => $imageUrl]);
    }
}
    }

    public function down()
    { 
        if (Schema::hasColumn('products', 'image')) 
        {
        Product::query()->update(['image' => null]);
    }

    }
}
