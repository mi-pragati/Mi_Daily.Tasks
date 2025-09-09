@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <!-- Product Title -->
    <h1 class="fw-bold mb-4">{{ $product->title }}</h1>

    <div class="row">
        <!-- Product Image and Description -->
        <div class="col-md-6">
            <div class="mb-3">
                <!-- Check if the product has an image -->
                @if($product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->title }}" class="img-fluid rounded shadow-sm">
                @else
                    <p>No image available for this product.</p>
                @endif
            </div>

            <div class="mb-3">
                <strong>Description:</strong>
                <p>{{ $product->description }}</p>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <div class="mb-3">
                <strong>Category:</strong> {{ $product->category->name }}
            </div>
            <div class="mb-3">
                <strong>Price:</strong> ₹{{ number_format($product->price, 2) }}
            </div>
            <div class="mb-3">
                <strong>Stock:</strong> {{ $product->stock }} available
            </div>

            <!-- Add to Cart and Buy Now buttons -->
            <div class="mt-3">
                <button class="btn btn-primary" {{ $product->stock <= 0 ? 'disabled' : '' }}>Add to Cart</button>
                <button class="btn btn-success" {{ $product->stock <= 0 ? 'disabled' : '' }}>Buy Now</button>
            </div>
        </div>
    </div>

    <!-- Back to Category Link -->
    <div class="mt-4">
        <a href="{{ route('categories.show', $product->category->slug) }}" class="text-muted">
            <i class="fas fa-arrow-left"></i> Back to {{ $product->category->name }} category
        </a>
    </div>

</div>
@endsection
