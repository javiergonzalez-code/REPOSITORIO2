<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MantenimientoController extends Controller
{
    public function toggle($modulo)
    {
        if (!in_array(auth()->user()->role, ['superadmin', 'admin'])) {
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

        return response()->json(['success' => true, 'message' => $mensaje]);
    }
}
