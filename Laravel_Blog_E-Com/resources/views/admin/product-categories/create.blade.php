@extends('admin.layouts.app')

@section('title', 'Create Product Category')

@section('content')
<div class="container">
    <h1 class="my-4">Create New Product Category</h1>

    <!-- Display any validation errors -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.product-categories.store') }}">
        @csrf

        <!-- Category Name -->
        <div class="form-group">
            <label for="name">Category Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <!-- Category Slug -->
        <div class="form-group">
            <label for="slug">Category Slug</label>
            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Category</button>
    </form>
</div>
@endsection
