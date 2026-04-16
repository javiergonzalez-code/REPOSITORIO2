<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Log;
use Illuminate\Support\Facades\Auth; // 🚨 Importamos la fachada Auth para mantener el estándar

class MantenimientoController extends Controller
{
    public function toggle($modulo)
    {
        $user = Auth::user(); // 🚨 Uso de la fachada

        if (!in_array($user->role, ['superadmin', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $setting = DB::table('modulo_settings')->where('nombre_modulo', $modulo)->first();

        if ($setting) {
            DB::table('modulo_settings')->where('nombre_modulo', $modulo)->update([
                'en_mantenimiento' => !$setting->en_mantenimiento,
                'updated_at' => now()
            ]);
            $nuevoEstado = !$setting->en_mantenimiento;
        } else {
            DB::table('modulo_settings')->insert([
                'nombre_modulo' => $modulo,
                'en_mantenimiento' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $nuevoEstado = true;
        }

        $mensaje = $nuevoEstado ? 'Módulo en mantenimiento' : 'Módulo abierto al público';

        Log::create([
            'user_id' => $user->CardCode,
            'accion'  => 'MANTENIMIENTO - Cambió estado de ' . strtoupper($modulo) . ' a: ' . ($nuevoEstado ? 'CERRADO' : 'ABIERTO'),
            'modulo'  => 'AJUSTES'
        ]);

        return response()->json(['success' => true, 'message' => $mensaje]);
    }
}