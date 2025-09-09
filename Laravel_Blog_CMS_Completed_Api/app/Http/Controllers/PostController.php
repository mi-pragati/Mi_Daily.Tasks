<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\NewPostPublished;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index','show']);
    }

    public function mine()
    {
        $posts = Post::with('category')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('posts.mine', compact('posts'));
    }

    public function index(Request $request)
    {
        $categorySlug = $request->query('category');
        $query = Post::with('author','category');

        if ($categorySlug) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        $posts = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('posts.index', compact('posts','categories'));
    }

    public function show(Post $post)
    {
        $post->load('comments.user','tags','author','category');

        $category = $post->category;
        $related  = Post::where('category_id', $post->category_id)
                        ->where('id','!=',$post->id)
                        ->latest()->take(5)->get();

        $allCategories = Category::withCount('posts')->orderBy('name')->get();

        return view('posts.show', compact('post','category','related','allCategories'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Post::class);

        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();

        // Use admin view when the route is under /admin/*
        $view = $request->routeIs('admin.*') ? 'admin.posts.create' : 'posts.create';

        return view($view, compact('categories','tags'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt'     => 'nullable|string',
            'body'        => 'required|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'integer|exists:tags,id',
        ]);

        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $data['user_id'] = auth()->id();
        $post = Post::create($data);

        if (!empty($tags)) {
            $post->tags()->sync($tags);
        }

        $recipients = User::where('id', '!=', auth()->id())->get();
    Notification::send($recipients, new NewPostPublished($post));

        if ($request->routeIs('admin.*') || (auth()->user()?->role === 'admin')) {
            return redirect()->route('admin.posts.index')->with('success', 'Post created.');
        }

        return redirect()->route('posts.mine')->with('success', 'Post created.');
    }

    public function edit(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();

        // You can also choose an admin edit view if you have one
        return view('posts.edit', compact('post','categories','tags'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'excerpt'     => 'nullable|string',
            'body'        => 'required|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'integer|exists:tags,id',
        ]);

        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $post->update($data);
        $post->tags()->sync($tags);

        if ($request->routeIs('admin.*') || (auth()->user()?->role === 'admin')) {
            return redirect()->route('admin.posts.index')->with('success', 'Post updated.');
        }

        return redirect()->route('posts.show', $post->slug)->with('success', 'Post updated.');
    }

    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        if ($request->routeIs('admin.*') || (auth()->user()?->role === 'admin')) {
            return redirect()->route('admin.posts.index')->with('success', 'Post deleted.');
        }

        return redirect()->route('posts.mine')->with('success', 'Post deleted.');
    }
}
