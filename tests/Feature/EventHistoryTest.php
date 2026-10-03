<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Athlete;
use App\Models\Competition;
use App\Models\CompetitionEvent;
use App\Models\CompetitionEntry;
use App\Models\Result;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin user
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    public function test_admin_can_view_event_history_index(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('event-history.index'));

        $response->assertOk();
    }

    public function test_admin_can_view_athlete_event_history(): void
    {
        $athlete = Athlete::factory()->create();
        
        $response = $this->actingAs($this->admin)
            ->get(route('event-history.show', $athlete));

        $response->assertOk();
    }
}
