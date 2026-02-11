<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Archivo;
use App\Models\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;       // [IMPORTANTE] Necesario para crear roles
use Spatie\Permission\Models\Permission; // [IMPORTANTE] Necesario para permisos

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Limpiar caché de permisos para evitar errores
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear los Roles en la tabla de Spatie
        // Usamos 'firstOrCreate' para que no falle si ejecutas el seeder dos veces
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleProveedor = Role::firstOrCreate(['name' => 'proveedor']);

        // 2. Crear 5 ADMINISTRADORES
        User::factory(5)->create([
            'role' => 'admin', // Actualizamos la columna de texto simple (para que lo veas en la DB)
        ])->each(function ($user) use ($roleAdmin) {
            $user->assignRole($roleAdmin); // Asignamos el rol real de Spatie
        });

        // 3. Crear 95 USUARIOS NORMALES (Proveedores)
        User::factory(95)->create([
            'role' => 'proveedor', // Columna de texto simple
        ])->each(function ($user) use ($roleProveedor) {
            $user->assignRole($roleProveedor); // Rol de Spatie
        });

        // 4. Tu usuario de prueba (Super Admin)
        $myUser = User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('holamundo1234'),
            'role'     => 'admin',
        ]);
        $myUser->assignRole($roleAdmin);


        // 5. Crear 100 archivos y sus logs
        // Nota: Esto creará 100 usuarios extra (propios de cada archivo) si no vinculamos
        // los archivos a los usuarios que acabamos de crear. 
        // Para este ejemplo, dejaremos que factory cree usuarios nuevos para los archivos 
        // o puedes vincularlos a los 95 proveedores si prefieres.
        Archivo::factory(100)->create()->each(function ($archivo) {
            
            Log::create([
                'user_id'    => $archivo->user_id,
                'accion'     => 'SUBIDA DE ARCHIVO',
                'modulo'     => 'ORDENES DE COMPRA',
                'created_at' => $archivo->created_at,
            ]);
        });
    }
}