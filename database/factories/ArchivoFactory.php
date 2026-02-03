<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArchivoFactory extends Factory
{
    public function definition(): array
    {
        // Faker nos ayuda a crear nombres de archivos realistas
        $extensiones = ['csv', 'xml'];
        $ext = $this->faker->randomElement($extensiones);
        $nombreBase = $this->faker->word;

        return [
            // Selecciona un ID de un usuario que ya exista en la base de datos
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'nombre_original' => $nombreBase . '.' . $ext,
            'nombre_sistema' => $this->faker->uuid . '.' . $ext,
            'tipo_archivo' => ($ext === 'csv') ? 'text/csv' : 'application/xml',
            'ruta' => 'uploads/' . $this->faker->uuid . '.' . $ext,
        ];
    }
}