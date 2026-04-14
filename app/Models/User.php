<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $guard_name = 'web';

    // 1. CONFIGURACIÓN CRÍTICA: Definir la nueva Llave Primaria
    protected $primaryKey = 'CardCode';
    public $incrementing = false; // Ya no es un número autoincrementable
    protected $keyType = 'string'; // Ahora es un texto (nvarchar)

    protected static $recordEvents = ['created', 'updated'];

    // 2. Mapeo de las nuevas columnas de SQL Server
    protected $fillable = [
        'CardCode',
        'CardName',
        'E_Mail',
        'password',
        'LicTradNum',
        'Cellular',
        'role'
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

    // 3. Indicarle al sistema de verificación nativo de Laravel cuál es el campo de correo
    public function getEmailForVerification()
    {
        return $this->E_Mail;
    }

    // 4. Actualizar el registro de Logs para que rastree las columnas correctas
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['CardCode', 'CardName', 'E_Mail', 'LicTradNum', 'Cellular', 'role'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Creación',
                'updated' => 'Actualización',
                default => $eventName,
            });
    }

    // 5. Asegurar que la relación con archivos busque 'CardCode' en lugar de 'id'
    public function archivos()
    {
        return $this->hasMany(Archivo::class, 'user_id', 'CardCode');
    }
}   