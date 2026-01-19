<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErroresController extends Controller
{
    public function index()
{

    return view('errores.index'); 
}
}
