@extends('layouts.parent')

@section('title', 'Ajukan Dispensasi')
@section('header_title', 'Formulir Pengajuan Dispensasi')

@section('content')
<div class="data-card" style="max-width: 680px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">📋 Formulir Pengajuan Surat Dispensasi</h3>
        <a href="{{ route('parent.dispensation.index') }}" class="form-link">◀ Batal & Kembali</a>
    </div>
    <hr style="margin: 1.25rem 0; border: none; border-bottom: 1px solid var(--border-color);">

    @php
        $resubmitId = request('resubmit');
        $resubmit   = $resubmitId ? $requests->firstWhere('id', (int) $resubmitId) : null;
    @endphp

    @if($resubmit)
        <div style="background: rgba(249,115,22,0.08); border: 1px solid rgba(249,115,22,0.25); border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
            <strong style="color: #c2410c;">🔄 Pengajuan Ulang</strong>
            <p style="margin: 0.4rem 0 0; font-size: 0.875rem; color: var(--text-muted);">
                Anda mengajukan ulang dispensasi untuk <strong>{{ $resubmit->athlete->name }}</strong>
                di kompetisi <strong>{{ $resubmit->competition->name }}</strong>.
                Catatan pelatih sebelumnya: <em>{{ $resubmit->coach_note }}</em>
            </p>
        </div>
    @endif

    <form method="POST" action="{{ route('parent.dispensation.store') }}">
        @csrf

        {{-- Pilih Atlet --}}
        <div class="form-group">
            <label for="athlete_id" class="form-label">Atlet <span style="color: #ef4444;">*</span></label>
            @if($athletes->isEmpty())
                <div style="background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2); border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.875rem; color: #dc2626;">
                    ⚠️ Akun Anda belum ditautkan dengan profil atlet. Hubungi pelatih untuk menautkan.
                </div>
            @else
                <select name="athlete_id" id="athlete_id" class="form-input" required>
                    <option value="">-- Pilih Atlet --</option>
                    @foreach($athletes as $athlete)
                        <option value="{{ $athlete->id }}"
                            {{ (old('athlete_id', $resubmit?->athlete_id) == $athlete->id) ? 'selected' : '' }}>
                            {{ $athlete->name }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- Pilih Kompetisi --}}
        <div class="form-group">
            <label for="competition_id" class="form-label">Kompetisi <span style="color: #ef4444;">*</span></label>
            @if($competitions->isEmpty())
                <div style="background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2); border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.875rem; color: #dc2626;">
                    ⚠️ Belum ada kompetisi yang tersedia.
                </div>
            @else
                <select name="competition_id" id="competition_id" class="form-input" required>
                    <option value="">-- Pilih Kompetisi --</option>
                    @foreach($competitions as $comp)
                        <option value="{{ $comp->id }}"
                            {{ (old('competition_id', $resubmit?->competition_id) == $comp->id) ? 'selected' : '' }}>
                            {{ $comp->name }} – {{ $comp->start_date->format('d/m/Y') }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- Kelas & No Absen --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="school_class" class="form-label">
                    Kelas Sekolah <span style="color: #ef4444;">*</span>
                    <span style="font-size: 0.75rem; font-weight: 400; color: var(--text-muted);">(bukan kelas renang)</span>
                </label>
                <input type="text" name="school_class" id="school_class" class="form-input"
                       value="{{ old('school_class', $resubmit?->school_class) }}"
                       placeholder="Contoh: VIII-A / XI IPA 2"
                       required>
            </div>
            <div class="form-group">
                <label for="school_absence_no" class="form-label">
                    Nomor Absen <span style="color: #ef4444;">*</span>
                    <span style="font-size: 0.75rem; font-weight: 400; color: var(--text-muted);">(absen sekolah)</span>
                </label>
                <input type="text" name="school_absence_no" id="school_absence_no" class="form-input"
                       value="{{ old('school_absence_no', $resubmit?->school_absence_no) }}"
                       placeholder="Contoh: 15"
                       required maxlength="50">
            </div>
        </div>

        {{-- Info Box --}}
        <div style="background: rgba(2,132,199,0.05); border: 1px solid rgba(2,132,199,0.15); border-radius: 0.75rem; padding: 1rem 1.25rem; margin: 1.25rem 0;">
            <p style="margin: 0; font-size: 0.845rem; color: var(--text-muted); line-height: 1.6;">
                ℹ️ Data nama atlet akan diambil otomatis dari sistem. Setelah pengajuan diverifikasi pelatih, surat dispensasi dalam format PDF akan tersedia untuk diunduh.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <button type="submit" class="btn btn-primary" {{ $athletes->isEmpty() || $competitions->isEmpty() ? 'disabled' : '' }}>
                📤 Kirim Pengajuan
            </button>
            <a href="{{ route('parent.dispensation.index') }}" class="btn" style="background: rgba(2,132,199,0.08); color: var(--text-muted); border: 1px solid var(--border-color);">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
// Pre-select from resubmit query params (already handled via old() / resubmit object)
// Optionally: auto-select athlete if only one child
document.addEventListener('DOMContentLoaded', function() {
    var athleteSelect = document.getElementById('athlete_id');
    if (athleteSelect && athleteSelect.options.length === 2) {
        // Only one athlete available, auto-select
        athleteSelect.selectedIndex = 1;
    }
});
</script>
@endsection
