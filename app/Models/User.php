<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    // 2. Añadir CrudTrait al uso de la clase
    use HasFactory, Notifiable, CrudTrait, HasRoles;
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
            //'password' => 'hashed',
            //se corre el riesgo de hashear el hash (doble encriptación), lo que hará que el usuario nunca pueda iniciar sesión
        ];
    }

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value); // O Hash::make($value)
        }
        // Si $value es null o vacío, no hace nada, manteniendo la contraseña vieja
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
}

