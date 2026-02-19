<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Limpiar caché de permisos (importante para que se apliquen de inmediato)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        Permission::firstOrCreate(['name' => 'list users']);
        Permission::firstOrCreate(['name' => 'create users']);

        // Crear rol y asignar
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(['list users', 'create users']);
    }
}