<?php

namespace Tests\Feature;

use App\Models\Athlete;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_any_authenticated_routes(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('super_admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('parent.dashboard'))->assertRedirect(route('login'));
    }

    public function test_parent_cannot_access_admin_routes(): void
    {
        $parent = $this->createParent();
        $this->actingAsParent($parent);

        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('athletes.index'))->assertForbidden();
        $this->get(route('competitions.index'))->assertForbidden();
        $this->get(route('attendance.index'))->assertForbidden();
    }

    public function test_parent_cannot_access_super_admin_routes(): void
    {
        $parent = $this->createParent();
        $this->actingAsParent($parent);

        $this->get(route('super_admin.dashboard'))->assertForbidden();
        $this->get(route('settings.index'))->assertForbidden();
    }

    public function test_admin_cannot_access_super_admin_dashboard(): void
    {
        $admin = $this->createAdmin();
        $this->actingAsAdmin($admin);

        $this->get(route('super_admin.dashboard'))->assertForbidden();
    }

    public function test_admin_cannot_update_settings(): void
    {
        $admin = $this->createAdmin();
        $this->actingAsAdmin($admin);

        // Admin can view settings index
        $this->get(route('settings.index'))->assertOk();

        // But Admin cannot POST to update settings (only super_admin)
        $this->post(route('settings.update'), [
            'app_name' => 'Hacked Name',
        ])->assertForbidden();
    }

    public function test_super_admin_can_update_settings(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $this->actingAsSuperAdmin($superAdmin);

        $this->get(route('super_admin.dashboard'))->assertOk();

        $response = $this->post(route('settings.update'), [
            'club_name' => 'MSC Swimming Club Official',
            'organizer' => 'Pengurus MSC',
            'address' => 'Jl. Renang No. 1',
            'phone' => '081234567890',
            'wa_status' => 'Terhubung',
            'poin_lebih_cepat' => 3,
            'poin_sama' => 1,
            'poin_lebih_lambat' => 0,
            'poin_debut' => 2,
            'performance_good_min' => 75,
            'performance_fair_min' => 50,
        ]);

        $response->assertSessionHas('success');
    }

    public function test_parent_cannot_view_other_parents_child_event_history(): void
    {
        $parentA = $this->createParent();
        $parentB = $this->createParent();

        // Child belonging to Parent A
        $childA = Athlete::factory()->create(['parent_user_id' => $parentA->id]);

        // Child belonging to Parent B
        $childB = Athlete::factory()->create(['parent_user_id' => $parentB->id]);

        // Parent A can view their own child's event history
        $this->actingAsParent($parentA)
            ->get(route('parent.event-history.show', $childA))
            ->assertOk();

        // Parent A CANNOT view Parent B's child's event history (403 Forbidden)
        $this->actingAsParent($parentA)
            ->get(route('parent.event-history.show', $childB))
            ->assertForbidden();
    }
}
