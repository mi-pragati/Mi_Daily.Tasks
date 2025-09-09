@extends('layouts.editor')

@section('content')
<div class="container mt-4">

  {{-- Breadcrumb (editor-only) --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item">
        <a href="{{ route('editor.home') }}">Home</a>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('editor.categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
      </li>
      <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
    </ol>
  </nav>

  <h1 class="h4 fw-bold mb-2">{{ $post->title }}</h1>
  <div class="text-muted mb-3">
    {{ $post->category->name }} • {{ optional($post->created_at)->format('d M Y') }} • by <strong>{{ optional($post->author)->name ?? 'Unknown' }}</strong>
  </div>

  <article class="mb-4" style="line-height:1.8">
    {!! nl2br(e($post->body)) !!}
  </article>

  <hr>

  {{-- Post content ... --}}

<hr class="my-4">

{{-- ⬇️ paste your comment block here --}}
{{-- Comment form (editors only) --}}
<hr class="my-4">

@auth
  @php
    $role = trim(strtolower(auth()->user()->role ?? ''));
  @endphp

  @if(in_array($role, ['editor','admin']))
    <form method="POST" action="{{ route('comments.store', $post->slug) }}" class="mb-3">
      @csrf
      <div class="mb-2">
        <label class="form-label">Add a comment</label>
        <textarea name="body" rows="3" class="form-control" required>{{ old('body') }}</textarea>
      </div>
      <button class="btn btn-primary btn-sm">Post Comment</button>
    </form>
  @else
    <div class="alert alert-info small">
      Only editors and admins can comment.
      <a href="{{ route('login') }}">Login</a> or
      <a href="{{ route('register') }}">Register</a>.
    </div>
  @endif
@else
  <div class="alert alert-info small">
    Sign in to comment. <a href="{{ route('login') }}">Login</a>
  </div>
@endauth

{{-- Existing comments --}}
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
            @csrf
            @method('DELETE')
            <input type="hidden" name="return_to" value="{{ url()->current() }}">
            <button class="btn btn-link btn-sm text-danger p-0">Delete</button>
          </form>
        </div>
      @endif
    @endauth
  </div>
@endforeach

<hr>

  {{-- Related posts in same category --}}
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h2 class="h6 mb-0">Related posts in {{ $post->category->name }}</h2>
    <a class="small text-decoration-underline" href="{{ route('editor.categories.show', $post->category->slug) }}">View all</a>
  </div>

  @forelse($related as $r)
    <div class="d-flex justify-content-between border-bottom py-2">
      <a href="{{ route('editor.posts.show', $r->slug) }}" class="text-decoration-none">{{ $r->title }}</a>
      <small class="text-muted">{{ optional($r->created_at)->format('d M Y') }}</small>
    </div>
  @empty
    <div class="text-muted">No related posts yet.</div>
  @endforelse

</div>
@endsection
