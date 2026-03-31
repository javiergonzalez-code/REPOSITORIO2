<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity, SoftDeletes;
    
    protected $guard_name = 'web';

    protected static $recordEvents = ['created', 'updated'];

    protected $fillable = [
        'name', 'email', 'password', 'codigo', 'rfc', 'telefono', 'role', 'roles', 'permissions',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * LÓGICA MANUAL PARA SOFT DELETES
     */
/**
     * LÓGICA MANUAL PARA SOFT DELETES
     */
    protected static function booted()
    {

        // Cuando se CREA un usuario (CARGA)
        static::created(function ($user) {
            if (auth()->check()) {
                \App\Models\Log::create([
                    'user_id' => auth()->id(), // El admin que lo creó
                    'accion'  => 'CARGA',
                    'modulo'  => 'USUARIOS'
                ]);
            }
        });
    }
    /**
     * Configuración del Log automático (Solo para Cargas y Actualizaciones)
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'codigo', 'rfc', 'telefono', 'role'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Creación',
                'updated' => 'Actualización',
                default => $eventName,
            });
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class);
    }
}