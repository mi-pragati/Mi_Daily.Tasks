@extends('layouts.editor')

@section('content')
<div class="container mt-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="h4 fw-bold mb-0">My Posts</h1>
    <a href="{{ route('posts.create') }}" class="btn btn-primary btn-sm">Create Post</a>
  </div>

  @if($posts->count())
    <div class="list-group">
      @foreach($posts as $post)
        <div class="list-group-item d-flex justify-content-between align-items-start">
          <div class="me-3">
            <div class="fw-semibold">
              <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none">{{ $post->title }}</a>
            </div>
            <small class="text-muted">
              {{ optional($post->category)->name ?? 'Uncategorized' }} •
              {{ optional($post->created_at)->format('d M Y') }}
            </small>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
            <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?')">
              @csrf @method('DELETE')
              <button class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-3">{{ $posts->links() }}</div>
  @else
    <div class="alert alert-info">You haven’t written any posts yet.</div>
  @endif
</div>
@endsection
