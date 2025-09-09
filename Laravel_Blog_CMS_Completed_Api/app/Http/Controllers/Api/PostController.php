<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return Post::with('category', 'user')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);

        return Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $request->category_id,
            'user_id' => $request->user()->id
        ]);
    }

    public function show(Post $post)
    {
        return $post->load('category', 'user');
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'sometimes|required',
            'body' => 'sometimes|required',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        $post->update($request->all());
        return $post;
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Post deleted']);
    }
}
