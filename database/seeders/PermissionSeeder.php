<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
// database/seeders/PermissionSeeder.php
public function run()
{
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // Crear todos los permisos usados en web.php
    Permission::firstOrCreate(['name' => 'list users']);
    Permission::firstOrCreate(['name' => 'create users']);
    Permission::firstOrCreate(['name' => 'edit users']);   // <--- Importante
    Permission::firstOrCreate(['name' => 'delete users']); // <--- Importante

    $admin = Role::firstOrCreate(['name' => 'admin']);
    // Asignar todos los permisos al admin
    $admin->syncPermissions(Permission::all());
}
}