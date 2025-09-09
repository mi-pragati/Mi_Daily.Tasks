<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $sort     = $request->query('sort', 'newest');

        // Fetch products filtered by category and sorted by the given criteria
        $products = Product::query()
            ->published()
            ->with(['category', 'seller'])
            ->inCategory($category)
            ->sort($sort)
            ->paginate(12)
            ->withQueryString();

        // Get all categories for filtering products
        $categories = ProductCategory::orderBy('name')->get();

        // If the request expects JSON (for API responses)
        if ($request->expectsJson()) {
            return response()->json([
                'data'       => $products,
                'categories' => $categories,
                'filters'    => ['category' => $category, 'sort' => $sort],
            ]);
        }

        return view('shop.index', compact('products', 'categories', 'category', 'sort'));
    }

    public function show(Product $product)
    {
        // Abort if the product is not published or the user is not authorized
        abort_unless(
            $product->status === 'published' ||
            (auth()->check() && (auth()->user()->role === 'admin' || auth()->id() === $product->user_id)),
            404
        );

        return view('shop.show', compact('product'));
    }

    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validate image
            'category_id' => 'required|exists:product_categories,id', // Ensure category exists
        ]);

        // Handle image upload if exists
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Store the image in the 'products' folder
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Create the product record
        $product = Product::create([
            'user_id' => auth()->id(),  // Assuming the logged-in user is the seller
            'product_category_id' => $request->category_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'status' => 'published',
            'stock' => $request->input('stock', 10),  // Default to 10 in stock
        ]);

        return redirect()->route('shop.index')->with('success', 'Product created successfully!');
    }

    public function update(Request $request, Product $product)
    {
        // Validate the incoming data
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validate image
            'category_id' => 'required|exists:product_categories,id', // Ensure category exists
        ]);

        // Handle image upload if exists
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($product->image) {
                Storage::delete('public/' . $product->image);
            }

            // Store the new image in the 'products' folder
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        // Update the product record
        $product->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'price' => $request->price,
            'product_category_id' => $request->category_id,
            'status' => 'published',
            'stock' => $request->input('stock', $product->stock),
        ]);

        return redirect()->route('shop.show', $product->slug)->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Delete the image file from storage if it exists
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }

        // Delete the product
        $product->delete();

        return redirect()->route('shop.index')->with('success', 'Product deleted successfully!');
    }
}
