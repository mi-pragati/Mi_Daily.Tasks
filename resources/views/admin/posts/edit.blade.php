@extends('admin.layouts.app')

@section('title', 'Edit Post')

@section('content')
<div class="container">
    <h1 class="my-4">Edit Post: {{ $post->title }}</h1>

    <!-- Edit Post Form -->
    <form method="POST" action="{{ route('admin.posts.update', $post->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')  <!-- This tells Laravel we are using the PUT method for updating -->

        <!-- Post Title -->
        <div class="form-group mb-3">
            <label for="title">Post Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
            @error('title')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Post Content -->
        <div class="form-group mb-3">
            <label for="content">Content</label>
            <textarea name="content" class="form-control" rows="5" required>{{ old('content', $post->content) }}</textarea>
            @error('content')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Post Status -->
        <div class="form-group mb-3">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option value="draft" @selected($post->status == 'draft')>Draft</option>
                <option value="published" @selected($post->status == 'published')>Published</option>
            </select>
            @error('status')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <!-- Post Image (Optional) -->
        <div class="form-group mb-3">
            <label for="image">Post Image</label>
            <input type="file" name="image" class="form-control">
            @if($post->image)
                <img src="{{ asset('storage/'.$post->image) }}" alt="Post Image" class="mt-2" style="width: 100px;">
            @endif
            @error('image')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>
</div>
@endsection
