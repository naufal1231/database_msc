@extends('layouts.parent')

@section('title', 'Riwayat Perlombaan - ' . $athlete->name)
@section('header_title', 'Riwayat Perlombaan Anak')

@section('styles')
<style>
    /* ─── PARENT EVENT HISTORY SHOW STYLES ─── */
    .eh-profile-card {
        background: linear-gradient(160deg, rgba(12,24,48,0.9) 0%, rgba(6,15,34,0.95) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .eh-profile-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(200,216,236,0.2) 50%, transparent);
    }
    .eh-big-avatar {
        width: 56px; height: 56px; border-radius: 50%;
        background: rgba(26,107,181,0.25); border: 2px solid rgba(91,168,232,0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem; font-weight: 900; color: #5ba8e8;
        overflow: hidden; flex-shrink: 0;
    }
    .eh-big-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .info-tag {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .25rem .65rem; border-radius: 2rem;
        background: rgba(10,22,40,.5); border: 1px solid var(--glass-border);
        font-size: .75rem; font-weight: 600; color: var(--text-muted);
    }

    /* Stat row */
    .eh-stat-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    .eh-mini-stat {
        background: linear-gradient(160deg, rgba(12,24,48,0.85) 0%, rgba(6,15,34,0.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1rem; padding: 1.15rem;
        text-align: center; position: relative; overflow: hidden;
    }
    .eh-mini-stat::before {
        content:''; position:absolute; top:0; left:0; right:0; height:1px;
        background: linear-gradient(90deg,transparent,rgba(200,216,236,.18) 50%,transparent);
    }
    .eh-mini-stat-val { font-size: 1.85rem; font-weight: 900; letter-spacing: -.03em; line-height: 1; }
    .eh-mini-stat-val.blue { color: #5ba8e8; }
    .eh-mini-stat-val.green { color: #4ade80; }
    .eh-mini-stat-val.gold { color: #e8c96d; }
    .eh-mini-stat-lbl {
        font-size: .67rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: .75px; color: var(--text-muted); margin-top: .35rem;
    }

    /* Personal Best Cards */
    .pb-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: .85rem;
    }
    .pb-card {
        background: linear-gradient(160deg, rgba(12,24,48,0.8) 0%, rgba(6,15,34,0.9) 100%);
        border: 1px solid rgba(201,168,76,.25); border-radius: 1rem; padding: 1.1rem;
        position: relative; overflow: hidden;
        transition: all 0.3s ease;
    }
    .pb-card::before {
        content:''; position:absolute; top:0; left:0; right:0; height:1px;
        background: linear-gradient(90deg,transparent,rgba(201,168,76,.35) 50%,transparent);
    }
    .pb-card-ev   { font-size:.78rem; font-weight:700; color:var(--silver-200,#dde8f4); margin-bottom:.2rem; }
    .pb-card-date { font-size:.68rem; color:var(--text-muted); margin-bottom:.75rem; }
    .pb-card-time { font-family:monospace; font-size:1.6rem; font-weight:900; color:#e8c96d; }
    .pb-card-sub  { font-size:.6rem; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:#c9a84c; margin-top:.15rem; }
    .pb-card-comp { font-size:.68rem; color:var(--text-muted); margin-top:.65rem; padding-top:.55rem; border-top:1px solid rgba(201,168,76,.12); }

    /* Section per event */
    .eh-section {
        background: linear-gradient(160deg,rgba(10,22,40,.8) 0%,rgba(6,15,34,.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem; overflow: hidden; margin-bottom: 1.15rem;
    }
    .eh-section-header {
        background: linear-gradient(135deg,#0369a1 0%,#1e3a5f 100%);
        padding: .85rem 1.5rem; display: flex; align-items: center; justify-content: space-between;
    }
    .eh-section-title { font-size:.92rem; font-weight:700; color:#fff; }
    .eh-section-badge {
        font-size:.7rem; font-weight:700; background:rgba(255,255,255,.15);
        border:1px solid rgba(255,255,255,.2); color:#fff; padding:.2rem .6rem; border-radius:2rem;
    }
    .eh-footer {
        background:rgba(6,15,34,.5); border-top:1px solid var(--glass-border);
        padding:.5rem 1.5rem; font-size:.7rem; color:var(--text-muted);
    }

    /* Clean Difference Labels (No up/down arrows) */
    .time-cell { font-family: monospace; font-size: 1.15rem; font-weight: 900; }
    .time-cell.best { color: #4ade80; }
    .time-cell.normal { color: #5ba8e8; }
    .pb-cell { font-family: monospace; font-size: .88rem; font-weight: 700; color: #a78bfa; }
    .diff-faster { font-family: monospace; font-size: .82rem; font-weight: 800; color: #4ade80; }
    .diff-slower { font-family: monospace; font-size: .82rem; font-weight: 800; color: #f87171; }
    .diff-equal  { font-family: monospace; font-size: .82rem; font-weight: 800; color: var(--text-muted); }
    .diff-first  { font-size: .75rem; font-style: italic; color: var(--text-muted); }

    .status-dns { display: inline-block; padding: .2rem .5rem; border-radius: .35rem; font-size: .7rem; font-weight: 700; background: rgba(248,113,113,.12); border: 1px solid rgba(248,113,113,.2); color: #f87171; }
    .status-dq  { display: inline-block; padding: .2rem .5rem; border-radius: .35rem; font-size: .7rem; font-weight: 700; background: rgba(251,146,60,.12); border: 1px solid rgba(251,146,60,.2); color: #fb923c; }

    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-icon  { font-size: 3rem; opacity: .35; margin-bottom: 1rem; }
    .empty-title { font-size: 1rem; font-weight: 700; color: var(--silver-200,#dde8f4); margin-bottom: .4rem; }
    .empty-desc  { font-size: .82rem; color: var(--text-muted); font-style: italic; }

    .btn-back {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1rem; border-radius: .65rem; font-size: .82rem; font-weight: 600;
        text-decoration: none; color: var(--silver-300,#c8d8ec);
        border: 1px solid rgba(91,168,232,.2); background: transparent; transition: all .2s ease;
    }
    .btn-back:hover { background: rgba(91,168,232,.1); color: #edf4fc; }

    /* ── Mobile Layout Adjustments ── */
    @media (max-width: 768px) {
        .desktop-only { display: none !important; }
        .mobile-only  { display: block !important; }

        /* PB Swipeable Strip */
        .pb-cards-grid {
            display: flex;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x mandatory;
            gap: 0.75rem;
            padding-bottom: 0.5rem;
        }
        .pb-cards-grid::-webkit-scrollbar { height: 3px; }
        .pb-cards-grid::-webkit-scrollbar-thumb { background: rgba(91,168,232,0.25); border-radius: 2px; }
        .pb-card {
            min-width: 180px;
            scroll-snap-align: start;
        }
        .pb-card-time { font-size: 1.4rem; }

        /* Stat row: 3 compact cols */
        .eh-stat-row { grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
        .eh-mini-stat { padding: 0.875rem 0.625rem; }
        .eh-mini-stat-val { font-size: 1.5rem; }

        /* Accordion per event */
        .eh-section { border-radius: 0.875rem; }
        .eh-section-header { padding: 0.75rem 1rem; border-radius: 0; cursor: pointer; }
        .eh-section-title { font-size: 0.85rem; }
    }

    @media (min-width: 769px) {
        .desktop-only { display: block !important; }
        .mobile-only  { display: none !important; }
    }
</style>
@endsection

@section('content')
@php
    $filterStroke = request('stroke', '');
    $filterYear   = request('year', '');
    $mobileFilterCount = ($filterStroke ? 1 : 0) + ($filterYear ? 1 : 0);
    $filteredSections = collect($historyByEvent)->filter(function($evData) use ($filterStroke, $filterYear) {
        if ($filterStroke && $evData['stroke'] !== $filterStroke) return false;
        if ($filterYear) {
            $hasYear = collect($evData['history'])->contains(fn($r) => substr($r['competition_date'],0,4) === $filterYear);
            if (!$hasYear) return false;
        }
        return true;
    });
@endphp

{{-- ═══════════════════════════════════════════════════════════════════
     MOBILE LAYOUT (Sesuai Spesifikasi design.md untuk Akun Ortu)
     ═══════════════════════════════════════════════════════════════════ --}}
<div class="mobile-only mobile-has-action-bar" style="display: flex; flex-direction: column; gap: 0.875rem;">

    {{-- ── 1. Profile Hero Anak ── --}}
    <div class="data-card" style="padding: 0; overflow: hidden;">
        <div style="height: 4px; background: linear-gradient(90deg, #0369a1 0%, #5ba8e8 50%, #e8c96d 100%);"></div>
        <div style="padding: 1.25rem 1rem;">
            <div style="display: flex; align-items: center; gap: 0.875rem; margin-bottom: 0.85rem;">
                <div class="eh-big-avatar" style="width: 52px; height: 52px; font-size: 1.2rem;">
                    @if($athlete->photo)
                        <img src="{{ asset('storage/'.$athlete->photo) }}" alt="{{ $athlete->name }}">
                    @else
                        {{ strtoupper(substr($athlete->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: #fff;">{{ $athlete->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">
                        📊 Riwayat Perlombaan Anak
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                <span class="info-tag">🏫 {{ $athlete->currentClass->name ?? '-' }}</span>
                <span class="info-tag">📅 KU {{ $athlete->ku ?? '-' }}</span>
                <span class="info-tag">{{ $athlete->gender === 'L' ? '♂ PA' : '♀ PI' }}</span>
            </div>
        </div>
    </div>

    {{-- ── 2. Stat Row Compact ── --}}
    <div class="eh-stat-row">
        <div class="eh-mini-stat">
            <div class="eh-mini-stat-val blue">{{ count($historyRows) }}</div>
            <div class="eh-mini-stat-lbl">Nomor</div>
        </div>
        <div class="eh-mini-stat">
            <div class="eh-mini-stat-val green">{{ count($historyByEvent) }}</div>
            <div class="eh-mini-stat-lbl">Kombinasi</div>
        </div>
        <div class="eh-mini-stat">
            <div class="eh-mini-stat-val gold">{{ count($currentPBs) }}</div>
            <div class="eh-mini-stat-lbl">PB</div>
        </div>
    </div>

    {{-- ── 3. Accordion: Personal Best Saat Ini (Swipeable Strip) ── --}}
    @if(count($currentPBs) > 0)
    <div class="m-section-accordion open">
        <div class="m-section-header">
            <span class="m-section-header-title">🏅 Personal Best ({{ count($currentPBs) }} nomor)</span>
            <span class="m-section-chevron">▾</span>
        </div>
        <div class="m-section-body">
            <div class="m-section-body-inner" style="padding: 0.875rem;">
                <div class="pb-cards-grid">
                    @foreach($currentPBs as $pb)
                    <div class="pb-card">
                        <div class="pb-card-ev">🏊 {{ $pb['event_label'] }}</div>
                        <div class="pb-card-date">{{ $pb['competition_date'] }}</div>
                        <div class="pb-card-time">{{ $pb['time_result'] }}</div>
                        <div class="pb-card-sub">Waktu Terbaik</div>
                        <div class="pb-card-comp">📍 {{ $pb['competition_name'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── 4. Mobile Filter Bar & Active Chips ── --}}
    @if(count($historyRows) > 0)
    <div>
        <div class="mobile-header-row" style="margin-bottom: 0.5rem; display: flex; gap: 0.5rem;">
            <span style="flex: 1; display: flex; align-items: center;
                         padding: 0.6rem 0.875rem; background: var(--navy-800);
                         border: 1px solid var(--glass-border); border-radius: 0.65rem;
                         font-size: 0.8rem; color: var(--text-muted);">
                📋 {{ count($historyRows) }} riwayat · {{ $filteredSections->count() }} nomor
            </span>
            <button type="button" id="open-parent-filter-sheet" class="mobile-filter-btn" style="cursor: pointer;">
                ⚙ Filter
                @if($mobileFilterCount > 0)
                    <span class="mobile-filter-btn-badge">{{ $mobileFilterCount }}</span>
                @endif
            </button>
        </div>

        @if($filterStroke || $filterYear)
        <div class="active-filter-chips" style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
            @if($filterStroke)
                <span class="active-chip">
                    Gaya {{ ucfirst($filterStroke) }}
                    <a href="{{ request()->fullUrlWithQuery(['stroke' => null]) }}" class="active-chip-remove" style="text-decoration: none;">✕</a>
                </span>
            @endif
            @if($filterYear)
                <span class="active-chip">
                    Tahun {{ $filterYear }}
                    <a href="{{ request()->fullUrlWithQuery(['year' => null]) }}" class="active-chip-remove" style="text-decoration: none;">✕</a>
                </span>
            @endif
            <a href="{{ route('parent.event-history.show', $athlete->id) }}" style="font-size: 0.72rem; color: #f87171; align-self: center; margin-left: 0.25rem;">
                Reset Filter
            </a>
        </div>
        @endif
    </div>
    @endif

    {{-- ── 5. History Sections per Event (Mobile Accordion) ── --}}
    @if($filteredSections->count() > 0)
        @foreach($filteredSections as $evKey => $evData)
        @php
            $sectionRows = collect($evData['history']);
            if ($filterYear) {
                $sectionRows = $sectionRows->filter(fn($r) => substr($r['competition_date'],0,4) === $filterYear);
            }
        @endphp
        <div class="m-section-accordion open">
            <div class="m-section-header"
                 style="background: linear-gradient(135deg, rgba(3,105,161,0.6) 0%, rgba(30,58,95,0.7) 100%);">
                <span class="m-section-header-title">🏊 {{ $evData['event_label'] }}</span>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 0.65rem; font-weight: 700; background: rgba(255,255,255,0.15);
                                 color: #fff; padding: 0.15rem 0.5rem; border-radius: 99px;">
                        {{ $sectionRows->count() }}×
                    </span>
                    <span class="m-section-chevron">▾</span>
                </div>
            </div>
            <div class="m-section-body">
                <div class="m-section-body-inner" style="padding: 0.75rem;">
                    @foreach($sectionRows as $row)
                    <div style="padding: 0.75rem 0; border-bottom: 1px solid var(--glass-border);">
                        {{-- Row: Event name + date --}}
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                            <div style="font-size: 0.82rem; font-weight: 700; color: var(--silver-200); flex: 1; padding-right: 0.5rem;">
                                {{ $row['competition_name'] }}
                            </div>
                            <div style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; flex-shrink: 0;">
                                {{ $row['competition_date_formatted'] }}
                            </div>
                        </div>

                        {{-- Row: Result + rank + diff (Clean No-Arrow) --}}
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            {{-- Result --}}
                            @if($row['result_status'] === 'finish')
                                <span class="time-cell {{ $row['is_first_valid_result'] ? 'best' : 'normal' }}"
                                      style="font-size: 1.05rem;">
                                    {{ $row['time_result'] }}
                                </span>
                                @if($row['is_first_valid_result'])
                                    <span style="font-size: 0.62rem; color: #4ade80; font-weight: 700; background: rgba(74,222,128,0.1); padding: 0.1rem 0.4rem; border-radius: 99px;">★ PB Awal</span>
                                @endif
                            @elseif($row['result_status'] === 'DNS')
                                <span class="status-dns">DNS</span>
                            @elseif($row['result_status'] === 'DQ')
                                <span class="status-dq">DQ</span>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.8rem;">—</span>
                            @endif

                            {{-- Rank --}}
                            @if($row['rank'])
                                <span style="font-weight: 800; font-size: 0.85rem;
                                             color: {{ $row['rank'] == 1 ? '#e8c96d' : ($row['rank'] == 2 ? '#9ca3af' : ($row['rank'] == 3 ? '#fb923c' : 'var(--text-muted)')) }};">
                                    #{{ $row['rank'] }}
                                </span>
                            @endif

                            {{-- Difference without arrows --}}
                            @if($row['result_status'] === 'finish' && !$row['is_first_valid_result'])
                                @if($row['difference'] !== null)
                                    @if($row['is_faster'] === true)
                                        <span class="diff-faster" style="font-size: 0.82rem;">-{{ $row['difference'] }}</span>
                                    @elseif($row['is_faster'] === false && $row['difference'] !== '00.00.00')
                                        <span class="diff-slower" style="font-size: 0.82rem;">+{{ $row['difference'] }}</span>
                                    @else
                                        <span class="diff-equal" style="font-size: 0.82rem;">00.00.00</span>
                                    @endif
                                @endif
                            @endif
                        </div>

                        {{-- PB before event --}}
                        @if($row['result_status'] === 'finish' && $row['pb_before_event'] && !$row['is_first_valid_result'])
                        <div style="margin-top: 0.35rem; font-size: 0.68rem; color: var(--text-light-muted);">
                            PB sebelumnya: <span class="pb-cell" style="font-size: 0.72rem;">{{ $row['pb_before_event'] }}</span>
                        </div>
                        @endif
                    </div>
                    @endforeach

                    {{-- Footer note (Clean No-Arrow) --}}
                    <div style="font-size: 0.65rem; color: var(--text-light-muted); padding-top: 0.5rem; text-align: center;">
                        <span style="color: #4ade80;">- lebih cepat</span> · <span style="color: #f87171;">+ lebih lambat</span> · #N = peringkat
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="data-card">
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <div class="empty-title">Tidak Ada Data</div>
                <p class="empty-desc">
                    @if($filterStroke || $filterYear)
                        Tidak ada riwayat yang sesuai dengan filter yang dipilih.
                    @else
                        {{ $athlete->name }} belum memiliki riwayat perlombaan yang tercatat.
                    @endif
                </p>
            </div>
        </div>
    @endif

    {{-- ── 6. Filter Bottom Sheet Modal (Mobile) Sesuai design.md ── --}}
    <div class="filter-bottom-sheet-overlay" id="parent-filter-sheet-overlay" style="display: none;">
        <div class="filter-bottom-sheet">
            <div class="filter-bottom-sheet-handle"></div>
            <div class="filter-bottom-sheet-title">⚙ Filter Riwayat Lomba</div>

            <form method="GET" action="{{ route('parent.event-history.show', $athlete->id) }}">
                {{-- Gaya Renang --}}
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--silver-200); margin-bottom: 0.4rem; display: block;">
                        Gaya Renang
                    </label>
                    <select name="stroke" class="form-input" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;">
                        <option value="">Semua Gaya Renang</option>
                        @foreach(['bebas'=>'Gaya Bebas','dada'=>'Gaya Dada','punggung'=>'Gaya Punggung','kupu'=>'Gaya Kupu-Kupu','ganti'=>'Gaya Ganti','estafet'=>'Estafet'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $filterStroke === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun Perlombaan --}}
                @php
                    $years = collect($historyRows)
                        ->map(fn($r) => substr($r['competition_date'], 0, 4))
                        ->unique()->sortDesc()->values();
                @endphp
                @if($years->count() > 0)
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--silver-200); margin-bottom: 0.4rem; display: block;">
                        Tahun Kejuaraan
                    </label>
                    <select name="year" class="form-input" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem;">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ $filterYear === $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; padding: 0.85rem; border-radius: 0.75rem; font-weight: 700; justify-content: center;">
                        Terapkan Filter
                    </button>
                    @if($filterStroke || $filterYear)
                        <a href="{{ route('parent.event-history.show', $athlete->id) }}" class="btn btn-accent" style="padding: 0.85rem 1.25rem; border-radius: 0.75rem; text-decoration: none; justify-content: center; font-weight: 600;">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── 7. Sticky Action Bar di Mobile ── --}}
    <div class="mobile-action-bar">
        <a href="{{ route('parent.dashboard') }}" class="btn-action-mobile outline" style="flex: 1; justify-content: center;">
            ◀ Kembali ke Dashboard
        </a>
    </div>

</div>{{-- /.mobile-only --}}


{{-- ═══════════════════════════════════════════════════════════════════
     DESKTOP LAYOUT (Tampilan Desktop Asli Utuh & Fungsional)
     ═══════════════════════════════════════════════════════════════════ --}}
<div class="desktop-only" style="display:flex; flex-direction:column; gap:1.75rem;">

    {{-- Desktop Profile --}}
    <div class="eh-profile-card">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1rem;flex:1;">
                <div class="eh-big-avatar">
                    @if($athlete->photo)
                        <img src="{{ asset('storage/'.$athlete->photo) }}" alt="{{ $athlete->name }}">
                    @else
                        {{ strtoupper(substr($athlete->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <h2 style="font-size:1.2rem;font-weight:800;color:var(--silver-200,#dde8f4);margin-bottom:.45rem;">📊 {{ $athlete->name }}</h2>
                    <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
                        <span class="info-tag">🏫 {{ $athlete->currentClass->name ?? '-' }}</span>
                        <span class="info-tag">KU {{ $athlete->ku ?? '-' }}</span>
                        <span class="info-tag">{{ $athlete->gender === 'L' ? '♂ PA' : '♀ PI' }}</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('parent.dashboard') }}" class="btn-back">◀ Dashboard</a>
        </div>
    </div>

    {{-- Desktop Stats --}}
    <div class="eh-stat-row">
        <div class="eh-mini-stat"><div class="eh-mini-stat-val blue">{{ count($historyRows) }}</div><div class="eh-mini-stat-lbl">Nomor Diikuti</div></div>
        <div class="eh-mini-stat"><div class="eh-mini-stat-val green">{{ count($historyByEvent) }}</div><div class="eh-mini-stat-lbl">Kombinasi Gaya</div></div>
        <div class="eh-mini-stat"><div class="eh-mini-stat-val gold">{{ count($currentPBs) }}</div><div class="eh-mini-stat-lbl">Personal Best</div></div>
    </div>

    {{-- Desktop PBs --}}
    @if(count($currentPBs) > 0)
    <div class="data-card">
        <div class="card-header">
            <div>
                <h3 class="card-title">🏅 Personal Best Saat Ini</h3>
                <p style="font-size:.78rem;color:var(--text-muted);margin-top:.2rem;">Waktu terbaik untuk setiap nomor lomba</p>
            </div>
            <span style="background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.25);color:#e8c96d;padding:.35rem .85rem;border-radius:2rem;font-size:.76rem;font-weight:700;">{{ count($currentPBs) }} PB</span>
        </div>
        <div class="pb-cards-grid">
            @foreach($currentPBs as $pb)
            <div class="pb-card">
                <div class="pb-card-ev">🏊 {{ $pb['event_label'] }}</div>
                <div class="pb-card-date">{{ $pb['competition_date'] }}</div>
                <div class="pb-card-time">{{ $pb['time_result'] }}</div>
                <div class="pb-card-sub">Waktu Terbaik</div>
                <div class="pb-card-comp">📍 {{ $pb['competition_name'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Desktop History --}}
    @if(count($historyByEvent) > 0)
        @foreach($historyByEvent as $evData)
        <div class="eh-section">
            <div class="eh-section-header">
                <span class="eh-section-title">🏊 {{ $evData['event_label'] }}</span>
                <span class="eh-section-badge">{{ count($evData['history']) }} Riwayat</span>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Perlombaan</th>
                            <th style="text-align:center;">Tanggal</th>
                            <th style="text-align:center;">Hasil</th>
                            <th style="text-align:center;">PB Sebelumnya</th>
                            <th style="text-align:center;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evData['history'] as $row)
                        <tr>
                            <td><div style="font-weight:700;color:var(--silver-200,#dde8f4);font-size:.85rem;">{{ $row['competition_name'] }}</div></td>
                            <td style="text-align:center;white-space:nowrap;font-size:.85rem;">{{ $row['competition_date_formatted'] }}</td>
                            <td style="text-align:center;">
                                @if($row['result_status']==='finish')
                                    <span class="time-cell {{ $row['is_first_valid_result'] ? 'best' : 'normal' }}">{{ $row['time_result'] }}</span>
                                    @if($row['is_first_valid_result'])<div style="font-size:.6rem;color:#4ade80;font-weight:700;">★ PB Awal</div>@endif
                                @elseif($row['result_status']==='DNS')<span class="status-dns">DNS</span>
                                @elseif($row['result_status']==='DQ')<span class="status-dq">DQ</span>
                                @else<span style="color:var(--text-muted);font-size:.78rem;">—</span>@endif
                            </td>
                            <td style="text-align:center;">
                                @if($row['result_status']==='finish' && $row['pb_before_event'])
                                    <span class="pb-cell">{{ $row['pb_before_event'] }}</span>
                                @else
                                    <span style="color:var(--text-muted);font-size:.78rem;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($row['result_status']==='finish')
                                    @if($row['is_first_valid_result'])<span class="diff-first">PB Awal</span>
                                    @elseif($row['difference']!==null)
                                        @if($row['is_faster']===true)<span class="diff-faster">-{{ $row['difference'] }}</span>
                                        @elseif($row['difference']==='00.00.00')<span class="diff-equal">00.00.00</span>
                                        @else<span class="diff-slower">+{{ $row['difference'] }}</span>@endif
                                    @else<span style="color:var(--text-muted);font-size:.78rem;">—</span>@endif
                                @else<span style="color:var(--text-muted);font-size:.78rem;">—</span>@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="eh-footer">
                <span style="color:#4ade80;">- = lebih cepat</span> &bull;
                <span style="color:#f87171;">+ = lebih lambat</span> &bull;
                PB Awal = belum ada data sebelumnya
            </div>
        </div>
        @endforeach
    @else
        <div class="data-card">
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <div class="empty-title">Belum Ada Riwayat</div>
                <p class="empty-desc">{{ $athlete->name }} belum memiliki catatan perlombaan.</p>
            </div>
        </div>
    @endif

</div>{{-- /.desktop-only --}}
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Accordion toggle ────────────────────────────────
    document.querySelectorAll('.m-section-header').forEach(function (header) {
        header.addEventListener('click', function () {
            this.closest('.m-section-accordion').classList.toggle('open');
        });
    });

    // ── Show-page filter bottom sheet (Parent Portal) ───
    const overlay = document.getElementById('parent-filter-sheet-overlay');
    const openBtn = document.getElementById('open-parent-filter-sheet');
    if (openBtn && overlay) {
        openBtn.addEventListener('click', function () {
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        });
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
        // Swipe-down to close
        let startY = 0;
        const sheet = overlay.querySelector('.filter-bottom-sheet');
        if (sheet) {
            sheet.addEventListener('touchstart', function (e) { startY = e.touches[0].clientY; }, { passive: true });
            sheet.addEventListener('touchend', function (e) {
                if (e.changedTouches[0].clientY - startY > 80) {
                    overlay.classList.remove('open');
                    document.body.style.overflow = '';
                }
            }, { passive: true });
        }
    }
});
</script>
@endsection