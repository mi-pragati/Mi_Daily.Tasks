<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    //
    function login(Request $req){
        $user = User::where('email',$req->email)->first();
        if(!$user || !Hash::check($req->password,$user->password)){
            return ['result'=>"User Not Found","success"=>false];
        }
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;

        return ['result'=>$success,'msg'=>"User Registered Successfully"];

    }


    public function signup(Request $req)
{
    $req->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users,email',
        'password' => 'required|string|min:6'
    ]);

    $input = $req->all();
    $input['password'] = bcrypt($input['password']);

    $user = User::create($input);

    $success['token'] = $user->createToken('MyApp')->plainTextToken;
    $success['name'] = $user->name;

    return response()->json([
        'success' => true,
        'result' => $success,
        'msg' => 'User registered successfully'
    ]);
}

}
