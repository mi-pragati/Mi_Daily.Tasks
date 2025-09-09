@extends('admin.layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container">
    <h1 class="my-4">{{ $product->title }}</h1>

    {{-- Product Category --}}
    <p><strong>Category:</strong> {{ $product->category->name ?? 'No Category' }}</p>

    {{-- Product Description --}}
    <p><strong>Description:</strong> {{ $product->description ?? 'No description available.' }}</p>

    {{-- Product Price --}}
    <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>

    {{-- Product Status --}}
    <p><strong>Status:</strong> {{ ucfirst($product->status) }}</p>

    {{-- Product Image --}}
    <div class="mb-4">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" alt="{{ $product->title }}">
        @else
            <img src="https://via.placeholder.com/600x400" class="img-fluid" alt="No Image Available">
        @endif
    </div>

    {{-- Edit and Delete Buttons --}}
    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning mt-3">Edit</a>

    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger mt-3">Delete</button>
    </form>

    {{-- Back to Products Link --}}
    <div class="mt-4">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Back to Products</a>
    </div>
</div>
@endsection
