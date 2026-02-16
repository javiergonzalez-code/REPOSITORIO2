<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Models used in the User, Role and Permission CRUDs.
    |
    */
    'models' => [
        'user'       => config('backpack.base.user_model_fqn', \App\Models\User::class),
        'permission' => \Backpack\PermissionManager\app\Models\Permission::class,
        'role'       => \Backpack\PermissionManager\app\Models\Role::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Disallow the user to create/update/delete roles or permissions
    |--------------------------------------------------------------------------
    |
    | By default the user is allowed to create/update/delete roles and permissions.
    | You can disable this feature by setting the following values to false.
    |
    */
    'allow_permission_create' => true,
    'allow_permission_update' => true,
    'allow_permission_delete' => true,
    'allow_role_create'       => true,
    'allow_role_update'       => true,
    'allow_role_delete'       => true,

    /*
    |--------------------------------------------------------------------------
    | Multiple Guards
    |--------------------------------------------------------------------------
    |
    | When enabled, the user will be able to select the guard when creating
    | a role or permission.
    |
    */
    'multiple_guards' => false,

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Controls whether the package should register its own routes.
    | Setting this to false allows you to define your own routes in your
    | routes/backpack/custom.php file to use your custom controllers.
    |
    */
    'setup_admin_routes' => false, 
];