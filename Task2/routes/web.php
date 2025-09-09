<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login','users');

Route::post('/users',[UserController::class,'testRequests']);


Route::put('/admin',[UserController::class,'testAdmin']);
Route::view('/alogin','admin');

Route::delete('/someone',[UserController::class,'testSomeone']);
Route::view('/Slogin','someone');

Route::patch('/padmin',[UserController::class,'testPadmin']);
Route::view('/padmin','padmin');
