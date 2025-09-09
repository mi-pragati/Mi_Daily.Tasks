@extends('admin.layouts.app')

@section('title', 'All Products')

@section('content')
<div class="container">
    <h1 class="my-4">All Products</h1>

    {{-- Add Product Link --}}
    <nav class="mb-3">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
    </nav>

    {{-- Display Success Message (Popup) --}}
    @if(session('status'))
        <script>
            alert("{{ session('status') }}");
        </script>
    @endif

    {{-- Filter Products --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.products.index') }}">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="">Filter by Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
                <option value="other" @selected(request('category') == 'other')>Other Products</option>
            </select>
        </form>
    </div>

    {{-- Display Products --}}
    @if($products->count())
        <div class="row g-3">
            @foreach($products as $product)
                <div class="col-md-4">
                    <a href="{{ route('admin.products.show', $product->id) }}" style="text-decoration: none; color: inherit;">
                        <div class="card h-100">
                            {{-- Product Image (Dummy) --}}
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('storage/products/dummy-image.jpg') }}" class="card-img-top" alt="{{ $product->title }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->title }}</h5>
                                <p class="card-text">{{ Str::limit($product->description, 100) }}</p>
                                <p class="card-text"><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                                <p class="card-text"><strong>Status:</strong> {{ ucfirst($product->status) }}</p>
                                 {{-- Edit Button --}}
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">Edit</a>

                            {{-- Delete Form --}}
                            <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger mt-2">Delete</button>
                                  </form>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $products->links() }}
        </div>
    @else
        <div class="alert alert-info">No products available.</div>
    @endif
</div>
@endsection
