<?php

namespace Database\Seeders;

use App\Models\User;
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
        // 0. Limpiar caché (Spatie)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Permisos (Oficiales del sistema)
        $permissions = [
            'manage roles',
            'manage permissions',
            'create users',
            'edit users',
            'list users',
            'delete users',
            'list archivos',
            'upload archivos',
            'delete archivos',
            'list logs'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Crear Roles Oficiales
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'superadmin']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleProveedor = Role::firstOrCreate(['name' => 'proveedor']);

        // 3. Asignar permisos a los roles
        $roleSuperAdmin->syncPermissions(Permission::all());
        $roleAdmin->syncPermissions(Permission::all());
        $roleProveedor->syncPermissions(['list archivos', 'upload archivos']);

        // 4. Crear a tu usuario personal (Super Admin) - ADAPTADO A SQL SERVER
        $myUser = User::updateOrCreate(
            ['E_Mail' => 'admin@ragon.com'], // Se busca por la nueva columna de correo
            [
                'CardCode' => 'SUPERADMIN01', // CRÍTICO: Debemos enviarle la llave primaria manualmente (máx 15 chars)
                'CardName' => 'Administrador Principal', // Sustituye a 'name'
                'password' => bcrypt('holamundo1234'),
                'role'     => 'superadmin',
            ]
        );
        $myUser->assignRole($roleSuperAdmin);
    }
}