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
                'extensions:csv,xlsx,xls,xml,txt',
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

            $nombreSinExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $originalName = \Illuminate\Support\Str::slug($nombreSinExt, '_') . '.' . $file->getClientOriginalExtension();
            if (strlen($originalName) > 200) {
                $originalName = substr($originalName, -200);
            }

            $extension = $file->getClientOriginalExtension();

            $allowedExtensions = ['csv', 'xlsx', 'xls', 'xml', 'txt'];
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                throw new \Exception("Extensión de archivo maliciosa detectada.");
            }

            $systemName = time() . '_' . uniqid() . '_' . $originalName;

            // CORRECCIÓN 1: Guardar físicamente en la carpeta privada
            $path = $file->storeAs('private/uploads', $systemName, 'local');

            if (!$path) {
                throw new \Exception("El servidor denegó el permiso de escritura en el disco duro.");
            }

            Archivo::create([
                'user_id'         => auth()->id(),
                'nombre_original' => $originalName,
                'nombre_sistema'  => $systemName,
                'tipo_archivo'    => strtolower($extension),
                // CORRECCIÓN 2: Guardar la ruta correcta en la base de datos
                'ruta'            => 'private/uploads/' . $systemName,
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
            if (isset($path) && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

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

    public function download($id)
    {
        $archivo = Archivo::findOrFail($id);
        $user = auth()->user();

        // 1. Validación de seguridad (¡Esto lo hiciste perfecto!)
        if (($user->hasRole('proveedor') || $user->role === 'proveedor') && $archivo->user_id !== $user->id) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        // 2. Búsqueda y descarga limpia y nativa de Laravel (Sin str_replace)
        if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($archivo->ruta)) {
            abort(404, 'El archivo físico no se encuentra en el servidor.');
        }

        return \Illuminate\Support\Facades\Storage::disk('local')->download($archivo->ruta, $archivo->nombre_original);
    }
}
