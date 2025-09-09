@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Product</h1>

    {{-- Display Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Product Form --}}
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $product->title) }}" required>
        </div>

        <div class="form-group mt-3">
            <label for="product_category_id">Category</label>
            <select name="product_category_id" id="product_category_id" class="form-control">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->product_category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="form-group mt-3">
            <label for="price">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $product->price) }}" required>
        </div>

        <div class="form-group mt-3">
            <label for="stock">Stock</label>
            <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $product->stock) }}" required>
        </div>

        <div class="form-group mt-3">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="draft" {{ $product->status == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ $product->status == 'published' ? 'selected' : '' }}>Published</option>
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="image">Product Image</label>
            <input type="file" name="image" id="image" class="form-control">
        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Update Product</button>
        </div>
    </form>
</div>
@endsection
