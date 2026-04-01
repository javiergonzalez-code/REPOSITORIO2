<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;
use App\Models\Archivo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use RealRashid\SweetAlert\Facades\Alert;

class InputController extends Controller
{
    public function index()
    {
        return view('inputs.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archivo' => [
                'required',
                'file',
                'mimes:csv,txt,xlsx,xls,xml',
                'extensions:csv,xlsx,xls,xml',
                'max:5120',
            ]
        ], [
            'archivo.mimes' => 'El contenido del archivo no coincide con su extensión o tiene formato malicioso.',
            'archivo.extensions' => 'La extensión del archivo no está permitida por políticas de seguridad.',
            'archivo.max' => 'El archivo supera el límite máximo de 5MB.'
        ]);

        if ($validator->fails()) {
            $errores = implode(' | ', $validator->errors()->all());

            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Intento fallido (Validación/Seguridad): ' . $errores,
                'modulo'  => 'INPUTS',
            ]);

            Alert::error('Archivo no válido', $errores);
            return back();
        }

        try {
            $file = $request->file('archivo');

            $originalName = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $file->getClientOriginalName());
            if (strlen($originalName) > 200) {
                $originalName = substr($originalName, -200);
            }

            $extension = $file->getClientOriginalExtension();

            $allowedExtensions = ['csv', 'xlsx', 'xls', 'xml', 'txt'];
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                throw new \Exception("Extensión de archivo maliciosa detectada.");
            }

            $systemName = time() . '_' . uniqid() . '_' . $originalName;

            $path = $file->storeAs('uploads', $systemName, 'local');

            if (!$path) {
                throw new \Exception("El servidor denegó el permiso de escritura en el disco duro.");
            }

            Archivo::create([
                'user_id'         => auth()->id(),
                'nombre_original' => $originalName,
                'nombre_sistema'  => $systemName,
                'tipo_archivo'    => strtolower($extension),
                'ruta'            => 'uploads/' . $systemName,
                'modulo'          => 'INPUTS',
            ]);

            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Subió con éxito: ' . $originalName,
                'modulo'  => 'INPUTS',
            ]);

            Alert::success('¡Subida Exitosa!', 'Archivo subido y verificado correctamente.');
            return back();
        } catch (QueryException $e) {
            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Error interno (Base de Datos): ' . $e->getMessage(),
                'modulo'  => 'INPUTS'
            ]);

            Alert::error('Error Crítico', 'No se pudo registrar en la base de datos.');
            return back();
        } catch (\Exception $e) {
            Log::create([
                'user_id' => auth()->id(),
                'accion'  => 'Error interno (Seguridad/Servidor): ' . $e->getMessage(),
                'modulo'  => 'INPUTS',
            ]);

            Alert::error('Error del Servidor', 'Error al procesar el archivo: ' . $e->getMessage());
            return back();
        }
    }

    // EN EL MÉTODO download() de InputController
    public function download($id)
    {
        $archivo = Archivo::findOrFail($id);
        $user = auth()->user();

        if (($user->hasRole('proveedor') || $user->role === 'proveedor') && $archivo->user_id !== $user->id) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        // SOLUCIÓN DEFINITIVA: Limpiamos por si en la BD se guardó "private/uploads" o solo "uploads"
        $rutaLimpia = str_replace('private/', '', $archivo->ruta);
        $path = storage_path('app/private/' . $rutaLimpia);

        if (!file_exists($path)) {
            abort(404, 'El archivo físico no se encuentra en el servidor.');
        }

        return response()->download($path, $archivo->nombre_original);
    }
}
