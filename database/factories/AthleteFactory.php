<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\SwimClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Athlete>
 */
class AthleteFactory extends Factory
{
    protected $model = Athlete::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'birth_date' => fake()->dateTimeBetween('-17 years', '-8 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['L', 'P']),
            'photo' => null,
            'current_class_id' => SwimClass::factory(),
            'join_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'parent_user_id' => null,
        ];
    }

    public function withParent(?User $parent = null): static
    {
        return $this->state(fn () => [
            'parent_user_id' => $parent?->id ?? User::factory()->create(['role' => 'user', 'status' => 'active'])->id,
        ]);
    }
}
