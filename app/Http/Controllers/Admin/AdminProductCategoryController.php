<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class AdminProductCategoryController extends Controller
{
    // Show list of product categories
    public function index()
    {
        $categories = ProductCategory::orderBy('name')->paginate(20); // You can change pagination count as needed
        return view('admin.product-categories.index', compact('categories'));
    }

    // Show create form for product category
    public function create()
{
    $categories = ProductCategory::orderBy('name')->get();
    return view('admin.product-categories.create', compact('categories'));
}

    // Store a new product category
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_categories,slug'],
        ]);

        $data['slug'] = $data['slug'] ?? \Str::slug($data['name']);

        ProductCategory::create($data);

        return redirect()->route('admin.product-categories.index')->with('status', 'Product Category created successfully!');
    }

    // Show form for editing an existing product category
    public function edit(ProductCategory $productCategory)
    {
        return view('admin.product-categories.edit', compact('productCategory'));
    }

    // Update a product category
    public function update(Request $request, ProductCategory $productCategory)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_categories,slug,' . $productCategory->id],
        ]);

        $productCategory->update($request->all());

        return redirect()->route('admin.product-categories.index')->with('status', 'Product Category updated successfully!');
    }

    // Delete a product category
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();
        return redirect()->route('admin.product-categories.index')->with('status', 'Product Category deleted successfully!');
    }
}
