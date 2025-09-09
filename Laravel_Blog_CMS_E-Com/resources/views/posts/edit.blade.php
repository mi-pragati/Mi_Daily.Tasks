@extends('layouts.editor')

@section('content')
<div class="container mt-4" style="max-width: 760px;">
  <h1 class="h4 fw-bold mb-3">Edit Post</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('posts.update', $post) }}" class="card shadow-sm border-0">
    @csrf
    @method('PUT')
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Title</label>
        <input name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(old('category_id', $post->category_id)==$cat->id)>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Excerpt (optional)</label>
        <textarea name="excerpt" rows="3" class="form-control">{{ old('excerpt', $post->excerpt) }}</textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Body</label>
        <textarea name="body" rows="10" class="form-control" required>{{ old('body', $post->body) }}</textarea>
      </div>

      @if(isset($tags) && $tags->count())
        <div class="mb-3">
          <label class="form-label d-block">Tags</label>
          @php $selected = old('tags', $post->tags->pluck('id')->all()); @endphp
          @foreach($tags as $tag)
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}"
                     id="tag{{ $tag->id }}" @checked(in_array($tag->id, $selected))>
              <label class="form-check-label" for="tag{{ $tag->id }}">{{ $tag->name }}</label>
            </div>
          @endforeach
        </div>
      @endif

      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('editor.posts.show', $post->slug) }}" class="btn btn-outline-secondary">Cancel</a>
        <button class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </form>
</div>
@endsection
