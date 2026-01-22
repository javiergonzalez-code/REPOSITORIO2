<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ESTE ES EL MÉTODO QUE FALTA
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }
}