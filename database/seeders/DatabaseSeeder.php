<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Archivo;
use App\Models\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 0. Limpiar caché de permisos para evitar errores
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Permisos (Tus permisos específicos)
        // Usuarios
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'list users']);
        Permission::firstOrCreate(['name' => 'delete users']);
        // Archivos
        Permission::firstOrCreate(['name' => 'list archivos']);
        Permission::firstOrCreate(['name' => 'upload archivos']);
        Permission::firstOrCreate(['name' => 'delete archivos']); // Agregado por si acaso

        // 2. Crear Roles
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleProveedor = Role::firstOrCreate(['name' => 'proveedor']);

        // 3. Asignar permisos a los Roles
        // Admin tiene todo
        $roleAdmin->givePermissionTo(Permission::all());
        
        // Proveedor solo puede subir y listar sus archivos
        $roleProveedor->givePermissionTo(['list archivos', 'upload archivos']);

        // 4. Tu usuario de prueba (Super Admin) - Para que puedas loguearte ya
        $myUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'password' => bcrypt('holamundo1234'),
                // 'role'     => 'admin', // Descomentar solo si tienes la columna 'role' en tu tabla users
            ]
        );
        $myUser->assignRole($roleAdmin);

        // 5. Crear 5 ADMINISTRADORES extra
        User::factory(5)->create([
            // 'role' => 'admin', 
        ])->each(function ($user) use ($roleAdmin) {
            $user->assignRole($roleAdmin);
        });

        // 6. Crear 95 PROVEEDORES
        User::factory(95)->create([
            // 'role' => 'proveedor', 
        ])->each(function ($user) use ($roleProveedor) {
            $user->assignRole($roleProveedor);
        });

        // 7. Crear 100 archivos y sus logs correspondientes
        Archivo::factory(100)->create()->each(function ($archivo) {
            Log::create([
                'user_id'    => $archivo->user_id, // Usamos el ID del usuario que creó el archivo
                'accion'     => 'SUBIDA DE ARCHIVO',
                'modulo'     => $archivo->modulo ?? 'ORDENES DE COMPRA', // Usar el módulo del archivo o default
                'created_at' => $archivo->created_at,
            ]);
        });
    }
}