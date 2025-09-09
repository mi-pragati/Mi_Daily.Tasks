@extends('lay_app')

@section('title','Customer Registration')

@section('content')
<div class="container py-4" style="max-width: 560px;">
  <h1 class="h4 mb-3">Create a Customer Account</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('customer.register.store') }}" class="card shadow-sm">
    @csrf
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label" for="name">Full Name</label>
        <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required autofocus>
      </div>

      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <input id="password" name="password" type="password" class="form-control" required minlength="8">
      </div>

      <div class="mb-3">
        <label class="form-label" for="password_confirmation">Confirm Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required minlength="8">
      </div>

      <button class="btn btn-primary w-100">Register</button>
    </div>
  </form>

  <p class="mt-3 mb-0 text-muted">
    Already have an account?
    <a href="{{ route('login') }}">Sign in</a>
  </p>
</div>
@endsection
