<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    // Display all products
  public function index(Request $request)
{
    $categories = ProductCategory::all();

    $query = Product::with('category');

       if ($request->has('category') && $request->category != '') {
        $query->where('product_category_id', $request->category);
    }

    // If the user is not logged in, add dummy images to products without an image
    if (!auth()->check()) {
        $products = $query->get();  // Get all products first
        $products = $products->map(function($product) {
            // If there's no image, add a dummy image
            if (!$product->image) {
                $product->image = 'https://via.placeholder.com/150';
            }
            return $product;
        });

         $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products, $products->count(), 20, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]
        );
    } else {
        // If the user is logged in, paginate products
        $products = $query->paginate(20);  // 20 products per page
    }

    return view('products.index', compact('products', 'categories'));
}

public function store(Request $request)
{
    $request->validate([
        'title'        => 'required|string|max:255',
        'description' => 'required|string',
        'price'       => 'required|numeric|min:0',
        'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',

    ]);
$title = $request->title;

    // Slug from name (unique-ish)
    $base = Str::slug($request->name);
    $slug = $base ?: 'item';
    $i = 2;
    while (Product::where('slug', $slug)->exists()) {
        $slug = $base.'-'.$i++;
    }

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('products', 'public');
    } else {
        $imagePath = 'products/default-image.jpg';
    }

    Product::create([
        'title'        => $request->name,
        'slug'        => $slug,
        'description' => $request->description,
        'price'       => $request->price,
        'image'       => $imagePath,
        'product_category_id' => $request->product_category_id,
    ]);

    return redirect()->route('products.index')->with('status', 'Product created.');
}

public function show(Product $product)
{
    // main image URL (your existing logic)
    $imageUrl = $product->image
        ? asset('storage/'.$product->image)
        : 'https://picsum.photos/600/400?random=' . rand(1, 1000);

    // Eager-load category for the view
    $product->load('category');

   // Store recently viewed in session
    if (auth()->check()) {
        $sessionKey = 'recently_viewed_user_' . auth()->id();
    } else {
        $sessionKey = 'recently_viewed_guest';
    }

    $recentlyViewed = session($sessionKey, []);

    // Remove duplicates
    $recentlyViewed = collect($recentlyViewed)
                        ->reject(fn($p) => $p['id'] === $product->id)
                        ->take(9)  // max 9 old items
                        ->toArray();

    array_unshift($recentlyViewed, [
        'id' => $product->id,
        'title' => $product->title,
        'slug' => $product->slug,
        'price' => $product->price,
        'image' => $product->image ?? '/default-product.png',
    ]);

    session([$sessionKey => array_slice($recentlyViewed, 0, 10)]); // keep max 10



    // Try related by same category, exclude current
    $related = Product::with('category')
        ->where('id', '!=', $product->id)
        ->when($product->product_category_id, function ($q) use ($product) {
            $q->where('product_category_id', $product->product_category_id);
        })
        ->inRandomOrder()
        ->take(5)
        ->get();

    // Fallback: if fewer than 4, backfill with other categories
    if ($related->count() < 4) {
        $backfill = Product::with('category')
            ->where('id', '!=', $product->id)
            ->when($product->product_category_id, function ($q) use ($product) {
                $q->where('product_category_id', '!=', $product->product_category_id);
            })
            ->inRandomOrder()
            ->take(5 - $related->count())
            ->get();

        $related = $related->concat($backfill);
    }

    return view('products.show', compact('product', 'imageUrl', 'related'));
}


    // Filter products by category
    public function filterByCategory(ProductCategory $category, Request $request)
    {
        $categories = ProductCategory::orderBy('name')->get();

        $products = Product::with('category')
            ->published()
            ->where('product_category_id', $category->id)
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->input('search');
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', "%{$term}%")
                       ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products'  => $products,
            'categories'=> $categories,
            'activeCategory' => $category,
        ]);
    }
}
