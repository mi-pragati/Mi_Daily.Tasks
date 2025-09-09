<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerRegisterController extends Controller
{
    public function create()
    {
        // Show the customer registration page
        return view('auth.customer-register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required','string','max:255'],
            'email'                 => ['required','string','email','max:255','unique:users,email'],
            'password'              => ['required','string','min:8','confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'role'     => 'customer',   // <= important
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')
            ->with('status', 'Welcome! Your customer account was created.');
    }
}
