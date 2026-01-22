<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class OcController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');

    $ordenes = \App\Models\Archivo::with('user')
        ->when($search, function ($query, $search) {
            return $query->where('nombre_original', 'like', "%{$search}%")
                         ->orWhereHas('user', function ($q) use ($search) {
                             $q->where('name', 'like', "%{$search}%");
                         });
        })
        ->latest()
        ->get();

    return view('oc.index', compact('ordenes', 'search'));
}
}
