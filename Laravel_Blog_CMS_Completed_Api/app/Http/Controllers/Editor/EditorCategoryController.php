<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Category;

class EditorCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->orderBy('name')->paginate(12);
        return view('editor.categories.index', compact('categories'));
    }

    public function show(\App\Models\Category $category) {
    $posts = $category->posts()->with(['author','category'])->latest()->paginate(8)->withQueryString();
    return view('editor.categories.show', compact('category','posts'));
}
}
