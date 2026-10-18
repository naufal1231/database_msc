<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Attendance;
use App\Models\SwimClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'athlete_id' => Athlete::factory(),
            'swim_class_id' => SwimClass::factory(),
            'attendance_date' => now()->format('Y-m-d'),
            'status' => fake()->randomElement(['hadir', 'izin', 'sakit', 'alpha']),
            'note' => fake()->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }
}
