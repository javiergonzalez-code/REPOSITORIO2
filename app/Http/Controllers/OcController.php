<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Archivo;


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
            ->paginate(10);

        return view('oc.index', compact('ordenes', 'search'));
    }

    // En OcController.php
    public function download($id)
    {
        // return "HOLA, el ID es: " . $id; // <-- Descomenta esto para probar

        $oc = Archivo::findOrFail($id);
        $path = storage_path('app/public/uploads/' . $oc->nombre_sistema);

        if (!file_exists($path)) {
            return back()->with('error', 'El archivo no existe en la ruta: ' . $path);
        }

        return response()->download($path, $oc->nombre_original);
    }

    // public function previewCsv($id)
    // {
    //     $oc = Archivo::findOrFail($id);
    //     $path = storage_path('app/public/uploads' . $oc->nombre_sistema);

    //     $data = [];
    //     if (($handle = fopen($path, "r")) !== FALSE) {
    //         while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
    //             $data[] = $row; //Guarda cada fila
    //         }
    //         fclose($handle);
    //     }

    //     return view('oc.preview_csv', compact('data', 'oc'));
    // }

    // public function previewXml($id){
    //     $oc=Archivo::findOrFail($id);
    //     $path =storage_path('app/public/uploads' . $oc->nombre_sistema);

    //     $xmlContent = simplexml_load_file($path);
    //     //Convertir  JSON y luego en Array para manejo facil en blade
    //     $data=json_decode(json_decode($xmlContent), true);

    //     return view('oc.preview_csv', compact('data', 'oc'));
    // }

    public function preview($id)
{
    $oc = Archivo::findOrFail($id);
    $path = storage_path('app/public/uploads/' . $oc->nombre_sistema);
    $extension = pathinfo($oc->nombre_original, PATHINFO_EXTENSION);

    if ($extension == 'csv') {
        $data = [];
        if (($handle = fopen($path, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data[] = $row;
            }
            fclose($handle);
        }
    } else {
        // Lógica para XML
        $xmlContent = simplexml_load_file($path);
        $data = json_decode(json_encode($xmlContent), true);
    }

    return view('oc.preview', compact('data', 'oc', 'extension'));
}
}
