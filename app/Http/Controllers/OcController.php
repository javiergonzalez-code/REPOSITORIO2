<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;

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
        
        // 🚨 Validación nativa de rol y comparación por CardCode
        $esProveedor = $user->role === 'proveedor';

        if ($esProveedor && $oc->user_id !== $user->CardCode) {
            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Intento de descarga denegado (sin permisos): ' . $oc->nombre_original,
                'modulo'  => 'OC',
            ]);
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        if (!Storage::disk('local')->exists($oc->ruta)) {
            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Intento de descarga fallido (archivo físico extraviado): ' . $oc->nombre_original,
                'modulo'  => 'OC',
            ]);
            Alert::error('Extraviado', 'El archivo físico no existe.');
            return back();
        }

        Log::create([
            'user_id' => $user->CardCode,
            'accion'  => 'Descargó con éxito el archivo: ' . $oc->nombre_original,
            'modulo'  => 'OC',
        ]);

        return Storage::disk('local')->download($oc->ruta, $oc->nombre_original);
    }

    public function preview($id)
    {
        $oc = Archivo::findOrFail($id);
        $user = auth()->user();
        
        // 🚨 Validación nativa de rol
        $esProveedor = $user->role === 'proveedor';

        if ($esProveedor && $oc->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para previsualizar este archivo.');
        }

        // 🚨 Auditoría usando CardCode
        Log::create([
            'user_id' => $user->CardCode,
            'accion'  => 'Previsualizó el archivo: ' . $oc->nombre_original,
            'modulo'  => 'OC',
        ]);

        if (!Storage::disk('local')->exists($oc->ruta)) {
            Alert::error('Extraviado', 'El archivo físico no existe en el servidor.');
            return back();
        }

        $path = storage_path('app/' . $oc->ruta);
        $tamanoArchivo = filesize($path);

        if ($tamanoArchivo > 5242880) {
            Alert::warning('Archivo muy grande', 'El archivo es demasiado grande para previsualizarlo. Por favor, descárgalo.');
            return back();
        }

        $extension = strtolower($oc->tipo_archivo);
        
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
        
        // 🚨 Validación nativa de rol
        $esProveedor = $user->role === 'proveedor';

        if ($esProveedor && $oc->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para eliminar este archivo.');
        }

        try {
            $nombreOriginal = $oc->nombre_original;
            $oc->delete();

            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Envió a la papelera la OC: ' . $nombreOriginal,
                'modulo'  => 'OC',
            ]);

            Alert::success('¡Eliminado!', 'La orden de compra ha sido eliminada correctamente.');
            return redirect()->route('oc.index');
        } catch (\Exception $e) {
            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Error al intentar eliminar OC: ' . $e->getMessage(),
                'modulo'  => 'OC',
            ]);

            Alert::error('Error', 'Error al eliminar el archivo: ' . $e->getMessage());
            return redirect()->route('oc.index');
        }
    }
}