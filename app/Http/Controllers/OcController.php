<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class OcController extends Controller
{
    public function index()
    {
        return view('oc.index');
    }

    public function download($id)
    {
        $oc = Archivo::findOrFail($id);
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor && $oc->user_id !== $user->id) {
            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Intento de descarga denegado (sin permisos): ' . $oc->nombre_original,
                'modulo'  => 'OC',
            ]);

            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        // CORRECCIÓN: Se quitó 'uploads/' porque $oc->ruta ya lo trae.
        $path = storage_path('app/private/' . $oc->ruta);
        
        if (!file_exists($path)) {
            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Intento de descarga fallido (archivo físico extraviado): ' . $oc->nombre_original,
                'modulo'  => 'OC',
            ]);

            Alert::error('Extraviado', 'El archivo físico no existe.');
            return back();
        }

        \App\Models\Log::create([
            'user_id' => auth()->id(),
            'accion'  => 'Descargó con éxito el archivo: ' . $oc->nombre_original,
            'modulo'  => 'OC',
        ]);

        return response()->download($path, $oc->nombre_original);
    }

    public function preview($id)
    {
        $oc = Archivo::findOrFail($id);
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor && $oc->user_id !== $user->id) {
            abort(403, 'No tienes permiso para previsualizar este archivo.');
        }

        // CORRECCIÓN: Se quitó 'uploads/' porque $oc->ruta ya lo trae.
        $path = storage_path('app/private/' . $oc->ruta);
        
        if (!file_exists($path)) {
            Alert::error('Extraviado', 'El archivo físico no existe en el servidor.');
            return back();
        }

        $extension = strtolower(pathinfo($oc->nombre_original, PATHINFO_EXTENSION));
        $data = [];

        $tamanoArchivo = filesize($path);
        if ($tamanoArchivo > 5242880) {
            Alert::warning('Archivo muy grande', 'El archivo es demasiado grande para previsualizarlo. Por favor, descárgalo.');
            return back();
        }

        try {
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $sheets = Excel::toArray(new class {}, $path);
                $data = $sheets[0] ?? [];
            } elseif ($extension === 'xml') {
                libxml_use_internal_errors(true);
                $xmlContent = simplexml_load_file($path);

                if ($xmlContent === false) {
                    Alert::error('XML Corrupto', 'El archivo no tiene un formato XML válido.');
                    return back();
                }

                $data = json_decode(json_encode($xmlContent), true);
            } elseif ($extension === 'json') {
                $jsonContent = file_get_contents($path);
                $data = json_decode($jsonContent, true);
            } else {
                Alert::warning('No Soportado', 'Formato de previsualización no soportado.');
                return back();
            }
        } catch (\Exception $e) {
            Alert::error('Error de Lectura', 'Error al leer el archivo: ' . $e->getMessage());
            return back();
        }

        return view('oc.preview', compact('data', 'oc', 'extension'));
    }

    public function destroy($id)
    {
        $oc = Archivo::findOrFail($id);
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor && $oc->user_id !== $user->id) {
            abort(403, 'No tienes permiso para eliminar este archivo.');
        }

        try {
            $nombreOriginal = $oc->nombre_original;

            $oc->delete();

            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Envió a la papelera la OC: ' . $nombreOriginal, 
                'modulo'  => 'OC',
            ]);

            Alert::success('¡Eliminado!', 'La orden de compra ha sido eliminada correctamente.');
            return redirect()->route('oc.index');
        } catch (\Exception $e) {
            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Error al intentar eliminar OC: ' . $e->getMessage(),
                'modulo'  => 'OC',
            ]);

            Alert::error('Error', 'Error al eliminar el archivo: ' . $e->getMessage());
            return redirect()->route('oc.index');
        }
    }
}