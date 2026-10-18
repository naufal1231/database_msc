@extends('layouts.admin')

@section('title', 'Daftar Atlet')
@section('header_title', 'Manajemen Data Atlet')

@section('content')
<div class="data-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Atlet Marabunta Swimming Club</h3>
        <a href="{{ route('athletes.create') }}" class="btn btn-primary" style="max-width: 200px;">
            ➕ Tambah Atlet Baru
        </a>
    </div>

    {{-- ───────────────────────────────────────────────
         DESKTOP FILTER BAR (hidden on mobile)
    ─────────────────────────────────────────────── --}}
    <form id="desktop-filter-form" method="GET" action="{{ route('athletes.index') }}"
          class="filter-bar desktop-only"
          style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">

        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label for="search" class="form-label" style="font-size: 0.8rem;">Cari Nama Atlet</label>
            <input type="text" name="search" id="search" class="form-input"
                   placeholder="Ketik nama..." value="{{ request('search') }}">
        </div>

        <div class="form-group" style="margin-bottom: 0; width: 180px;">
            <label for="class_id" class="form-label" style="font-size: 0.8rem;">Kelas Latihan</label>
            <select name="class_id" id="class_id" class="form-input">
                <option value="">-- Semua Kelas --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0; width: 150px;">
            <label for="ku" class="form-label" style="font-size: 0.8rem;">Kelompok Umur (KU)</label>
            <select name="ku" id="ku" class="form-input">
                <option value="">-- Semua KU --</option>
                @foreach($kus as $kuYear)
                    <option value="{{ $kuYear }}" {{ request('ku') == $kuYear ? 'selected' : '' }}>
                        KU {{ $kuYear }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.25rem;">
                Filter
            </button>
            <a href="{{ route('athletes.index') }}" class="btn btn-accent"
               style="padding: 0.75rem 1.25rem; background-color: var(--text-muted); color: #fff;">
                Reset
            </a>
        </div>
    </form>

    {{-- ───────────────────────────────────────────────
         MOBILE: search row + filter button
    ─────────────────────────────────────────────── --}}
    <div class="mobile-only" style="margin-bottom: 0.25rem;">
        <form id="mobile-search-form" method="GET" action="{{ route('athletes.index') }}">
            {{-- Pass hidden values so sheet filters survive a search-only submit --}}
            <input type="hidden" name="class_id" id="m_class_id_hidden" value="{{ request('class_id') }}">
            <input type="hidden" name="ku"       id="m_ku_hidden"       value="{{ request('ku') }}">

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

        {{-- Active filter chips --}}
        @if(request('class_id') || request('ku'))
        <div class="active-filter-chips">
            @if(request('class_id'))
                @php $selectedClass = $classes->firstWhere('id', request('class_id')); @endphp
                @if($selectedClass)
                <span class="active-chip">
                    {{ $selectedClass->name }}
                    <a href="{{ request()->fullUrlWithQuery(['class_id' => null]) }}"
                       class="active-chip-remove" title="Hapus filter kelas">✕</a>
                </span>
                @endif
            @endif
            @if(request('ku'))
                <span class="active-chip">
                    KU {{ request('ku') }}
                    <a href="{{ request()->fullUrlWithQuery(['ku' => null]) }}"
                       class="active-chip-remove" title="Hapus filter KU">✕</a>
                </span>
            @endif
        </div>
        @endif
    </div>

    {{-- ───────────────────────────────────────────────
         DESKTOP TABLE (hidden on mobile)
    ─────────────────────────────────────────────── --}}
    <div class="desktop-athlete-table" style="margin-top: 1.5rem;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">NO</th>
                        <th style="width: 80px; text-align: center;">FOTO</th>
                        <th>NAMA ATLET</th>
                        <th>TGL LAHIR</th>
                        <th style="text-align: center;">KU</th>
                        <th style="text-align: center;">PA/PI</th>
                        <th>KELAS</th>
                        <th>ORANG TUA / WALI</th>
                        <th style="width: 220px; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($athletes as $index => $athlete)
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: var(--text-muted);">
                                {{ $athletes->firstItem() + $index }}
                            </td>
                            <td style="text-align: center;">
                                @if($athlete->photo)
                                    <img src="{{ asset('storage/' . $athlete->photo) }}" alt="Foto Atlet" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                                @else
                                    <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #a0aec0; border: 1px solid var(--border-color);">
                                        👤
                                    </div>
                                @endif
                            </td>
                            <td style="font-weight: 700; color: var(--primary-hover);">
                                <a href="{{ route('athletes.show', $athlete->id) }}" style="color: inherit; text-decoration: none;">
                                    {{ $athlete->name }}
                                </a>
                            </td>
                            <td>{{ $athlete->birth_date->format('d/m/Y') }}</td>
                            <td style="text-align: center; font-weight: 600;">
                                KU {{ $athlete->ku }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge {{ $athlete->gender === 'L' ? 'badge-primary' : 'badge-accent' }}" style="padding: 0.2rem 0.4rem;">
                                    {{ $athlete->gender === 'L' ? 'PA' : 'PI' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-success" style="background-color: var(--bg-light); color: var(--primary-hover); border: 1px solid rgba(2, 132, 199, 0.2);">
                                    {{ $athlete->currentClass->name }}
                                </span>
                            </td>
                            <td>
                                @if($athlete->parentUser)
                                    <span style="font-weight: 500;">{{ $athlete->parentUser->name }}</span>
                                    <br><small style="color: var(--text-muted);">{{ $athlete->parentUser->phone }}</small>
                                @else
                                    <span style="color: var(--text-muted); font-style: italic;">Belum ditautkan</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                    <a href="{{ route('athletes.show', $athlete->id) }}" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; background-color: #0284c7;">
                                        Detail
                                    </a>
                                    <a href="{{ route('athletes.edit', $athlete->id) }}" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; background-color: #ffb700; color: #fff;">
                                        Edit
                                    </a>
                                    <form action="{{ route('athletes.destroy', $athlete->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus atlet ini? Seluruh data riwayat kelas dan hasil lomba atlet ini juga akan terhapus permanen.');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-accent" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                                Data atlet tidak ditemukan.
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
    <div class="mobile-athlete-list" style="margin-top: 0.875rem;">
        @forelse($athletes as $athlete)
            <a href="{{ route('athletes.show', $athlete->id) }}" class="m-athlete-card">
                {{-- Avatar --}}
                @if($athlete->photo)
                    <img src="{{ asset('storage/' . $athlete->photo) }}"
                         alt="{{ $athlete->name }}"
                         class="m-athlete-avatar">
                @else
                    <div class="m-athlete-avatar-placeholder">👤</div>
                @endif

                {{-- Info --}}
                <div class="m-athlete-info">
                    <div class="m-athlete-name">{{ $athlete->name }}</div>
                    <div class="m-athlete-meta">
                        <span class="m-chip {{ $athlete->gender === 'L' ? 'm-chip-pa' : 'm-chip-pi' }}">
                            {{ $athlete->gender === 'L' ? 'PA' : 'PI' }}
                        </span>
                        <span class="m-chip m-chip-blue">KU {{ $athlete->ku }}</span>
                        <span class="m-chip m-chip-muted">{{ $athlete->currentClass->name }}</span>
                    </div>
                </div>

                {{-- Chevron --}}
                <span class="m-athlete-chevron">›</span>
            </a>
        @empty
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted); font-style: italic;">
                Data atlet tidak ditemukan.
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $athletes->links() }}
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
            {{-- Preserve search value --}}
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
            // Sync current search value into the hidden sheet field
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
        sheet.addEventListener('touchstart', function (e) { startY = e.touches[0].clientY; }, { passive: true });
        sheet.addEventListener('touchend',   function (e) {
            if (e.changedTouches[0].clientY - startY > 80) closeSheet();
        }, { passive: true });
    }

    function closeSheet() {
        if (overlay) {
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    // ── Accordion sections (show page) ─────────────────
    document.querySelectorAll('.m-section-header').forEach(function (header) {
        header.addEventListener('click', function () {
            const accordion = this.closest('.m-section-accordion');
            accordion.classList.toggle('open');
        });
    });
})();
</script>
@endsection
