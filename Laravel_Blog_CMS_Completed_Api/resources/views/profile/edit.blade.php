@extends(request()->routeIs('admin.*') ? 'admin.layouts.app' : 'layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container py-4" style="max-width: 960px;">

  {{-- Flash + validation --}}
  @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following:</div>
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-4">
    {{-- LEFT: Basic Info --}}
    <div class="col-lg-5">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Basic Info</h5>
          <form method="POST" action="{{ route('profile.update-name') }}" class="needs-validation" novalidate>
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input
                id="name"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}"
                required
                autocomplete="off"
              >
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-grid">
              <button class="btn btn-primary">Save Name</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- RIGHT: Security (Email + Password) --}}
    <div class="col-lg-7">
      {{-- Change Email --}}
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3 d-flex justify-content-between align-items-center">
            <span>Change Email</span>
            <small class="text-muted">Current: <strong>{{ $user->email }}</strong></small>
          </h5>

          {{-- Step 1: Request OTP (to current email) with new email value --}}
          <form method="POST" action="{{ route('profile.request-otp') }}" class="row g-3 align-items-end">
            @csrf
            <input type="hidden" name="purpose" value="email">

            <div class="col-md-8">
              <label for="new_email" class="form-label">New Email</label>
              <input
                type="email"
                id="new_email"
                name="new_email"
                class="form-control @error('new_email') is-invalid @enderror"
                value="{{ old('new_email') }}"
                placeholder="name@example.com"
                required
                autocomplete="off"
              >
              @error('new_email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">We’ll send an OTP to your current email address.</div>
            </div>

            <div class="col-md-4 d-grid">
              <button class="btn btn-outline-primary">Send OTP</button>
            </div>
          </form>

          {{-- Step 2: Confirm OTP for email change --}}
          <form method="POST" action="{{ route('profile.confirm-otp') }}" class="row g-3 align-items-end mt-2">
            @csrf
            <input type="hidden" name="purpose" value="email">

            <div class="col-md-8">
              <label for="email_otp" class="form-label">OTP Code</label>
              <input
                type="text"
                id="email_otp"
                name="otp_code"
                class="form-control @error('otp_code') is-invalid @enderror"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                placeholder="6-digit code"
                required
                autocomplete="one-time-code"
              >
              @error('otp_code')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4 d-grid">
              <button class="btn btn-primary">Confirm Email Change</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Change Password --}}
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="card-title mb-3">Change Password</h5>

          {{-- Step 1: Request OTP (to current email) --}}
          <form method="POST" action="{{ route('profile.request-otp') }}" class="row g-3 align-items-end">
            @csrf
            <input type="hidden" name="purpose" value="password">

            <div class="col-md-8">
              <label class="form-label">OTP will be sent to</label>
              <input type="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>

            <div class="col-md-4 d-grid">
              <button class="btn btn-outline-primary">Send OTP</button>
            </div>
          </form>

          {{-- Step 2: Confirm OTP + set new password --}}
          <form method="POST" action="{{ route('profile.confirm-otp') }}" class="row g-3 mt-2">
            @csrf
            <input type="hidden" name="purpose" value="password">

            <div class="col-md-4">
              <label for="pwd_otp" class="form-label">OTP Code</label>
              <input
                type="text"
                id="pwd_otp"
                name="otp_code"
                class="form-control @error('otp_code') is-invalid @enderror"
                inputmode="numeric"
                pattern="[0-9]{6}"
                maxlength="6"
                placeholder="6-digit code"
                required
                autocomplete="one-time-code"
              >
              @error('otp_code')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-8">
              <label for="current_password" class="form-label">Current Password</label>
              <input
                type="password"
                id="current_password"
                name="current_password"
                class="form-control @error('current_password') is-invalid @enderror"
                required
                autocomplete="current-password"
              >
              @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="new_password" class="form-label">New Password</label>
              <input
                type="password"
                id="new_password"
                name="new_password"
                class="form-control @error('new_password') is-invalid @enderror"
                required
                autocomplete="new-password"
              >
              @error('new_password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
              <input
                type="password"
                id="new_password_confirmation"
                name="new_password_confirmation"
                class="form-control"
                required
                autocomplete="new-password"
              >
            </div>

            <div class="col-12 d-grid">
              <button class="btn btn-primary">Update Password</button>
            </div>
          </form>
        </div>
      </div>

    </div> {{-- /col-lg-7 --}}
  </div> {{-- /row --}}

</div>
@endsection
