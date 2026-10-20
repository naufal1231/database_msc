@extends('layouts.admin')

@section('title', 'Dispensasi Atlet')
@section('header_title', 'Manajemen Dispensasi Atlet')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- Template Status Banner --}}
    @if(!$templateExists)
        <div class="alert alert-danger" style="display: flex; align-items: center; gap: 0.75rem; background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.05)); border: 1px solid rgba(239,68,68,0.3); border-radius: 0.75rem; padding: 1rem 1.25rem;">
            <span style="font-size: 1.5rem;">⚠️</span>
            <div>
                <strong style="color: #dc2626;">Template Belum Diupload</strong>
                <p style="margin: 0.25rem 0 0; font-size: 0.875rem; color: var(--text-muted);">
                    Surat dispensasi tidak bisa diproses sebelum template .docx diupload.
                    <a href="{{ route('admin.dispensation.template') }}" style="color: var(--primary); font-weight: 700;">Upload template sekarang →</a>
                </p>
            </div>
        </div>
    @else
        <div style="background: linear-gradient(135deg, rgba(34,197,94,0.08), rgba(22,163,74,0.04)); border: 1px solid rgba(34,197,94,0.25); border-radius: 0.75rem; padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <span style="font-size: 1.25rem;">📄</span>
                <div>
                    <span style="font-weight: 700; color: #15803d; font-size: 0.9rem;">Template Aktif:</span>
                    <span style="color: var(--text-muted); font-size: 0.875rem; margin-left: 0.35rem;">{{ $templateInfo['filename'] ?? 'template.docx' }}</span>
                    @if($templateInfo['uploaded_at'] ?? null)
                        <span style="color: var(--text-light-muted); font-size: 0.8rem; margin-left: 0.5rem;">
                            · Diupload {{ \Carbon\Carbon::parse($templateInfo['uploaded_at'])->diffForHumans() }}
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.dispensation.template') }}" class="btn btn-primary" style="padding: 0.4rem 0.9rem; font-size: 0.82rem; background-color: #4f46e5;">
                🔄 Ganti Template
            </a>
        </div>
    @endif

    {{-- Filter & Header --}}
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header" style="flex-wrap: wrap; gap: 0.75rem;">
            <h3 class="card-title">📋 Daftar Pengajuan Dispensasi</h3>
            <a href="{{ route('admin.dispensation.template') }}" class="btn btn-primary" style="background-color: #4f46e5; padding: 0.5rem 1rem; font-size: 0.85rem;">
                ⚙️ Kelola Template
            </a>
        </div>

        {{-- Filter Form --}}
        <form method="GET" action="{{ route('admin.dispensation.index') }}" style="padding: 0 0 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: flex-end;">
            <div class="form-group" style="margin: 0; min-width: 160px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.3rem;">Status</label>
                <select name="status" class="form-input" style="padding: 0.45rem 0.75rem; font-size: 0.875rem;">
                    <option value="">Semua Status</option>
                    <option value="pending"  {{ request('status') === 'pending'   ? 'selected' : '' }}>⏳ Menunggu</option>
                    <option value="approved" {{ request('status') === 'approved'  ? 'selected' : '' }}>✅ Disetujui</option>
                    <option value="rejected" {{ request('status') === 'rejected'  ? 'selected' : '' }}>❌ Ditolak</option>
                </select>
            </div>
            <div class="form-group" style="margin: 0; min-width: 200px;">
                <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.3rem;">Kompetisi</label>
                <select name="competition_id" class="form-input" style="padding: 0.45rem 0.75rem; font-size: 0.875rem;">
                    <option value="">Semua Kompetisi</option>
                    @foreach($competitions as $comp)
                        <option value="{{ $comp->id }}" {{ request('competition_id') == $comp->id ? 'selected' : '' }}>
                            {{ $comp->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.875rem; align-self: flex-end;">
                🔍 Filter
            </button>
            @if(request('status') || request('competition_id'))
                <a href="{{ route('admin.dispensation.index') }}" class="btn" style="padding: 0.45rem 1rem; font-size: 0.875rem; align-self: flex-end; background: rgba(2,132,199,0.08); color: var(--text-muted); border: 1px solid var(--border-color);">
                    ✕ Reset
                </a>
            @endif
        </form>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ATLET</th>
                        <th>ORANG TUA</th>
                        <th>KOMPETISI</th>
                        <th>KELAS SEKOLAH</th>
                        <th>NO ABSEN</th>
                        <th style="text-align: center;">STATUS</th>
                        <th style="text-align: center; width: 220px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td style="font-weight: 700; color: var(--primary-hover);">
                                {{ $req->athlete->name }}
                            </td>
                            <td>{{ $req->parent->name }}</td>
                            <td>
                                <span style="font-weight: 600;">{{ $req->competition->name }}</span>
                                <br><small style="color: var(--text-muted);">{{ $req->competition->start_date->format('d/m/Y') }}</small>
                            </td>
                            <td>{{ $req->school_class }}</td>
                            <td>{{ $req->school_absence_no }}</td>
                            <td style="text-align: center;">
                                <span class="badge {{ $req->status_badge_class }}">{{ $req->status_label }}</span>
                                @if($req->isApproved() && !$req->hasPdf())
                                    <div style="font-size: 0.72rem; color: #f97316; margin-top: 0.2rem;">⏳ PDF diproses…</div>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 0.35rem; justify-content: center; flex-wrap: wrap; align-items: center;">
                                    @if($req->isPending())
                                        {{-- Tombol Setuju --}}
                                        <form action="{{ route('admin.dispensation.approve', $req) }}" method="POST" style="display: inline;" onsubmit="return confirm('Setujui dispensasi ini? PDF akan langsung dibuat.')">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.7rem; font-size: 0.78rem; background-color: #16a34a;">
                                                ✅ Setuju
                                            </button>
                                        </form>
                                        {{-- Tombol Tolak --}}
                                        <button type="button" class="btn btn-accent" style="padding: 0.35rem 0.7rem; font-size: 0.78rem;"
                                                onclick="openRejectModal({{ $req->id }})">
                                            ❌ Tolak
                                        </button>
                                    @endif

                                    @if($req->isApproved() && $req->hasPdf())
                                        <a href="{{ route('admin.dispensation.pdf', $req) }}" class="btn btn-primary" style="padding: 0.35rem 0.7rem; font-size: 0.78rem; background-color: #0284c7;">
                                            ⬇ PDF
                                        </a>
                                    @endif

                                    @if($req->isRejected())
                                        <div style="font-size: 0.78rem; color: #ef4444; max-width: 160px; text-align: left; line-height: 1.4;">
                                            <strong>Catatan:</strong> {{ Str::limit($req->coach_note, 60) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                                Belum ada pengajuan dispensasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($requests->hasPages())
            <div style="padding: 1rem 0 0; display: flex; justify-content: center;">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Tolak --}}
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: var(--bg-card); border-radius: 1rem; padding: 2rem; max-width: 480px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-dark); margin-bottom: 1rem;">❌ Tolak Pengajuan Dispensasi</h3>
        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem;">Berikan catatan penolakan yang jelas agar orang tua dapat memperbaiki data dan mengajukan ulang.</p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Catatan Penolakan <span style="color: #ef4444;">*</span></label>
                <textarea name="coach_note" class="form-input" rows="4" required
                          placeholder="Contoh: Nama atlet tidak sesuai dengan data yang terdaftar di sistem, harap periksa kembali..."
                          style="resize: vertical;"></textarea>
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.25rem;">
                <button type="button" onclick="closeRejectModal()" class="btn" style="background: rgba(2,132,199,0.08); color: var(--text-muted); border: 1px solid var(--border-color);">
                    Batal
                </button>
                <button type="submit" class="btn btn-accent" style="background-color: #dc2626;">
                    ❌ Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/dispensasi/' + id + '/reject';
    var modal = document.getElementById('rejectModal');
    modal.style.display = 'flex';
    modal.querySelector('textarea').value = '';
    modal.querySelector('textarea').focus();
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endsection
