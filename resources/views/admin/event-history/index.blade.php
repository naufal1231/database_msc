@extends('layouts.admin')

@section('title', 'Riwayat Perlombaan - MSC Database')
@section('header_title', 'Riwayat Perlombaan')

@section('styles')
<style>
    .eh-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    @media (max-width: 768px) { .eh-stat-grid { grid-template-columns: repeat(3, 1fr); gap: 0.625rem; } }
    @media (max-width: 420px)  { .eh-stat-grid { grid-template-columns: 1fr; gap: 0.5rem; } }

    .eh-stat-card {
        background: linear-gradient(160deg, rgba(12, 24, 48, 0.85) 0%, rgba(6, 15, 34, 0.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @media (max-width: 768px) {
        .eh-stat-card { padding: 1rem 0.875rem; border-radius: 0.875rem; }
    }
    .eh-stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(200,216,236,0.2) 50%, transparent);
    }
    .eh-stat-card:hover {
        transform: translateY(-3px);
        border-color: rgba(91, 168, 232, 0.25);
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
    }
    .eh-stat-card.blue   { border-color: rgba(91, 168, 232, 0.2); }
    .eh-stat-card.green  { border-color: rgba(74, 222, 128, 0.2); }
    .eh-stat-card.purple { border-color: rgba(167, 139, 250, 0.2); }

    .eh-stat-icon {
        width: 48px; height: 48px;
        border-radius: 0.85rem;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 1rem; font-size: 1.5rem;
    }
    @media (max-width: 768px) { .eh-stat-icon { width: 36px; height: 36px; font-size: 1.1rem; margin-bottom: 0.625rem; border-radius: 0.65rem; } }
    .eh-stat-icon.blue   { background: rgba(91,168,232,0.12); border: 1px solid rgba(91,168,232,0.2); }
    .eh-stat-icon.green  { background: rgba(74,222,128,0.12); border: 1px solid rgba(74,222,128,0.2); }
    .eh-stat-icon.purple { background: rgba(167,139,250,0.12); border: 1px solid rgba(167,139,250,0.2); }

    .eh-stat-value {
        font-size: 2.5rem; font-weight: 900;
        letter-spacing: -0.04em; line-height: 1; margin-bottom: 0.35rem;
    }
    @media (max-width: 768px) { .eh-stat-value { font-size: 1.75rem; } }
    .eh-stat-value.blue   { color: #5ba8e8; }
    .eh-stat-value.green  { color: #4ade80; }
    .eh-stat-value.purple { color: #a78bfa; }

    .eh-stat-label {
        font-size: 0.7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.75px; color: var(--text-muted);
    }
    .eh-stat-desc {
        font-size: 0.75rem; color: var(--text-muted);
        margin-top: 0.5rem; padding-top: 0.75rem;
        border-top: 1px solid var(--glass-border);
    }
    @media (max-width: 768px) { .eh-stat-desc { display: none; } }

    .athlete-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: rgba(26,107,181,0.2); border: 2px solid rgba(91,168,232,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; font-weight: 800; color: #5ba8e8;
        overflow: hidden; flex-shrink: 0;
    }
    .athlete-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .perf-mini {
        display: inline-flex; align-items: center; gap: 0.25rem;
        padding: 0.2rem 0.5rem; border-radius: 0.4rem;
        font-size: 0.78rem; font-weight: 700;
    }
    .perf-mini.event { background: rgba(91,168,232,0.1); color: #5ba8e8; }
    .perf-mini.pb    { background: rgba(74,222,128,0.1); color: #4ade80; }

    .info-box {
        background: linear-gradient(160deg, rgba(12,24,48,0.7) 0%, rgba(6,15,34,0.8) 100%);
        border: 1px solid rgba(91,168,232,0.12);
        border-radius: 1rem; padding: 1.5rem;
    }
    .info-grid-2 {
        display: grid; grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem; margin-top: 1rem;
    }
    @media (max-width: 600px) { .info-grid-2 { grid-template-columns: 1fr; } }
    .info-item-box {
        background: rgba(10,22,40,0.5);
        border: 1px solid var(--glass-border);
        border-radius: 0.65rem; padding: 0.85rem 1rem;
    }
    .info-item-box h4 {
        font-size: 0.8rem; font-weight: 700; color: #5ba8e8; margin-bottom: 0.3rem;
    }
    .info-item-box p { font-size: 0.75rem; color: var(--text-muted); line-height: 1.5; }

    .btn-detail {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.45rem 1rem; border-radius: 0.65rem;
        font-size: 0.8rem; font-weight: 700; text-decoration: none;
        background: linear-gradient(135deg, rgba(38,132,212,0.9) 0%, rgba(14,50,90,0.9) 100%);
        color: #fff; border: 1px solid rgba(91,168,232,0.3);
        transition: all 0.25s ease; box-shadow: 0 4px 12px rgba(38,132,212,0.2);
    }
    .btn-detail:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(38,132,212,0.35); }
</style>
@endsection

@section('content')
@php
    $ehActiveCount = (request('search') ? 1 : 0) + (request('class_id') ? 1 : 0) + (request('ku') ? 1 : 0);
@endphp

<div style="display: flex; flex-direction: column; gap: 1.75rem;">

    {{-- Stat Cards (responsive — 3 col on mobile too, just smaller) --}}
    <div class="eh-stat-grid">
        <div class="eh-stat-card blue">
            <div class="eh-stat-icon blue">👥</div>
            <div class="eh-stat-value blue">{{ $athletes->total() }}</div>
            <div class="eh-stat-label">Total Atlet</div>
            <div class="eh-stat-desc">Semua atlet terdaftar di sistem</div>
        </div>
        <div class="eh-stat-card green">
            <div class="eh-stat-icon green">🏊</div>
            <div class="eh-stat-value green">{{ $athletes->where('total_events', '>', 0)->count() }}</div>
            <div class="eh-stat-label">Atlet Aktif</div>
            <div class="eh-stat-desc">Pernah mengikuti perlombaan</div>
        </div>
        <div class="eh-stat-card purple">
            <div class="eh-stat-icon purple">⭐</div>
            <div class="eh-stat-value purple">{{ $athletes->where('total_pbs', '>', 0)->count() }}</div>
            <div class="eh-stat-label">Prestasi PB</div>
            <div class="eh-stat-desc">Memiliki catatan waktu terbaik</div>
        </div>
    </div>

    {{-- Filter & Table Card --}}
    <div class="data-card">
        <div class="card-header">
            <div>
                <h3 class="card-title">🏆 Daftar Atlet &mdash; Riwayat Perlombaan</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;" class="desktop-only">
                    Analisis performa dan perkembangan atlet melalui data perlombaan historis
                </p>
            </div>
            <div style="background: rgba(91,168,232,0.1); border: 1px solid rgba(91,168,232,0.2);
                        border-radius: 0.65rem; padding: 0.45rem 1rem; font-size: 0.82rem; font-weight: 700; color: #5ba8e8;
                        white-space: nowrap;">
                {{ $athletes->total() }} Atlet
            </div>
        </div>

        {{-- Desktop filter bar --}}
        <form method="GET" action="{{ route('event-history.index') }}"
              class="filter-bar desktop-only" style="margin-bottom: 1.75rem;">
            <div class="form-group filter-input" style="margin-bottom: 0; min-width: 200px;">
                <label for="search" class="form-label">Cari Atlet</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="Ketik nama atlet..." class="form-input">
            </div>
            <div class="form-group filter-input" style="margin-bottom: 0;">
                <label for="class_id" class="form-label">Kelas</label>
                <select name="class_id" id="class_id" class="form-input">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group filter-input" style="margin-bottom: 0;">
                <label for="ku" class="form-label">Kelompok Umur</label>
                <select name="ku" id="ku" class="form-input">
                    <option value="">-- Semua KU --</option>
                    @foreach($kus as $ku)
                        <option value="{{ $ku }}" {{ request('ku') == $ku ? 'selected' : '' }}>
                            KU {{ $ku }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: flex-end; padding-bottom: 0;">
                <button type="submit" class="btn btn-primary" style="width: auto; padding: 0.75rem 1.25rem;">
                    🔍 Filter
                </button>
                @if(request()->hasAny(['search', 'class_id', 'ku']))
                    <a href="{{ route('event-history.index') }}" class="btn btn-primary"
                       style="width: auto; padding: 0.75rem 1.25rem; background: var(--text-muted); color: #fff;">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        {{-- Mobile: search row + filter button --}}
        <div class="mobile-only" style="margin-bottom: 1rem;">
            <form method="GET" action="{{ route('event-history.index') }}" id="eh-mobile-search-form">
                <input type="hidden" name="class_id" id="eh_class_hidden" value="{{ request('class_id') }}">
                <input type="hidden" name="ku"       id="eh_ku_hidden"    value="{{ request('ku') }}">
                <div class="mobile-header-row">
                    <input type="text" name="search" class="form-input"
                           placeholder="🔍  Cari nama atlet..." value="{{ request('search') }}">
                    <button type="button" id="open-eh-filter-sheet" class="mobile-filter-btn">
                        ⚙ Filter
                        @if(request('class_id') || request('ku'))
                            <span class="mobile-filter-btn-badge">{{ (request('class_id') ? 1 : 0) + (request('ku') ? 1 : 0) }}</span>
                        @endif
                    </button>
                </div>
            </form>

            @if(request('class_id') || request('ku') || request('search'))
            <div class="active-filter-chips">
                @if(request('search'))
                    <span class="active-chip">
                        "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="active-chip-remove">✕</a>
                    </span>
                @endif
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
            </div>
            @endif
        </div>

        {{-- Desktop Table --}}
        @if($athletes->count() > 0)
            <div class="desktop-athlete-table">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">#</th>
                                <th>ATLET</th>
                                <th>KELAS & KU</th>
                                <th style="text-align: center;">PERFORMA</th>
                                <th style="text-align: center; width: 130px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($athletes as $athlete)
                            <tr>
                                <td style="text-align: center; color: var(--text-muted); font-size: 0.8rem; font-weight: 600;">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div class="athlete-avatar">
                                            @if($athlete->photo)
                                                <img src="{{ asset('storage/' . $athlete->photo) }}" alt="{{ $athlete->name }}">
                                            @else
                                                {{ strtoupper(substr($athlete->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--silver-200, #dde8f4);">{{ $athlete->name }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $athlete->parentUser->name ?? 'Tanpa Wali' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                        <span class="badge badge-primary" style="width: fit-content;">
                                            {{ $athlete->currentClass->name ?? '-' }}
                                        </span>
                                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">
                                            KU {{ $athlete->ku ?? '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.85rem;">
                                        <div>
                                            <span class="perf-mini event">🏊 {{ $athlete->total_events }}</span>
                                            <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.2rem; text-align: center;">Event</div>
                                        </div>
                                        <div style="width: 1px; height: 28px; background: var(--border-color);"></div>
                                        <div>
                                            <span class="perf-mini pb">⭐ {{ $athlete->total_pbs }}</span>
                                            <div style="font-size: 0.65rem; color: var(--text-muted); margin-top: 0.2rem; text-align: center;">PB</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    @if($athlete->total_events > 0)
                                        <a href="{{ route('event-history.show', $athlete) }}" class="btn-detail">
                                            👁 Detail
                                        </a>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">Belum ada data</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($athletes->hasPages())
                    <div style="margin-top: 1.5rem;">
                        {{ $athletes->withQueryString()->links() }}
                    </div>
                @endif
            </div>

            {{-- Mobile Card List --}}
            <div class="mobile-athlete-list">
                @foreach($athletes as $athlete)
                    @if($athlete->total_events > 0)
                        <a href="{{ route('event-history.show', $athlete) }}" class="m-athlete-card">
                    @else
                        <div class="m-athlete-card" style="opacity: 0.65;">
                    @endif
                        {{-- Avatar --}}
                        @if($athlete->photo)
                            <img src="{{ asset('storage/' . $athlete->photo) }}"
                                 alt="{{ $athlete->name }}" class="m-athlete-avatar">
                        @else
                            <div class="m-athlete-avatar-placeholder"
                                 style="font-size: 1rem; font-weight: 800; color: #5ba8e8;">
                                {{ strtoupper(substr($athlete->name, 0, 1)) }}
                            </div>
                        @endif

                        {{-- Info --}}
                        <div class="m-athlete-info">
                            <div class="m-athlete-name">{{ $athlete->name }}</div>
                            <div class="m-athlete-meta" style="margin-bottom: 0.3rem;">
                                <span class="m-chip m-chip-blue">KU {{ $athlete->ku }}</span>
                                <span class="m-chip m-chip-muted">{{ $athlete->currentClass->name ?? '-' }}</span>
                            </div>
                            <div style="display: flex; gap: 0.75rem;">
                                <span style="font-size: 0.7rem; color: var(--text-muted);">
                                    🏊 <strong style="color: #5ba8e8;">{{ $athlete->total_events }}</strong> event
                                </span>
                                @if($athlete->total_pbs > 0)
                                <span style="font-size: 0.7rem; color: var(--text-muted);">
                                    ⭐ <strong style="color: #4ade80;">{{ $athlete->total_pbs }}</strong> PB
                                </span>
                                @endif
                            </div>
                        </div>

                        {{-- Right indicator --}}
                        @if($athlete->total_events > 0)
                            <span class="m-athlete-chevron">›</span>
                        @else
                            <span style="font-size: 0.65rem; color: var(--text-light-muted); text-align: center; flex-shrink: 0;">Belum<br>ada</span>
                        @endif
                    @if($athlete->total_events > 0)
                        </a>
                    @else
                        </div>
                    @endif
                @endforeach

                @if($athletes->hasPages())
                    <div style="margin-top: 1rem;">
                        {{ $athletes->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        @else
            <div style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
                <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🔍</div>
                <p style="font-size: 1rem; font-weight: 700; color: var(--silver-200, #dde8f4); margin-bottom: 0.5rem;">
                    Tidak Ditemukan
                </p>
                <p style="font-size: 0.875rem; font-style: italic;">
                    Tidak ada atlet yang sesuai dengan filter yang diterapkan.
                </p>
            </div>
        @endif
    </div>

    {{-- Info Box (desktop only to keep mobile clean) --}}
    <div class="info-box desktop-only">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
            <span style="font-size: 1.2rem;">📈</span>
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--silver-200, #dde8f4);">Informasi Analisis Riwayat</h3>
        </div>
        <p style="font-size: 0.8rem; color: var(--text-muted);">Panduan memahami data pada halaman riwayat perlombaan atlet.</p>
        <div class="info-grid-2">
            <div class="info-item-box">
                <h4>⬆️ Personal Best (PB)</h4>
                <p>Waktu terbaik atlet untuk kombinasi gaya, jarak, dan kategori tertentu yang pernah dicapai.</p>
            </div>
            <div class="info-item-box">
                <h4>⚙️ PB Sebelum Event</h4>
                <p>PB yang dimiliki atlet sebelum event berlangsung, bukan PB terkini.</p>
            </div>
            <div class="info-item-box">
                <h4>📊 Perbandingan Performa</h4>
                <p>Analisis selisih waktu: hijau = lebih cepat, merah = lebih lambat dari PB sebelumnya.</p>
            </div>
            <div class="info-item-box">
                <h4>👁 Lihat Detail Riwayat</h4>
                <p>Klik tombol "Detail" untuk analisis lengkap per event dan timeline perkembangan atlet.</p>
            </div>
        </div>
    </div>

</div>

{{-- ─── Event History Filter Bottom Sheet (mobile) ─── --}}
<div class="filter-bottom-sheet-overlay" id="eh-filter-sheet-overlay">
    <div class="filter-bottom-sheet" role="dialog" aria-modal="true">
        <div class="filter-sheet-handle"></div>
        <div class="filter-sheet-title">⚙ Filter Riwayat</div>

        <form method="GET" action="{{ route('event-history.index') }}" id="eh-mobile-filter-form">
            <input type="hidden" name="search" id="eh_filter_search" value="{{ request('search') }}">

            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="eh_m_class_id">Kelas Latihan</label>
                <select name="class_id" id="eh_m_class_id" class="form-input">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="eh_m_ku">Kelompok Umur</label>
                <select name="ku" id="eh_m_ku" class="form-input">
                    <option value="">-- Semua KU --</option>
                    @foreach($kus as $ku)
                        <option value="{{ $ku }}" {{ request('ku') == $ku ? 'selected' : '' }}>KU {{ $ku }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-sheet-actions">
                <a href="{{ route('event-history.index') }}" class="btn btn-accent"
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
(function () {
    const overlay = document.getElementById('eh-filter-sheet-overlay');
    const openBtn = document.getElementById('open-eh-filter-sheet');
    if (!openBtn || !overlay) return;

    openBtn.addEventListener('click', function () {
        // sync search value
        const mSearch = document.querySelector('#eh-mobile-search-form input[name="search"]');
        const sheetSearch = document.getElementById('eh_filter_search');
        if (mSearch && sheetSearch) sheetSearch.value = mSearch.value;

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
    sheet.addEventListener('touchend',   function (e) {
        if (e.changedTouches[0].clientY - startY > 80) {
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }, { passive: true });
})();
</script>
@endsection