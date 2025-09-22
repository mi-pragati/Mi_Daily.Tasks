<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get products
        $products = Product::query();

        // Apply filters
        if ($request->has('category') && $request->category != '') {
            $products->where('product_category_id', $request->category);
        }

        if ($request->has('price')) {
        $price = $request->input('price');
        switch ($price) {
            case 'less_than_299':
                $products->where('price', '<', 299);
                break;
            case '299_699':
                $products->whereBetween('price', [299, 699]);
                break;
            case '699_1249':
                 $products->whereBetween('price', [699, 1249]);
                break;
            case 'greater_than_1249':
                 $products->where('price', '>', 1249);
                break;
        }
    }

        if ($request->has('in_stock') && $request->in_stock == 'true') {
            $products->where('stock', '>', 0);
        }

        $products = $products->get();
        $categories = ProductCategory::all();

        return view('customer.dashboard', compact('products', 'categories'));
    }
}
