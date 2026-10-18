<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\CompetitionEntry;
use App\Models\CompetitionEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompetitionEntry>
 */
class CompetitionEntryFactory extends Factory
{
    protected $model = CompetitionEntry::class;

    public function definition(): array
    {
        return [
            'athlete_id' => Athlete::factory(),
            'competition_event_id' => CompetitionEvent::factory(),
            'status' => 'terdaftar',
            'seed_time' => '00:32.45',
        ];
    }
}
