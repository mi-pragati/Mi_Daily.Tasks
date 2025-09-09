@extends('layouts.editor')

@section('content')
<div class="container mt-4" style="max-width: 760px;">
  <h1 class="h4 fw-bold mb-3">Create Post</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('posts.store') }}" class="card shadow-sm border-0">
    @csrf
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" class="form-control" value="{{ old('title') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          <option value="" disabled selected>Select a category</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Excerpt (optional)</label>
        <textarea name="excerpt" rows="3" class="form-control">{{ old('excerpt') }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Body</label>
        <textarea name="body" rows="10" class="form-control" required>{{ old('body') }}</textarea>
      </div>

      @if(isset($tags) && $tags->count())
        <div class="mb-3">
          <label class="form-label">Tags (optional)</label>
          <select name="tags[]" class="form-select" multiple size="6">
            @foreach($tags as $tag)
              <option value="{{ $tag->id }}" @selected(collect(old('tags', []))->contains($tag->id))>
                {{ $tag->name }}
              </option>
            @endforeach
          </select>
          <div class="form-text">Hold Ctrl/Cmd to select multiple.</div>
        </div>
      @endif

      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('posts.mine') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Publish</button>
      </div>
    </div>
  </form>
</div>
@endsection
