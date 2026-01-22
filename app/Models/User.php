<?php

namespace App\Models;

// Importaciones necesarias para la autenticación, notificaciones y fábricas de datos
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // HasFactory: Permite crear usuarios de prueba fácilmente
    // Notifiable: Permite enviar correos o alertas al usuario (ej: restablecer contraseña)
    use HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar masivamente.
     * Permite usar User::create(['name' => '...', 'email' => '...', ...])
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Atributos ocultos.
     * Estos campos NO se incluirán cuando conviertas el usuario a JSON o un Array.
     * Es vital para la seguridad NO mostrar la contraseña ni el token de sesión.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casteo de atributos.
     * Define cómo se deben transformar los datos al salir o entrar a la base de datos.
     */
    protected function casts(): array
    {
        return [
            // Convierte la fecha de verificación de string a un objeto Carbon (DateTime)
            'email_verified_at' => 'datetime',
            // Indica a Laravel que la contraseña debe manejarse siempre como un Hash seguro (Bcrypt)
            'password' => 'hashed',
        ];
    }
}