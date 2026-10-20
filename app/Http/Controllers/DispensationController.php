<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadDispensationTemplateRequest;
use App\Jobs\GenerateDispensationPdf;
use App\Models\Competition;
use App\Models\DispensationRequest;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Controller sisi Coach / Admin untuk fitur Dispensasi Atlet.
 */
class DispensationController extends Controller
{
    protected string $settingsFile;

    public function __construct()
    {
        $this->settingsFile = storage_path('app/settings.json');
    }

    // ── Daftar Pengajuan ───────────────────────────────────────

    /**
     * Daftar seluruh pengajuan dispensasi, bisa difilter status & kompetisi.
     */
    public function index(Request $request)
    {
        $query = DispensationRequest::with([
            'athlete',
            'parent',
            'competition',
            'verifier',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('competition_id')) {
            $query->where('competition_id', $request->competition_id);
        }

        $requests     = $query->paginate(20)->withQueryString();
        $competitions = Competition::orderBy('start_date', 'desc')->get();

        // Informasi template aktif
        $templateExists = $this->templateExists();
        $templateInfo   = $this->getTemplateInfo();

        return view('admin.dispensation.index', compact(
            'requests',
            'competitions',
            'templateExists',
            'templateInfo'
        ));
    }

    // ── Verifikasi ─────────────────────────────────────────────

    /**
     * Setujui pengajuan → dispatch job GenerateDispensationPdf.
     */
    public function approve(DispensationRequest $dispensation)
    {
        if (!$dispensation->isPending()) {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat disetujui.');
        }

        if (!$this->templateExists()) {
            return redirect()->back()->with('error', 'Template dispensasi belum diupload. Upload template terlebih dahulu.');
        }

        $dispensation->update([
            'status'      => 'approved',
            'coach_note'  => null,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'pdf_path'    => null, // akan diisi oleh job
        ]);

        // Dispatch job pembuatan PDF
        GenerateDispensationPdf::dispatch($dispensation);

        // Notifikasi orang tua
        SystemNotification::create([
            'user_id' => $dispensation->parent_id,
            'title'   => '✅ Surat Dispensasi Disetujui',
            'message' => "Surat dispensasi atlet {$dispensation->athlete->name} untuk kompetisi \"{$dispensation->competition->name}\" telah disetujui oleh pelatih. Surat akan tersedia untuk diunduh sebentar lagi setelah proses pembuatan selesai.",
            'channel' => 'wa',
            'sent_at' => now(),
        ]);

        return redirect()->back()->with('success', "Pengajuan dispensasi {$dispensation->athlete->name} disetujui. PDF sedang diproses.");
    }

    /**
     * Tolak pengajuan – wajib mengisi coach_note.
     */
    public function reject(Request $request, DispensationRequest $dispensation)
    {
        if (!$dispensation->isPending()) {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat ditolak.');
        }

        $request->validate([
            'coach_note' => ['required', 'string', 'max:1000'],
        ], [
            'coach_note.required' => 'Catatan penolakan wajib diisi.',
        ]);

        $dispensation->update([
            'status'      => 'rejected',
            'coach_note'  => $request->coach_note,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'pdf_path'    => null,
        ]);

        // Notifikasi orang tua
        SystemNotification::create([
            'user_id' => $dispensation->parent_id,
            'title'   => '❌ Surat Dispensasi Ditolak',
            'message' => "Surat dispensasi atlet {$dispensation->athlete->name} untuk kompetisi \"{$dispensation->competition->name}\" ditolak oleh pelatih. Catatan: {$request->coach_note}. Silakan perbaiki data dan ajukan ulang.",
            'channel' => 'wa',
            'sent_at' => now(),
        ]);

        return redirect()->back()->with('success', "Pengajuan dispensasi {$dispensation->athlete->name} ditolak.");
    }

    // ── Template ───────────────────────────────────────────────

    /**
     * Halaman upload/ganti template .docx.
     */
    public function templatePage()
    {
        $templateExists = $this->templateExists();
        $templateInfo   = $this->getTemplateInfo();
        $requiredPlaceholders = \App\Http\Requests\UploadDispensationTemplateRequest::REQUIRED_PLACEHOLDERS;

        return view('admin.dispensation.template', compact(
            'templateExists',
            'templateInfo',
            'requiredPlaceholders'
        ));
    }

    /**
     * Proses upload template .docx.
     */
    public function uploadTemplate(UploadDispensationTemplateRequest $request)
    {
        // Hapus template lama jika ada
        $oldPath = $this->getTemplatePath();
        if ($oldPath && Storage::disk('local')->exists($oldPath)) {
            Storage::disk('local')->delete($oldPath);
        }

        // Simpan template baru
        $path = $request->file('template')->storeAs(
            'dispensation',
            'template.docx',
            'local'
        );

        // Update settings.json
        $settings = File::exists($this->settingsFile)
            ? (json_decode(File::get($this->settingsFile), true) ?? [])
            : [];

        $settings['dispensation_template_path']       = $path;
        $settings['dispensation_template_uploaded_at'] = now()->toISOString();
        $settings['dispensation_template_filename']    = $request->file('template')->getClientOriginalName();

        File::put($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->route('admin.dispensation.template')
            ->with('success', 'Template dispensasi berhasil diupload.');
    }

    // ── Download PDF (Admin) ───────────────────────────────────

    /**
     * Admin mengunduh PDF dispensasi.
     */
    public function downloadPdf(DispensationRequest $dispensation)
    {
        if (!$dispensation->isApproved() || !$dispensation->hasPdf()) {
            abort(404, 'PDF dispensasi belum tersedia.');
        }

        if (!Storage::disk('local')->exists($dispensation->pdf_path)) {
            abort(404, 'File PDF tidak ditemukan di server.');
        }

        $filename = 'Dispensasi_' . str_replace(' ', '_', $dispensation->athlete->name)
                  . '_' . str_replace(' ', '_', $dispensation->competition->name)
                  . '.pdf';

        return Storage::disk('local')->download($dispensation->pdf_path, $filename);
    }

    // ── Helper ─────────────────────────────────────────────────

    protected function templateExists(): bool
    {
        $path = $this->getTemplatePath();
        return $path && Storage::disk('local')->exists($path);
    }

    protected function getTemplatePath(): ?string
    {
        if (!File::exists($this->settingsFile)) {
            return null;
        }
        $settings = json_decode(File::get($this->settingsFile), true) ?? [];
        return $settings['dispensation_template_path'] ?? null;
    }

    protected function getTemplateInfo(): array
    {
        if (!File::exists($this->settingsFile)) {
            return [];
        }
        $settings = json_decode(File::get($this->settingsFile), true) ?? [];
        return [
            'filename'    => $settings['dispensation_template_filename'] ?? null,
            'uploaded_at' => $settings['dispensation_template_uploaded_at'] ?? null,
        ];
    }
}
