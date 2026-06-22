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
        // Limpiar caché Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear Permisos 
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

        // Crear Roles
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'superadmin']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleProveedor = Role::firstOrCreate(['name' => 'proveedor']);

        // Asignar permisos a los roles
        $roleSuperAdmin->syncPermissions(Permission::all());
        $roleAdmin->syncPermissions(Permission::all());
        $roleProveedor->syncPermissions(['list archivos', 'upload archivos']);

        // Crear Super Admin
        $myUser = User::updateOrCreate(
            ['E_Mail' => 'admin@ragon.com'], 
            [
                'CardCode' => 'SUPERADMIN01', 
                'CardName' => 'Administrador Principal',
                'password' => bcrypt('holamundo1234'),
                'role'     => 'superadmin',
            ]
        );
        $myUser->assignRole($roleSuperAdmin);
    }
}