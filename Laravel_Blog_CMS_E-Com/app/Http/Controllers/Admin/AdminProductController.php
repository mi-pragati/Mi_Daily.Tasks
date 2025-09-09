<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        // Get all categories
        $categories = ProductCategory::all();

        // Get products based on the selected category filter
        $query = Product::query();

        if ($request->has('category') && $request->category != 'other') {
            $query->where('product_category_id', $request->category);
        } elseif ($request->category == 'other') {
            $query->whereNull('product_category_id');
        }

        $products = $query->distinct()->paginate(7);

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'             => ['required', 'exists:users,id'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'title'               => ['required', 'string', 'max:255'],
            'slug'                => ['nullable', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'stock'               => ['required', 'integer', 'min:0'],
            'status'              => ['required', 'in:draft,published'],
            'image'               => ['nullable', 'image', 'max:2048'],
        ]);


        $data['user_id'] = auth()->id();

        if ($request->has('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

          $data['slug'] = $data['slug'] ?? \Str::slug($data['title']) . '-' . uniqid();

        Product::create($data);

        return redirect()->route('admin.products.index')->with('status', 'Product created Successfully!');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'price'               => ['required', 'numeric', 'min:0'],
            'stock'               => ['required', 'integer', 'min:0'],
            'status'              => ['required', 'in:draft,published'],
            'image'               => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->has('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('status', 'Product updated Successfully!');
    }

    public function destroy(Product $product)
{
    if ($product->user_id != auth()->id()) {
        return redirect()->route('admin.products.index')->with('status', 'You do not have permission to delete this product.');
    }

    $product->delete();

    return redirect()->route('admin.products.index')->with('status', 'Product deleted successfully!');
}


}
