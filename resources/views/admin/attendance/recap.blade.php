@extends('layouts.admin')

@section('title', 'Rekap Absensi Bulanan')
@section('header_title', 'Rekap Absensi')

@section('styles')
<style>
    /* ─── RECAP SPECIFIC STYLES ─── */
    .recap-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    @media (max-width: 900px) {
        .recap-stat-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 500px) {
        .recap-stat-grid { grid-template-columns: 1fr; }
    }

    .recap-stat-card {
        background: linear-gradient(160deg, rgba(12, 24, 48, 0.85) 0%, rgba(6, 15, 34, 0.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        padding: 1.25rem 1.5rem;
        position: relative;
        overflow: hidden;
        text-align: center;
    }
    .recap-stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(200,216,236,0.2) 50%, transparent);
    }
    .recap-stat-card.hadir { border-color: rgba(74, 222, 128, 0.2); }
    .recap-stat-card.izin  { border-color: rgba(251, 146, 60, 0.2); }
    .recap-stat-card.sakit { border-color: rgba(96, 165, 250, 0.2); }
    .recap-stat-card.alpha { border-color: rgba(248, 113, 113, 0.2); }

    .recap-stat-icon { font-size: 1.8rem; margin-bottom: 0.5rem; }
    .recap-stat-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.75px;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
    }
    .recap-stat-value {
        font-size: 2rem;
        font-weight: 900;
        color: var(--silver-200);
        letter-spacing: -0.03em;
        line-height: 1;
    }
    .recap-stat-value.hadir { color: #4ade80; }
    .recap-stat-value.izin  { color: #fb923c; }
    .recap-stat-value.sakit { color: #60a5fa; }
    .recap-stat-value.alpha { color: #f87171; }

    /* Progress Bar */
    .attendance-progress {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .progress-track {
        flex: 1;
        height: 7px;
        background: rgba(91, 168, 232, 0.08);
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    .progress-fill.high   { background: linear-gradient(90deg, #4ade80, #22c55e); }
    .progress-fill.medium { background: linear-gradient(90deg, #fb923c, #f97316); }
    .progress-fill.low    { background: linear-gradient(90deg, #f87171, #ef4444); }
    .progress-pct {
        font-size: 0.78rem;
        font-weight: 800;
        min-width: 38px;
        text-align: right;
        white-space: nowrap;
    }
    .progress-pct.high   { color: #4ade80; }
    .progress-pct.medium { color: #fb923c; }
    .progress-pct.low    { color: #f87171; }

    /* Count Pills */
    .count-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.2rem 0.5rem;
        border-radius: 0.4rem;
        font-size: 0.78rem;
        font-weight: 700;
        min-width: 28px;
    }
    .cp-hadir { background: rgba(74,222,128,0.12); color: #4ade80; }
    .cp-izin  { background: rgba(251,146,60,0.12); color: #fb923c; }
    .cp-sakit { background: rgba(96,165,250,0.12); color: #60a5fa; }
    .cp-alpha { background: rgba(248,113,113,0.12); color: #f87171; }

    .btn-sm-custom {
        padding: 0.45rem 0.85rem;
        font-size: 0.78rem;
        border-radius: 0.6rem;
        width: auto;
    }
    .btn-teal {
        background: linear-gradient(135deg, rgba(20,184,166,0.9), rgba(13,148,136,0.9));
        color: #fff;
        box-shadow: 0 4px 12px rgba(20,184,166,0.2);
    }
    .btn-teal:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(20,184,166,0.3);
    }
    .btn-success-custom {
        background: linear-gradient(135deg, rgba(74,222,128,0.85), rgba(34,197,94,0.9));
        color: #fff;
        box-shadow: 0 4px 12px rgba(74,222,128,0.15);
        width: auto;
    }
    .btn-success-custom:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(74,222,128,0.25);
    }
    .page-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-muted);
    }
    .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }
    .empty-state-text { font-size: 0.9rem; font-style: italic; }

    /* Modal */
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(4,13,26,0.85);
        backdrop-filter: blur(6px);
        z-index: 200;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .modal-backdrop-custom.active {
        opacity: 1;
        pointer-events: auto;
    }
    .modal-card {
        background: linear-gradient(160deg, rgba(12,24,48,0.98) 0%, rgba(6,15,34,0.99) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        width: 100%;
        max-width: 640px;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 40px 80px rgba(0,0,0,0.6), inset 0 1px 0 rgba(200,216,236,0.1);
        transform: translateY(16px);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .modal-backdrop-custom.active .modal-card {
        transform: translateY(0);
    }
    .detail-day-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.5rem;
        margin-top: 0.75rem;
    }
    .day-pill {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.4rem 0.65rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        background: rgba(10,22,40,0.5);
        border: 1px solid var(--glass-border);
    }
    .day-num { color: var(--text-muted); font-weight: 600; }
</style>
@endsection

@section('content')

{{-- ─── Header Card with Filters ─── --}}
<div class="data-card">
    <div class="card-header">
        <div>
            <h3 class="card-title">📊 Rekap Absensi Bulanan</h3>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">
                Ringkasan kehadiran seluruh atlet dalam satu periode
            </p>
        </div>
        <div class="page-actions">
            <a href="{{ route('attendance.index') }}" class="btn btn-teal btn-sm-custom">
                📅 Absensi Harian
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('attendance.recap') }}">
        <div class="filter-bar">
            <div class="form-group filter-input" style="margin-bottom: 0;">
                <label for="month" class="form-label">Bulan</label>
                <select name="month" id="month" class="form-input">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == $month ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="form-group filter-input" style="margin-bottom: 0;">
                <label for="year" class="form-label">Tahun</label>
                <select name="year" id="year" class="form-input">
                    @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                        <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div class="form-group filter-input" style="margin-bottom: 0;">
                <label for="swim_class_id" class="form-label">Kelas (opsional)</label>
                <select name="swim_class_id" id="swim_class_id" class="form-input">
                    <option value="">-- Semua Kelas --</option>
                    @php $classes = \App\Models\SwimClass::all(); @endphp
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('swim_class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: flex-end; padding-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.25rem;">
                    🔍 Filter
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ─── Summary Stats ─── --}}
@php
    $monthName = DateTime::createFromFormat('!m', $month)->format('F');
@endphp
<div class="recap-stat-grid">
    <div class="recap-stat-card hadir">
        <div class="recap-stat-icon">✅</div>
        <div class="recap-stat-label">Total Hadir</div>
        <div class="recap-stat-value hadir">{{ $summary['present'] ?? 0 }}</div>
    </div>
    <div class="recap-stat-card izin">
        <div class="recap-stat-icon">📨</div>
        <div class="recap-stat-label">Total Izin</div>
        <div class="recap-stat-value izin">{{ $summary['permission'] ?? 0 }}</div>
    </div>
    <div class="recap-stat-card sakit">
        <div class="recap-stat-icon">🤒</div>
        <div class="recap-stat-label">Total Sakit</div>
        <div class="recap-stat-value sakit">{{ $summary['sick'] ?? 0 }}</div>
    </div>
    <div class="recap-stat-card alpha">
        <div class="recap-stat-icon">❌</div>
        <div class="recap-stat-label">Tanpa Keterangan</div>
        <div class="recap-stat-value alpha">{{ $summary['absent'] ?? 0 }}</div>
    </div>
</div>

{{-- ─── Recap Table ─── --}}
<div class="data-card">
    <div class="card-header">
        <h3 class="card-title">
            Rekap Kehadiran —
            <span style="color: var(--primary); font-weight: 600;">{{ $monthName }} {{ $year }}</span>
        </h3>
        <a href="{{ route('attendance.export', ['month' => $month, 'year' => $year]) }}"
           class="btn btn-success-custom btn-sm-custom" target="_blank">
            📥 Export Excel (Semua)
        </a>
    </div>

    @if($attendances->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Atlet</th>
                        <th>Kelas</th>
                        <th style="text-align: center;">✅ Hadir</th>
                        <th style="text-align: center;">📨 Izin</th>
                        <th style="text-align: center;">🤒 Sakit</th>
                        <th style="text-align: center;">❌ Alpha</th>
                        <th>% Kehadiran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $athleteId => $attendanceRecords)
                        @php
                            $athlete        = $attendanceRecords->first()->athlete;
                            $presentCount   = $attendanceRecords->where('status', 'hadir')->count();
                            $permissionCount= $attendanceRecords->where('status', 'izin')->count();
                            $sickCount      = $attendanceRecords->where('status', 'sakit')->count();
                            $absentCount    = $attendanceRecords->where('status', 'tanpa_keterangan')->count();
                            $totalRecords   = $attendanceRecords->count();
                            $percentage     = $totalRecords > 0 ? ($presentCount / $totalRecords) * 100 : 0;
                            $pctClass       = $percentage >= 80 ? 'high' : ($percentage >= 60 ? 'medium' : 'low');
                        @endphp
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.8rem;">{{ $loop->iteration }}</td>
                            <td>
                                <button class="athlete-name-btn"
                                        onclick="openDetailModal({{ $athleteId }}, {{ $month }}, {{ $year }})">
                                    {{ $athlete->name }}
                                </button>
                            </td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $athlete->currentClass->name ?? '—' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="count-pill cp-hadir">{{ $presentCount }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="count-pill cp-izin">{{ $permissionCount }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="count-pill cp-sakit">{{ $sickCount }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="count-pill cp-alpha">{{ $absentCount }}</span>
                            </td>
                            <td style="min-width: 130px;">
                                <div class="attendance-progress">
                                    <div class="progress-track">
                                        <div class="progress-fill {{ $pctClass }}" style="width: {{ $percentage }}%;"></div>
                                    </div>
                                    <span class="progress-pct {{ $pctClass }}">
                                        {{ number_format($percentage, 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                    <button class="btn btn-primary btn-sm-custom"
                                            onclick="openDetailModal({{ $athleteId }}, {{ $month }}, {{ $year }})">
                                        👁 Detail
                                    </button>
                                    <a href="{{ route('attendance.export', ['month' => $month, 'year' => $year, 'class' => $athlete->current_class_id]) }}"
                                       class="btn btn-success-custom btn-sm-custom" target="_blank">
                                        📥
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <p style="font-size: 1rem; font-weight: 700; color: var(--silver-200); margin-bottom: 0.5rem;">
                Tidak Ada Data Absensi
            </p>
            <div class="empty-state-text">
                Tidak ada catatan absensi untuk periode <strong>{{ $monthName }} {{ $year }}</strong>.
            </div>
        </div>
    @endif
</div>

{{-- ─── Detail Modal ─── --}}
<div class="modal-backdrop-custom" id="detailModalBackdrop" onclick="closeDetailModal(event)">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="detailModalTitle">📋 Detail Absensi</div>
                <div class="modal-subtitle" id="detailModalSubtitle">Rincian kehadiran per sesi</div>
            </div>
            <button class="modal-close" onclick="closeDetailModalBtn()" title="Tutup">✕</button>
        </div>
        <div class="modal-body" id="detailModalContent">
            <div style="text-align: center; padding: 2rem; color: var(--text-muted);">
                ⏳ Memuat data...
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ─── Detail Modal ───
    function openDetailModal(athleteId, month, year) {
        const backdrop = document.getElementById('detailModalBackdrop');
        const content  = document.getElementById('detailModalContent');

        content.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--text-muted);">⏳ Memuat data...</div>';
        backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';

        fetch(`/api/attendance/details/${athleteId}/${month}/${year}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('detailModalTitle').textContent = `📋 ${data.athlete_name ?? 'Detail Absensi'}`;
                document.getElementById('detailModalSubtitle').textContent =
                    `Rincian ${data.month_label ?? ''} — ${data.records?.length ?? 0} sesi tercatat`;

                if (data.html) {
                    content.innerHTML = data.html;
                } else if (data.records && data.records.length > 0) {
                    // Fallback: render manually if API returns JSON records
                    let html = '<div class="detail-day-grid">';
                    const statusMap = {
                        hadir: { icon: '✅', color: '#4ade80' },
                        izin:  { icon: '📨', color: '#fb923c' },
                        sakit: { icon: '🤒', color: '#60a5fa' },
                        tanpa_keterangan: { icon: '❌', color: '#f87171' },
                    };
                    data.records.forEach(r => {
                        const s = statusMap[r.status] || { icon: '—', color: 'var(--text-muted)' };
                        html += `<div class="day-pill">
                                    <span class="day-num">${r.date}</span>
                                    <span style="color:${s.color}; font-weight:700;">${s.icon} ${r.status}</span>
                                 </div>`;
                    });
                    html += '</div>';
                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:1rem;">Tidak ada data detail.</p>';
                }
            })
            .catch(() => {
                content.innerHTML = '<p style="text-align:center;color:#f87171;padding:1rem;">❌ Gagal memuat detail absensi.</p>';
            });
    }

    function closeDetailModal(event) {
        if (event.target === document.getElementById('detailModalBackdrop')) {
            closeDetailModalBtn();
        }
    }

    function closeDetailModalBtn() {
        document.getElementById('detailModalBackdrop').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDetailModalBtn();
    });
</script>
@endsection