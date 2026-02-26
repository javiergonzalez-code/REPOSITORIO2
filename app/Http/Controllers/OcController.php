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

        $user = auth()->user();
        $query = Archivo::with('user')->where('modulo', 'OC');

        // Validar si el usuario es proveedor
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor) {
            // El proveedor solo ve sus propias órdenes
            $query->where('user_id', $user->id);
            $usuarios_filtro = collect([$user]);
        } else {
            // Admin y Superadmin ven todo
            $usuarios_filtro = User::orderBy('name')->get();
        }

        if ($search) {
            $query->where('nombre_original', 'like', "%{$search}%");
        }

        if ($userFilter) {
            $query->whereHas('user', function ($q) use ($userFilter) {
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
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        // Seguridad: Evita que un proveedor descargue archivos ajenos
        if ($esProveedor && $oc->user_id !== $user->id) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        $path = storage_path('app/public/uploads/' . $oc->nombre_sistema);

        if (!file_exists($path)) {
            return back()->with('error', 'El archivo físico no existe.');
        }

        return response()->download($path, $oc->nombre_original);
    }

    public function preview($id)
    {
        $oc = Archivo::findOrFail($id);
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        // Seguridad: Evita que un proveedor previsualice archivos ajenos
        if ($esProveedor && $oc->user_id !== $user->id) {
            abort(403, 'No tienes permiso para previsualizar este archivo.');
        }

        // CORRECCIÓN: Usar la misma ruta exacta que usas en el método download
        $path = storage_path('app/private/uploads/' . $oc->nombre_sistema);

        if (!file_exists($path)) {
            return back()->with('error', 'El archivo físico no existe en el servidor.');
        }

        $extension = strtolower(pathinfo($oc->nombre_original, PATHINFO_EXTENSION));
        $data = [];

        try {
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $sheets = Excel::toArray(new \stdClass, $path);
                $data = $sheets[0] ?? [];
            } elseif ($extension === 'xml') {
                $xmlContent = simplexml_load_file($path);
                $data = json_decode(json_encode($xmlContent), true);
            } elseif ($extension === 'json') {
                // BONUS: Soporte para previsualizar JSON
                $jsonContent = file_get_contents($path);
                $data = json_decode($jsonContent, true);
            } else {
                return back()->with('error', 'Formato de previsualización no soportado.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al leer el archivo: ' . $e->getMessage());
        }

        return view('oc.preview', compact('data', 'oc', 'extension'));
    }

    public function destroy($id)
    {
        $oc = Archivo::findOrFail($id);
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        // Seguridad: Evita que un proveedor elimine archivos ajenos
        if ($esProveedor && $oc->user_id !== $user->id) {
            abort(403, 'No tienes permiso para eliminar este archivo.');
        }

        try {
            $nombreOriginal = $oc->nombre_original;
            $path = storage_path('app/public/uploads/' . $oc->nombre_sistema);

            // 1. Eliminar el archivo físico del disco si existe
            if (file_exists($path)) {
                unlink($path);
            }

            // 2. Eliminar el registro de la base de datos
            $oc->delete();

            // 3. Generar el Log
            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Eliminó con éxito la OC: ' . $nombreOriginal,
                'modulo'  => 'OC',
            ]);

            // CORRECCIÓN: Forzamos la redirección a oc.index en lugar de usar back()
            return redirect()->route('oc.index')->with('success', 'La orden de compra ha sido eliminada correctamente.');
        } catch (\Exception $e) {
            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Error al intentar eliminar OC: ' . $e->getMessage(),
                'modulo'  => 'OC',
            ]);

            // Si hay error, también es mejor mandarlo al index
            return redirect()->route('oc.index')->with('error', 'Error al eliminar el archivo: ' . $e->getMessage());
        }
    }
}
