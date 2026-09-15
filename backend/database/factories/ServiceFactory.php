<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'category' => fake()->randomElement(['Sonido', 'Iluminación', 'DJ', 'Pantalla LED', 'Efectos', 'Packs']),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(100000, 700000),
            'is_active' => true,
        ];
    }
}