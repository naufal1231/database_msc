<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\CompetitionEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionEvent>
 */
class CompetitionEventFactory extends Factory
{
    protected $model = CompetitionEvent::class;

    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'stroke' => fake()->randomElement(['bebas', 'dada', 'kupu', 'punggung', 'ganti', 'estafet']),
            'distance' => fake()->randomElement([50, 100, 200]),
            'category' => fake()->randomElement(['perorangan', 'estafet_mix']),
        ];
    }
}
