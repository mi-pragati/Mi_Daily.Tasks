<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Show all categories
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    // Show posts by category
    public function show(\App\Models\Category $category)
    {
        $posts = $category->posts()
        ->with(['author','category'])
        ->latest()
        ->paginate(8);

    return view('categories.show', compact('category','posts')
    );
    }
}
