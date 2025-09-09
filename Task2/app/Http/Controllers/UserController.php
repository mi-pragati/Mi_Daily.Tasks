<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    function testRequests(Request $req){
        return $req->input();
    }

    function testAdmin(Request $req){
        return $req->input();
    }
    function testSomeone(Request $req){
        return $req->input();
    }
    function testPadmin(Request $req){
        return $req->input();
    }
}
