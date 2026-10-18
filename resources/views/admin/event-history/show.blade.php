@extends('layouts.admin')

@section('title', 'Riwayat Event - ' . $athlete->name)
@section('header_title', 'Riwayat Perlombaan Atlet')

@section('styles')
<style>
    /* ─── EVENT HISTORY SHOW ─── */
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
        width: 64px; height: 64px; border-radius: 50%;
        background: rgba(26,107,181,0.25); border: 3px solid rgba(91,168,232,0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; font-weight: 900; color: #5ba8e8;
        overflow: hidden; flex-shrink: 0;
    }
    .eh-big-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .info-tag {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.3rem 0.75rem; border-radius: 2rem;
        background: rgba(10,22,40,0.5); border: 1px solid var(--glass-border);
        font-size: 0.78rem; font-weight: 600; color: var(--text-muted);
    }

    /* Stat cards */
    .eh-stat-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    .eh-mini-stat {
        background: linear-gradient(160deg, rgba(12,24,48,0.85) 0%, rgba(6,15,34,0.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1rem; padding: 1.25rem;
        position: relative; overflow: hidden;
        text-align: center;
    }
    .eh-mini-stat::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(200,216,236,0.18) 50%, transparent);
    }
    .eh-mini-stat-val { font-size: 2rem; font-weight: 900; letter-spacing: -0.03em; line-height: 1; }
    .eh-mini-stat-val.blue   { color: #5ba8e8; }
    .eh-mini-stat-val.green  { color: #4ade80; }
    .eh-mini-stat-val.gold   { color: #e8c96d; }
    .eh-mini-stat-lbl {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.75px; color: var(--text-muted); margin-top: 0.4rem;
    }

    /* Filter bar */
    .eh-filter-row {
        display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;
        background: rgba(10,22,40,0.5); border: 1px solid var(--glass-border);
        border-radius: 0.85rem; padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .eh-filter-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .eh-filter-group label { font-size: 0.72rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }

    /* PB Cards */
    .pb-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.85rem;
    }
    .pb-card {
        background: linear-gradient(160deg, rgba(12,24,48,0.8) 0%, rgba(6,15,34,0.9) 100%);
        border: 1px solid rgba(201,168,76,0.25);
        border-radius: 1rem; padding: 1.15rem;
        position: relative; overflow: hidden;
        transition: all 0.3s ease;
    }
    .pb-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
        background: linear-gradient(90deg, transparent, rgba(201,168,76,0.35) 50%, transparent);
    }
    .pb-card:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(0,0,0,0.3); }
    .pb-card-label-ev { font-size: 0.8rem; font-weight: 700; color: var(--silver-200,#dde8f4); margin-bottom: 0.2rem; }
    .pb-card-date     { font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.85rem; }
    .pb-card-time     { font-family: monospace; font-size: 1.75rem; font-weight: 900; color: #e8c96d; letter-spacing: 0.02em; }
    .pb-card-sublabel { font-size: 0.62rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #c9a84c; margin-top: 0.15rem; }
    .pb-card-comp     { font-size: 0.7rem; color: var(--text-muted); margin-top: 0.75rem; padding-top: 0.6rem; border-top: 1px solid rgba(201,168,76,0.12); }

    /* Main history table */
    .eh-section {
        background: linear-gradient(160deg, rgba(10,22,40,0.8) 0%, rgba(6,15,34,0.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem; overflow: hidden; margin-bottom: 1.25rem;
    }
    .eh-section-header {
        background: linear-gradient(135deg, #0369a1 0%, #1e3a5f 100%);
        padding: 0.9rem 1.5rem;
        display: flex; align-items: center; justify-content: space-between;
    }
    .eh-section-title { font-size: 0.95rem; font-weight: 700; color: #fff; }
    .eh-section-badge {
        font-size: 0.72rem; font-weight: 700;
        background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);
        color: #fff; padding: 0.2rem 0.65rem; border-radius: 2rem;
    }
    .eh-table-footer {
        background: rgba(6,15,34,0.5); border-top: 1px solid var(--glass-border);
        padding: 0.55rem 1.5rem; font-size: 0.72rem; color: var(--text-muted);
    }

    /* Result cells */
    .time-cell      { font-family: monospace; font-size: 1.25rem; font-weight: 900; }
    .time-cell.best { color: #4ade80; }
    .time-cell.normal { color: #5ba8e8; }
    .pb-cell        { font-family: monospace; font-size: 0.9rem; font-weight: 700; color: #a78bfa; }
    .diff-faster    { font-family: monospace; font-size: 0.85rem; font-weight: 800; color: #4ade80; }
    .diff-slower    { font-family: monospace; font-size: 0.85rem; font-weight: 800; color: #f87171; }
    .diff-equal     { font-family: monospace; font-size: 0.85rem; font-weight: 800; color: var(--text-muted); }
    .diff-first     { font-size: 0.78rem; font-style: italic; color: var(--text-muted); }

    .status-dns { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 0.4rem; font-size: 0.72rem; font-weight: 700; background: rgba(248,113,113,0.12); border: 1px solid rgba(248,113,113,0.2); color: #f87171; }
    .status-dq  { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 0.4rem; font-size: 0.72rem; font-weight: 700; background: rgba(251,146,60,0.12); border: 1px solid rgba(251,146,60,0.2); color: #fb923c; }

    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-icon  { font-size: 3.5rem; opacity: 0.35; margin-bottom: 1rem; }
    .empty-title { font-size: 1.05rem; font-weight: 700; color: var(--silver-200,#dde8f4); margin-bottom: 0.4rem; }
    .empty-desc  { font-size: 0.85rem; color: var(--text-muted); font-style: italic; max-width: 380px; margin: 0 auto; line-height: 1.6; }

    .btn-outline-back {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.55rem 1.1rem; border-radius: 0.65rem;
        font-size: 0.85rem; font-weight: 600; text-decoration: none;
        color: var(--silver-300,#c8d8ec); border: 1px solid rgba(91,168,232,0.2);
        background: transparent; transition: all 0.2s ease;
    }
    .btn-outline-back:hover { background: rgba(91,168,232,0.1); color: #edf4fc; }
    .btn-primary-link {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.55rem 1.15rem; border-radius: 0.65rem;
        font-size: 0.85rem; font-weight: 700; text-decoration: none;
        color: #0a1628;
        background: linear-gradient(135deg, #5ba8e8 0%, #2684d4 100%);
        box-shadow: 0 4px 16px rgba(38,132,212,0.25);
        transition: all 0.2s ease;
    }
    .btn-primary-link:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(38,132,212,0.4); }

    /* ── Mobile PB horizontal scroll strip ── */
    @media (max-width: 768px) {
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

        /* eh-section (per event) accordion on mobile */
        .eh-section { border-radius: 0.875rem; }
        .eh-section-header { padding: 0.75rem 1rem; border-radius: 0; cursor: pointer; }
        .eh-section-title { font-size: 0.85rem; }

        .eh-filter-row { display: none; } /* replaced by bottom sheet on mobile */
    }
</style>
@endsection

@section('content')
@php
    $filterStroke = request('stroke', '');
    $filterYear   = request('year', '');
    $filterEvent  = request('event_name', '');
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

{{-- ═══════════════════════════════════════
     MOBILE LAYOUT
═══════════════════════════════════════ --}}
<div class="mobile-only mobile-has-action-bar" style="display: flex; flex-direction: column; gap: 0.875rem;">

    {{-- ── Profile Hero ──────────────────────── --}}
    <div class="data-card" style="padding: 0; overflow: hidden;">
        <div style="height: 4px; background: linear-gradient(90deg, #0369a1 0%, #5ba8e8 50%, #e8c96d 100%);"></div>
        <div style="padding: 1.25rem 1rem;">
            <div style="display: flex; align-items: center; gap: 0.875rem; margin-bottom: 1rem;">
                <div class="eh-big-avatar" style="width: 54px; height: 54px; font-size: 1.2rem;">
                    @if($athlete->photo)
                        <img src="{{ asset('storage/'.$athlete->photo) }}" alt="{{ $athlete->name }}">
                    @else
                        {{ strtoupper(substr($athlete->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <div style="font-size: 1rem; font-weight: 800; color: var(--silver-100);">{{ $athlete->name }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">
                        📊 Riwayat Perlombaan
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                <span class="info-tag">🏫 {{ $athlete->currentClass->name ?? '-' }}</span>
                <span class="info-tag">📅 KU {{ $athlete->ku ?? '-' }}</span>
                <span class="info-tag">{{ $athlete->gender === 'L' ? '♂ PA' : '♀ PI' }}</span>
                @if($athlete->parentUser)
                    <span class="info-tag">👤 {{ $athlete->parentUser->name }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Stat Row ──────────────────────────── --}}
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

    {{-- ── Accordion: Personal Best ──────────── --}}
    @if(count($currentPBs) > 0)
    <div class="m-section-accordion open">
        <div class="m-section-header">
            <span class="m-section-header-title">🏅 Personal Best ({{ count($currentPBs) }} nomor)</span>
            <span class="m-section-chevron">▾</span>
        </div>
        <div class="m-section-body">
            <div class="m-section-body-inner" style="padding: 0.875rem;">
                <div class="pb-cards-grid">
                    @foreach($currentPBs as $key => $pb)
                    <div class="pb-card">
                        <div class="pb-card-label-ev">🏊 {{ $pb['event_label'] }}</div>
                        <div class="pb-card-date">{{ $pb['competition_date'] }}</div>
                        <div class="pb-card-time">{{ $pb['time_result'] }}</div>
                        <div class="pb-card-sublabel">Waktu Terbaik</div>
                        <div class="pb-card-comp">📍 {{ $pb['competition_name'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Mobile filter trigger (for event history) ── --}}
    @if(count($historyRows) > 0)
    <div>
        <div class="mobile-header-row" style="margin-bottom: 0.5rem;">
            <span style="flex: 1; display: flex; align-items: center;
                         padding: 0.6rem 0.875rem; background: var(--navy-800);
                         border: 1px solid var(--glass-border); border-radius: 0.65rem;
                         font-size: 0.8rem; color: var(--text-muted);">
                📋 {{ count($historyRows) }} riwayat · {{ $filteredSections->count() }} nomor
            </span>
            <button type="button" id="open-show-filter-sheet" class="mobile-filter-btn">
                ⚙ Filter
                @if($mobileFilterCount > 0)
                    <span class="mobile-filter-btn-badge">{{ $mobileFilterCount }}</span>
                @endif
            </button>
        </div>

        @if($filterStroke || $filterYear)
        <div class="active-filter-chips">
            @if($filterStroke)
                <span class="active-chip">
                    Gaya {{ ucfirst($filterStroke) }}
                    <a href="{{ request()->fullUrlWithQuery(['stroke' => null]) }}" class="active-chip-remove">✕</a>
                </span>
            @endif
            @if($filterYear)
                <span class="active-chip">
                    Tahun {{ $filterYear }}
                    <a href="{{ request()->fullUrlWithQuery(['year' => null]) }}" class="active-chip-remove">✕</a>
                </span>
            @endif
        </div>
        @endif
    </div>
    @endif

    {{-- ── History Sections per Event (Mobile Accordion) ── --}}
    @if($filteredSections->count() > 0)
        @foreach($filteredSections as $evKey => $evData)
        @php
            $sectionRows = collect($evData['history']);
            if ($filterYear) {
                $sectionRows = $sectionRows->filter(fn($r) => substr($r['competition_date'],0,4) === $filterYear);
            }
        @endphp
        <div class="m-section-accordion">
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
                            <div style="font-size: 0.8rem; font-weight: 700; color: var(--silver-200); flex: 1; padding-right: 0.5rem;">
                                {{ $row['competition_name'] }}
                            </div>
                            <div style="font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; flex-shrink: 0;">
                                {{ $row['competition_date_formatted'] }}
                            </div>
                        </div>

                        {{-- Row: Result + rank + diff --}}
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                            {{-- Result --}}
                            @if($row['result_status'] === 'finish')
                                <span class="time-cell {{ $row['is_first_valid_result'] ? 'best' : 'normal' }}"
                                      style="font-size: 1rem;">
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

                            {{-- Diff --}}
                            @if($row['result_status'] === 'finish' && !$row['is_first_valid_result'])
                                @if($row['difference'] !== null)
                                    @if($row['is_faster'] === true)
                                        <span class="diff-faster" style="font-size: 0.78rem;">▲ {{ $row['difference'] }}</span>
                                    @elseif($row['is_faster'] === false && $row['difference'] !== '00.00.00')
                                        <span class="diff-slower" style="font-size: 0.78rem;">▼ {{ $row['difference'] }}</span>
                                    @else
                                        <span class="diff-equal" style="font-size: 0.78rem;">= PB</span>
                                    @endif
                                @endif
                            @endif
                        </div>

                        {{-- PB before event (small) --}}
                        @if($row['result_status'] === 'finish' && $row['pb_before_event'] && !$row['is_first_valid_result'])
                        <div style="margin-top: 0.35rem; font-size: 0.68rem; color: var(--text-light-muted);">
                            PB sebelumnya: <span class="pb-cell" style="font-size: 0.72rem;">{{ $row['pb_before_event'] }}</span>
                        </div>
                        @endif
                    </div>
                    @endforeach

                    {{-- Footer note --}}
                    <div style="font-size: 0.65rem; color: var(--text-light-muted); padding-top: 0.5rem; text-align: center;">
                        ▲ lebih cepat · ▼ lebih lambat · #N = peringkat
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
                        Atlet ini belum memiliki riwayat perlombaan yang tercatat.
                    @endif
                </p>
            </div>
        </div>
    @endif

    <div style="height: 0.5rem;"></div>
</div>

{{-- ── Sticky Action Bar (mobile) ──────────── --}}
<div class="mobile-action-bar">
    <a href="{{ route('event-history.index') }}" class="btn btn-primary"
       style="background: var(--text-muted); color: #fff;">
        ◀ Kembali
    </a>
    <a href="{{ route('athletes.show', $athlete) }}" class="btn btn-primary"
       style="background: linear-gradient(135deg, #5ba8e8 0%, #2684d4 100%); color: #fff;">
        👤 Profil
    </a>
    @if($athlete->parentUser && $athlete->parentUser->phone)
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $athlete->parentUser->phone) }}"
       target="_blank" rel="noopener"
       class="btn btn-primary"
       style="background: #25D366; color: #fff;">
        💬 WA
    </a>
    @endif
</div>

{{-- ── Filter Bottom Sheet (mobile show page) ── --}}
<div class="filter-bottom-sheet-overlay" id="show-filter-sheet-overlay">
    <div class="filter-bottom-sheet">
        <div class="filter-sheet-handle"></div>
        <div class="filter-sheet-title">⚙ Filter Riwayat Event</div>

        <form method="GET" action="{{ route('event-history.show', $athlete) }}">
            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="m_stroke">Gaya Renang</label>
                <select name="stroke" id="m_stroke" class="form-input">
                    <option value="">Semua Gaya</option>
                    @foreach(['bebas'=>'Bebas','dada'=>'Dada','kupu'=>'Kupu-Kupu','punggung'=>'Punggung','ganti'=>'Ganti'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $filterStroke === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="m_year">Tahun Event</label>
                <select name="year" id="m_year" class="form-input">
                    <option value="">Semua Tahun</option>
                    @php $years = collect($historyRows)->map(fn($r) => substr($r['competition_date'],0,4))->unique()->sortDesc(); @endphp
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ $filterYear === $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-sheet-actions">
                <a href="{{ route('event-history.show', $athlete) }}" class="btn btn-accent"
                   style="text-align: center; background-color: var(--text-muted); color: #fff;">
                    Reset
                </a>
                <button type="submit" class="btn btn-primary">Terapkan</button>
            </div>
        </form>
    </div>
</div>


{{-- ═══════════════════════════════════════
     DESKTOP LAYOUT (unchanged)
═══════════════════════════════════════ --}}
<div style="display:flex;flex-direction:column;gap:1.75rem;" class="desktop-only">

    {{-- ─── Profile Header ─── --}}
    <div class="eh-profile-card">
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1.25rem;">
            <div style="display:flex;align-items:center;gap:1.25rem;flex:1;">
                <div class="eh-big-avatar">
                    @if($athlete->photo)
                        <img src="{{ asset('storage/'.$athlete->photo) }}" alt="{{ $athlete->name }}">
                    @else
                        {{ strtoupper(substr($athlete->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <h2 style="font-size:1.35rem;font-weight:800;color:var(--silver-200,#dde8f4);margin-bottom:.5rem;">
                        📊 {{ $athlete->name }}
                    </h2>
                    <div style="display:flex;flex-wrap:wrap;gap:.45rem;">
                        <span class="info-tag">🏫 {{ $athlete->currentClass->name ?? '-' }}</span>
                        <span class="info-tag">📅 KU {{ $athlete->ku ?? '-' }}</span>
                        <span class="info-tag">{{ $athlete->gender === 'L' ? '♂ PA' : '♀ PI' }}</span>
                        @if($athlete->parentUser)
                            <span class="info-tag">👤 {{ $athlete->parentUser->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:.65rem;flex-wrap:wrap;">
                <a href="{{ route('athletes.show', $athlete) }}" class="btn-primary-link">👤 Profil Atlet</a>
                <a href="{{ route('event-history.index') }}" class="btn-outline-back">◀ Kembali</a>
            </div>
        </div>
    </div>

    {{-- ─── Summary Stats ─── --}}
    <div class="eh-stat-row">
        <div class="eh-mini-stat">
            <div class="eh-mini-stat-val blue">{{ count($historyRows) }}</div>
            <div class="eh-mini-stat-lbl">Total Nomor Diikuti</div>
        </div>
        <div class="eh-mini-stat">
            <div class="eh-mini-stat-val green">{{ count($historyByEvent) }}</div>
            <div class="eh-mini-stat-lbl">Kombinasi Gaya & Jarak</div>
        </div>
        <div class="eh-mini-stat">
            <div class="eh-mini-stat-val gold">{{ count($currentPBs) }}</div>
            <div class="eh-mini-stat-lbl">Personal Best Tercatat</div>
        </div>
    </div>

    {{-- ─── Current PBs ─── --}}
    @if(count($currentPBs) > 0)
    <div class="data-card">
        <div class="card-header">
            <div>
                <h3 class="card-title">🏅 Personal Best Saat Ini</h3>
                <p style="font-size:.8rem;color:var(--text-muted);margin-top:.2rem;">Waktu terbaik sepanjang waktu per nomor lomba</p>
            </div>
            <span style="background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.25);color:#e8c96d;padding:.4rem .9rem;border-radius:2rem;font-size:.78rem;font-weight:700;">
                {{ count($currentPBs) }} PB
            </span>
        </div>
        <div class="pb-cards-grid">
            @foreach($currentPBs as $key => $pb)
            <div class="pb-card">
                <div class="pb-card-label-ev">🏊 {{ $pb['event_label'] }}</div>
                <div class="pb-card-date">{{ $pb['competition_date'] }}</div>
                <div class="pb-card-time">{{ $pb['time_result'] }}</div>
                <div class="pb-card-sublabel">Waktu Terbaik</div>
                <div class="pb-card-comp">📍 {{ $pb['competition_name'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ─── Filter ─── --}}
    @if(count($historyRows) > 0)
    <form method="GET" action="{{ route('event-history.show', $athlete) }}">
        <div class="eh-filter-row">
            <div class="eh-filter-group">
                <label>Gaya Renang</label>
                <select name="stroke" class="form-input" style="width:160px;padding:.55rem .75rem;font-size:.85rem;">
                    <option value="">Semua Gaya</option>
                    @foreach(['bebas'=>'Bebas','dada'=>'Dada','kupu'=>'Kupu-Kupu','punggung'=>'Punggung','ganti'=>'Ganti'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $filterStroke === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="eh-filter-group">
                <label>Tahun Event</label>
                <select name="year" class="form-input" style="width:120px;padding:.55rem .75rem;font-size:.85rem;">
                    <option value="">Semua Tahun</option>
                    @php $years = collect($historyRows)->map(fn($r) => substr($r['competition_date'],0,4))->unique()->sortDesc(); @endphp
                    @foreach($years as $yr)
                        <option value="{{ $yr }}" {{ $filterYear === $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:.5rem;align-items:flex-end;">
                <button type="submit" class="btn btn-primary" style="width:auto;padding:.6rem 1.15rem;">🔍 Filter</button>
                @if($filterStroke || $filterYear)
                    <a href="{{ route('event-history.show', $athlete) }}" class="btn btn-primary" style="width:auto;padding:.6rem 1.15rem;background:var(--text-muted);color:#fff;">Reset</a>
                @endif
            </div>
        </div>
    </form>
    @endif

    {{-- ─── History Sections per Event Key ─── --}}
    @php
        $filteredSectionsDesktop = collect($historyByEvent)->filter(function($evData) use ($filterStroke, $filterYear) {
            if ($filterStroke && $evData['stroke'] !== $filterStroke) return false;
            if ($filterYear) {
                $hasYear = collect($evData['history'])->contains(fn($r) => substr($r['competition_date'],0,4) === $filterYear);
                if (!$hasYear) return false;
            }
            return true;
        });
    @endphp

    @if($filteredSectionsDesktop->count() > 0)
        @foreach($filteredSectionsDesktop as $evKey => $evData)
        @php
            $sectionRows = collect($evData['history']);
            if ($filterYear) {
                $sectionRows = $sectionRows->filter(fn($r) => substr($r['competition_date'],0,4) === $filterYear);
            }
        @endphp
        <div class="eh-section">
            <div class="eh-section-header">
                <span class="eh-section-title">🏊 {{ $evData['event_label'] }}</span>
                <span class="eh-section-badge">{{ $sectionRows->count() }} Riwayat</span>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event / Perlombaan</th>
                            <th style="text-align:center;">Tanggal</th>
                            <th style="text-align:center;">Hasil</th>
                            <th style="text-align:center;">PB Sebelum Event</th>
                            <th style="text-align:center;">Selisih / Keterangan</th>
                            <th style="text-align:center;width:70px;">Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sectionRows as $row)
                        <tr>
                            <td>
                                <div style="font-weight:700;color:var(--silver-200,#dde8f4);font-size:.875rem;">
                                    {{ $row['competition_name'] }}
                                </div>
                            </td>
                            <td style="text-align:center;white-space:nowrap;">
                                <span style="font-size:.85rem;color:var(--silver-200,#dde8f4);">
                                    {{ $row['competition_date_formatted'] }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                @if($row['result_status'] === 'finish')
                                    <span class="time-cell {{ $row['is_first_valid_result'] ? 'best' : 'normal' }}">
                                        {{ $row['time_result'] }}
                                    </span>
                                    @if($row['is_first_valid_result'])
                                        <div style="font-size:.62rem;color:#4ade80;font-weight:700;margin-top:.15rem;">★ PB Awal</div>
                                    @endif
                                @elseif($row['result_status'] === 'DNS')
                                    <span class="status-dns">DNS</span>
                                @elseif($row['result_status'] === 'DQ')
                                    <span class="status-dq">DQ</span>
                                @else
                                    <span style="color:var(--text-muted);font-style:italic;font-size:.8rem;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($row['result_status'] === 'finish')
                                    @if($row['pb_before_event'])
                                        <span class="pb-cell">{{ $row['pb_before_event'] }}</span>
                                    @else
                                        <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                                    @endif
                                @else
                                    <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($row['result_status'] === 'finish')
                                    @if($row['is_first_valid_result'])
                                        <span class="diff-first">PB Awal</span>
                                    @elseif($row['difference'] !== null)
                                        @if($row['is_faster'] === true)
                                            <span class="diff-faster">{{ $row['difference'] }}</span>
                                        @elseif($row['is_faster'] === false && $row['difference'] !== '00.00.00')
                                            <span class="diff-slower">{{ $row['difference'] }}</span>
                                        @else
                                            <span class="diff-equal">00.00.00</span>
                                        @endif
                                    @else
                                        <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                                    @endif
                                @else
                                    <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($row['rank'])
                                    <span style="font-weight:800;font-size:.95rem;color:{{ $row['rank'] == 1 ? '#e8c96d' : ($row['rank'] == 2 ? '#9ca3af' : ($row['rank'] == 3 ? '#fb923c' : 'var(--text-muted)')) }};">
                                        #{{ $row['rank'] }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted);font-size:.8rem;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="eh-table-footer">
                📌 Diurutkan berdasarkan tanggal event terbaru &bull;
                <span style="color:#4ade80;">Hijau = lebih cepat dari PB</span> &bull;
                <span style="color:#f87171;">Merah = lebih lambat dari PB</span>
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
                        Atlet ini belum memiliki riwayat perlombaan yang tercatat.
                    @endif
                </p>
            </div>
        </div>
    @endif

</div>

{{-- ─── Legend ─── --}}
<div class="desktop-only" style="margin-top:1rem;padding:.85rem 1.25rem;background:rgba(10,22,40,.5);border:1px solid var(--glass-border);border-radius:.75rem;font-size:.78rem;color:var(--text-muted);display:flex;flex-wrap:wrap;gap:1.25rem;">
    <span>📖 <strong style="color:var(--silver-200);">Keterangan:</strong></span>
    <span><span class="diff-faster" style="font-size:.78rem;">00.00.XX</span> = Lebih cepat dari PB (hijau)</span>
    <span><span class="diff-slower" style="font-size:.78rem;">00.00.XX</span> = Lebih lambat dari PB (merah)</span>
    <span><span class="diff-equal" style="font-size:.78rem;">00.00.00</span> = Sama persis dengan PB</span>
    <span><span class="diff-first" style="font-size:.78rem;">PB Awal</span> = Hasil pertama, belum ada PB sebelumnya</span>
    <span><span class="status-dns">DNS</span> / <span class="status-dq">DQ</span> = Tidak masuk perhitungan PB</span>
</div>
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

    // ── Show-page filter bottom sheet ───────────────────
    const overlay = document.getElementById('show-filter-sheet-overlay');
    const openBtn = document.getElementById('open-show-filter-sheet');
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
        sheet.addEventListener('touchstart', function (e) { startY = e.touches[0].clientY; }, { passive: true });
        sheet.addEventListener('touchend', function (e) {
            if (e.changedTouches[0].clientY - startY > 80) {
                overlay.classList.remove('open');
                document.body.style.overflow = '';
            }
        }, { passive: true });
    }
});
</script>
@endsection