<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('posts')->orderBy('name')->get();

        $labels = $categories->pluck('name');
        $counts = $categories->pluck('posts_count');

        return view('admin.categories.index', compact('categories','labels','counts'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120', Rule::unique('categories','name')],
            'slug' => ['nullable','string','max:160', Rule::unique('categories','slug')],
            'description' => ['nullable','string'],
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('status','Category created.');
    }
    public function show(Category $category)
    {
        $posts = Post::with('author','category')
            ->where('category_id', $category->id)
            ->latest()
            ->paginate(12);

        return view('admin.categories.show', compact('category','posts'));
    }
}
