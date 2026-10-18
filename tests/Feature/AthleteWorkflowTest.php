<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\ClassHistory;
use App\Models\SwimClass;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AthleteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_athlete_list_and_search(): void
    {
        $this->actingAsAdmin();
        $class = SwimClass::factory()->create(['name' => 'Prestasi']);
        $athlete1 = Athlete::factory()->create(['name' => 'Michael Phelps', 'current_class_id' => $class->id]);
        $athlete2 = Athlete::factory()->create(['name' => 'Katie Ledecky', 'current_class_id' => $class->id]);

        $response = $this->get(route('athletes.index', ['search' => 'Phelps']));

        $response->assertOk();
        $response->assertSee('Michael Phelps');
        $response->assertDontSee('Katie Ledecky');
    }

    public function test_admin_can_create_athlete_successfully(): void
    {
        $admin = $this->createAdmin();
        $this->actingAsAdmin($admin);

        $class = SwimClass::factory()->create();
        $parent = $this->createParent();

        $athleteData = [
            'name' => 'Ahmad Dahlan',
            'birth_date' => '2012-05-15',
            'gender' => 'L',
            'current_class_id' => $class->id,
            'join_date' => '2024-01-10',
            'parent_user_id' => $parent->id,
        ];

        $response = $this->post(route('athletes.store'), $athleteData);

        $response->assertRedirect(route('athletes.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('athletes', [
            'name' => 'Ahmad Dahlan',
            'gender' => 'L',
            'current_class_id' => $class->id,
            'parent_user_id' => $parent->id,
        ]);

        // Verifikasi ClassHistory dibuat otomatis
        $athlete = Athlete::where('name', 'Ahmad Dahlan')->first();
        $this->assertDatabaseHas('class_histories', [
            'athlete_id' => $athlete->id,
            'class_id' => $class->id,
            'changed_by' => $admin->id,
        ]);
    }

    public function test_athlete_validation_rules(): void
    {
        $this->actingAsAdmin();

        $response = $this->post(route('athletes.store'), [
            'name' => '',
            'birth_date' => 'invalid-date',
            'gender' => 'X',
            'current_class_id' => 99999, // non-existent class
            'join_date' => '',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'birth_date',
            'gender',
            'current_class_id',
            'join_date',
        ]);
    }

    public function test_admin_can_update_athlete_and_promote_class(): void
    {
        $admin = $this->createAdmin();
        $this->actingAsAdmin($admin);

        $classOld = SwimClass::factory()->create(['name' => 'Pemula']);
        $classNew = SwimClass::factory()->create(['name' => 'Prestasi']);
        $parent = $this->createParent();

        $athlete = Athlete::factory()->create([
            'name' => 'Siti Nurhaliza',
            'current_class_id' => $classOld->id,
            'parent_user_id' => $parent->id,
        ]);

        ClassHistory::create([
            'athlete_id' => $athlete->id,
            'class_id' => $classOld->id,
            'start_date' => '2023-01-01',
            'changed_by' => $admin->id,
        ]);

        $response = $this->put(route('athletes.update', $athlete), [
            'name' => 'Siti Nurhaliza Putri',
            'birth_date' => $athlete->birth_date->format('Y-m-d'),
            'gender' => $athlete->gender,
            'current_class_id' => $classNew->id,
            'join_date' => $athlete->join_date->format('Y-m-d'),
            'parent_user_id' => $parent->id,
            'class_change_notes' => 'Naik ke kelas prestasi karena lolos seleksi',
        ]);

        $response->assertRedirect(route('athletes.show', $athlete->id));

        $this->assertDatabaseHas('athletes', [
            'id' => $athlete->id,
            'name' => 'Siti Nurhaliza Putri',
            'current_class_id' => $classNew->id,
        ]);

        // Verifikasi notifikasi terkirim ke parent
        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $parent->id,
            'title' => '📢 Kenaikan Kelas Latihan',
        ]);
    }

    public function test_admin_can_delete_athlete(): void
    {
        $this->actingAsAdmin();
        $athlete = Athlete::factory()->create();

        $response = $this->delete(route('athletes.destroy', $athlete));

        $response->assertRedirect(route('athletes.index'));
        $this->assertDatabaseMissing('athletes', [
            'id' => $athlete->id,
        ]);
    }

    public function test_parent_cannot_modify_athletes(): void
    {
        $this->actingAsParent();
        $class = SwimClass::factory()->create();

        $response = $this->post(route('athletes.store'), [
            'name' => 'Hacker Kid',
            'birth_date' => '2015-01-01',
            'gender' => 'L',
            'current_class_id' => $class->id,
            'join_date' => '2024-01-01',
        ]);

        // Middleware role:admin,super_admin intercepts parent
        $response->assertForbidden();
    }
}
