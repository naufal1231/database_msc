<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_parent_can_register_and_is_set_to_pending(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('waiting.approval'));

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'user',
            'status' => 'pending',
            'phone' => '081234567890',
        ]);

        $this->assertAuthenticated();
    }

    public function test_pending_parent_cannot_access_parent_dashboard(): void
    {
        $pendingParent = User::factory()->create([
            'role' => 'user',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($pendingParent)->get('/parent/dashboard');

        // Middleware active redirects pending users to waiting approval
        $response->assertRedirect(route('waiting.approval'));
    }

    public function test_admin_can_approve_pending_parent(): void
    {
        $admin = $this->createAdmin();
        $pendingParent = User::factory()->create([
            'role' => 'user',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('parents.approve', $pendingParent));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $pendingParent->id,
            'status' => 'active',
        ]);

        // After approval, the parent can access parent dashboard
        $dashboardResponse = $this->actingAs($pendingParent->fresh())
            ->get(route('parent.dashboard'));
        $dashboardResponse->assertOk();
    }

    public function test_admin_can_reject_pending_parent(): void
    {
        $admin = $this->createAdmin();
        $pendingParent = User::factory()->create([
            'role' => 'user',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('parents.reject', $pendingParent));

        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $pendingParent->id,
        ]);
    }

    public function test_admin_cannot_approve_already_active_user(): void
    {
        $admin = $this->createAdmin();
        $activeParent = User::factory()->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('parents.approve', $activeParent));

        $response->assertSessionHas('error');
    }
}
