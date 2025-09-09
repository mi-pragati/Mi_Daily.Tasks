@extends('layouts.app')

@section('content')
<h1 class="text-3xl mb-4">Posts</h1>

<div class="grid grid-cols-3 gap-6">
    <div class="col-span-2">
        @foreach($posts as $post)
            <article class="mb-6 border-b pb-4">
                <h2 class="text-xl"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
                <p class="text-sm text-gray-600">By {{ $post->author->name }} in {{ $post->category->name }} • {{ $post->created_at->diffForHumans() }}</p>
                <p class="mt-2">{{ \Illuminate\Support\Str::limit($post->excerpt ?? $post->body, 200) }}</p>
            </article>
        @endforeach

        {{ $posts->links() }}
    </div>

    <aside>
        <h3 class="font-bold mb-2">Categories</h3>
        <ul>
            @foreach($categories as $cat)
                <li><a href="{{ route('categories.show', $cat->slug) }}">{{ $cat->name }} ({{ $cat->posts()->count() }})</a></li>
            @endforeach
        </ul>
    </aside>
</div>
@endsection
