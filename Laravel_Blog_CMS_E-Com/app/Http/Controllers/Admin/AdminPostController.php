<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AdminPostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author','category')->latest()->paginate(12);

        $rows = Comment::selectRaw('categories.name as label, COUNT(comments.id) as total')
            ->join('posts','comments.post_id','=','posts.id')
            ->join('categories','posts.category_id','=','categories.id')
            ->groupBy('categories.id','categories.name')
            ->orderBy('categories.name')
            ->get();

        $labels = $rows->pluck('label');
        $totals = $rows->pluck('total');
        return view('admin.posts.index', compact('posts','labels','totals'));
    }
        public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();

        // IMPORTANT: return the admin view, not the editor one
        return view('admin.posts.create', compact('categories','tags'));
    }

    public function show(\App\Models\Post $post)
{
    $post->load(['author','category','comments.user']);

    $related = \App\Models\Post::where('category_id', $post->category_id)
        ->where('id', '!=', $post->id)
        ->latest()
        ->limit(5)
        ->get();

    return view('admin.posts.show', compact('post','related'));
}

    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', 'Post deleted.');
    }
}
