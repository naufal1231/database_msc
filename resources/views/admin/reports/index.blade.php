@extends('layouts.admin')

@section('title', 'Laporan Kemajuan Atlet')
@section('header_title', 'Laporan Rekap Performa & Kemajuan Atlet')

@section('content')
@php
    $activeCount = (request('class_id') ? 1 : 0) + (request('ku') ? 1 : 0) + (request('gender') ? 1 : 0);
@endphp

<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    {{-- ═══════════════════════════════════════════════════════
         DESKTOP FILTER CARD (hidden on mobile)
    ═══════════════════════════════════════════════════════ --}}
    <div class="data-card desktop-only" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">🔍 Filter Rekap Laporan</h3>
        </div>
        <hr style="margin: 0 0 1.25rem; border: none; border-bottom: 1px solid var(--glass-border);">

        <form method="GET" action="{{ route('reports.index') }}"
              style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="class_id" class="form-label">Kelas Latihan</label>
                <select name="class_id" id="class_id" class="form-input">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="ku" class="form-label">Kelompok Umur (KU)</label>
                <select name="ku" id="ku" class="form-input">
                    <option value="">Semua KU</option>
                    @foreach($kus as $k)
                        <option value="{{ $k }}" {{ request('ku') == $k ? 'selected' : '' }}>
                            KU {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="gender" class="form-label">Kategori Gender</label>
                <select name="gender" id="gender" class="form-input">
                    <option value="">Semua Gender</option>
                    <option value="L" {{ request('gender') === 'L' ? 'selected' : '' }}>PA (Putera)</option>
                    <option value="P" {{ request('gender') === 'P' ? 'selected' : '' }}>PI (Puteri)</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: flex-end; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 100px; padding: 0.75rem 1rem;">
                    Tampilkan
                </button>
                <a href="{{ route('reports.index', array_merge(request()->all(), ['print' => '1'])) }}"
                   target="_blank"
                   class="btn btn-primary"
                   style="flex: 1; min-width: 100px; padding: 0.75rem 1rem; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); box-shadow: 0 4px 14px rgba(22, 163, 74, 0.25); text-decoration: none;">
                    🖨️ Cetak
                </a>
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         MOBILE SEARCH + FILTER BAR
    ═══════════════════════════════════════════════════════ --}}
    <div class="mobile-only">
        <div class="data-card" style="padding: 1rem; margin-bottom: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                <span style="font-size: 0.95rem; font-weight: 700; color: var(--silver-200);">📊 Rekap Kemajuan</span>
                <a href="{{ route('reports.index', array_merge(request()->all(), ['print' => '1'])) }}"
                   target="_blank"
                   style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.4rem 0.85rem;
                          background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
                          border-radius: 0.65rem; font-size: 0.75rem; font-weight: 700; color: #fff;
                          text-decoration: none;">
                    🖨️ Cetak
                </a>
            </div>

            <div class="mobile-header-row">
                <span style="flex: 1; display: flex; align-items: center; gap: 0.5rem;
                             padding: 0.65rem 0.875rem;
                             background: var(--navy-800); border: 1px solid var(--glass-border);
                             border-radius: 0.65rem; color: var(--text-muted); font-size: 0.85rem;">
                    📋 {{ count($reportData) }} atlet ditampilkan
                </span>
                <button type="button" id="open-report-filter-sheet" class="mobile-filter-btn">
                    ⚙ Filter
                    @if($activeCount > 0)
                        <span class="mobile-filter-btn-badge">{{ $activeCount }}</span>
                    @endif
                </button>
            </div>

            {{-- Active chips --}}
            @if($activeCount > 0)
            <div class="active-filter-chips">
                @if(request('class_id'))
                    @php $sc = $classes->firstWhere('id', request('class_id')); @endphp
                    @if($sc)
                    <span class="active-chip">{{ $sc->name }}
                        <a href="{{ request()->fullUrlWithQuery(['class_id' => null]) }}" class="active-chip-remove">✕</a>
                    </span>
                    @endif
                @endif
                @if(request('ku'))
                    <span class="active-chip">KU {{ request('ku') }}
                        <a href="{{ request()->fullUrlWithQuery(['ku' => null]) }}" class="active-chip-remove">✕</a>
                    </span>
                @endif
                @if(request('gender'))
                    <span class="active-chip">{{ request('gender') === 'L' ? 'PA' : 'PI' }}
                        <a href="{{ request()->fullUrlWithQuery(['gender' => null]) }}" class="active-chip-remove">✕</a>
                    </span>
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         DESKTOP TABLE + MOBILE CARDS (Report Data)
    ═══════════════════════════════════════════════════════ --}}
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">📊 Tabel Rekap Kemajuan Atlet</h3>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;" class="desktop-only">
                Klik nama atlet untuk melihat best time semua nomor lomba
            </span>
        </div>
        <hr style="margin: 0 0 1.25rem; border: none; border-bottom: 1px solid var(--glass-border);">

        {{-- Desktop Table --}}
        <div class="desktop-athlete-table">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">NO</th>
                            <th>NAMA ATLET</th>
                            <th style="text-align: center; width: 90px;">KU</th>
                            <th style="text-align: center; width: 80px;">PA/PI</th>
                            <th>KELAS SEKARANG</th>
                            <th style="text-align: center; width: 130px;">LOMBA DIIKUTI</th>
                            <th style="text-align: center; width: 130px;">STATUS PB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $index => $row)
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--text-muted);">
                                    {{ $index + 1 }}
                                </td>
                                <td>
                                    <button
                                        class="athlete-name-btn"
                                        onclick="showAthleteModal({{ $index }})"
                                        title="Klik untuk melihat best time">
                                        {{ $row['name'] }}
                                    </button>
                                </td>
                                <td style="text-align: center; color: var(--text-muted);">KU {{ $row['ku'] }}</td>
                                <td style="text-align: center;">
                                    <span class="badge {{ $row['gender'] === 'PA' ? 'badge-primary' : 'badge-accent' }}">
                                        {{ $row['gender'] }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--silver-200);">{{ $row['class_name'] }}</td>
                                <td style="text-align: center; font-weight: 700;">{{ $row['races_count'] }} kali</td>
                                <td style="text-align: center;">
                                    @if(count($row['personal_bests']) > 0)
                                        <span class="badge badge-success">
                                            {{ count($row['personal_bests']) }} nomor
                                        </span>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">Belum ada</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                                    Tidak ada data atlet yang sesuai dengan kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile Card List --}}
        <div class="mobile-athlete-list">
            @forelse($reportData as $index => $row)
                <div class="m-athlete-card" style="cursor: pointer;" onclick="showAthleteMobileModal({{ $index }})">
                    {{-- Avatar --}}
                    <div class="m-athlete-avatar-placeholder" style="font-size: 1rem; font-weight: 800; color: #5ba8e8;">
                        {{ strtoupper(substr($row['name'], 0, 1)) }}
                    </div>

                    {{-- Info --}}
                    <div class="m-athlete-info">
                        <div class="m-athlete-name">{{ $row['name'] }}</div>
                        <div class="m-athlete-meta" style="margin-bottom: 0.3rem;">
                            <span class="m-chip {{ $row['gender'] === 'PA' ? 'm-chip-pa' : 'm-chip-pi' }}">{{ $row['gender'] }}</span>
                            <span class="m-chip m-chip-blue">KU {{ $row['ku'] }}</span>
                            <span class="m-chip m-chip-muted">{{ $row['class_name'] }}</span>
                        </div>
                        <div style="display: flex; gap: 0.75rem; margin-top: 0.25rem;">
                            <span style="font-size: 0.72rem; color: var(--text-muted);">
                                🏊 <strong style="color: #5ba8e8;">{{ $row['races_count'] }}</strong> lomba
                            </span>
                            @if(count($row['personal_bests']) > 0)
                            <span style="font-size: 0.72rem; color: var(--text-muted);">
                                ⭐ <strong style="color: #4ade80;">{{ count($row['personal_bests']) }}</strong> PB
                            </span>
                            @else
                            <span style="font-size: 0.72rem; color: var(--text-light-muted); font-style: italic;">Belum ada PB</span>
                            @endif
                        </div>
                    </div>

                    {{-- Tap to expand indicator --}}
                    <span style="font-size: 0.72rem; color: var(--text-muted); flex-shrink: 0; text-align: center; line-height: 1.3;">
                        🏆<br><span style="font-size: 0.65rem;">Lihat PB</span>
                    </span>
                </div>
            @empty
                <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted); font-style: italic; font-size: 0.875rem;">
                    Tidak ada data atlet yang sesuai dengan kriteria filter.
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     DESKTOP MODAL (Best Time popup — unchanged)
═══════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="pbModal" onclick="closePbModal(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="modalAthleteNameTitle">Nama Atlet</div>
                <div class="modal-subtitle" id="modalAthleteSubtitle">Best Time Semua Nomor Lomba</div>
            </div>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body" id="modalBodyContent">
            <!-- Filled by JS -->
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     MOBILE PB BOTTOM SHEET
═══════════════════════════════════════════════════════ --}}
<div class="filter-bottom-sheet-overlay" id="mobile-pb-sheet-overlay">
    <div class="filter-bottom-sheet" id="mobile-pb-sheet">
        <div class="filter-sheet-handle"></div>
        <div id="mobile-pb-sheet-title" class="filter-sheet-title">🏆 Personal Best</div>
        <div id="mobile-pb-sheet-sub" style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1rem;"></div>
        <div id="mobile-pb-sheet-body"><!-- filled by JS --></div>
        <div style="margin-top: 1.25rem;">
            <button type="button" onclick="closeMobilePbSheet()"
                    class="btn btn-primary" style="width: 100%;">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     REPORT FILTER BOTTOM SHEET (mobile)
═══════════════════════════════════════════════════════ --}}
<div class="filter-bottom-sheet-overlay" id="report-filter-sheet-overlay">
    <div class="filter-bottom-sheet" role="dialog" aria-modal="true">
        <div class="filter-sheet-handle"></div>
        <div class="filter-sheet-title">⚙ Filter Laporan</div>

        <form method="GET" action="{{ route('reports.index') }}" id="mobile-report-filter-form">
            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="m_class_id">Kelas Latihan</label>
                <select name="class_id" id="m_class_id" class="form-input">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="m_ku">Kelompok Umur</label>
                <select name="ku" id="m_ku" class="form-input">
                    <option value="">Semua KU</option>
                    @foreach($kus as $k)
                        <option value="{{ $k }}" {{ request('ku') == $k ? 'selected' : '' }}>KU {{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="m_gender">Kategori Gender</label>
                <select name="gender" id="m_gender" class="form-input">
                    <option value="">Semua Gender</option>
                    <option value="L" {{ request('gender') === 'L' ? 'selected' : '' }}>PA (Putera)</option>
                    <option value="P" {{ request('gender') === 'P' ? 'selected' : '' }}>PI (Puteri)</option>
                </select>
            </div>
            <div class="filter-sheet-actions">
                <a href="{{ route('reports.index') }}" class="btn btn-accent"
                   style="text-align: center; background-color: var(--text-muted); color: #fff;">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const reportData = @json($reportData);

    // ── Desktop modal (original) ──────────────────────────
    function showAthleteModal(index) {
        const athlete = reportData[index];
        const modal = document.getElementById('pbModal');
        document.getElementById('modalAthleteNameTitle').textContent = '🏊 ' + athlete.name;
        document.getElementById('modalAthleteSubtitle').textContent = 'KU ' + athlete.ku + ' · ' + athlete.gender + ' · ' + athlete.class_name;
        const bodyEl = document.getElementById('modalBodyContent');

        if (!athlete.personal_bests || athlete.personal_bests.length === 0) {
            bodyEl.innerHTML = `<div style="text-align:center;padding:2.5rem 1rem;color:var(--text-muted);">
                <div style="font-size:2rem;margin-bottom:.75rem;">⏱️</div>
                <p style="font-style:italic;font-size:.875rem;">Belum ada catatan waktu finish untuk atlet ini.</p>
            </div>`;
        } else {
            let rows = athlete.personal_bests.map(pb => `
                <tr>
                    <td style="font-weight:700;color:var(--silver-200);">${pb.event_label}</td>
                    <td style="text-align:center;font-family:monospace;font-weight:800;font-size:1rem;color:#93c5fd;letter-spacing:.05em;">${pb.time_result}</td>
                    <td style="font-size:.8rem;color:var(--text-muted);">${pb.competition_name}<br><small>${pb.competition_date}</small></td>
                </tr>
            `).join('');
            bodyEl.innerHTML = `<div class="table-responsive">
                <table class="table">
                    <thead><tr>
                        <th>NOMOR LOMBA</th>
                        <th style="text-align:center;width:130px;">WAKTU TERBAIK</th>
                        <th>DARI EVENT</th>
                    </tr></thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
        }
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('pbModal').classList.remove('active');
        document.body.style.overflow = '';
    }
    function closePbModal(event) {
        if (event.target === document.getElementById('pbModal')) closeModal();
    }
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });

    // ── Mobile PB bottom sheet ────────────────────────────
    function showAthleteMobileModal(index) {
        const athlete = reportData[index];
        const overlay = document.getElementById('mobile-pb-sheet-overlay');
        document.getElementById('mobile-pb-sheet-title').textContent = '🏆 ' + athlete.name;
        document.getElementById('mobile-pb-sheet-sub').textContent = 'KU ' + athlete.ku + ' · ' + athlete.gender + ' · ' + athlete.class_name;
        const bodyEl = document.getElementById('mobile-pb-sheet-body');

        if (!athlete.personal_bests || athlete.personal_bests.length === 0) {
            bodyEl.innerHTML = `<div style="text-align:center;padding:2rem 0;color:var(--text-muted);">
                <div style="font-size:2rem;margin-bottom:.5rem;">⏱️</div>
                <p style="font-size:.85rem;font-style:italic;">Belum ada catatan waktu finish.</p>
                <p style="font-size:.75rem;margin-top:.3rem;color:var(--text-light-muted);">🏊 ${athlete.races_count} kali lomba diikuti</p>
            </div>`;
        } else {
            let items = athlete.personal_bests.map(pb => `
                <div style="display:flex;justify-content:space-between;align-items:flex-start;
                            padding:.65rem 0;border-bottom:1px solid var(--glass-border);">
                    <div>
                        <div style="font-size:.8rem;font-weight:700;color:var(--silver-200);">${pb.event_label}</div>
                        <div style="font-size:.7rem;color:var(--text-muted);margin-top:.2rem;">${pb.competition_name}</div>
                        <div style="font-size:.68rem;color:var(--text-light-muted);">${pb.competition_date}</div>
                    </div>
                    <div style="font-family:monospace;font-size:1.15rem;font-weight:900;color:#93c5fd;
                                flex-shrink:0;text-align:right;">${pb.time_result}</div>
                </div>
            `).join('');
            bodyEl.innerHTML = `<div style="margin-bottom:.5rem;font-size:.72rem;color:var(--text-muted);">
                🏊 ${athlete.races_count} kali lomba · ⭐ ${athlete.personal_bests.length} nomor PB
            </div>${items}`;
        }
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeMobilePbSheet() {
        document.getElementById('mobile-pb-sheet-overlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    document.getElementById('mobile-pb-sheet-overlay').addEventListener('click', function(e) {
        if (e.target === this) closeMobilePbSheet();
    });

    // ── Report filter sheet ───────────────────────────────
    (function() {
        const overlay = document.getElementById('report-filter-sheet-overlay');
        const openBtn = document.getElementById('open-report-filter-sheet');
        if (openBtn && overlay) {
            openBtn.addEventListener('click', function() {
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            });
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('open');
                    document.body.style.overflow = '';
                }
            });
        }
    })();
</script>
@endsection
