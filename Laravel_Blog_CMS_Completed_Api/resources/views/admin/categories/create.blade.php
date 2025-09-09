@extends('admin.layouts.app')
@section('title','Admin • Create Category')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h1 class="h5 mb-3">Create Category</h1>
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Slug (optional)</label>
              <input name="slug" value="{{ old('slug') }}" class="form-control @error('slug') is-invalid @enderror">
              @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">Leave empty to auto-generate from name.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Description (optional)</label>
              <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-between">
              <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
              <button class="btn btn-primary">Create</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
