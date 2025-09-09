@extends('admin.layouts.app') {{-- not layouts.app --}}

@section('title','Admin • Create Post')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Create Post</h1>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
  </div>

  <form method="POST" action="{{ route('posts.store') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Category</label>
      <select name="category_id" class="form-select" required>
        <option value="" disabled selected>Select a category</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>{{ $cat->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Excerpt (optional)</label>
      <input type="text" name="excerpt" class="form-control" value="{{ old('excerpt') }}">
    </div>

    <div class="mb-3">
      <label class="form-label">Body</label>
      <textarea name="body" rows="8" class="form-control" required>{{ old('body') }}</textarea>
    </div>

    <div class="mb-4">
      <label class="form-label">Tags</label>
      <select name="tags[]" class="form-select" multiple>
        @foreach($tags as $tag)
          <option value="{{ $tag->id }}" @selected(collect(old('tags',[]))->contains($tag->id))>
            {{ $tag->name }}
          </option>
        @endforeach
      </select>
    </div>

    <button class="btn btn-primary">Publish</button>
  </form>
</div>
@endsection
