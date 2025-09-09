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

  {{-- Display Products --}}
  @if($products->count())
    <h2 class="fw-bold mb-4">Products</h2>
    <div class="row g-3">
      @foreach($products as $product)
        <div class="col-md-6 col-xl-4">
          <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
              <div class="small text-muted mb-1">
                ₹{{ number_format($product->price, 2) }} • {{ $product->category->name }}
              </div>
              <h5 class="card-title mb-1">
                <a class="text-decoration-none" href="{{ route('admin.products.show', $product->slug) }}">{{ $product->title }}</a>
              </h5>
              <p class="text-muted mb-3">
                {{ \Illuminate\Support\Str::limit(strip_tags($product->description), 140) }}
              </p>
              <div class="d-flex gap-2">
                <a href="{{ route('admin.products.edit', $product->slug) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form method="POST" action="{{ route('admin.products.destroy', $product->slug) }}"
                      onsubmit="return confirm('Delete this product?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
    <div class="mt-3">{{ $products->links() }}</div>
  @else
    <div class="alert alert-info">No products in this category yet.</div>
  @endif
</div>
@endsection
