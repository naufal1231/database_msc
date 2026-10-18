<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Competition;
use App\Models\CompetitionEntry;
use App\Models\CompetitionEvent;
use App\Models\Result;
use App\Models\SwimClass;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompetitionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_competition_with_events(): void
    {
        $this->actingAsAdmin();

        $competitionData = [
            'name' => 'Kejuaraan Renang Antar Pelajar Se-Jawa',
            'organizer' => 'PRSI',
            'location' => 'Stadion Renang Gelora Bung Karno',
            'start_date' => now()->addDays(10)->format('Y-m-d'),
            'end_date' => now()->addDays(12)->format('Y-m-d'),
            'auto_create_events' => '1',
        ];

        $response = $this->post(route('competitions.store'), $competitionData);

        $competition = Competition::where('name', 'Kejuaraan Renang Antar Pelajar Se-Jawa')->first();
        $response->assertRedirect(route('competitions.show', $competition->id));

        $this->assertDatabaseHas('competitions', [
            'name' => 'Kejuaraan Renang Antar Pelajar Se-Jawa',
            'location' => 'Stadion Renang Gelora Bung Karno',
        ]);

        $competition = Competition::where('name', 'Kejuaraan Renang Antar Pelajar Se-Jawa')->first();
        $this->assertDatabaseHas('competition_events', [
            'competition_id' => $competition->id,
            'stroke' => 'bebas',
            'distance' => 50,
        ]);
    }

    public function test_admin_can_register_athlete_entries_with_seed_time(): void
    {
        $this->actingAsAdmin();

        $competition = Competition::factory()->create();
        $event = CompetitionEvent::factory()->create([
            'competition_id' => $competition->id,
            'stroke' => 'bebas',
            'distance' => 50,
            'category' => 'perorangan',
        ]);

        $athlete = Athlete::factory()->create();

        $response = $this->post(route('competitions.entries.store', $competition), [
            'displayed_athlete_ids' => [$athlete->id],
            'entries' => [
                $athlete->id => [$event->id]
            ],
            'seed_times' => [
                $athlete->id => [
                    $event->id => '00:31.50'
                ]
            ]
        ]);

        $response->assertRedirect(route('competitions.show', $competition->id));

        $this->assertDatabaseHas('competition_entries', [
            'athlete_id' => $athlete->id,
            'competition_event_id' => $event->id,
            'seed_time' => '00:31.50',
        ]);
    }

    public function test_admin_can_input_results_and_notify_parent(): void
    {
        $admin = $this->createAdmin();
        $this->actingAsAdmin($admin);

        $parent = $this->createParent();
        $athlete = Athlete::factory()->create(['parent_user_id' => $parent->id]);

        $competition = Competition::factory()->create(['name' => 'MSC Open 2026']);
        $event = CompetitionEvent::factory()->create(['competition_id' => $competition->id]);

        $entry = CompetitionEntry::factory()->create([
            'athlete_id' => $athlete->id,
            'competition_event_id' => $event->id,
        ]);

        $response = $this->post(route('competitions.results.store', $competition), [
            'results' => [
                $entry->id => [
                    'status' => 'finish',
                    'time_result' => '00.29.85',
                ]
            ]
        ]);

        $response->assertRedirect(route('competitions.show', $competition->id));

        $this->assertDatabaseHas('results', [
            'competition_entry_id' => $entry->id,
            'time_result' => '00.29.85',
            'status' => 'finish',
            'input_by' => $admin->id,
        ]);

        // Verifikasi notifikasi terkirim ke akun orang tua
        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $parent->id,
            'title' => '⏱️ Hasil Waktu Lomba Baru',
        ]);
    }

    public function test_result_validation_rejects_invalid_time_format(): void
    {
        $this->actingAsAdmin();

        $competition = Competition::factory()->create();
        $event = CompetitionEvent::factory()->create(['competition_id' => $competition->id]);
        $entry = CompetitionEntry::factory()->create(['competition_event_id' => $event->id]);

        $response = $this->post(route('competitions.results.store', $competition), [
            'results' => [
                $entry->id => [
                    'status' => 'finish',
                    'time_result' => 'invalid_time', // Invalid format (should be 00.00.00)
                ]
            ]
        ]);

        $response->assertSessionHasErrors(["results.{$entry->id}.time_result"]);
    }
}
