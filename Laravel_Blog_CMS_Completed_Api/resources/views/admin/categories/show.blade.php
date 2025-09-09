@extends('admin.layouts.app')

@section('title', 'Category • '.$category->name)

@section('content')
<div class="container">
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
    </ol>
  </nav>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">{{ $category->name }}</h1>
    <span class="text-muted">{{ $posts->total() }} posts</span>
  </div>

  <div class="row g-3">
    @forelse($posts as $post)
      <div class="col-md-6 col-xl-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body">
            <div class="small text-muted mb-1">
              {{ optional($post->created_at)->format('d M Y') }} • by {{ optional($post->author)->name }}
            </div>
            <h5 class="card-title mb-1">
              <a class="text-decoration-none" href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
            </h5>
            <p class="text-muted mb-3">
              {{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->body), 140) }}
            </p>
            <div class="d-flex gap-2">
              <a href="{{ route('admin.posts.edit', $post->slug) }}" class="btn btn-sm btn-outline-primary">Edit</a>
              <form method="POST" action="{{ route('admin.posts.destroy', $post->slug) }}"
                    onsubmit="return confirm('Delete this post?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><div class="alert alert-info">No posts in this category yet.</div></div>
    @endforelse
  </div>

  <div class="mt-3">{{ $posts->links() }}</div>
</div>
@endsection
