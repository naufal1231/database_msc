<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\Competition;
use App\Models\SwimClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test halaman publik untuk tamu (guest)
     */
    public function test_guest_can_access_public_pages(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    /**
     * Test proteksi halaman admin dan parent jika belum login
     */
    public function test_guest_redirected_to_login_on_protected_pages(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/admin/athletes')->assertRedirect('/login');
        $this->get('/admin/competitions')->assertRedirect('/login');
        $this->get('/admin/absensi')->assertRedirect('/login');
        $this->get('/super-admin/dashboard')->assertRedirect('/login');
        $this->get('/super-admin/settings')->assertRedirect('/login');
        $this->get('/parent/dashboard')->assertRedirect('/login');
    }

    /**
     * Test rute redirect /dashboard sesuai dengan role masing-masing
     */
    public function test_dashboard_redirects_correctly_based_on_role(): void
    {
        // 1. Super Admin
        $this->actingAsSuperAdmin();
        $this->get('/dashboard')->assertRedirect(route('super_admin.dashboard'));

        // 2. Admin (Pelatih)
        $this->actingAsAdmin();
        $this->get('/dashboard')->assertRedirect(route('admin.dashboard'));

        // 3. Orang Tua (Active)
        $this->actingAsParent();
        $this->get('/dashboard')->assertRedirect(route('parent.dashboard'));

        // 4. Orang Tua (Pending Approval)
        $pendingUser = User::factory()->create(['role' => 'user', 'status' => 'pending']);
        $this->actingAs($pendingUser);
        $this->get('/dashboard')->assertRedirect(route('waiting.approval'));
    }

    /**
     * Test admin dapat membuka semua halaman utama tanpa error 500
     */
    public function test_admin_can_access_all_admin_pages(): void
    {
        $this->actingAsAdmin();

        $swimClass = SwimClass::factory()->create();
        $athlete = Athlete::factory()->create(['current_class_id' => $swimClass->id]);
        $competition = Competition::factory()->create();

        $routes = [
            route('admin.dashboard'),
            route('athletes.index'),
            route('athletes.create'),
            route('athletes.show', $athlete),
            route('athletes.edit', $athlete),
            route('competitions.index'),
            route('competitions.create'),
            route('competitions.show', $competition),
            route('competitions.edit', $competition),
            route('competitions.entries.create', $competition),
            route('competitions.results.edit', $competition),
            route('attendance.index'),
            route('attendance.recap'),
            route('event-history.index'),
            route('reports.index'),
            route('settings.index'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $response->assertOk();
        }
    }

    /**
     * Test parent dapat membuka halaman portal orang tua tanpa error 500
     */
    public function test_parent_can_access_parent_pages(): void
    {
        $parent = $this->createParent();
        $this->actingAsParent($parent);

        $athlete = Athlete::factory()->create(['parent_user_id' => $parent->id]);

        $this->get(route('parent.dashboard'))->assertOk();
        $this->get(route('parent.event-history.show', $athlete))->assertOk();

        // Note: route('parent.attendance') requires resources/views/parent/attendance/history.blade.php
        // which has not been created yet in the codebase.
    }

    /**
     * Test super admin dapat membuka dashboard dan settings
     */
    public function test_super_admin_can_access_super_admin_pages(): void
    {
        $this->actingAsSuperAdmin();

        $this->get(route('super_admin.dashboard'))->assertOk();
        $this->get(route('settings.index'))->assertOk();
    }
}
