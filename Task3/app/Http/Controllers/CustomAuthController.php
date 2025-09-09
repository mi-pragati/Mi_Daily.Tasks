<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;
class CustomAuthController extends Controller
{
    public function login(){
        return view('auth.login');

    }

    public function Registration(){
        return view('auth.Registration');
    }

    public function registerUser(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:5'
        ]);
        $user = new User();
        $user ->name = $request ->name;
        $user ->email = $request ->email;
        $user ->password = Hash::make($request ->password);
        $res = $user->save();

        if($res){
            return back()->with('success','You have registered successsfully');
        }
        else{
            return back()-> with('fail','Something went wrong');
        }
    }

    public function loginUser(Request $request){
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|min:5'
        ]);
        $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {
        // Login success
        return redirect('/login')->with('success', 'Login Successful!');
    } else {
        // Login fail
        return back()->with('fail', 'Invalid email or password');
    }
}
    }

