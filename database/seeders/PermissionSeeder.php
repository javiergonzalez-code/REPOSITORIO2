<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar caché de Spatie (Obligatorio antes de sembrar)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Crear la lista completa de permisos de tu sistema
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

        // 3. Crear los roles oficiales del sistema
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'superadmin']);
        $roleAdmin      = Role::firstOrCreate(['name' => 'admin']);
        $roleProveedor  = Role::firstOrCreate(['name' => 'proveedor']);

        // 4. Asignar los permisos correspondientes a cada rol
        $roleSuperAdmin->syncPermissions(Permission::all()); // Superadmin tiene todo
        $roleAdmin->syncPermissions(Permission::all());      // Admin tiene todo
        $roleProveedor->syncPermissions(['list archivos', 'upload archivos']); // Proveedor está limitado
    }
}