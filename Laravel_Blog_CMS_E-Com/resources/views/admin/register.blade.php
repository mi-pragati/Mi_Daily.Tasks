@extends('admin.layouts.app')

@section('title', 'Admin • Registration')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

      <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
          <h1 class="h4 mb-0">Create Admin Account</h1>
          <div class="text-muted small">Admins can manage categories, posts and comments.</div>
        </div>

        <div class="card-body">
          @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
          @endif

          @if ($errors->any())
            <div class="alert alert-danger">
              <strong>Please fix the errors below.</strong>
              <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.register') }}" novalidate>
            @csrf
            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="form-control @error('email') is-invalid @enderror" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Use at least 8 characters.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>

              <div class="col-12 mt-2">
                <hr>
                <h2 class="h6 text-uppercase text-muted mb-3">Company details</h2>
              </div>

              <div class="col-md-6">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}"
                       class="form-control @error('company_name') is-invalid @enderror">
                @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label">Company Phone</label>
                <input type="text" name="company_phone" value="{{ old('company_phone') }}"
                       class="form-control @error('company_phone') is-invalid @enderror">
                @error('company_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12">
                <label class="form-label">Company Address</label>
                <textarea name="company_address" rows="3"
                          class="form-control @error('company_address') is-invalid @enderror">{{ old('company_address') }}</textarea>
                @error('company_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12 d-flex justify-content-between align-items-center mt-2">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancel</a>
                <button class="btn btn-primary">Create Admin</button>
              </div>

            </div>
          </form>
        </div>
      </div>

      <p class="text-center small text-muted mt-3">
        Already an admin?
        <span class="text-secondary">Sign in (coming soon)</span>
      </p>

    </div>
  </div>
</div>
@endsection
