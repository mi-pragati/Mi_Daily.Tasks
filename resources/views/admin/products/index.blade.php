@extends('admin.layouts.app')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-3">
        <form method="GET"
        action="{{ isset($category) ? route('admin.products.byCategory', $category->slug) : route('admin.products.index') }}"
        class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search by keyword"
        name="search" value="{{ request('search') }}">
    <button class="btn btn-outline-primary" type="submit">Search</button>
</form>

        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add Product</a>
    </div>

    {{-- Status message --}}
    @if(session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    {{-- Products Table --}}
    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Thumbnail</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price (₹)</th>
                <th>Stock</th>
                <th style="width: 160px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr style="cursor:pointer;" 
                 onclick="window.location='{{ route('admin.products.show', $product) }}'">
                    <td>{{ $product->id }}</td>
                    <td style="width:120px">
                        <img src="{{ $product->image_url }}" alt="{{ $product->title ?? $product->name }}" class="img-fluid rounded" style="max-height:80px">
                    </td>
                    <td>{{ $product->title ?? $product->name }}</td>
                    <td>{{ optional($product->category)->name ?? '—' }}</td>
                    <td style="max-width:320px">{{ Str::limit($product->description, 80) }}</td>
                    <td>{{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-info">Edit</a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this product?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $products->links() }}
</div>
@endsection