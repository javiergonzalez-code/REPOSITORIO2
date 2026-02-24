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
        $user = User::where('email', 'admin@ragon.com')->first();
        
        if ($user) {
            // Actualiza la columna de tu base de datos MySQL
            $user->role = 'superadmin';
            $user->save();
            
            // Le asigna el rol de Spatie
            $user->assignRole($superAdminRole);
        }
    }
}