<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArchivoFactory extends Factory
{
    public function definition(): array
    {
        $extensiones = ['csv', 'xml'];
        $ext = $this->faker->randomElement($extensiones);
        $nombreBase = $this->faker->word;

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'nombre_original' => $nombreBase . '.' . $ext,
            'nombre_sistema' => $this->faker->uuid . '.' . $ext,
            'tipo_archivo' => ($ext === 'csv') ? 'text/csv' : 'application/xml',
            'ruta' => 'uploads/archivos/' . $this->faker->uuid . '.' . $ext, // Ruta coherente con el mutator
            'modulo' => $this->faker->randomElement(['ORDENES DE COMPRA', 'FACTURACION', 'RRHH']), // CAMPO REQUERIDO
        ];
    }
}