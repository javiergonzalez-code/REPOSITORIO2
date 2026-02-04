<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Archivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OcController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userFilter = $request->input('user');
        $extension = $request->input('extension');
        $fecha = $request->input('fecha');

        // Variable para el datalist de la vista
        $usuarios_filtro = User::orderBy('name')->get();

        $query = Archivo::with('user')->where('modulo', 'OC');

        if ($search) {
            $query->where('nombre_original', 'like', "%{$search}%");
        }

        if ($userFilter) {
            $query->whereHas('user', function($q) use ($userFilter) {
                $q->where('name', $userFilter);
            });
        }

        if ($extension) {
            $query->where('tipo_archivo', $extension);
        }

        if ($fecha) {
            $query->whereDate('created_at', $fecha);
        }

        $ordenes = $query->latest()->paginate(10);

        return view('oc.index', compact('ordenes', 'search', 'usuarios_filtro'));
    }

    public function download($id)
    {
        $oc = Archivo::findOrFail($id);
        $path = storage_path('app/public/uploads/' . $oc->nombre_sistema);

        if (!file_exists($path)) {
            return back()->with('error', 'El archivo físico no existe.');
        }

        return response()->download($path, $oc->nombre_original);
    }

    /**
     * Previsualiza contenido de Excel, CSV o XML
     */
    public function preview($id)
    {
        $oc = Archivo::findOrFail($id);
        $path = storage_path('app/public/' . $oc->ruta); // Usamos la ruta guardada en BD

        if (!file_exists($path)) {
            return back()->with('error', 'El archivo físico no existe en el servidor.');
        }

        // Extraer extensión del nombre original
        $extension = strtolower(pathinfo($oc->nombre_original, PATHINFO_EXTENSION));
        $data = [];

        try {
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                /** * LÓGICA PARA EXCEL Y CSV
                 * Excel::toArray convierte las hojas en arrays. Tomamos la primera [0].
                 */
                $sheets = Excel::toArray(new \stdClass, $path);
                $data = $sheets[0] ?? [];
            } elseif ($extension === 'xml') {
                /**
                 * LÓGICA PARA XML
                 */
                $xmlContent = simplexml_load_file($path);
                $data = json_decode(json_encode($xmlContent), true);
            } else {
                return back()->with('error', 'Formato de previsualización no soportado.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al leer el archivo: ' . $e->getMessage());
        }

        return view('oc.preview', compact('data', 'oc', 'extension'));
    }
}