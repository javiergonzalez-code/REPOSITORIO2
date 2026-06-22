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
        // Renderiza la vista del cargador de archivos
        return view('inputs.index');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Reglas de validación estrictas contra archivos maliciosos
        $validator = Validator::make($request->all(), [
            'archivo' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls,xml', // Valida contenido real
                'extensions:csv,xlsx,xls,xml', // Valida la extensión escrita
                'max:5120', // Máximo 5MB
            ]
        ], [
            'archivo.mimes' => 'El contenido del archivo no coincide con su extensión o tiene formato malicioso.',
            'archivo.extensions' => 'La extensión del archivo no está permitida por políticas de seguridad.',
            'archivo.max' => 'El archivo supera el límite máximo de 5MB.'
        ]);

        // MANEJO DE ERRORES DE VALIDACIÓN
        if ($validator->fails()) {
            $file = $request->file('archivo');
            $nivelAmenaza = 'Advertencia';
            $detalleFallo = 'Error de validación desconocido.';

            if ($file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $mimeReal = $file->getMimeType(); 
                $maliciosos = ['exe', 'php', 'sh', 'bat', 'js', 'vbs', 'msi', 'cmd', 'ps1'];

                // Caso A: Intento obvio de subir un script ejecutable
                if (in_array($ext, $maliciosos)) {
                    $nivelAmenaza = '🚨 CRÍTICO (Intento de Malware)';
                    $detalleFallo = "Se intentó subir un archivo ejecutable/script explícito. Extensión enviada: .$ext";
                }
                // Caso B: Ataque Spoofing 
                elseif (in_array($ext, ['csv', 'xlsx', 'xls', 'xml']) && $validator->errors()->has('archivo.mimes')) {
                    $nivelAmenaza = '🚨 CRÍTICO (Archivo Disfrazado/Spoofing)';
                    $detalleFallo = "El archivo finge ser un .$ext pero su contenido interno real detectado es: $mimeReal";
                }
                // Caso C: Error humano común (Metió un PDF o imagen)
                elseif (!in_array($ext, ['csv', 'xlsx', 'xls', 'xml'])) {
                    $nivelAmenaza = '⚠️ Leve (Error de Formato)';
                    $detalleFallo = "El usuario subió un formato no soportado (.$ext en lugar de Excel/XML). No representa una amenaza directa.";
                }
                // Caso D: Formato correcto pero pesa de más
                elseif ($validator->errors()->has('archivo.max')) {
                    $nivelAmenaza = '⚠️ Leve (Exceso de Tamaño)';
                    $detalleFallo = "El archivo es del formato correcto (.$ext), pero supera el límite de 5MB.";
                }
            } else {
                // Caso E: Sin mandar nada
                $nivelAmenaza = '⚠️ Leve (Formulario Vacío)';
                $detalleFallo = "Se envió el formulario sin ningún documento adjunto.";
            }

            // Guarda el error
            try {
                Log::create([
                    'user_id' => $user ? $user->CardCode : 'Atacante Anónimo',
                    'accion'  => "$nivelAmenaza - $detalleFallo",
                    'modulo'  => 'ERRORES',
                ]);
            } catch (\Exception $logE) {
                \Illuminate\Support\Facades\Log::warning("Alerta de Seguridad (BD CAÍDA) | IP: {$request->ip()} | Fallo: $detalleFallo");
            }

            // Alerta genérica para no darle pistas al atacante de por qué rebotó
            $mensajeAmigable = 'El documento no pudo ser procesado. Verifique que sea un formato válido (Excel, CSV o XML) y no exceda los 5MB.';
            Alert::error('Archivo no admitido', $mensajeAmigable);
            return back();
        }

        // PROCESAMIENTO DE ARCHIVO EXITOSO
        try {
            $file = $request->file('archivo');
            $nombreSinExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            
            // Sanitiza el nombre original y limita a 200 caracteres max
            $originalName = Str::slug($nombreSinExt, '_') . '.' . $file->getClientOriginalExtension();
            if (strlen($originalName) > 200) {
                $originalName = substr($originalName, -200);
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $systemName = time() . '_' . uniqid() . '_' . $originalName;

            // Guarda el archivo
            $path = $file->storeAs('private/uploads', $systemName, 'local');

            if (!$path) {
                throw new \Exception("El servidor denegó el permiso de escritura.");
            }

            // Lee los primeros 500 bytes para ver qué contiene
            $contenidoParcial = file_get_contents($file->getRealPath(), false, null, 0, 500);
            $contenidoUpper = strtoupper($contenidoParcial);
            $moduloDestino = 'OC'; // Destino por defecto, OC

            // Si contiene palabras clave, lo redirige de módulo automáticamente
            if (str_contains($contenidoUpper, 'TIPOERROR') || str_contains($contenidoUpper, 'EXCEPCION') || str_contains($contenidoUpper, 'FORMATO')) {
                $moduloDestino = 'ERRORES';
            } elseif (str_contains($contenidoUpper, 'EXTRA') || str_contains($contenidoUpper, 'EXITOSA') || str_contains($contenidoUpper, 'OC HIJA CREADA')) {
                $moduloDestino = 'LOGS';
            }

            // Registra el archivo en BD
            Archivo::create([
                'user_id'         => $user->CardCode,
                'nombre_original' => $originalName,
                'nombre_sistema'  => $systemName,
                'tipo_archivo'    => $extension,
                'ruta'            => 'private/uploads/' . $systemName,
                'modulo'          => $moduloDestino,
            ]);

            // Registra la acción en los logs
            Log::create([
                'user_id' => $user->CardCode,
                'accion'  => 'Subió con éxito: ' . $originalName,
                'modulo'  => $moduloDestino,
            ]);

            Alert::success('¡Subida Exitosa!', 'Archivo clasificado en el módulo: ' . $moduloDestino);
            return back();

        } catch (QueryException $e) {
            // Si la BD truena, borra el archivo físico para no dejar basura colgada
            if (isset($path)) Storage::disk('local')->delete($path);

            // Respaldo de logs en archivo físico si cae la base de datos
            try {
                Log::create([
                    'user_id' => $user->CardCode,
                    'accion'  => Str::limit('Error BD: ' . $e->getMessage(), 200) .'',
                    'modulo'  => 'ERRORES'
                ]);
            } catch (\Exception $logE) {
                \Illuminate\Support\Facades\Log::error('CRÍTICO BD CAÍDA | IP: ' . $request->ip() . ' | Error: ' . $e->getMessage());
            }

            Alert::error('Error Crítico', 'No se pudo registrar en la base de datos.');
            return back();
        } catch (\Exception $e) {
            // Manejo de errores generales del servidor
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

        // Los proveedores sólo descargan sus propios archivos
        if ($user->role === 'proveedor' && $archivo->user_id !== $user->CardCode) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        // Verifica existencia física antes de intentar la descarga
        if (!Storage::disk('local')->exists($archivo->ruta)) {
            abort(404, 'El archivo físico no se encuentra en el servidor.');
        }

        return Storage::disk('local')->download($archivo->ruta, $archivo->nombre_original);
    }
}