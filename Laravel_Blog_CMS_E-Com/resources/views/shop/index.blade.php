@extends('layouts.app')

@section('content')

    <h1>{{ $category->name }}</h1>
    <p>{{ $category->description }}</p>

    <h2>Posts</h2>
    <div class="row">
        @foreach($posts as $post)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <a href="{{ route('posts.show', $post) }}" class="h6 d-block">{{ $post->title }}</a>
                        <div class="small text-muted">{{ $post->category->name }}</div>
                        <div class="fw-bold mt-2">{{ $post->created_at->format('F j, Y') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $posts->links() }}

    <h2>Products</h2>
    <div class="row">
        @foreach($products as $product)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <a href="{{ route('shop.show', $product) }}" class="h6 d-block">{{ $product->title }}</a>
                        <div class="small text-muted">{{ $product->category->name }}</div>
                        <div class="fw-bold mt-2">₹{{ number_format($product->price, 2) }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $products->links() }}

@endsection
