<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LogsController extends Controller
{
public function index() {
    // Obtenemos los logs más recientes, paginados de 15 en 15
    $logs = \App\Models\Log::with('user')->latest()->paginate(15);
    return view('logs.index', compact('logs'));
}
}
