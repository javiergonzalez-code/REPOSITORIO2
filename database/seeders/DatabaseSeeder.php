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
        $roleAdmin->syncPermissions(Permission::all());
        $roleProveedor->syncPermissions(['list archivos', 'upload archivos']);

        // 4. Crear a tu usuario personal (Super Admin) - EL ÚNICO USUARIO INICIAL
        $myUser = User::updateOrCreate(
            ['email' => 'admin@ragon.com'],
            [
                'name'     => 'Administrador Principal',
                'password' => bcrypt('holamundo1234'), // Asegúrate de encriptarlo con bcrypt() si tu modelo no lo hace solo
                'role'     => 'superadmin', 
            ]
        );
        $myUser->assignRole($roleSuperAdmin); 


    }
}