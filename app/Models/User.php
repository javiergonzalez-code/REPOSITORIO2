<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// 1. Importar el Trait de Backpack
use Backpack\CRUD\app\Models\Traits\CrudTrait;
// Si vas a usar roles de Spatie (vi el paquete en tu composer.json), descomenta la siguiente línea:
// use Spatie\Permission\Traits\HasRoles; 

class User extends Authenticatable
{
    // 2. Añadir CrudTrait al uso de la clase
    use HasFactory, Notifiable, CrudTrait; 
    // use HasRoles; // Si usas Spatie

    protected $fillable = [
        'name',
        'email',
        'password',
        'codigo',
        'rfc',
        'telefono',
        'role', // Veo que tienes un campo 'role' directo en la tabla
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}