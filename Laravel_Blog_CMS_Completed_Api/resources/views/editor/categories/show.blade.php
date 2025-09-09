@extends('layouts.editor')

@section('content')
<div class="container mt-4">
  <h1 class="fw-bold mb-4">Posts in {{ $category->name }}</h1>

  @if($posts->count())
    <style>
      .post-box{border:1px solid rgba(0,0,0,.08);border-left:4px solid #0d6efd;background:#fff;border-radius:6px;padding:1.25rem;transition:.18s}
      .post-box:hover{box-shadow:0 .75rem 1.25rem rgba(0,0,0,.08)}
    </style>

    <div class="d-flex flex-column gap-4">
      @foreach($posts as $post)
        @php
          $author = $post->author ?? null;
          $authorName = optional($author)->name ?? 'Unknown';
          $categoryName = optional($post->category)->name ?? 'Uncategorized';
        @endphp

        <div class="post-box">
          <div class="small text-muted mb-2">
            <span class="badge bg-secondary">{{ $categoryName }}</span>
            • {{ optional($post->created_at)->format('d M Y') }}
            • by <strong class="text-dark">{{ $authorName }}</strong>
          </div>

          <h2 class="h4 mb-2">
            <a href="{{ route('editor.posts.show', $post->slug) }}" class="text-decoration-none text-dark">
              {{ $post->title }}
            </a>
          </h2>

          <p class="mb-0 text-secondary" style="line-height:1.7">
            {{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->body), 600) }}
          </p>

          <div class="mt-3">
            <a href="{{ route('editor.posts.show', $post->slug) }}" class="link-primary text-decoration-underline fw-semibold">
              Read more
            </a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-4">{{ $posts->links() }}</div>
  @else
    <div class="alert alert-info">No posts available in this category yet.</div>
  @endif
</div>
@endsection
