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
        // La consulta a la BD y filtros se movieron a Livewire
        return view('oc.index');
    }

    public function download($id)
    {
        $oc = Archivo::findOrFail($id);
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor && $oc->user_id !== $user->id) {
            // LOG: Intento no autorizado
            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Intento de descarga denegado (sin permisos): ' . $oc->nombre_original,
                'modulo'  => 'OC',
            ]);

            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        $path = storage_path('app/' . $oc->ruta);
        if (!file_exists($path)) {
            // LOG: Archivo no encontrado
            \App\Models\Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Intento de descarga fallido (archivo físico extraviado): ' . $oc->nombre_original,
                'modulo'  => 'OC',
            ]);

            Alert::error('Extraviado', 'El archivo físico no existe.');
            return back();
        }

        // LOG: DESCARGA EXITOSA (Agregado aquí)
        // Utilizamos la palabra "Descargó" para que coincida con tu filtro Livewire
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

        $path = storage_path('app/' . $oc->ruta);
        if (!file_exists($path)) {
            Alert::error('Extraviado', 'El archivo físico no existe en el servidor.');
            return back();
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
                'accion'  => 'Envió a la papelera la OC: ' . $nombreOriginal, // Opcional: ajustar el texto del log
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
