<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;
use Illuminate\Support\Facades\Auth; 

class OcController extends Controller
{
    public function index()
    {
        // El listado de Órdenes de Compra se maneja por detrás en la vista
        return view('oc.index');
    }

    public function download($id)
    {
        $oc = Archivo::findOrFail($id);
        $user = Auth::user(); 
        $esProveedor = $user->role === 'proveedor';

        // Los proveedores sólo descargan sus propios archivos
        if ($esProveedor && $oc->user_id !== $user->CardCode) {
            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Intento de descarga denegado (sin permisos): ' . $oc->nombre_original,
                'modulo'  => 'OC',
            ]);
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        // Verifica si el archivo se borró
        if (!Storage::disk('local')->exists($oc->ruta)) {
            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Intento de descarga fallido (archivo físico extraviado): ' . $oc->nombre_original,
                'modulo'  => 'OC',
            ]);
            Alert::error('Extraviado', 'El archivo físico no existe.');
            return back();
        }

        // Log de descarga exitosa
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
        $user = Auth::user(); 
        $esProveedor = $user->role === 'proveedor';

        // Bloqueo de seguridad basado en pertenencia por CardCode
        if ($esProveedor && $oc->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para previsualizar este archivo.');
        }

        Log::create([
            'user_id' => $user->CardCode,
            'accion'  => 'Previsualizó el archivo: ' . $oc->nombre_original,
            'modulo'  => 'OC',
        ]);

        // Validación de existencia nativa de Laravel
        if (!Storage::disk('local')->exists($oc->ruta)) {
            Alert::error('Extraviado', 'El archivo físico no existe en el servidor.');
            return back();
        }

        // Obtener tamaño y ruta absoluta de forma segura para Windows/Linux
        $tamanoArchivo = Storage::disk('local')->size($oc->ruta);
        $path = Storage::disk('local')->path($oc->ruta);

        // Si pesa más de 5MB no se renderiza en pantalla para evitar lag
        if ($tamanoArchivo > 5242880) {
            Alert::warning('Archivo muy grande', 'El archivo es demasiado grande para previsualizarlo. Por favor, descárgalo.');
            return back();
        }

        $extension = strtolower($oc->tipo_archivo);
        
        // PROCESADOR MULTI-FORMATO PARA LA VISTA
        try {
            // Caso Excel / CSV: Convierte las celdas en arrays
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $sheets = Excel::toArray(new class {}, $path);
                $data = $sheets[0] ?? [];
            } 
            // Caso XML: Lee la estructura controlando errores de sintaxis
            elseif ($extension === 'xml') {
                libxml_use_internal_errors(true);
                $xmlContent = simplexml_load_file($path);

                if ($xmlContent === false) {
                    Alert::error('XML Corrupto', 'El archivo no tiene un formato XML válido.');
                    return back();
                }

                $data = json_decode(json_encode($xmlContent), true);
            } 
            else {
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
        $user = Auth::user();
        $esProveedor = $user->role === 'proveedor';

        // El proveedor no puede borrar archivos ajenos
        if ($esProveedor && $oc->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para eliminar este archivo.');
        }

        try {
            $nombreOriginal = $oc->nombre_original;
            $rutaArchivo = $oc->ruta;
            
            // Elimina el registro de la Base de Datos
            $oc->delete();

            // OPCIONAL pero recomendado: Eliminar también el archivo físico del storage si ya no existe el registro
            if (Storage::disk('local')->exists($rutaArchivo)) {
                Storage::disk('local')->delete($rutaArchivo);
            }

            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Eliminó la OC y su archivo físico: ' . $nombreOriginal,
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