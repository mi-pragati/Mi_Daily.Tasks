<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str; 
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Throwable;

class EditorPostController extends Controller
{
    // Show a single post
    public function show(Post $post)
    {
        try {
            // Eager load relations
            $post->load(['author','category','tags','comments.user']);

            // Related posts
            $related = Post::where('category_id', $post->category_id)
                ->where('id', '<>', $post->id)
                ->latest()
                ->take(5)
                ->get();

            return view('editor.posts.show', compact('post','related'));

        } catch (QueryException $e) {
            Log::error('Editor post show DB error', [
                'post_id' => $post->id ?? null,
                'error'   => $e->getMessage(),
            ]);

            $msg = str_contains($e->getMessage(), 'database is locked')
                ? 'Database is busy. Please try again in a moment.'
                : 'Could not load the post right now.';

            return back()->withErrors($msg);

        } catch (Throwable $e) {
            report($e);
            return back()->withErrors('Unexpected error. Please try again.');
        }
    }

    // Show create form
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('editor.posts.create', compact('categories','tags'));
    }

    // Store a new post
   public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'excerpt' => 'nullable|string',
        'body' => 'required|string',
        'tags' => 'array',
    ]);

    $post = new Post($validated);
    $post->user_id = auth()->id(); // 👈 attach logged-in user
    $post->slug = Str::slug($request->title) . '-' . time();
    $post->save();

    // sync tags if needed
    if ($request->has('tags')) {
        $post->tags()->sync($request->tags);
    }

    return redirect()->route('editor.posts.index')
        ->with('success', 'Post created successfully.');
}

    // Optional: edit method if needed
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('editor.posts.edit', compact('post','categories','tags'));
    }
}
