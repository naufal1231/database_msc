<?php

namespace Database\Factories;

use App\Models\CompetitionEntry;
use App\Models\Result;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Result>
 */
class ResultFactory extends Factory
{
    protected $model = Result::class;

    public function definition(): array
    {
        return [
            'competition_entry_id' => CompetitionEntry::factory(),
            'time_result' => '00:31.20',
            'rank' => fake()->numberBetween(1, 10),
            'status' => 'official',
            'input_by' => User::factory(),
        ];
    }
}
