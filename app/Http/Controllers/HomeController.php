<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get all product categories
        $categories = ProductCategory::all();

        $categoryImages = $categories->mapWithKeys(function ($category) {
            $categoryName = strtolower(str_replace(' ', '-', $category->name));

            $imageUrl = 'https://picsum.photos/300/200?random=' . rand(1, 1000);

            return [$category->id => $imageUrl];
        });

        $featuredProducts = Product::limit(12)->get();

         $featuredProductImages = $featuredProducts->mapWithKeys(function ($product) {
            $imageUrl = 'https://picsum.photos/300/200?random=' . rand(1, 1000);

            return [$product->id => $imageUrl];
        });

        return view('homi', compact('categories', 'featuredProducts', 'categoryImages','featuredProductImages'));
    }
}
