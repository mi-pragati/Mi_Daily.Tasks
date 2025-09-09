<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewCommentOnYourPost;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {

        if (!auth()->check() || !in_array(auth()->user()->role, ['editor','admin'])) {
            abort(403);
        }

        $data = $request->validate([
            'body' => ['required','string','max:2000'],
        ]);

        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);

        if ($post->author && $post->author->id !== auth()->id()) {
            Log::info('Sending NewCommentOnYourPost', [
            'to'      => $post->author->email,
            'post_id' => $post->id,
            'by'      => auth()->id(),
        ]);

        $post->author->notifyNow(new NewCommentOnYourPost($comment));
        Log::info('NewCommentOnYourPost sent');

        }

        return back()->with('success', 'Comment added.');
    }

    public function edit(Comment $comment)
    {
        $this->authorize('update', $comment);
        return view('comments.edit', compact('comment'));
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $comment->update(['body' => $data['body']]);

        $route = request()->routeIs('admin.*')
            ? 'admin.posts.show'
            : (request()->routeIs('editor.*') ? 'editor.posts.show' : 'posts.show');

        return redirect()->route($route, $comment->post->slug)
            ->with('success', 'Comment updated.');
    }

    public function destroy(Request $request, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $post = $comment->post;
        $comment->delete();

        if ($request->filled('return_to')) {
        return redirect()->to($request->input('return_to'))
            ->with('success', 'Comment deleted.');
    }

    $target = in_array(auth()->user()->role ?? null, ['editor','admin'])
        ? route('editor.posts.show', $post->slug)
        : route('posts.show', $post->slug);

    return redirect($target)->with('success', 'Comment deleted.');
}
}
