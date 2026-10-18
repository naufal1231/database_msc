<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Attendance;
use App\Models\SwimClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_attendance(): void
    {
        $admin = $this->createAdmin();
        $this->actingAsAdmin($admin);

        $class = SwimClass::factory()->create();
        $athlete = Athlete::factory()->create(['current_class_id' => $class->id]);

        $response = $this->post(route('attendance.store'), [
            'athlete_id' => $athlete->id,
            'swim_class_id' => $class->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'hadir',
            'note' => 'Latihan teknik gaya dada',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'athlete_id' => $athlete->id,
            'swim_class_id' => $class->id,
            'status' => 'hadir',
            'created_by' => $admin->id,
        ]);

        $this->assertTrue(Attendance::where('athlete_id', $athlete->id)->whereDate('attendance_date', now()->toDateString())->exists());
    }

    public function test_cannot_record_duplicate_attendance_for_same_day(): void
    {
        $this->actingAsAdmin();

        $class = SwimClass::factory()->create();
        $athlete = Athlete::factory()->create(['current_class_id' => $class->id]);
        $date = now()->toDateString();

        // Absensi pertama
        $this->post(route('attendance.store'), [
            'athlete_id' => $athlete->id,
            'swim_class_id' => $class->id,
            'attendance_date' => $date,
            'status' => 'hadir',
        ]);

        // Coba absensi kedua di tanggal yang sama
        $response = $this->post(route('attendance.store'), [
            'athlete_id' => $athlete->id,
            'swim_class_id' => $class->id,
            'attendance_date' => $date,
            'status' => 'izin',
        ]);

        $response->assertSessionHasErrors(['attendance']);
    }

    public function test_admin_can_view_attendance_recap(): void
    {
        $this->actingAsAdmin();

        $class = SwimClass::factory()->create();
        $athlete = Athlete::factory()->create(['current_class_id' => $class->id]);

        Attendance::create([
            'athlete_id' => $athlete->id,
            'swim_class_id' => $class->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'hadir',
            'created_by' => auth()->id(),
        ]);

        $response = $this->get(route('attendance.recap', [
            'month' => now()->month,
            'year' => now()->year,
            'swim_class_id' => $class->id,
        ]));

        $response->assertOk();
        $response->assertSee($athlete->name);
    }

    public function test_parent_cannot_record_attendance(): void
    {
        $this->actingAsParent();

        $class = SwimClass::factory()->create();
        $athlete = Athlete::factory()->create(['current_class_id' => $class->id]);

        $response = $this->post(route('attendance.store'), [
            'athlete_id' => $athlete->id,
            'swim_class_id' => $class->id,
            'attendance_date' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        // Intercepted by role:admin,super_admin
        $response->assertForbidden();
    }
}
