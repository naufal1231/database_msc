@extends('layouts.admin')

@section('title', 'Daftar Atlet')
@section('header_title', 'Manajemen Data Atlet')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- ═══════════════════════════════════════════════════════
         RINGKASAN STATISTIK (STAT CARDS)
    ═══════════════════════════════════════════════════════ --}}
    <div class="athlete-stat-grid">
        <div class="athlete-stat-card">
            <div class="athlete-stat-icon-box blue">👥</div>
            <div class="athlete-stat-info">
                <div class="athlete-stat-value">{{ $stats['total'] ?? $athletes->total() }}</div>
                <div class="athlete-stat-label">Total Atlet Terdaftar</div>
            </div>
        </div>

        <div class="athlete-stat-card">
            <div class="athlete-stat-icon-box cyan">🏊‍♂️</div>
            <div class="athlete-stat-info">
                <div class="athlete-stat-value">{{ $stats['male'] ?? '-' }}</div>
                <div class="athlete-stat-label">Putra (PA)</div>
            </div>
        </div>

        <div class="athlete-stat-card">
            <div class="athlete-stat-icon-box rose">🏊‍♀️</div>
            <div class="athlete-stat-info">
                <div class="athlete-stat-value">{{ $stats['female'] ?? '-' }}</div>
                <div class="athlete-stat-label">Putri (PI)</div>
            </div>
        </div>

        <div class="athlete-stat-card">
            <div class="athlete-stat-icon-box amber">🏅</div>
            <div class="athlete-stat-info">
                <div class="athlete-stat-value">{{ $stats['classes_count'] ?? count($classes) }}</div>
                <div class="athlete-stat-label">Kelas Latihan Aktif</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         MAIN DATA CARD CONTAINER
    ═══════════════════════════════════════════════════════ --}}
    <div class="data-card" style="margin-bottom: 0;">
        {{-- Card Header --}}
        <div class="card-header" style="align-items: flex-start; padding-bottom: 1.25rem; border-bottom: 1px solid var(--glass-border);">
            <div>
                <h3 class="card-title" style="display: flex; align-items: center; gap: 0.5rem; font-size: 1.2rem;">
                    <span>🏊‍♂️</span> Daftar Atlet Marabunta Swimming Club
                </h3>
                <p style="font-size: 0.825rem; color: var(--text-muted); margin-top: 0.35rem;">
                    Kelola data identitas perenang, kelompok umur, kategori kelas, dan penautan akun orang tua/wali.
                </p>
            </div>
            <a href="{{ route('athletes.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.25rem; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 14px rgba(38, 132, 212, 0.35);">
                <span>➕</span> Tambah Atlet Baru
            </a>
        </div>

        {{-- ───────────────────────────────────────────────
             DESKTOP FILTER TOOLBAR (hidden on mobile)
        ─────────────────────────────────────────────── --}}
        <div class="desktop-only" style="margin-top: 1.25rem; margin-bottom: 1rem;">
            <div class="athlete-filter-box">
                <form id="desktop-filter-form" method="GET" action="{{ route('athletes.index') }}"
                      style="display: grid; grid-template-columns: 2fr 1.2fr 1.2fr auto; gap: 1rem; align-items: flex-end;">

                    {{-- Search Input --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="search" class="form-label" style="font-size: 0.775rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">
                            Cari Nama Atlet
                        </label>
                        <div class="filter-input-wrap">
                            <span class="filter-input-icon">🔍</span>
                            <input type="text" name="search" id="search" class="form-input"
                                   placeholder="Ketik nama atlet..." value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Class Select --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="class_id" class="form-label" style="font-size: 0.775rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">
                            Kelas Latihan
                        </label>
                        <select name="class_id" id="class_id" class="form-input">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- KU Select --}}
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="ku" class="form-label" style="font-size: 0.775rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted);">
                            Kelompok Umur (KU)
                        </label>
                        <select name="ku" id="ku" class="form-input">
                            <option value="">Semua KU</option>
                            @foreach($kus as $kuYear)
                                <option value="{{ $kuYear }}" {{ request('ku') == $kuYear ? 'selected' : '' }}>
                                    KU {{ $kuYear }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div style="display: flex; gap: 0.5rem; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.15rem; font-weight: 700;">
                            Filter
                        </button>
                        @if(request('search') || request('class_id') || request('ku'))
                            <a href="{{ route('athletes.index') }}" class="btn btn-accent"
                               style="padding: 0.65rem 1rem; background: rgba(255, 255, 255, 0.08); color: var(--silver-200); border: 1px solid var(--glass-border);"
                               title="Reset semua filter">
                                ✕ Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Active Filter Tags --}}
            @if(request('search') || request('class_id') || request('ku'))
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                    <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Filter aktif:</span>
                    @if(request('search'))
                        <span class="active-chip">
                            Nama: "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="active-chip-remove">✕</a>
                        </span>
                    @endif
                    @if(request('class_id'))
                        @php $selectedClass = $classes->firstWhere('id', request('class_id')); @endphp
                        @if($selectedClass)
                            <span class="active-chip">
                                Kelas: {{ $selectedClass->name }}
                                <a href="{{ request()->fullUrlWithQuery(['class_id' => null]) }}" class="active-chip-remove">✕</a>
                            </span>
                        @endif
                    @endif
                    @if(request('ku'))
                        <span class="active-chip">
                            KU {{ request('ku') }}
                            <a href="{{ request()->fullUrlWithQuery(['ku' => null]) }}" class="active-chip-remove">✕</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- ───────────────────────────────────────────────
             MOBILE SEARCH ROW + FILTER BUTTON
        ─────────────────────────────────────────────── --}}
        <div class="mobile-only" style="margin-top: 1rem; margin-bottom: 0.5rem;">
            <form id="mobile-search-form" method="GET" action="{{ route('athletes.index') }}">
                <input type="hidden" name="class_id" id="m_class_id_hidden" value="{{ request('class_id') }}">
                <input type="hidden" name="ku" id="m_ku_hidden" value="{{ request('ku') }}">

                <div class="mobile-header-row">
                    <input type="text" name="search" class="form-input"
                           placeholder="🔍  Cari nama atlet..." value="{{ request('search') }}">
                    <button type="button" id="open-filter-sheet" class="mobile-filter-btn">
                        ⚙ Filter
                        @php $activeFilterCount = (request('class_id') ? 1 : 0) + (request('ku') ? 1 : 0); @endphp
                        @if($activeFilterCount > 0)
                            <span class="mobile-filter-btn-badge">{{ $activeFilterCount }}</span>
                        @endif
                    </button>
                </div>
            </form>

            @if(request('class_id') || request('ku') || request('search'))
                <div class="active-filter-chips" style="margin-top: 0.5rem;">
                    @if(request('search'))
                        <span class="active-chip">
                            "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="active-chip-remove">✕</a>
                        </span>
                    @endif
                    @if(request('class_id'))
                        @php $selectedClass = $classes->firstWhere('id', request('class_id')); @endphp
                        @if($selectedClass)
                            <span class="active-chip">
                                {{ $selectedClass->name }}
                                <a href="{{ request()->fullUrlWithQuery(['class_id' => null]) }}" class="active-chip-remove">✕</a>
                            </span>
                        @endif
                    @endif
                    @if(request('ku'))
                        <span class="active-chip">
                            KU {{ request('ku') }}
                            <a href="{{ request()->fullUrlWithQuery(['ku' => null]) }}" class="active-chip-remove">✕</a>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- ───────────────────────────────────────────────
             DESKTOP TABLE (hidden on mobile)
        ─────────────────────────────────────────────── --}}
        <div class="desktop-athlete-table desktop-only" style="margin-top: 0.75rem;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">NO</th>
                            <th style="width: 60px; text-align: center;">FOTO</th>
                            <th>NAMA ATLET</th>
                            <th>TGL LAHIR</th>
                            <th style="text-align: center;">KU</th>
                            <th style="text-align: center;">GENDER</th>
                            <th>KELAS</th>
                            <th>ORANG TUA / WALI</th>
                            <th style="width: 210px; text-align: center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($athletes as $index => $athlete)
                            <tr>
                                {{-- Nomor Urut --}}
                                <td style="text-align: center; font-weight: 700; color: var(--text-muted); font-size: 0.8rem;">
                                    {{ $athletes->firstItem() + $index }}
                                </td>

                                {{-- Foto / Avatar Initials --}}
                                <td style="text-align: center;">
                                    @if($athlete->photo)
                                        <img src="{{ asset('storage/' . $athlete->photo) }}" alt="{{ $athlete->name }}" class="athlete-avatar">
                                    @else
                                        @php
                                            $words = explode(' ', trim($athlete->name));
                                            $initials = '';
                                            foreach (array_slice($words, 0, 2) as $w) {
                                                $initials .= mb_substr($w, 0, 1);
                                            }
                                            $initials = strtoupper($initials ?: 'A');
                                        @endphp
                                        <div class="athlete-avatar-placeholder" title="{{ $athlete->name }}">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Nama Atlet --}}
                                <td>
                                    <a href="{{ route('athletes.show', $athlete->id) }}" class="athlete-name-link">
                                        {{ $athlete->name }}
                                    </a>
                                </td>

                                {{-- Tanggal Lahir --}}
                                <td>
                                    <div style="font-weight: 600; color: var(--silver-200);">
                                        {{ $athlete->birth_date->format('d/m/Y') }}
                                    </div>
                                    <small style="color: var(--text-muted); font-size: 0.72rem;">
                                        {{ \Carbon\Carbon::parse($athlete->birth_date)->age }} tahun
                                    </small>
                                </td>

                                {{-- Kelompok Umur --}}
                                <td style="text-align: center;">
                                    <span class="badge-ku">KU {{ $athlete->ku }}</span>
                                </td>

                                {{-- Gender (PA/PI) --}}
                                <td style="text-align: center;">
                                    @if($athlete->gender === 'L')
                                        <span class="badge-gender-pa">🏊‍♂️ PA</span>
                                    @else
                                        <span class="badge-gender-pi">🏊‍♀️ PI</span>
                                    @endif
                                </td>

                                {{-- Kelas Latihan --}}
                                <td>
                                    <span class="badge-class">
                                        {{ $athlete->currentClass->name ?? 'Belum ditentukan' }}
                                    </span>
                                </td>

                                {{-- Orang Tua / Wali --}}
                                <td>
                                    @if($athlete->parentUser)
                                        <div style="font-weight: 600; color: var(--silver-200);">
                                            {{ $athlete->parentUser->name }}
                                        </div>
                                        @if($athlete->parentUser->phone)
                                            <small style="color: var(--text-muted); font-size: 0.75rem; display: flex; align-items: center; gap: 0.25rem;">
                                                <span>📞</span> {{ $athlete->parentUser->phone }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="badge-unlinked">
                                            <span>⚠️</span> Belum ditautkan
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi Buttons --}}
                                <td style="text-align: center;">
                                    <div class="action-btn-group">
                                        <a href="{{ route('athletes.show', $athlete->id) }}" class="btn-action btn-action-detail" title="Lihat detail atlet">
                                            Detail
                                        </a>
                                        <a href="{{ route('athletes.edit', $athlete->id) }}" class="btn-action btn-action-edit" title="Edit data atlet">
                                            Edit
                                        </a>
                                        <form action="{{ route('athletes.destroy', $athlete->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus atlet {{ $athlete->name }}? Seluruh data riwayat kelas dan hasil lomba atlet ini akan terhapus.');"
                                              style="display: inline; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-action-delete" title="Hapus atlet">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 3.5rem 1rem;">
                                    <div style="font-size: 2.2rem; margin-bottom: 0.5rem; opacity: 0.6;">🏊‍♂️</div>
                                    <div style="font-weight: 700; color: var(--silver-200); font-size: 1rem;">Tidak ada data atlet ditemukan</div>
                                    <p style="font-size: 0.825rem; color: var(--text-muted); margin-top: 0.25rem;">
                                        Coba periksa kembali kata kunci pencarian atau reset filter yang dipilih.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ───────────────────────────────────────────────
             MOBILE CARD LIST (hidden on desktop)
        ─────────────────────────────────────────────── --}}
        <div class="mobile-athlete-list mobile-only" style="margin-top: 0.875rem;">
            @forelse($athletes as $athlete)
                <a href="{{ route('athletes.show', $athlete->id) }}" class="m-athlete-card">
                    {{-- Avatar --}}
                    @if($athlete->photo)
                        <img src="{{ asset('storage/' . $athlete->photo) }}"
                             alt="{{ $athlete->name }}"
                             class="m-athlete-avatar">
                    @else
                        @php
                            $words = explode(' ', trim($athlete->name));
                            $mInitials = '';
                            foreach (array_slice($words, 0, 2) as $w) {
                                $mInitials .= mb_substr($w, 0, 1);
                            }
                            $mInitials = strtoupper($mInitials ?: 'A');
                        @endphp
                        <div class="athlete-avatar-placeholder" style="width: 44px; height: 44px; flex-shrink: 0;">
                            {{ $mInitials }}
                        </div>
                    @endif

                    {{-- Info --}}
                    <div class="m-athlete-info">
                        <div class="m-athlete-name">{{ $athlete->name }}</div>
                        <div class="m-athlete-meta">
                            <span class="m-chip {{ $athlete->gender === 'L' ? 'm-chip-pa' : 'm-chip-pi' }}">
                                {{ $athlete->gender === 'L' ? 'PA' : 'PI' }}
                            </span>
                            <span class="m-chip m-chip-blue">KU {{ $athlete->ku }}</span>
                            <span class="m-chip m-chip-muted">{{ $athlete->currentClass->name ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Chevron --}}
                    <span class="m-athlete-chevron">›</span>
                </a>
            @empty
                <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                    <div style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;">🏊‍♂️</div>
                    <div style="font-weight: 700; color: var(--silver-200);">Data atlet tidak ditemukan</div>
                </div>
            @endforelse
        </div>

        {{-- ───────────────────────────────────────────────
             PAGINATION
        ─────────────────────────────────────────────── --}}
        {{ $athletes->withQueryString()->links('vendor.pagination.custom') }}

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     BOTTOM SHEET FILTER MODAL (mobile only)
═══════════════════════════════════════════════════════ --}}
<div class="filter-bottom-sheet-overlay" id="filter-sheet-overlay">
    <div class="filter-bottom-sheet" role="dialog" aria-modal="true" aria-label="Filter Atlet">
        <div class="filter-sheet-handle"></div>
        <div class="filter-sheet-title">⚙ Filter Atlet</div>

        <form method="GET" action="{{ route('athletes.index') }}" id="mobile-filter-form">
            <input type="hidden" name="search" id="m_filter_search" value="{{ request('search') }}">

            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="m_class_id">Kelas Latihan</label>
                <select name="class_id" id="m_class_id" class="form-input">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-sheet-group">
                <label class="filter-sheet-label" for="m_ku">Kelompok Umur (KU)</label>
                <select name="ku" id="m_ku" class="form-input">
                    <option value="">-- Semua KU --</option>
                    @foreach($kus as $kuYear)
                        <option value="{{ $kuYear }}" {{ request('ku') == $kuYear ? 'selected' : '' }}>
                            KU {{ $kuYear }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-sheet-actions">
                <a href="{{ route('athletes.index') }}" class="btn btn-accent"
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
    // ── Bottom sheet open/close ─────────────────────────
    const overlay  = document.getElementById('filter-sheet-overlay');
    const openBtn  = document.getElementById('open-filter-sheet');

    if (openBtn && overlay) {
        openBtn.addEventListener('click', function () {
            const mSearch = document.querySelector('#mobile-search-form input[name="search"]');
            const sheetSearch = document.getElementById('m_filter_search');
            if (mSearch && sheetSearch) sheetSearch.value = mSearch.value;

            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        });

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeSheet();
        });

        // Swipe-down to close
        let startY = 0;
        const sheet = overlay.querySelector('.filter-bottom-sheet');
        if (sheet) {
            sheet.addEventListener('touchstart', function (e) { startY = e.touches[0].clientY; }, { passive: true });
            sheet.addEventListener('touchend',   function (e) {
                if (e.changedTouches[0].clientY - startY > 80) closeSheet();
            }, { passive: true });
        }
    }

    function closeSheet() {
        if (overlay) {
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }
})();
</script>
@endsection
