@extends('admin.layouts.app')

@section('title', 'Edit Product Category')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Product Category</h1>

    <form method="POST" action="{{ route('admin.product-categories.update', $productCategory->id) }}">
        @csrf
        @method('PUT')

        <!-- Category Name -->
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $productCategory->name) }}" required>
        </div>

        <!-- Category Slug -->
        <div class="form-group">
            <label for="slug">Category Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug', $productCategory->slug) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Category</button>
    </form>
</div>
@endsection
