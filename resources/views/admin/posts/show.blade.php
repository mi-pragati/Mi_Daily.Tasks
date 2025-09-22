@extends('admin.layouts.app')
@section('title', 'Admin • '.$post->title)

@section('content')
<div class="container py-4">
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
      @if($post->category)
        <li class="breadcrumb-item">
          <a href="{{ route('admin.categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
        </li>
      @endif
      <li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($post->title, 50) }}</li>
    </ol>
  </nav>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <h1 class="h3 mb-2">{{ $post->title }}</h1>
      <div class="text-muted small mb-3">
        @if($post->category)
          <span class="badge bg-secondary">{{ $post->category->name }}</span>
          <span class="mx-1">•</span>
        @endif
        <time>{{ optional($post->created_at)->format('d M Y') }}</time>
        <span class="mx-1">•</span>
        by <strong class="text-dark">{{ optional($post->author)->name ?? 'Unknown' }}</strong>
      </div>
      <div style="line-height:1.8">{!! $post->body !!}</div>
    </div>
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <h2 class="h6 mb-3">Comments</h2>

      @auth
        @if(in_array(auth()->user()->role, ['editor','admin']))
          <form method="POST" action="{{ route('comments.store', $post->slug) }}" class="mb-3">
            @csrf
            <label class="form-label">Add a comment</label>
            <textarea name="body" rows="3" class="form-control mb-2" required>{{ old('body') }}</textarea>
            <button class="btn btn-primary btn-sm">Post Comment</button>
          </form>
        @else
          <div class="alert alert-info small">Only editors and admins can comment.</div>
        @endif
      @endauth

      @foreach($post->comments as $comment)
        <div class="border rounded p-2 mb-2">
          <div class="small text-muted mb-1">
            <strong>{{ $comment->user->name }}</strong>
            <span> • {{ $comment->created_at->diffForHumans() }}</span>
          </div>
          <div class="mb-2">{{ $comment->body }}</div>
          @auth
            @if(auth()->id() === $comment->user_id || auth()->user()->role === 'admin')
              <div class="d-flex gap-2">
                <a href="{{ route('comments.edit', $comment) }}" class="btn btn-link btn-sm p-0">Edit</a>
                <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                      onsubmit="return confirm('Delete this comment?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-link btn-sm text-danger p-0">Delete</button>
                </form>
              </div>
            @endif
          @endauth
        </div>
      @endforeach
    </div>
  </div>

  @if($related->count())
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h2 class="h6 mb-3">Related posts in {{ optional($post->category)->name }}</h2>
        <ul class="list-group list-group-flush">
          @foreach($related as $r)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <a class="text-decoration-none" href="{{ route('admin.posts.show', $r->slug) }}">{{ $r->title }}</a>
              <small class="text-muted">{{ $r->created_at->format('d M Y') }}</small>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif
</div>
@endsection
