<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;


class AdminRegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'email'=>['required','email','max:255', Rule::unique('users')],
            'password'=>'required|min:8|confirmed',
            'company_name'=>'required|string|max:255',
            'company_address'=>'required|string|max:1000',
            'company_phone'=>'required|string|max:50'
        ]);

        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'role'=>'admin',
            'company_name'=>$data['company_name'],
            'company_address'=>$data['company_address'],
            'company_phone'=>$data['company_phone']
        ]);

        auth()->login($user);

        return redirect()->route('login')->with('success','Admin created. Please sign in.');
    }
}
