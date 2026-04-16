<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;
use App\Models\Archivo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str; // 🚨 Importación agregada
use Illuminate\Support\Facades\Auth; // 🚨 Importación agregada

class InputController extends Controller
{
    public function index()
    {
        return view('inputs.index');
    }

    public function store(Request $request)
    {
        $user = Auth::user(); // 🚨 Extraemos el usuario autenticado

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
                'user_id' => $user->CardCode, // 🚨 Usamos CardCode
                'accion'  => 'Intento fallido (Validación/Seguridad): ' . $errores,
                'modulo'  => 'INPUTS',
            ]);
            Alert::error('Archivo no válido', $errores);
            return back();
        }

        try {
            $file = $request->file('archivo');
            $nombreSinExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $originalName = Str::slug($nombreSinExt, '_') . '.' . $file->getClientOriginalExtension();
            
            if (strlen($originalName) > 200) {
                $originalName = substr($originalName, -200);
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $systemName = time() . '_' . uniqid() . '_' . $originalName;

            // Guardar físicamente
            $path = $file->storeAs('private/uploads', $systemName, 'local');

            if (!$path) {
                throw new \Exception("El servidor denegó el permiso de escritura.");
            }

            // ====================================================================
            // CLASIFICADOR UNIFICADO (A PRUEBA DE ERRORES)
            // ====================================================================
            
            // Leemos el encabezado y lo pasamos a MAYÚSCULAS
            $contenidoParcial = file_get_contents($file->getRealPath(), false, null, 0, 500);
            $contenidoUpper = strtoupper($contenidoParcial);
            
            $moduloDestino = 'OC'; // Valor por defecto

            if (
                str_contains($contenidoUpper, 'TIPOERROR') || 
                str_contains($contenidoUpper, 'EXCEPCION') || 
                str_contains($contenidoUpper, 'FORMATO')
            ) {
                $moduloDestino = 'ERRORES';
            } 
            elseif (
                str_contains($contenidoUpper, 'EXTRA') || 
                str_contains($contenidoUpper, 'EXITOSA') || 
                str_contains($contenidoUpper, 'OC HIJA CREADA')
            ) {
                $moduloDestino = 'LOGS';
            }

            // Crear registro en Base de Datos
            Archivo::create([
                'user_id'         => $user->CardCode, // 🚨 Usamos CardCode
                'nombre_original' => $originalName,
                'nombre_sistema'  => $systemName,
                'tipo_archivo'    => $extension,
                'ruta'            => 'private/uploads/' . $systemName,
                'modulo'          => $moduloDestino, 
            ]);

            // Registrar el Log de la acción
            Log::create([
                'user_id' => $user->CardCode, // 🚨 Usamos CardCode
                'accion'  => 'Subió con éxito: ' . $originalName,
                'modulo'  => $moduloDestino,
            ]);

            Alert::success('¡Subida Exitosa!', 'Archivo clasificado en el módulo: ' . $moduloDestino);
            return back();

        } catch (QueryException $e) {
            if (isset($path)) Storage::disk('local')->delete($path);
            Log::create([
                'user_id' => $user->CardCode, // 🚨 Usamos CardCode
                'accion'  => Str::limit('Error BD: ' . $e->getMessage(), 250),
                'modulo'  => 'INPUTS'
            ]);
            Alert::error('Error Crítico', 'No se pudo registrar en la base de datos.');
            return back();
        } catch (\Exception $e) {
            Log::create([
                'user_id' => $user->CardCode, // 🚨 Usamos CardCode
                'accion'  => Str::limit('Error Servidor: ' . $e->getMessage(), 250),
                'modulo'  => 'INPUTS',
            ]);
            Alert::error('Error del Servidor', 'Error al procesar el archivo.');
            return back();
        }
    }

    public function download($id)
    {
        $archivo = Archivo::findOrFail($id);
        $user = Auth::user(); // 🚨 Fachada Auth estandarizada

        // 1. Validación de seguridad con verificación de Rol Nativo
        // 🚨 Comparamos CardCode con user_id (string)
        if ($user->role === 'proveedor' && $archivo->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        // 2. Búsqueda y descarga limpia y nativa de Laravel
        if (!Storage::disk('local')->exists($archivo->ruta)) {
            abort(404, 'El archivo físico no se encuentra en el servidor.');
        }

        return Storage::disk('local')->download($archivo->ruta, $archivo->nombre_original);
    }
}