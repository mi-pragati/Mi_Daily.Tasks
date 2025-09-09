<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Throwable;

class EditorPostController extends Controller
{
    public function show(Post $post)
    {

        if (request()->boolean('fail')) { throw new \RuntimeException('TEST: manual failure'); }
    if (request()->boolean('sqlfail')) { DB::select('SELECT * FROM __not_a_table__'); } // forces QueryException

        try {
            // Eager load heavy relations
            $post->load(['author','category','tags','comments.user']);

            // Related posts (pure read)
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
            // Any other unexpected failure
            report($e);
            return back()->withErrors('Unexpected error. Please try again.');
        }
    }
}
