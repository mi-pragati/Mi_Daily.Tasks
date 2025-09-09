@extends('admin.layouts.app')

@section('title', 'Create New Product')

@section('content')
<div class="container">
    <h1 class="my-4">Create New Product</h1>

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Category Dropdown -->
        <div class="form-group">
            <label for="product_category_id">Category</label>
            <select name="product_category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Product Title -->
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <!-- Product Image -->
        <div class="form-group">
            <label for="image">Product Image</label>
            <input type="file" name="image" class="form-control">
        </div>

        <!-- Product Price -->
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" required min="0">
        </div>

        <!-- Product Stock -->
        <div class="form-group">
            <label for="stock">Stock</label>
            <input type="number" name="stock" class="form-control" required min="0">
        </div>

        <!-- Product Status -->
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Product</button>
    </form>
</div>
@endsection
