@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl mb-4">Admin Registration</h1>

    <form method="POST" action="{{ route('admin.register') }}">
        @csrf
        <div class="mb-4">
            <label>Name</label>
            <input name="name" value="{{ old('name') }}" required class="border p-2 w-full" />
        </div>

        <div class="mb-4">
            <label>Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required class="border p-2 w-full" />
        </div>

        <div class="mb-4">
            <label>Password</label>
            <input name="password" type="password" required class="border p-2 w-full" />
        </div>

        <div class="mb-4">
            <label>Confirm Password</label>
            <input name="password_confirmation" type="password" required class="border p-2 w-full" />
        </div>

        <h2 class="mt-6 mb-2">Company Details</h2>

        <div class="mb-4">
            <label>Company Name</label>
            <input name="company_name" value="{{ old('company_name') }}" required class="border p-2 w-full" />
        </div>

        <div class="mb-4">
            <label>Company Address</label>
            <textarea name="company_address" required class="border p-2 w-full">{{ old('company_address') }}</textarea>
        </div>

        <div class="mb-4">
            <label>Company Phone</label>
            <input name="company_phone" value="{{ old('company_phone') }}" required class="border p-2 w-full" />
        </div>

        <button class="btn btn-primary px-4 py-2 bg-blue-600 text-white">Register as Admin</button>
    </form>
</div>
@endsection
