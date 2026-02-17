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
        // 0. Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Permisos
        $permissions = [
            'edit users', 'list users', 'delete users',
            'list archivos', 'upload archivos', 'delete archivos'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Crear Roles
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleProveedor = Role::firstOrCreate(['name' => 'proveedor']);

        // 3. Asignar permisos a los Roles
        $roleAdmin->syncPermissions(Permission::all());
        $roleProveedor->syncPermissions(['list archivos', 'upload archivos']);

        // 4. Tu usuario personal (Super Admin)
        // Usamos updateOrCreate para que si ya existe, solo le asigne el rol correctamente
        $myUser = User::updateOrCreate(
            ['email' => 'admin@ragon.com'], // Usa el correo que usaste en Tinker
            [
                'name'     => 'Administrador Principal',
                'password' => 'holamundo1234',
                'role'     => 'admin', // Actualizamos tu columna física
            ]
        );
        $myUser->assignRole($roleAdmin);    

        // 5. Crear 5 ADMINISTRADORES extra
        User::factory(5)->create([
            'role' => 'admin',
        ])->each(function ($user) use ($roleAdmin) {
            $user->assignRole($roleAdmin);
        });

        // 6. Crear 95 PROVEEDORES
        User::factory(95)->create([
            'role' => 'proveedor', // Aseguramos que la columna física diga proveedor
        ])->each(function ($user) use ($roleProveedor) {
            $user->assignRole($roleProveedor);
        });

        // 7. Crear 100 archivos y sus logs
        Archivo::factory(100)->create()->each(function ($archivo) {
            Log::create([
                'user_id'    => $archivo->user_id,
                'accion'     => 'SUBIDA DE ARCHIVO',
                'modulo'     => $archivo->modulo ?? 'ORDENES DE COMPRA',
                'created_at' => $archivo->created_at,
            ]);
        });
    }
}