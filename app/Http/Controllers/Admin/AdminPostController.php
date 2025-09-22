<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author', 'category')->latest()->paginate(12);

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        // Fetch categories and tags for the dropdowns
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Create the post
        $post = Post::create([
        'title' => $validated['title'],
        'category_id' => $validated['category_id'],
        'excerpt' => $validated['excerpt'],
        'body' => $validated['body'],
        'user_id' => auth()->id(),
        'slug' => \Str::slug($validated['title']) . '-' . uniqid(),
    ]);

    if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()->route('admin.posts.index')->with('status', 'Post created successfully!');
    }


    public function show(Post $post)
    {
        $post->load(['author', 'category', 'comments.user']);
        $related = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.posts.show', compact('post', 'related'));
    }

    public function edit(Post $post)
{
    $categories = categories::orderBy('name')->get();  // Get categories for the dropdown
    return view('admin.posts.edit', compact('post', 'categories'));
}


    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Post deleted.');
    }
}
