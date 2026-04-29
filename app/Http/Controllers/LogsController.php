<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        return view('logs.index');
    }

    /**
     * Muestra el detalle de un registro de log específico.
     */
    public function show($id)
    {
        // 1. Buscamos el log con la información del usuario
        $log = \App\Models\Log::with('user')->findOrFail($id);

        // 2. Seguridad: Si es proveedor, validar que el log sea suyo
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor) {
            // Verifica contra CardCode (o id, dependiendo de cómo guardes user_id en logs)
            if ($log->user_id != $user->CardCode && $log->user_id != $user->id) {
                abort(403, 'No tienes permiso para ver este registro de auditoría.');
            }
        }

        // 3. Retornamos la vista que creamos pasándole los datos
        return view('logs.show', compact('log'));
    }

    public function destroy($id)
    {
        // 1. SEGURIDAD BACKEND: Evitar borrado por Postman/cURL de usuarios no autorizados
        $user = auth()->user();
        $esProveedor = $user->hasRole('proveedor') || $user->role === 'proveedor';

        if ($esProveedor) {
            // Si un proveedor intenta llegar aquí, le cortamos el paso inmediatamente
            abort(403, 'Acceso denegado: No tienes privilegios para eliminar registros de auditoría.');
        }

        // 2. Si pasa la validación (es admin/superadmin), procedemos a borrar
        $log = Log::findOrFail($id);
        $log->delete();

        // Redirige de vuelta a la lista de logs con una alerta de éxito
        return redirect()->route('logs.index')
            ->with('success', 'Registro de log eliminado correctamente.');
    }
}