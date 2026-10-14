@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Dashboard Pelatih Kelas')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    .stat-card {
        background: linear-gradient(160deg, rgba(12,24,48,0.85) 0%, rgba(6,15,34,0.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        padding: 1.75rem 1.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-card);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(200,216,236,0.2) 50%, transparent);
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,0.3); }
    .stat-card.blue  { border-color: rgba(14,165,233,0.25); }
    .stat-card.green { border-color: rgba(74,222,128,0.25); }
    .stat-card.amber { border-color: rgba(251,191,36,0.25); }
    .stat-icon { font-size: 2.25rem; margin-bottom: 0.75rem; line-height: 1; }
    .stat-value { font-size: 2.25rem; font-weight: 900; line-height: 1; margin-bottom: 0.5rem; letter-spacing: -0.03em; }
    .stat-card.blue  .stat-value { color: #38bdf8; }
    .stat-card.green .stat-value { color: #4ade80; }
    .stat-card.amber .stat-value { color: #fbbf24; }
    .stat-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); }
    .action-cell { display: flex; gap: 0.5rem; justify-content: center; }
    .btn-approve {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.45rem 0.9rem; border-radius: 0.6rem; font-size: 0.78rem;
        font-weight: 700; font-family: var(--font-sans); cursor: pointer;
        background: rgba(74,222,128,0.15); color: #4ade80;
        border: 1px solid rgba(74,222,128,0.3); transition: all 0.2s ease; white-space: nowrap;
    }
    .btn-approve:hover { background: rgba(74,222,128,0.25); }
    .btn-reject {
        display: inline-flex; align-items: center; justify-content: center;
        padding: 0.45rem 0.9rem; border-radius: 0.6rem; font-size: 0.78rem;
        font-weight: 700; font-family: var(--font-sans); cursor: pointer;
        background: rgba(239,68,68,0.1); color: #f87171;
        border: 1px solid rgba(239,68,68,0.25); transition: all 0.2s ease; white-space: nowrap;
    }
    .btn-reject:hover { background: rgba(239,68,68,0.2); }
    .welcome-card {
        border-left: 3px solid var(--primary);
        background: linear-gradient(135deg, rgba(38,132,212,0.08) 0%, rgba(12,24,48,0.9) 60%);
    }
    .welcome-text { color: var(--text-muted); font-size: 0.9rem; line-height: 1.7; margin: 0; }
    .welcome-text strong { color: var(--silver-200); font-weight: 700; }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 0.65rem; }
        .stat-card { padding: 1rem 0.6rem; border-radius: 0.85rem; }
        .stat-icon { font-size: 1.5rem; margin-bottom: 0.4rem; }
        .stat-value { font-size: 1.5rem; }
        .stat-label { font-size: 0.6rem; letter-spacing: 0.3px; }
        .action-cell { flex-direction: column; align-items: stretch; gap: 0.3rem; min-width: 90px; }
        .btn-approve, .btn-reject { width: 100%; padding: 0.5rem; font-size: 0.72rem; justify-content: center; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; gap: 0.65rem; }
        .stat-card { display: flex; align-items: center; gap: 1rem; text-align: left; padding: 0.9rem 1.1rem; }
        .stat-icon { font-size: 2rem; margin-bottom: 0; flex-shrink: 0; }
        .stat-value { font-size: 1.75rem; }
    }
</style>
@endsection

@section('content')
<div style="display:flex; flex-direction:column; gap:1.5rem;">

    {{-- Welcome Card --}}
    <div class="data-card welcome-card" style="margin-bottom:0;">
        <div class="card-header" style="margin-bottom:0.6rem;">
            <h3 class="card-title">&#x1F44B; Selamat Datang</h3>
        </div>
        <p class="welcome-text">
            Halo <strong>{{ auth()->user()->name }}</strong>, selamat bekerja!
            Gunakan menu di sebelah kiri untuk mengelola data atlet, event lomba, dan menyetujui akun orang tua.
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">&#x1F3CA;</div>
            <div class="stat-value">{{ $stats['athletes_count'] }}</div>
            <div class="stat-label">Atlet Aktif</div>
        </div>
        <div class="stat-card green">
            <div class="stat-icon">&#x1F3EB;</div>
            <div class="stat-value">{{ $stats['classes_count'] }}</div>
            <div class="stat-label">Kelas Latihan</div>
        </div>
        <div class="stat-card amber">
            <div class="stat-icon">&#x1F3C6;</div>
            <div class="stat-value">{{ $stats['competitions_count'] }}</div>
            <div class="stat-label">Event Perlombaan</div>
        </div>
    </div>

    {{-- Parent Approval Table --}}
    <div class="data-card" style="margin-bottom:0;">
        <div class="card-header">
            <h3 class="card-title">
                &#x23F3; Persetujuan Akun Orang Tua
                @if($pendingParents->count() > 0)
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:rgba(251,191,36,0.2);color:#fbbf24;font-size:0.72rem;font-weight:800;margin-left:0.4rem;border:1px solid rgba(251,191,36,0.3);vertical-align:middle;">{{ $pendingParents->count() }}</span>
                @endif
            </h3>
        </div>
        <hr style="margin-bottom:1.25rem; border:none; border-bottom:1px solid var(--border-color);">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:44px; text-align:center;">NO</th>
                        <th>NAMA ORANG TUA / WALI</th>
                        <th>EMAIL</th>
                        <th>NO. TELEPON</th>
                        <th>TANGGAL DAFTAR</th>
                        <th style="text-align:center; min-width:140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingParents as $index => $parent)
                        <tr>
                            <td style="text-align:center; font-weight:600; color:var(--text-muted);">{{ $index + 1 }}</td>
                            <td style="font-weight:700; color:var(--silver-200);">{{ $parent->name }}</td>
                            <td style="color:var(--text-muted);">{{ $parent->email }}</td>
                            <td>{{ $parent->phone ?? '-' }}</td>
                            <td style="color:var(--text-muted); white-space:nowrap;">{{ $parent->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="action-cell">
                                    <form action="{{ route('parents.approve', $parent->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-approve">&#x2713; Setujui</button>
                                    </form>
                                    <form action="{{ route('parents.reject', $parent->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menolak pendaftaran ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-reject">&#x2715; Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:3rem; color:var(--text-muted); font-style:italic;">
                                Tidak ada permohonan pendaftaran akun orang tua baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
