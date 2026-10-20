<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDispensationRequest;
use App\Models\Competition;
use App\Models\DispensationRequest;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Controller sisi Orang Tua untuk fitur Dispensasi Atlet.
 */
class DispensationController extends Controller
{
    /**
     * Daftar pengajuan dispensasi milik orang tua yang login.
     */
    public function index()
    {
        $requests = DispensationRequest::with(['athlete', 'competition'])
            ->where('parent_id', auth()->id())
            ->latest()
            ->get();

        return view('parent.dispensation.index', compact('requests'));
    }

    /**
     * Form pengajuan baru.
     */
    public function create()
    {
        $athletes     = auth()->user()->athletes()->orderBy('name')->get();
        $competitions = Competition::orderBy('start_date', 'desc')->get();
        // Dibutuhkan agar tampilan resubmit bisa menampilkan data existing yang ditolak
        $requests     = DispensationRequest::with(['athlete', 'competition'])
            ->where('parent_id', auth()->id())
            ->get();

        return view('parent.dispensation.create', compact('athletes', 'competitions', 'requests'));
    }

    /**
     * Simpan pengajuan baru.
     * Jika sudah ada record (competition_id, athlete_id) dan statusnya rejected,
     * perbarui record yang sama (status kembali pending) agar tidak duplikat.
     */
    public function store(StoreDispensationRequest $request)
    {
        $data = $request->validated();

        // Cek kepemilikan atlet (redundant safety check, sudah di FormRequest)
        $athlete = auth()->user()->athletes()->findOrFail($data['athlete_id']);

        // Cek apakah sudah ada record untuk kombinasi ini
        $existing = DispensationRequest::where('competition_id', $data['competition_id'])
            ->where('athlete_id', $data['athlete_id'])
            ->first();

        if ($existing) {
            if ($existing->isRejected() && $existing->parent_id === auth()->id()) {
                // Pengajuan ulang setelah ditolak: reset ke pending
                $existing->update([
                    'school_class'      => $data['school_class'],
                    'school_absence_no' => $data['school_absence_no'],
                    'status'            => 'pending',
                    'coach_note'        => null,
                    'verified_by'       => null,
                    'verified_at'       => null,
                    'pdf_path'          => null,
                ]);

                $this->notifyCoaches($existing);

                return redirect()->route('parent.dispensation.index')
                    ->with('success', 'Pengajuan dispensasi berhasil diajukan ulang dan sedang menunggu verifikasi pelatih.');
            }

            if ($existing->parent_id === auth()->id()) {
                return redirect()->back()
                    ->with('error', 'Anda sudah memiliki pengajuan dispensasi untuk atlet dan kompetisi ini.');
            }

            // Atlet sudah diajukan oleh orang tua lain (seharusnya tidak terjadi jika validasi benar)
            abort(403, 'Pengajuan tidak valid.');
        }

        // Buat pengajuan baru
        $dispensation = DispensationRequest::create([
            'competition_id'    => $data['competition_id'],
            'athlete_id'        => $data['athlete_id'],
            'parent_id'         => auth()->id(),
            'school_class'      => $data['school_class'],
            'school_absence_no' => $data['school_absence_no'],
            'status'            => 'pending',
        ]);

        $this->notifyCoaches($dispensation);

        return redirect()->route('parent.dispensation.index')
            ->with('success', 'Pengajuan dispensasi berhasil dikirim dan sedang menunggu verifikasi pelatih.');
    }

    /**
     * Unduh PDF dispensasi (hanya jika approved dan PDF sudah tersedia).
     */
    public function download(DispensationRequest $dispensation)
    {
        // Pastikan ini milik orang tua yang login
        if ($dispensation->parent_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke surat ini.');
        }

        if (!$dispensation->isApproved() || !$dispensation->hasPdf()) {
            abort(404, 'Surat belum tersedia untuk diunduh.');
        }

        if (!Storage::disk('local')->exists($dispensation->pdf_path)) {
            abort(404, 'File surat tidak ditemukan di server. Hubungi pelatih.');
        }

        $filename = 'Dispensasi_' . str_replace(' ', '_', $dispensation->athlete->name)
                  . '_' . str_replace(' ', '_', $dispensation->competition->name)
                  . '.pdf';

        return Storage::disk('local')->download($dispensation->pdf_path, $filename);
    }

    /**
     * Kirim notifikasi ke semua coach/admin saat ada pengajuan baru/ulang.
     */
    protected function notifyCoaches(DispensationRequest $dispensation): void
    {
        $coaches = User::whereIn('role', ['admin', 'super_admin'])
            ->where('status', 'active')
            ->get();

        foreach ($coaches as $coach) {
            SystemNotification::create([
                'user_id' => $coach->id,
                'title'   => '📋 Pengajuan Dispensasi Baru',
                'message' => "Orang tua {$dispensation->parent->name} mengajukan dispensasi untuk atlet {$dispensation->athlete->name} pada kompetisi \"{$dispensation->competition->name}\". Silakan verifikasi.",
                'channel' => 'wa',
                'sent_at' => now(),
            ]);
        }
    }
}
