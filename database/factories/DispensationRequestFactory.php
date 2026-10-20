<?php

namespace Database\Factories;

use App\Models\Athlete;
use App\Models\Competition;
use App\Models\DispensationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DispensationRequest>
 */
class DispensationRequestFactory extends Factory
{
    protected $model = DispensationRequest::class;

    public function definition(): array
    {
        return [
            'competition_id'    => Competition::factory(),
            'athlete_id'        => Athlete::factory(),
            'parent_id'         => User::factory()->parentUser(),
            'school_class'      => fake()->randomElement(['VII-A', 'VIII-B', 'IX-C', 'X IPA 1', 'XI IPS 2']),
            'school_absence_no' => (string) fake()->numberBetween(1, 35),
            'status'            => 'pending',
            'coach_note'        => null,
            'verified_by'       => null,
            'verified_at'       => null,
            'pdf_path'          => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status'      => 'approved',
            'verified_by' => User::factory()->admin(),
            'verified_at' => now(),
            'pdf_path'    => 'dispensation/pdf/test.pdf',
        ]);
    }

    public function approvedNoPdf(): static
    {
        return $this->state(fn () => [
            'status'      => 'approved',
            'verified_by' => User::factory()->admin(),
            'verified_at' => now(),
            'pdf_path'    => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'      => 'rejected',
            'coach_note'  => 'Data tidak lengkap, harap periksa kembali.',
            'verified_by' => User::factory()->admin(),
            'verified_at' => now(),
        ]);
    }
}
