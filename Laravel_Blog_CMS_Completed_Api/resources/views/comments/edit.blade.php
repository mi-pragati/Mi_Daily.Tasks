@extends('layouts.editor')

@section('content')
<div class="container mt-4" style="max-width:720px;">
  <h1 class="h5 fw-bold mb-3">Edit Comment</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('comments.update', $comment) }}" class="card border-0 shadow-sm">
    @csrf @method('PUT')
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Comment</label>
        <textarea name="body" rows="4" class="form-control" required>{{ old('body', $comment->body) }}</textarea>
      </div>
      <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('editor.posts.show', $comment->post->slug) }}" class="btn btn-outline-secondary">Cancel</a>
        <button class="btn btn-primary">Save Changes</button>
      </div>
    </div>
  </form>
</div>
@endsection
