@extends('lay_app')

@section('title', 'Login')

@push('styles')
<style>
  .auth-shell{
    min-height: calc(100vh - var(--nav-h) - var(--footer-h));
    display:flex; align-items:center; justify-content:center;
    padding: 1rem;
  }
  .auth-card{ width: 100%; max-width: 420px; }
</style>
@endpush

@section('content')
<div class="auth-shell">
  <div class="card shadow-sm auth-card">
    <div class="card-body p-4">
      <h1 class="h4 mb-3">Welcome back</h1>

      @if (session('status'))
        <div class="alert alert-success py-2" role="alert">
          {{ session('status') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            class="form-control @error('email') is-invalid @enderror"
            required
            autofocus
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
            autocomplete="current-password">
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
            <label class="form-check-label" for="remember">Remember me</label>
          </div>
          @if (Route::has('password.request'))
            <a class="small" href="{{ route('password.request') }}">Forgot your password?</a>
          @endif
        </div>

        <button class="btn btn-primary w-100">Log in</button>
      </form>

      <div class="text-center mt-3 small">
        Don’t have an account?
        <a href="{{ route('register') }}">Register</a>
      </div>
    </div>
  </div>
</div>
@endsection
