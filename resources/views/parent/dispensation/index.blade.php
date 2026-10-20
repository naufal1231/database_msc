@extends('layouts.parent')

@section('title', 'Surat Dispensasi Saya')
@section('header_title', 'Dispensasi Atlet')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- Header Card --}}
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">📋 Daftar Pengajuan Dispensasi Saya</h3>
            <a href="{{ route('parent.dispensation.create') }}" class="btn btn-primary" style="max-width: 200px;">
                ➕ Ajukan Dispensasi
            </a>
        </div>

        <div class="table-responsive" style="margin-top: 0.5rem;">
            <table class="table">
                <thead>
                    <tr>
                        <th>ATLET</th>
                        <th>KOMPETISI</th>
                        <th>KELAS SEKOLAH</th>
                        <th>NO ABSEN</th>
                        <th style="text-align: center;">STATUS</th>
                        <th style="text-align: center; width: 170px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td style="font-weight: 700; color: var(--primary-hover);">
                                {{ $req->athlete->name }}
                            </td>
                            <td>
                                <span style="font-weight: 600;">{{ $req->competition->name }}</span>
                                <br><small style="color: var(--text-muted);">{{ $req->competition->start_date->format('d/m/Y') }}</small>
                            </td>
                            <td>{{ $req->school_class }}</td>
                            <td>{{ $req->school_absence_no }}</td>
                            <td style="text-align: center; vertical-align: top; padding-top: 0.9rem;">
                                <span class="badge {{ $req->status_badge_class }}">{{ $req->status_label }}</span>
                                @if($req->isApproved() && !$req->hasPdf())
                                    <div style="font-size: 0.72rem; color: #f97316; margin-top: 0.3rem;">⏳ Surat sedang dibuat…</div>
                                @endif
                            </td>
                            <td style="text-align: center; vertical-align: top; padding-top: 0.85rem;">
                                @if($req->isApproved() && $req->hasPdf())
                                    <a href="{{ route('parent.dispensation.download', $req) }}"
                                       class="btn btn-primary"
                                       style="padding: 0.4rem 0.85rem; font-size: 0.82rem; background-color: #16a34a;">
                                        ⬇ Unduh Surat
                                    </a>
                                @elseif($req->isApproved() && !$req->hasPdf())
                                    <span style="font-size: 0.8rem; color: #f97316; font-style: italic;">Diproses…</span>
                                @elseif($req->isRejected())
                                    <a href="{{ route('parent.dispensation.create', ['resubmit' => $req->id]) }}"
                                       class="btn btn-primary"
                                       style="padding: 0.4rem 0.85rem; font-size: 0.82rem; background-color: #d97706;">
                                        🔄 Ajukan Ulang
                                    </a>
                                @else
                                    <span style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">Menunggu verifikasi</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Catatan Pelatih jika ditolak --}}
                        @if($req->isRejected() && $req->coach_note)
                            <tr>
                                <td colspan="6" style="background: rgba(239,68,68,0.05); border-top: none; padding: 0.6rem 1rem;">
                                    <div style="display: flex; align-items: flex-start; gap: 0.6rem; font-size: 0.845rem;">
                                        <span style="color: #ef4444; font-size: 1rem; flex-shrink: 0;">💬</span>
                                        <div>
                                            <strong style="color: #dc2626;">Catatan Pelatih:</strong>
                                            <span style="color: var(--text-muted); margin-left: 0.35rem;">{{ $req->coach_note }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                                Belum ada pengajuan dispensasi.<br>
                                <a href="{{ route('parent.dispensation.create') }}" style="color: var(--primary); font-weight: 700;">Klik di sini untuk mengajukan →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info Box --}}
    <div style="background: rgba(2,132,199,0.05); border: 1px solid rgba(2,132,199,0.15); border-radius: 0.75rem; padding: 1rem 1.25rem;">
        <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--primary-hover); margin-bottom: 0.5rem;">ℹ️ Informasi Alur Dispensasi</h4>
        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem; color: var(--text-muted); line-height: 1.8;">
            <li><strong>Ajukan</strong> → Pilih kompetisi dan atlet, isi kelas &amp; nomor absen.</li>
            <li><strong>Menunggu</strong> → Pelatih akan memverifikasi pengajuan Anda.</li>
            <li><strong>Disetujui</strong> → Surat PDF akan tersedia untuk diunduh dalam beberapa menit.</li>
            <li><strong>Ditolak</strong> → Baca catatan pelatih, perbaiki data, lalu ajukan ulang.</li>
        </ul>
    </div>
</div>
@endsection
