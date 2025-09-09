<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login',[CustomAuthController::class,'login']);

Route::get('/Registration',[CustomAuthController::class,'Registration']);

Route::post('/register-user',[CustomAuthController::class,'registerUser'])->name('register-user');

Route::post('/login-user',[CustomAuthController::class,'loginUser'])->name('login-user');
