<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear los roles en Spatie
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'proveedor', 'guard_name' => 'web']);

        // 2. Asignar rol al admin principal tanto en la columna normal como en Spatie
        // BUSCAMOS POR "E_Mail" en lugar de "email"
        $user = User::where('E_Mail', 'admin@ragon.com')->first();
        
        if ($user) {
            // Actualiza la columna de tu base de datos MySQL (este atributo se llama 'role')
            $user->role = 'superadmin';
            $user->save();
            
            // Le asigna el rol de Spatie
            $user->assignRole($superAdminRole);
        }
    }
}