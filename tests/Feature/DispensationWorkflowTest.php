<?php

namespace Tests\Feature;

use App\Jobs\GenerateDispensationPdf;
use App\Models\Athlete;
use App\Models\Competition;
use App\Models\DispensationRequest;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\Events\JobDispatched;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DispensationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    // ── Alur Pengajuan Orang Tua ───────────────────────────────

    public function test_parent_can_see_dispensation_index(): void
    {
        $parent = $this->createParent();
        $this->actingAs($parent);

        $response = $this->get(route('parent.dispensation.index'));
        $response->assertOk();
        $response->assertViewIs('parent.dispensation.index');
    }

    public function test_parent_can_access_create_form(): void
    {
        $parent = $this->createParent();
        $this->actingAs($parent);

        $response = $this->get(route('parent.dispensation.create'));
        $response->assertOk();
    }

    public function test_parent_can_submit_dispensation_request(): void
    {
        Queue::fake();

        $parent      = $this->createParent();
        $athlete     = Athlete::factory()->withParent($parent)->create();
        $competition = Competition::factory()->create();

        $this->actingAs($parent);

        $response = $this->post(route('parent.dispensation.store'), [
            'competition_id'    => $competition->id,
            'athlete_id'        => $athlete->id,
            'school_class'      => 'VIII-A',
            'school_absence_no' => '12',
        ]);

        $response->assertRedirect(route('parent.dispensation.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('dispensation_requests', [
            'competition_id'    => $competition->id,
            'athlete_id'        => $athlete->id,
            'parent_id'         => $parent->id,
            'school_class'      => 'VIII-A',
            'school_absence_no' => '12',
            'status'            => 'pending',
        ]);
    }

    public function test_parent_cannot_submit_for_other_parents_athlete(): void
    {
        $parent      = $this->createParent();
        $otherParent = $this->createParent();
        $athlete     = Athlete::factory()->withParent($otherParent)->create();
        $competition = Competition::factory()->create();

        $this->actingAs($parent);

        $response = $this->post(route('parent.dispensation.store'), [
            'competition_id'    => $competition->id,
            'athlete_id'        => $athlete->id,
            'school_class'      => 'VIII-A',
            'school_absence_no' => '12',
        ]);

        $response->assertSessionHasErrors('athlete_id');
        $this->assertDatabaseMissing('dispensation_requests', [
            'athlete_id' => $athlete->id,
            'parent_id'  => $parent->id,
        ]);
    }

    public function test_parent_cannot_submit_duplicate_request(): void
    {
        $parent      = $this->createParent();
        $athlete     = Athlete::factory()->withParent($parent)->create();
        $competition = Competition::factory()->create();

        // Buat pengajuan pertama (pending)
        DispensationRequest::factory()->create([
            'competition_id' => $competition->id,
            'athlete_id'     => $athlete->id,
            'parent_id'      => $parent->id,
            'status'         => 'pending',
        ]);

        $this->actingAs($parent);

        $response = $this->post(route('parent.dispensation.store'), [
            'competition_id'    => $competition->id,
            'athlete_id'        => $athlete->id,
            'school_class'      => 'VIII-A',
            'school_absence_no' => '12',
        ]);

        $response->assertSessionHas('error');
        // Pastikan tidak ada record duplikat
        $this->assertEquals(1, DispensationRequest::where([
            'competition_id' => $competition->id,
            'athlete_id'     => $athlete->id,
        ])->count());
    }

    public function test_parent_can_resubmit_rejected_request(): void
    {
        Queue::fake();

        $parent      = $this->createParent();
        $athlete     = Athlete::factory()->withParent($parent)->create();
        $competition = Competition::factory()->create();

        $dispensation = DispensationRequest::factory()->create([
            'competition_id' => $competition->id,
            'athlete_id'     => $athlete->id,
            'parent_id'      => $parent->id,
            'status'         => 'rejected',
            'coach_note'     => 'Data salah.',
        ]);

        $this->actingAs($parent);

        $response = $this->post(route('parent.dispensation.store'), [
            'competition_id'    => $competition->id,
            'athlete_id'        => $athlete->id,
            'school_class'      => 'VIII-B',
            'school_absence_no' => '15',
        ]);

        $response->assertRedirect(route('parent.dispensation.index'));
        $response->assertSessionHas('success');

        $dispensation->refresh();
        $this->assertEquals('pending',  $dispensation->status);
        $this->assertEquals('VIII-B',   $dispensation->school_class);
        $this->assertNull($dispensation->coach_note);
    }

    // ── Persetujuan Coach ──────────────────────────────────────

    public function test_admin_can_approve_dispensation_and_dispatches_job(): void
    {
        Queue::fake();

        $admin       = $this->createAdmin();
        $parent      = $this->createParent();
        $athlete     = Athlete::factory()->withParent($parent)->create();
        $competition = Competition::factory()->create();

        $dispensation = DispensationRequest::factory()->create([
            'competition_id' => $competition->id,
            'athlete_id'     => $athlete->id,
            'parent_id'      => $parent->id,
            'status'         => 'pending',
        ]);

        // Buat template palsu di storage
        Storage::fake('local');
        Storage::disk('local')->put('dispensation/template.docx', 'fake-content');
        $settingsData = json_encode(['dispensation_template_path' => 'dispensation/template.docx']);
        file_put_contents(storage_path('app/settings.json'), $settingsData);

        $this->actingAs($admin);

        $response = $this->post(route('admin.dispensation.approve', $dispensation));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $dispensation->refresh();
        $this->assertEquals('approved', $dispensation->status);
        $this->assertEquals($admin->id, $dispensation->verified_by);

        // Pastikan job terkirim
        Queue::assertPushed(GenerateDispensationPdf::class, function ($job) use ($dispensation) {
            return $job->dispensation->id === $dispensation->id;
        });

        // Pastikan notifikasi terkirim ke orang tua
        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $parent->id,
            'title'   => '✅ Surat Dispensasi Disetujui',
        ]);
    }

    public function test_admin_can_reject_dispensation_with_note(): void
    {
        $admin       = $this->createAdmin();
        $parent      = $this->createParent();
        $athlete     = Athlete::factory()->withParent($parent)->create();
        $competition = Competition::factory()->create();

        $dispensation = DispensationRequest::factory()->create([
            'competition_id' => $competition->id,
            'athlete_id'     => $athlete->id,
            'parent_id'      => $parent->id,
            'status'         => 'pending',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.dispensation.reject', $dispensation), [
            'coach_note' => 'Nama atlet tidak sesuai data sistem.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $dispensation->refresh();
        $this->assertEquals('rejected', $dispensation->status);
        $this->assertEquals('Nama atlet tidak sesuai data sistem.', $dispensation->coach_note);

        // Notifikasi ke orang tua
        $this->assertDatabaseHas('system_notifications', [
            'user_id' => $parent->id,
            'title'   => '❌ Surat Dispensasi Ditolak',
        ]);
    }

    public function test_admin_reject_requires_coach_note(): void
    {
        $admin        = $this->createAdmin();
        $dispensation = DispensationRequest::factory()->create(['status' => 'pending']);

        $this->actingAs($admin);

        $response = $this->post(route('admin.dispensation.reject', $dispensation), [
            'coach_note' => '',
        ]);

        $response->assertSessionHasErrors('coach_note');
    }

    // ── Pembatasan Unduh ───────────────────────────────────────

    public function test_parent_cannot_download_before_approved(): void
    {
        $parent       = $this->createParent();
        $athlete      = Athlete::factory()->withParent($parent)->create();
        $competition  = Competition::factory()->create();

        $dispensation = DispensationRequest::factory()->create([
            'athlete_id'     => $athlete->id,
            'competition_id' => $competition->id,
            'parent_id'      => $parent->id,
            'status'         => 'pending',
            'pdf_path'       => null,
        ]);

        $this->actingAs($parent);

        $response = $this->get(route('parent.dispensation.download', $dispensation));
        $response->assertStatus(404);
    }

    public function test_parent_cannot_download_others_dispensation(): void
    {
        Storage::fake('local');

        $parent1 = $this->createParent();
        $parent2 = $this->createParent();
        $athlete  = Athlete::factory()->withParent($parent2)->create();
        $comp     = Competition::factory()->create();

        Storage::disk('local')->put('dispensation/pdf/test.pdf', 'fake-pdf');

        $dispensation = DispensationRequest::factory()->create([
            'athlete_id'     => $athlete->id,
            'competition_id' => $comp->id,
            'parent_id'      => $parent2->id,
            'status'         => 'approved',
            'pdf_path'       => 'dispensation/pdf/test.pdf',
        ]);

        $this->actingAs($parent1);

        $response = $this->get(route('parent.dispensation.download', $dispensation));
        $response->assertStatus(403);
    }

    public function test_parent_can_download_approved_dispensation_with_pdf(): void
    {
        Storage::fake('local');

        $parent      = $this->createParent();
        $athlete     = Athlete::factory()->withParent($parent)->create();
        $competition = Competition::factory()->create();

        Storage::disk('local')->put('dispensation/pdf/test.pdf', 'fake-pdf-content');

        $dispensation = DispensationRequest::factory()->create([
            'athlete_id'     => $athlete->id,
            'competition_id' => $competition->id,
            'parent_id'      => $parent->id,
            'status'         => 'approved',
            'pdf_path'       => 'dispensation/pdf/test.pdf',
        ]);

        $this->actingAs($parent);

        $response = $this->get(route('parent.dispensation.download', $dispensation));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    // ── Validasi Template ──────────────────────────────────────

    public function test_admin_cannot_upload_template_without_placeholders(): void
    {
        // Buat file docx palsu (bukan docx asli, tapi untuk test format checking kita gunakan file text)
        // Dalam test environment, TemplateProcessor mungkin error - kita test validasi mime type dulu
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $file = UploadedFile::fake()->create('template.txt', 100, 'text/plain');

        $response = $this->post(route('admin.dispensation.template.upload'), [
            'template' => $file,
        ]);

        $response->assertSessionHasErrors('template');
    }

    public function test_non_admin_cannot_upload_template(): void
    {
        $parent = $this->createParent();
        $this->actingAs($parent);

        $response = $this->get(route('admin.dispensation.template'));
        $response->assertStatus(403);
    }

    public function test_non_admin_cannot_access_admin_dispensation_index(): void
    {
        $parent = $this->createParent();
        $this->actingAs($parent);

        $response = $this->get(route('admin.dispensation.index'));
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_parent_dispensation(): void
    {
        $admin = $this->createAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('parent.dispensation.index'));
        $response->assertStatus(403);
    }

    public function test_notifications_sent_to_all_coaches_on_new_request(): void
    {
        Queue::fake();

        $admin1  = $this->createAdmin();
        $admin2  = $this->createSuperAdmin();
        $parent  = $this->createParent();
        $athlete = Athlete::factory()->withParent($parent)->create();
        $comp    = Competition::factory()->create();

        $this->actingAs($parent);

        $this->post(route('parent.dispensation.store'), [
            'competition_id'    => $comp->id,
            'athlete_id'        => $athlete->id,
            'school_class'      => 'X IPA 1',
            'school_absence_no' => '7',
        ]);

        // Notifikasi harus terkirim ke admin1 dan admin2
        $this->assertDatabaseHas('system_notifications', ['user_id' => $admin1->id]);
        $this->assertDatabaseHas('system_notifications', ['user_id' => $admin2->id]);
    }
}
