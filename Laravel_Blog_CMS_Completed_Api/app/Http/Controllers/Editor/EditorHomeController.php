<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Category;

class EditorHomeController extends Controller
{
    public function index() {
    $categories = \App\Models\Category::withCount('posts')->orderBy('name')->paginate(12);
    return view('editor.categories.index', compact('categories'));
}
}
