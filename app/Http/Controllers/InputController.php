<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Log;
use App\Models\Archivo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class InputController extends Controller
{
    public function index()
    {
        return view('inputs.index');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'archivo' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls,xml',
                'extensions:csv,xlsx,xls,xml',
                'max:5120',
            ]
        ], [
            'archivo.mimes' => 'El contenido del archivo no coincide con su extensión o tiene formato malicioso.',
            'archivo.extensions' => 'La extensión del archivo no está permitida por políticas de seguridad.',
            'archivo.max' => 'El archivo supera el límite máximo de 5MB.'
        ]);

        if ($validator->fails()) {
            $file = $request->file('archivo');
            $nivelAmenaza = 'Advertencia';
            $detalleFallo = 'Error de validación desconocido.';

            // 1. CLASIFICADOR DE AMENAZAS AL VUELO
            if ($file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $mimeReal = $file->getMimeType(); // Laravel lee los bytes reales del archivo, no se fía del nombre

                $maliciosos = ['exe', 'php', 'sh', 'bat', 'js', 'vbs', 'msi', 'cmd', 'ps1'];

                // CASO A: HACKER (Ataque Directo) - Sube explícitamente un ejecutable o script
                if (in_array($ext, $maliciosos)) {
                    $nivelAmenaza = '🚨 CRÍTICO (Intento de Malware)';
                    $detalleFallo = "Se intentó subir un archivo ejecutable/script explícito. Extensión enviada: .$ext";
                }
                // CASO B: HACKER (Spoofing/Disfraz) - Sube un archivo malicioso renombrado a .csv o .xlsx
                elseif (in_array($ext, ['csv', 'xlsx', 'xls', 'xml']) && $validator->errors()->has('archivo.mimes')) {
                    $nivelAmenaza = '🚨 CRÍTICO (Archivo Disfrazado/Spoofing)';
                    $detalleFallo = "El archivo finge ser un .$ext pero su contenido interno real detectado es: $mimeReal";
                }
                // CASO C: USUARIO TORPE - Subió un PDF, JPG o Word por equivocación
                elseif (!in_array($ext, ['csv', 'xlsx', 'xls', 'xml'])) {
                    $nivelAmenaza = '⚠️ Leve (Error de Formato)';
                    $detalleFallo = "El usuario subió un formato no soportado (.$ext en lugar de Excel/XML). No representa una amenaza directa.";
                }
                // CASO D: USUARIO NORMAL - Archivo muy pesado
                elseif ($validator->errors()->has('archivo.max')) {
                    $nivelAmenaza = '⚠️ Leve (Exceso de Tamaño)';
                    $detalleFallo = "El archivo es del formato correcto (.$ext), pero supera el límite de 5MB.";
                }
            } else {
                // CASO E: Formulario vacío (Posible Bot escaneando)
                $nivelAmenaza = '⚠️ Leve (Formulario Vacío)';
                $detalleFallo = "Se envió el formulario sin ningún documento adjunto.";
            }

            // 2. Guardamos la radiografía exacta en la Base de Datos para el Admin
            try {
                Log::create([
                    'user_id' => $user ? $user->CardCode : 'Atacante Anónimo',
                    'accion'  => "$nivelAmenaza - $detalleFallo",
                    'modulo'  => 'ERRORES',
                ]);
            } catch (\Exception $logE) {
                // Escribimos en storage/logs/laravel.log si la BD no responde
                \Illuminate\Support\Facades\Log::warning("Alerta de Seguridad (BD CAÍDA) | IP: {$request->ip()} | Fallo: $detalleFallo");
            }

            // 3. Mensaje genérico y amable para el usuario / atacante (Lo que sale en SweetAlert)
            $mensajeAmigable = 'El documento no pudo ser procesado. Verifique que sea un formato válido (Excel, CSV o XML) y no exceda los 5MB.';

            Alert::error('Archivo no admitido', $mensajeAmigable);
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

            // CLASIFICADOR UNIFICADO
            $contenidoParcial = file_get_contents($file->getRealPath(), false, null, 0, 500);
            $contenidoUpper = strtoupper($contenidoParcial);
            $moduloDestino = 'OC'; // Valor por defecto

            if (str_contains($contenidoUpper, 'TIPOERROR') || str_contains($contenidoUpper, 'EXCEPCION') || str_contains($contenidoUpper, 'FORMATO')) {
                $moduloDestino = 'ERRORES';
            } elseif (str_contains($contenidoUpper, 'EXTRA') || str_contains($contenidoUpper, 'EXITOSA') || str_contains($contenidoUpper, 'OC HIJA CREADA')) {
                $moduloDestino = 'LOGS';
            }

            Archivo::create([
                'user_id'         => $user->CardCode,
                'nombre_original' => $originalName,
                'nombre_sistema'  => $systemName,
                'tipo_archivo'    => $extension,
                'ruta'            => 'private/uploads/' . $systemName,
                'modulo'          => $moduloDestino,
            ]);

            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Subió con éxito: ' . $originalName,
                'modulo'  => $moduloDestino,
            ]);

            Alert::success('¡Subida Exitosa!', 'Archivo clasificado en el módulo: ' . $moduloDestino);
            return back();
        } catch (QueryException $e) {
            if (isset($path)) Storage::disk('local')->delete($path);

            // 🚨 ESCENARIO 3 RESUELTO: Prevención de Efecto Dominó si la BD cae
            try {
                Log::create([
                    'user_id' => $user->CardCode,
                    'accion'  => Str::limit('Error BD: ' . $e->getMessage(), 200) . ' | IP: ' . $request->ip(),
                    'modulo'  => 'ERRORES'
                ]);
            } catch (\Exception $logE) {
                \Illuminate\Support\Facades\Log::error('CRÍTICO BD CAÍDA | IP: ' . $request->ip() . ' | Error: ' . $e->getMessage());
            }

            Alert::error('Error Crítico', 'No se pudo registrar en la base de datos.');
            return back();
        } catch (\Exception $e) {

            try {
                Log::create([
                    'user_id' => $user->CardCode,
                    'accion'  => Str::limit('Error Servidor: ' . $e->getMessage(), 200) . ' | IP: ' . $request->ip(),
                    'modulo'  => 'ERRORES',
                ]);
            } catch (\Exception $logE) {
                \Illuminate\Support\Facades\Log::error('CRÍTICO SERVIDOR | IP: ' . $request->ip() . ' | Error: ' . $e->getMessage());
            }

            Alert::error('Error del Servidor', 'Error al procesar el archivo.');
            return back();
        }
    }

    public function download($id)
    {
        $archivo = Archivo::findOrFail($id);
        $user = Auth::user();

        // 1. Validación de seguridad con verificación de Rol Nativo
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
