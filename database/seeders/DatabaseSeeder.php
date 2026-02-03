<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Archivo; // Importante: Importar el modelo Archivo
use App\Models\Log;     // Importante: Importar el modelo Log
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Creamos 100 usuarios aleatorios con Faker
        User::factory(100)->create();

        // 2. Creamos tu usuario específico para que siempre puedas loguearte
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('holamundo1234')
        ]);

        // 3. Creamos 100 archivos de prueba
        // Usamos factory(100)->create() para insertar los registros
        // El método ->each() recorre cada archivo justo después de crearlo
        Archivo::factory(100)->create()->each(function ($archivo) {
            
            // 4. Por cada archivo, creamos un registro manual en la tabla de Logs
            Log::create([
                'user_id'    => $archivo->user_id,         // Usamos el ID del usuario que "subió" el archivo
                'accion'     => 'SUBIDA DE ARCHIVO',       // Definimos la acción para que brille en azul en tu vista
                'modulo'     => 'ORDENES DE COMPRA',
                'created_at' => $archivo->created_at,      // Sincronizamos la fecha del log con la del archivo
            ]);
        });
    }
}