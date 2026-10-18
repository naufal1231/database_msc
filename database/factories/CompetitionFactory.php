<?php

namespace Database\Factories;

use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    protected $model = Competition::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+2 months');
        $endDate = (clone $startDate)->modify('+2 days');

        return [
            'name' => 'Kejuaraan Renang ' . fake()->city(),
            'organizer' => 'PRSI ' . fake()->city(),
            'location' => 'Kolam Renang ' . fake()->company(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'status' => 'akan_datang',
        ];
    }
}
