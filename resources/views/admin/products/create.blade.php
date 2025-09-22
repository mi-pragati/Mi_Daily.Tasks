@extends('admin.layouts.app')

@section('title', 'Create New Product')

@section('content')
<div class="container">
    <h1 class="my-4">Create New Product</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Category -->
        <div class="form-group mb-3">
            <label for="product_category_id">Category</label>
            <select name="product_category_id" id="product_category_id" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Title -->
        <div class="form-group mb-3">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
        </div>

        <!-- Description -->
        <div class="form-group mb-3">
            <label for="description">Description (optional)</label>
            <textarea name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Price (₹) *</label>
                <input type="number" step="0.01" min="0" name="price" value="{{ old('price', 0) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Stock *</label>
                <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="draft" @selected(old('status')==='draft')>Draft</option>
                    <option value="published" @selected(old('status')==='published')>Published</option>
                </select>
            </div>
        </div>

        <!-- Main Image -->
        <div class="form-group mt-3">
            <label for="image">Main Image (optional)</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <small class="text-muted">If you don’t upload one, a default image will be used.</small>
        </div>

        <!-- Extra Media (optional) -->
        <div class="form-group mt-3">
            <label for="media">Additional Media (optional)</label>
            <input type="file" name="media[]" class="form-control" accept="image/*,video/*" multiple>
        </div>

        <div class="mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Create Product</button>
        </div>
    </form>
</div>
@endsection
