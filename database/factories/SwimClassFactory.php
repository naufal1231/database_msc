<?php

namespace Database\Factories;

use App\Models\SwimClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SwimClass>
 */
class SwimClassFactory extends Factory
{
    protected $model = SwimClass::class;

    public function definition(): array
    {
        return [
            'name' => 'Kelas ' . fake()->unique()->randomElement(['Pemula', 'Prestasi', 'Taruna', 'Remaja', 'Senior']),
            'description' => fake()->sentence(),
        ];
    }
}
