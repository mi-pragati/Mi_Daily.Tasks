@extends('lay_app')

@section('title', 'Register')

@push('styles')
<style>
  .auth-shell{
    min-height: calc(100vh - var(--nav-h) - var(--footer-h));
    display:flex; align-items:center; justify-content:center;
    padding: 1rem;
  }
  .auth-card{ width: 100%; max-width: 480px; }
</style>
@endpush

@section('content')
<div class="auth-shell">
  <div class="card shadow-sm auth-card">
    <div class="card-body p-4">
      <h1 class="h4 mb-3">Create your account</h1>

      <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="mb-3">
          <label for="name" class="form-label">Name</label>
          <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            class="form-control @error('name') is-invalid @enderror"
            required
            autofocus
            autocomplete="name">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
            required
            autocomplete="username">
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input
            id="password"
            type="password"
            name="password"
            class="form-control @error('password') is-invalid @enderror"
            required
            autocomplete="new-password">
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirm Password</label>
          <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            class="form-control @error('password_confirmation') is-invalid @enderror"
            required
            autocomplete="new-password">
          @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <button class="btn btn-primary w-100">Register</button>
      </form>

      <div class="text-center mt-3 small">
        Already registered?
        <a href="{{ route('login') }}">Log in</a>
      </div>
    </div>
  </div>
</div>
@endsection
