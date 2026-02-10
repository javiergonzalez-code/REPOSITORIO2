<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
//use Spatie\Permission\Traits\HasRoles; 

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
        'role',
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