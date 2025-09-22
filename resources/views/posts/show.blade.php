@extends(request()->routeIs('admin.*') ? 'admin.layouts.app' : 'layouts.app')

@section('content')
<div class="container mt-4">

  {{-- Small styles for this page --}}
  <style>
    .crumb-badge { padding: .45rem .85rem; border-radius: .5rem; }
    .related-card .related-link { transition: background-color .15s ease; }
    .related-card .related-link:hover { background-color: #f8f9fa; }
    .related-card .list-group-item { border: 0; border-top: 1px solid #f0f0f0; }
    .related-card .list-group-item:first-child { border-top: 0; }
  </style>

  {{-- Breadcrumb: Home (bold boxed) → Category --}}
  {{-- Increased bottom margin from mb-3 → mb-5 for more space under Home --}}
  <nav class="mb-5">
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a href="{{ url('/') }}" class="text-decoration-none">
        <span class="badge bg-primary fw-bold crumb-badge">Home</span>
      </a>
      @if($post->category)
        <span class="text-muted">›</span>
        <a href="{{ route('categories.show', $post->category->slug) }}" class="text-decoration-none">
          <span class="badge bg-light text-dark border crumb-badge">
            {{ $post->category->name }}
          </span>
        </a>
      @endif
    </div>
  </nav>

  {{-- Title (single, no duplicates) --}}
  <h1 class="fw-bold mb-2 mt-4">{{ $post->title }}</h1>

  {{-- Meta --}}
  <div class="text-muted mb-4">
    @if($post->category)
      <span class="badge bg-secondary">{{ $post->category->name }}</span>
      <span class="mx-1">•</span>
    @endif
    <time>{{ optional($post->created_at)->format('d M Y') }}</time>
    <span class="mx-1">•</span>
    by <strong class="text-dark">{{ optional($post->author)->name ?? 'Unknown' }}</strong>
  </div>

  {{-- Body --}}
  <div class="mb-4" style="line-height:1.8">
    {!! $post->body !!}
  </div>

  <hr class="mt-5 mb-4" style="border-top:1px solid gray;">


  {{-- Post content ... --}}

<hr class="my-4">

{{-- Comment form (admins + editors) --}}
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

  {{-- Related posts (styled) --}}
  @isset($related)
    @if($related->count())
      <div class="card shadow-sm border-0 related-card">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
          <span class="fw-semibold">Related posts in {{ optional($post->category)->name }}</span>
          @if($post->category)
            <a href="{{ route('categories.show', $post->category->slug) }}"
               class="small text-primary text-decoration-underline">View all</a>
          @endif
        </div>

        <ul class="list-group list-group-flush">
          @foreach($related as $r)
            <li class="list-group-item p-0">
              <a href="{{ route('posts.show', $r->slug) }}"
                 class="related-link d-flex align-items-center justify-content-between gap-3 py-3 px-3 text-decoration-none">
                <div class="d-flex flex-column">
                  <span class="fw-semibold text-dark">{{ $r->title }}</span>
                  <small class="text-muted">{{ optional($r->created_at)->format('d M Y') }}</small>
                </div>
                <span class="text-primary" aria-hidden="true">&rsaquo;</span>
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    @endif
  @endisset

</div>
@endsection
