<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Str;

class AdminProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::orderBy('name')->paginate(20);
        return view('admin.product-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.product-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255','unique:product_categories,slug'],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        ProductCategory::create($data);
        return redirect()->route('admin.product-categories.index')->with('status', 'Category created.');
    }

    public function edit(ProductCategory $productCategory)
    {
        return view('admin.product-categories.edit', compact('productCategory'));
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'slug' => ['nullable','string','max:255', Rule::unique('product_categories','slug')->ignore($productCategory->id)],
        ]);
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $productCategory->update($data);
        return redirect()->route('admin.product-categories.index')->with('status', 'Category updated.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();
        return back()->with('status', 'Category deleted.');
    }
}
