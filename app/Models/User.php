<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class User extends Authenticatable
{
    use HasFactory, Notifiable, CrudTrait, HasRoles, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'codigo',
        'rfc',
        'telefono',
        'role',
        'roles',
        'permissions',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }
/**
     * 3. Configuración de las opciones del Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Definimos qué campos queremos auditar
            ->logOnly(['name', 'email', 'codigo', 'rfc', 'telefono'])
            // Evitamos que se registre el log si no hubo cambios reales
            ->logOnlyDirty()
            // Guardamos el estado anterior para comparar (útil en ediciones)
            ->dontSubmitEmptyLogs()
            // Nombre de la bitácora (opcional)
            ->useLogName('user');
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

