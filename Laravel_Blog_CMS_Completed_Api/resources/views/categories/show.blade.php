@extends('layouts.app')

@section('content')
<div class="container mt-4">

  {{-- Styles for post boxes --}}
  <style>
    .post-box {
      border: 1px solid #e5e5e5;
      border-left: 4px solid #0d6efd;
      background: #fff;
      border-radius: 6px;
      padding: 1.25rem;
      transition: box-shadow 0.2s ease-in-out;
    }
    .post-box:hover {
      box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .post-meta {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 0.5rem;
    }
    .post-title {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    .post-desc {
      color: #555;
      line-height: 1.6;
    }
  </style>

  {{-- Single bold heading --}}
  <h1 class="fw-bold mb-4">Posts in {{ $category->name }}</h1>

  @if($posts->count())
      <div class="d-flex flex-column gap-4">
        @foreach($posts as $post)
          @php
            $author = $post->author ?? null;
            $authorName = optional($author)->name ?? 'Unknown';
            $categoryName = optional($post->category)->name ?? 'Uncategorized';
          @endphp

          <div class="post-box">
            <div class="post-meta">
              <span class="badge bg-secondary">{{ $categoryName }}</span>
              • {{ optional($post->created_at)->format('d M Y') }}
              • by <strong>{{ $authorName }}</strong>
            </div>

            <div class="post-title">
              <a href="{{ route('posts.show', $post->slug) }}" class="text-decoration-none text-dark">
                {{ $post->title }}
              </a>
            </div>

            <div class="post-desc mb-3">
              {!! \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?? $post->body), 600) !!}
            </div>

            <a href="{{ route('posts.show', $post->slug) }}"
               class="text-primary text-decoration-underline fw-semibold">
              Read more
            </a>
          </div>
        @endforeach
      </div>

      {{-- pagination --}}
      <div class="mt-4">
        {{ $posts->links() }}
      </div>
  @else
      <div class="alert alert-info">No posts available in this category yet.</div>
  @endif

</div>
@endsection
