<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 🚨 Mapeo a estructura SAP Business One
            'CardCode'   => strtoupper($this->faker->unique()->bothify('USR####??')), // Ej: USR1234AB
            'CardName'   => fake()->name(),
            'E_Mail'     => fake()->unique()->safeEmail(),
            'LicTradNum' => strtoupper($this->faker->bothify('????######???')), // Simulación de RFC de 13 caracteres
            'Cellular'   => fake()->numerify('##########'), // Teléfono de 10 dígitos
            
            // 🚨 Campos de sistema requeridos
            'role'       => fake()->randomElement(['admin', 'proveedor']), // Superadmin suele sembrarse manualmente
            'password'   => static::$password ??= Hash::make('password'), 
            
            // Campos por defecto de Laravel
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}