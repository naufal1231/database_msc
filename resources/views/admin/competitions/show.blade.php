@extends('layouts.admin')

@section('title', 'Detail Event - ' . $competition->name)
@section('header_title', 'Dashboard Event Perlombaan')

@section('styles')
<style>
    /* ─── SCOPED DASHBOARD EVENT STYLES ─── */
    .comp-hero-card {
        background: linear-gradient(160deg, rgba(14,30,55,0.95) 0%, rgba(8,18,35,0.98) 100%);
        border: 1px solid var(--border-color);
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .comp-hero-accent {
        height: 4px;
        background: linear-gradient(90deg, #0369a1 0%, #2684d4 40%, #e8c96d 100%);
    }

    /* Tab Switcher Segmented Control */
    .comp-tab-bar {
        display: flex;
        background: rgba(10, 22, 40, 0.85);
        border: 1px solid var(--border-color);
        border-radius: 0.875rem;
        padding: 0.35rem;
        gap: 0.35rem;
        width: 100%;
        max-width: 440px;
    }
    .comp-tab-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.65rem 1rem;
        border: none;
        border-radius: 0.65rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        color: var(--text-muted);
        background: transparent;
    }
    .comp-tab-btn.active {
        background: linear-gradient(135deg, #0284c7 0%, #2684d4 100%);
        color: #ffffff;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(38, 132, 212, 0.35);
    }

    /* Mobile Stat Grid */
    .comp-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    .comp-stat-card {
        background: linear-gradient(160deg, rgba(10,22,40,0.85) 0%, rgba(6,15,34,0.92) 100%);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        padding: 1rem 0.875rem;
        text-align: center;
    }
    .comp-stat-val {
        font-size: 1.6rem;
        font-weight: 900;
        line-height: 1.1;
        font-family: monospace;
    }
    .comp-stat-lbl {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Event Accordion on Mobile */
    .comp-event-accordion {
        background: linear-gradient(160deg, rgba(10,22,40,0.85) 0%, rgba(6,15,34,0.92) 100%);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 0.875rem;
        transition: all 0.25s ease;
    }
    .comp-event-header {
        padding: 0.9rem 1rem;
        background: rgba(15, 32, 64, 0.5);
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        gap: 0.5rem;
        user-select: none;
    }
    .comp-event-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--silver-100);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .comp-event-meta {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-top: 0.15rem;
    }

    /* Mobile Athlete Entry Card */
    .comp-entry-card {
        background: rgba(6, 15, 34, 0.65);
        border: 1px solid rgba(91, 168, 232, 0.08);
        border-radius: 0.75rem;
        padding: 0.875rem;
        margin-bottom: 0.625rem;
    }
    .comp-entry-card:last-child {
        margin-bottom: 0;
    }

    /* Time badge helpers - Clean without arrows */
    .time-clean-diff {
        font-family: monospace;
        font-weight: 800;
        font-size: 0.85rem;
    }
    .time-faster { color: #22c55e; }
    .time-slower { color: #ef4444; }
    .time-equal  { color: var(--text-muted); }

    /* Action Buttons in Hero */
    .comp-action-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }
    .comp-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.65rem 0.75rem;
        border-radius: 0.65rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s ease;
    }

    @media (max-width: 768px) {
        .desktop-only { display: none !important; }
        .mobile-only  { display: block !important; }
    }
    @media (min-width: 769px) {
        .desktop-only { display: block !important; }
        .mobile-only  { display: none !important; }
    }
</style>
@endsection

@section('content')
<div x-data="{
    activeTab: 'nomor',
    filterNama: '',
    filterKu: '',
    filterGender: '',
    mobileFilterOpen: false,
    mobileExportOpen: false,
    expandedEvents: {},
    toggleEvent(id) {
        this.expandedEvents[id] = this.expandedEvents[id] === undefined ? false : !this.expandedEvents[id];
    },
    isEventOpen(id) {
        return this.expandedEvents[id] === undefined ? true : this.expandedEvents[id];
    },
    activeFilterCount() {
        let count = 0;
        if (this.filterKu !== '') count++;
        if (this.filterGender !== '') count++;
        return count;
    },
    resetFilters() {
        this.filterNama = '';
        this.filterKu = '';
        this.filterGender = '';
    }
}">

    {{-- ═══════════════════════════════════════════════════════════════════
         DESKTOP LAYOUT (Tampilan Asli Utuh & Fungsional)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="desktop-only" style="display: flex; flex-direction: column; gap: 2rem;">

        {{-- Desktop Event Hero Card --}}
        <div class="data-card">
            <div class="card-header">
                <div>
                    <h3 class="card-title" style="font-size: 1.4rem; color: var(--primary-hover);">🏆 {{ $competition->name }}</h3>
                    <p class="club-subtitle">{{ $competition->organizer }} | {{ $competition->location }}</p>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('competitions.entries.create', $competition->id) }}" class="btn btn-primary" style="background-color: #0284c7; color: #fff;">
                        👥 Daftarkan Atlet (Entry)
                    </a>
                    <a href="{{ route('competitions.results.edit', $competition->id) }}" class="btn btn-primary" style="background-color: #f97316; color: #fff;">
                        ⏱️ Input Catatan Waktu
                    </a>
                    <a href="{{ route('competitions.edit', $competition->id) }}" class="btn btn-primary" style="background-color: #4f46e5; color: #fff;">
                        ✏️ Edit Event
                    </a>
                    <a href="{{ route('competitions.index') }}" class="btn btn-primary" style="background-color: var(--text-muted); color: #fff; max-width: 120px;">
                        Kembali
                    </a>
                </div>
            </div>
            <hr style="margin: 1.5rem 0; border: none; border-bottom: 1px solid var(--border-color);">
            <div class="info-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
                <div class="info-item">
                    <span class="info-label">Tanggal Pelaksanaan</span>
                    <span class="info-value">
                        @if($competition->start_date->equalTo($competition->end_date))
                            {{ $competition->start_date->format('d F Y') }}
                        @else
                            {{ $competition->start_date->format('d F Y') }} - {{ $competition->end_date->format('d F Y') }}
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status Perlombaan</span>
                    <span class="info-value" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        @if($competition->status === 'akan_datang')
                            <span class="badge badge-primary">Akan Datang</span>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal;">({{ $competition->status_keterangan }})</span>
                        @elseif($competition->status === 'berlangsung')
                            <span class="badge badge-accent">Berlangsung</span>
                            <span style="font-size: 0.85rem; color: #ea580c; font-weight: 600;">({{ $competition->status_keterangan }})</span>
                        @else
                            <span class="badge badge-success">Selesai</span>
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal;">({{ $competition->status_keterangan }})</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Desktop Excel Export Card --}}
        <div class="data-card" style="background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, rgba(6,15,34,0.85) 100%); border: 1px solid rgba(16,185,129,0.25);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h4 style="font-weight: bold; color: #f1f5f9; font-size: 1.05rem; margin: 0;">📊 Ekspor Hasil Lomba Resmi (Excel)</h4>
                    <p style="font-size: 0.825rem; color: var(--text-muted); margin: 0.25rem 0 0 0;">Ekspor hasil catatan waktu atlet ke format formulir standar A1, A2, atau A3.</p>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('competitions.export', ['competition' => $competition->id, 'form' => 'A1']) }}" class="btn btn-primary" style="background-color: #16a34a; color: #fff; padding: 0.5rem 1rem; font-size: 0.85rem;">
                        📥 Unduh Form A1 (Perorangan)
                    </a>
                    <a href="{{ route('competitions.export', ['competition' => $competition->id, 'form' => 'A2']) }}" class="btn btn-primary" style="background-color: #16a34a; color: #fff; padding: 0.5rem 1rem; font-size: 0.85rem;">
                        📥 Unduh Form A2 (Gaya Ganti)
                    </a>
                    <a href="{{ route('competitions.export', ['competition' => $competition->id, 'form' => 'A3']) }}" class="btn btn-primary" style="background-color: #16a34a; color: #fff; padding: 0.5rem 1rem; font-size: 0.85rem;">
                        📥 Unduh Form A3 (Estafet)
                    </a>
                </div>
            </div>
        </div>

        {{-- Desktop Tab Header --}}
        <div>
            <div style="display: flex; gap: 0; border-radius: 0.75rem 0.75rem 0 0; overflow: hidden; border: 1px solid var(--border-color); border-bottom: none; width: fit-content;">
                <button
                    id="tab-btn-nomor-desktop"
                    @click="activeTab = 'nomor'"
                    :style="activeTab === 'nomor'
                        ? 'background:#0369a1; color:#fff; font-weight:700;'
                        : 'background:var(--bg-light); color:var(--text-muted); font-weight:400;'"
                    style="padding: 0.65rem 1.4rem; border: none; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; display:flex; align-items:center; gap:0.4rem;">
                    🏊 Per Nomor Lomba
                </button>
                <button
                    id="tab-btn-atlet-desktop"
                    @click="activeTab = 'atlet'"
                    :style="activeTab === 'atlet'
                        ? 'background:#0369a1; color:#fff; font-weight:700;'
                        : 'background:var(--bg-light); color:var(--text-muted); font-weight:400;'"
                    style="padding: 0.65rem 1.4rem; border: none; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; display:flex; align-items:center; gap:0.4rem; border-left: 1px solid var(--border-color);">
                    🎽 Per Atlet
                </button>
            </div>

            {{-- TAB 1 DESKTOP: PER NOMOR LOMBA --}}
            <div x-show="activeTab === 'nomor'" x-transition class="data-card" style="border-radius: 0 0.75rem 0.75rem 0.75rem; margin-top: 0;">
                <div class="card-header">
                    <h3 class="card-title">🏊‍♂️ Nomor Lomba &amp; Hasil Catatan Waktu</h3>
                </div>
                <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">

                @if($competition->events->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 2.5rem;">
                        @foreach($competition->events as $evt)
                            <div style="background: #0369a1; border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.25rem;">
                                <h4 style="font-size: 1.1rem; font-weight: 700; color: #ffffffff; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px dashed var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                                    <span>
                                        Gaya {{ ucfirst($evt->stroke) }} - {{ $evt->distance }} Meter
                                        <small style="font-weight: 400; font-size: 0.8rem; color: #ffffffff; text-transform: uppercase; margin-left: 0.5rem;">
                                            ({{ $evt->category === 'perorangan' ? 'Perorangan' : 'Estafet Mix' }})
                                        </small>
                                    </span>
                                    <span class="badge badge-primary" style="font-size: 0.75rem;">
                                        {{ $evt->entries->count() }} Atlet Terdaftar
                                    </span>
                                </h4>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th style="width: 60px; text-align: center;">NO</th>
                                                <th>NAMA ATLET</th>
                                                <th style="text-align: center; width: 100px;">KU</th>
                                                <th style="text-align: center; width: 100px;">PA/PI</th>
                                                <th style="text-align: center; width: 130px;">LIMIT DAFTAR</th>
                                                <th style="text-align: center; width: 120px;">STATUS</th>
                                                <th style="width: 150px; text-align: center;">CATATAN WAKTU</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($evt->entries as $index => $entry)
                                                <tr>
                                                    <td style="text-align: center; font-weight: 600; color: var(--text-muted);">{{ $index + 1 }}</td>
                                                    <td style="font-weight: 700;">
                                                        <a href="{{ route('athletes.show', $entry->athlete->id) }}" style="color: inherit; text-decoration: none;">
                                                            {{ $entry->athlete->name }}
                                                        </a>
                                                    </td>
                                                    <td style="text-align: center;">KU {{ $entry->athlete->ku }}</td>
                                                    <td style="text-align: center;">
                                                        <span class="badge {{ $entry->athlete->gender === 'L' ? 'badge-primary' : 'badge-accent' }}" style="padding: 0.15rem 0.35rem; font-size: 0.7rem;">
                                                            {{ $entry->athlete->gender === 'L' ? 'PA' : 'PI' }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: center; font-family: monospace; font-weight: 700; color: var(--accent);">
                                                        {{ $entry->seed_time ?: '-' }}
                                                    </td>
                                                    <td style="text-align: center;">
                                                        @if($entry->status === 'terdaftar')
                                                            <span class="badge badge-primary" style="font-size: 0.7rem;">Terdaftar</span>
                                                        @elseif($entry->status === 'DNS')
                                                            <span class="badge badge-accent" style="font-size: 0.7rem; background-color: #fee2e2; color: #ef4444;">DNS</span>
                                                        @else
                                                            <span class="badge badge-accent" style="font-size: 0.7rem; background-color: #fef3c7; color: #d97706;">DQ</span>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: center; font-family: monospace; font-weight: 700; font-size: 1.05rem; color: white;">
                                                        @if($entry->result)
                                                            @if($entry->result->status === 'finish')
                                                                {{ $entry->result->time_result }}
                                                            @else
                                                                <span style="color: white; font-weight: 400; font-family: inherit; font-size: 0.9rem;">{{ $entry->result->status }}</span>
                                                            @endif
                                                        @else
                                                            <span style="color: white; font-weight: 400; font-family: inherit; font-size: 0.9rem; font-style: italic;">Belum bertanding</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" style="text-align: center; padding: 1.5rem; color: var(--text-muted); font-style: italic; font-size: 0.85rem;">
                                                        Belum ada atlet yang didaftarkan pada nomor ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                        Belum ada nomor lomba yang didefinisikan untuk event ini.
                    </div>
                @endif
            </div>

            {{-- TAB 2 DESKTOP: PER ATLET --}}
            <div x-show="activeTab === 'atlet'" x-transition class="data-card" style="border-radius: 0 0.75rem 0.75rem 0.75rem; margin-top: 0;">
                <div class="card-header">
                    <h3 class="card-title">🎽 Performa Per Atlet</h3>
                </div>
                <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">

                @if(count($athleteView) > 0)
                    @php
                        $totalAthleteBertanding = 0;
                        $totalPbMembaik         = 0;
                        $sumPersentase          = 0;
                        $countHasPersentase     = 0;
                        foreach ($athleteView as $av) {
                            if ($av['counted_entries'] > 0) {
                                $totalAthleteBertanding++;
                                $sumPersentase += $av['percentage'];
                                $countHasPersentase++;
                            }
                            foreach ($av['entry_results'] as $er) {
                                if (isset($er['direction']) && $er['direction'] === 'faster') {
                                    $totalPbMembaik++;
                                }
                            }
                        }
                        $rataPersentase = $countHasPersentase > 0
                            ? round($sumPersentase / $countHasPersentase, 1)
                            : null;
                    @endphp

                    {{-- Ringkasan Event Desktop --}}
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
                        <div style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.6rem; padding: 1rem; text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary-hover);">{{ count($athleteView) }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">Total Atlet</div>
                        </div>
                        <div style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.6rem; padding: 1rem; text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: 800; color: #22c55e;">{{ $totalAthleteBertanding }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">Sudah Bertanding</div>
                        </div>
                        <div style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.6rem; padding: 1rem; text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: 800; color: #10b981;">{{ $totalPbMembaik }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">PB Membaik</div>
                        </div>
                        <div style="background: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.6rem; padding: 1rem; text-align: center;">
                            <div style="font-size: 1.8rem; font-weight: 800; color: #f59e0b;">
                                {{ $rataPersentase !== null ? $rataPersentase . '%' : '-' }}
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.2rem;">Rata-rata Poin</div>
                        </div>
                    </div>

                    {{-- Filter Desktop --}}
                    <div style="margin-bottom: 1.5rem;">
                        <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 0.75rem; align-items: end; flex-wrap: wrap;">
                            <div>
                                <label style="font-size: 0.78rem; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Cari Nama Atlet</label>
                                <input type="text" x-model="filterNama" placeholder="Ketik nama atlet..." class="form-input" style="width: 100%; padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="font-size: 0.78rem; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">KU</label>
                                <select x-model="filterKu" class="form-input" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                    <option value="">Semua KU</option>
                                    @php $kuList = collect($athleteView)->pluck('athlete.ku')->unique()->sort()->values(); @endphp
                                    @foreach($kuList as $ku)
                                        <option value="{{ $ku }}">KU {{ $ku }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 0.78rem; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Gender</label>
                                <select x-model="filterGender" class="form-input" style="padding: 0.5rem 0.75rem; font-size: 0.875rem;">
                                    <option value="">PA &amp; PI</option>
                                    <option value="L">PA (Putra)</option>
                                    <option value="P">PI (Putri)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Daftar kartu atlet Desktop --}}
                        <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1.5rem;">
                            @foreach($athleteView as $athleteId => $av)
                                @php
                                    $athlete       = $av['athlete'];
                                    $category      = $av['category'];
                                    $badgeBg       = match($category) {
                                        \App\Services\CompetitionPointService::LABEL_BAIK  => '#16a34a',
                                        \App\Services\CompetitionPointService::LABEL_CUKUP => '#d97706',
                                        \App\Services\CompetitionPointService::LABEL_GAGAL => '#dc2626',
                                        default => '#6b7280',
                                    };
                                    $genderLabel   = $athlete->gender === 'L' ? 'PA' : 'PI';
                                    $genderBadge   = $athlete->gender === 'L' ? 'badge-primary' : 'badge-accent';
                                    $athleteNameLc = strtolower($athlete->name);
                                @endphp

                                <div
                                    x-show="
                                        (filterNama === '' || '{{ $athleteNameLc }}'.includes(filterNama.toLowerCase())) &&
                                        (filterKu === '' || '{{ $athlete->ku }}' === filterKu) &&
                                        (filterGender === '' || '{{ $athlete->gender }}' === filterGender)
                                    "
                                    style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 0.75rem; overflow: hidden;"
                                >
                                    {{-- Header kartu --}}
                                    <div style="background: #0f172a; padding: 1rem 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 0.85rem;">
                                            <div style="width: 42px; height: 42px; border-radius: 50%; background: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; color: #fff; flex-shrink: 0; overflow:hidden;">
                                                @if($athlete->photo)
                                                    <img src="{{ asset('storage/'.$athlete->photo) }}" alt="{{ $athlete->name }}" style="width:100%; height:100%; object-fit:cover;">
                                                @else
                                                    {{ strtoupper(substr($athlete->name, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 1rem; color: #f1f5f9;">
                                                    <a href="{{ route('athletes.show', $athlete->id) }}" style="color: inherit; text-decoration: none;">{{ $athlete->name }}</a>
                                                </div>
                                                <div style="display: flex; gap: 0.4rem; margin-top: 0.25rem; flex-wrap: wrap;">
                                                    <span class="badge {{ $genderBadge }}" style="font-size: 0.68rem; padding: 0.1rem 0.35rem;">{{ $genderLabel }}</span>
                                                    <span class="badge badge-primary" style="font-size: 0.68rem; padding: 0.1rem 0.35rem; background:#334155; border-color:#334155;">KU {{ $athlete->ku }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                            @if($av['counted_entries'] > 0)
                                                <div style="text-align: right;">
                                                    <div style="font-size: 0.72rem; color: #94a3b8;">Total Poin</div>
                                                    <div style="font-weight: 800; font-size: 1.2rem; color: #f1f5f9;">{{ $av['total_points'] }} / {{ $av['max_points'] }}</div>
                                                    <div style="font-size: 0.72rem; color: #94a3b8;">{{ $av['percentage'] }}%</div>
                                                </div>
                                                <span style="background: {{ $badgeBg }}; color: #fff; padding: 0.35rem 0.85rem; border-radius: 9999px; font-weight: 700; font-size: 0.82rem; white-space: nowrap;">
                                                    @if($category === \App\Services\CompetitionPointService::LABEL_BAIK)✅
                                                    @elseif($category === \App\Services\CompetitionPointService::LABEL_CUKUP)⚠️
                                                    @else ❌ @endif {{ $category }}
                                                </span>
                                            @else
                                                <span style="color: #94a3b8; font-style: italic; font-size: 0.85rem;">Belum bertanding</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Tabel entry Desktop --}}
                                    <div style="overflow-x: auto;">
                                        <table class="table" style="min-width: 580px;">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 140px;">ACARA</th>
                                                    <th style="text-align: center; width: 110px;">PB (LIMIT)</th>
                                                    <th style="text-align: center; width: 110px;">FINAL</th>
                                                    <th style="text-align: center; width: 130px;">SELISIH</th>
                                                    <th style="text-align: center; width: 65px;">POIN</th>
                                                    <th style="text-align: center; width: 90px;">STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($av['entry_results'] as $er)
                                                    @php
                                                        $entryRow = $er['entry'];
                                                        $evtRow   = $entryRow->competitionEvent;
                                                        $strokeMap = [
                                                            'bebas'    => 'Bebas',
                                                            'dada'     => 'Dada',
                                                            'kupu'     => 'Kupu-Kupu',
                                                            'punggung' => 'Punggung',
                                                            'ganti'    => 'Ganti',
                                                            'estafet'  => 'Estafet',
                                                        ];
                                                        $acaraLabel = ($strokeMap[$evtRow->stroke] ?? ucfirst($evtRow->stroke))
                                                            . ' ' . $evtRow->distance . 'M';
                                                        $isDnsOrDq = in_array($er['status_label'], ['DNS', 'DQ']);
                                                        $notCounted = !$er['is_counted'] && $er['points'] === null;
                                                    @endphp
                                                    <tr>
                                                        <td style="font-weight: 600;">{{ $acaraLabel }}</td>
                                                        <td style="text-align: center; font-family: monospace; font-weight: 600; color: var(--accent);">
                                                            @if($er['is_debut'] || $er['limit_cs'] === null)
                                                                <span style="color: var(--text-muted); font-family: inherit; font-weight: 400; font-size: 0.8rem;">N/A</span>
                                                            @else
                                                                {{ $entryRow->seed_time }}
                                                            @endif
                                                        </td>
                                                        <td style="text-align: center; font-family: monospace; font-weight: 700;">
                                                            @if($notCounted)
                                                                <span style="color: var(--text-muted); font-size: 0.8rem; font-family: inherit; font-style: italic;">Belum</span>
                                                            @elseif($isDnsOrDq)
                                                                <span style="color: var(--text-muted); font-family: inherit;">—</span>
                                                            @else
                                                                <span style="color: #f1f5f9;">{{ $entryRow->result->time_result ?? '—' }}</span>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: center; font-family: monospace; font-weight: 700;">
                                                            @if($notCounted)
                                                                <span style="color: var(--text-muted);">—</span>
                                                            @elseif($er['is_debut'])
                                                                <span style="color: #10b981; font-size: 0.75rem; font-family: inherit;">Pertama kali</span>
                                                            @elseif($isDnsOrDq)
                                                                <span style="color: var(--text-muted);">—</span>
                                                            @elseif($er['direction'] === 'faster')
                                                                <span style="color: #22c55e;">{{ \App\Services\CompetitionPointService::formatSelisih($er['selisih_cs'], 'faster') }}</span>
                                                            @elseif($er['direction'] === 'slower')
                                                                <span style="color: #ef4444;">{{ \App\Services\CompetitionPointService::formatSelisih($er['selisih_cs'], 'slower') }}</span>
                                                            @else
                                                                <span style="color: var(--text-muted);">{{ \App\Services\CompetitionPointService::formatSelisih(0, 'equal') }}</span>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: center; font-weight: 800; font-size: 1rem;">
                                                            @if($er['points'] === null)
                                                                <span style="color: var(--text-muted); font-weight: 400;">—</span>
                                                            @elseif($er['points'] >= 10)
                                                                <span style="color: #22c55e;">{{ $er['points'] }}</span>
                                                            @elseif($er['points'] >= 5)
                                                                <span style="color: #f59e0b;">{{ $er['points'] }}</span>
                                                            @else
                                                                <span style="color: #ef4444;">{{ $er['points'] }}</span>
                                                            @endif
                                                        </td>
                                                        <td style="text-align: center;">
                                                            @if($er['status_label'] === 'DNS')
                                                                <span style="background:#fee2e2;color:#ef4444;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.7rem;font-weight:700;">DNS</span>
                                                            @elseif($er['status_label'] === 'DQ')
                                                                <span style="background:#fef3c7;color:#d97706;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.7rem;font-weight:700;">DQ</span>
                                                            @elseif($er['is_debut'])
                                                                <span style="background:#dbeafe;color:#2563eb;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.7rem;font-weight:700;">Debut</span>
                                                            @elseif($er['is_counted'])
                                                                <span style="background:#d1fae5;color:#059669;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.7rem;font-weight:700;">Finish</span>
                                                            @else
                                                                <span style="color:var(--text-muted);font-size:0.75rem;font-style:italic;">Belum</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Footer kartu Desktop --}}
                                    <div style="padding: 0.85rem 1.25rem; background: #0f172a; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; border-top: 1px solid var(--border-color);">
                                        @if($av['counted_entries'] > 0)
                                            <div style="font-size: 0.82rem; color: #94a3b8;">
                                                {{ $av['counted_entries'] }} nomor dihitung
                                                @if(count($av['entry_results']) - $av['counted_entries'] > 0)
                                                    · {{ count($av['entry_results']) - $av['counted_entries'] }} belum bertanding
                                                @endif
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">
                                                    Total: <span style="color: #f1f5f9;">{{ $av['total_points'] }} poin</span> &nbsp;·&nbsp; {{ $av['percentage'] }}%
                                                </span>
                                                <span style="background: {{ $badgeBg }}; color: #fff; padding: 0.2rem 0.7rem; border-radius: 9999px; font-weight: 700; font-size: 0.78rem;">{{ $category }}</span>
                                            </div>
                                        @else
                                            <div style="font-size: 0.82rem; color: #94a3b8; font-style: italic;">Belum ada nomor yang selesai bertanding.</div>
                                        @endif
                                    </div>

                                </div>{{-- /kartu desktop --}}
                            @endforeach
                        </div>

                    </div>{{-- /filter desktop --}}
                @else
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                        Belum ada atlet yang terdaftar pada event ini.
                    </div>
                @endif
            </div>

        </div>{{-- /tabs desktop --}}

    </div>{{-- /.desktop-only --}}


    {{-- ═══════════════════════════════════════════════════════════════════
         MOBILE LAYOUT (Sesuai Spesifikasi design.md)
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mobile-only mobile-has-action-bar" style="display: flex; flex-direction: column; gap: 0.875rem;">

        {{-- 1. Mobile Hero Card Event --}}
        <div class="comp-hero-card">
            <div class="comp-hero-accent"></div>
            <div style="padding: 1.15rem 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.65rem;">
                    <div>
                        <h2 style="font-size: 1.15rem; font-weight: 800; color: #fff; line-height: 1.3; margin: 0;">
                            🏆 {{ $competition->name }}
                        </h2>
                        <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem;">
                            📍 {{ $competition->organizer }} • {{ $competition->location }}
                        </div>
                    </div>
                    <div>
                        @if($competition->status === 'akan_datang')
                            <span class="badge badge-primary" style="font-size: 0.7rem; white-space: nowrap;">Akan Datang</span>
                        @elseif($competition->status === 'berlangsung')
                            <span class="badge badge-accent" style="font-size: 0.7rem; white-space: nowrap;">Berlangsung</span>
                        @else
                            <span class="badge badge-success" style="font-size: 0.7rem; white-space: nowrap;">Selesai</span>
                        @endif
                    </div>
                </div>

                <div style="display: flex; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 1rem;">
                    <span class="info-tag" style="font-size: 0.72rem;">
                        📅 @if($competition->start_date->equalTo($competition->end_date))
                            {{ $competition->start_date->format('d/m/Y') }}
                        @else
                            {{ $competition->start_date->format('d/m/y') }} - {{ $competition->end_date->format('d/m/y') }}
                        @endif
                    </span>
                    <span class="info-tag" style="font-size: 0.72rem;">
                        🏊 {{ $competition->events->count() }} Nomor Lomba
                    </span>
                    <span class="info-tag" style="font-size: 0.72rem;">
                        👥 {{ count($athleteView) }} Atlet
                    </span>
                </div>

                {{-- Action Grid di Mobile Hero --}}
                <div class="comp-action-grid">
                    <a href="{{ route('competitions.entries.create', $competition->id) }}" class="comp-action-btn" style="background: #0284c7; color: #fff;">
                        👥 Entry Atlet
                    </a>
                    <a href="{{ route('competitions.results.edit', $competition->id) }}" class="comp-action-btn" style="background: #f97316; color: #fff;">
                        ⏱️ Input Waktu
                    </a>
                    <a href="{{ route('competitions.edit', $competition->id) }}" class="comp-action-btn" style="background: rgba(79, 70, 229, 0.25); border: 1px solid rgba(79, 70, 229, 0.4); color: #c7d2fe;">
                        ✏️ Edit Event
                    </a>
                    <button type="button" @click="mobileExportOpen = true" class="comp-action-btn" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.35); color: #6ee7b7; cursor: pointer;">
                        📥 Form Excel
                    </button>
                </div>
            </div>
        </div>

        {{-- 2. Segmented Tab Switcher --}}
        <div style="display: flex; justify-content: center;">
            <div class="comp-tab-bar">
                <button
                    type="button"
                    class="comp-tab-btn"
                    :class="{ 'active': activeTab === 'nomor' }"
                    @click="activeTab = 'nomor'">
                    🏊 Per Nomor Lomba
                </button>
                <button
                    type="button"
                    class="comp-tab-btn"
                    :class="{ 'active': activeTab === 'atlet' }"
                    @click="activeTab = 'atlet'">
                    🎽 Per Atlet
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             MOBILE TAB 1: PER NOMOR LOMBA
             ══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'nomor'" x-transition style="display: flex; flex-direction: column; gap: 0.75rem;">
            @if($competition->events->count() > 0)
                @foreach($competition->events as $evt)
                    <div class="comp-event-accordion">
                        {{-- Accordion Header --}}
                        <div class="comp-event-header" @click="toggleEvent({{ $evt->id }})">
                            <div style="flex: 1;">
                                <div class="comp-event-title">
                                    <span>🏊</span>
                                    <span>Gaya {{ ucfirst($evt->stroke) }} - {{ $evt->distance }}M</span>
                                </div>
                                <div class="comp-event-meta">
                                    {{ $evt->category === 'perorangan' ? 'Perorangan' : 'Estafet Mix' }} &nbsp;•&nbsp; {{ $evt->entries->count() }} Atlet Terdaftar
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="badge badge-primary" style="font-size: 0.68rem; padding: 0.15rem 0.5rem;">
                                    {{ $evt->entries->count() }}
                                </span>
                                <span style="font-size: 0.85rem; color: var(--text-muted); transition: transform 0.25s ease;"
                                      :style="isEventOpen({{ $evt->id }}) ? 'transform: rotate(180deg)' : 'transform: rotate(0deg)'">
                                    ▼
                                </span>
                            </div>
                        </div>

                        {{-- Accordion Body: Vertical Athlete Cards --}}
                        <div x-show="isEventOpen({{ $evt->id }})" x-transition style="padding: 0.75rem;">
                            @forelse($evt->entries as $entryIdx => $entry)
                                <div class="comp-entry-card">
                                    {{-- Row Atas: Foto, Nama & Badge --}}
                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.6rem; margin-bottom: 0.65rem;">
                                        <div style="display: flex; align-items: center; gap: 0.6rem; min-width: 0;">
                                            <div style="width: 38px; height: 38px; border-radius: 50%; background: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem; color: #fff; flex-shrink: 0; overflow:hidden;">
                                                @if($entry->athlete->photo)
                                                    <img src="{{ asset('storage/'.$entry->athlete->photo) }}" alt="{{ $entry->athlete->name }}" style="width:100%; height:100%; object-fit:cover;">
                                                @else
                                                    {{ strtoupper(substr($entry->athlete->name, 0, 1)) }}
                                                @endif
                                            </div>
                                            <div style="min-width: 0;">
                                                <a href="{{ route('athletes.show', $entry->athlete->id) }}" style="font-size: 0.88rem; font-weight: 700; color: var(--silver-100); text-decoration: none; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $entry->athlete->name }}
                                                </a>
                                                <div style="display: flex; gap: 0.3rem; margin-top: 0.15rem;">
                                                    <span class="badge {{ $entry->athlete->gender === 'L' ? 'badge-primary' : 'badge-accent' }}" style="padding: 0.05rem 0.3rem; font-size: 0.62rem;">
                                                        {{ $entry->athlete->gender === 'L' ? 'PA' : 'PI' }}
                                                    </span>
                                                    <span class="badge" style="background: rgba(142, 184, 216, 0.12); color: var(--silver-300); padding: 0.05rem 0.3rem; font-size: 0.62rem;">
                                                        KU {{ $entry->athlete->ku }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div style="text-align: right; flex-shrink: 0;">
                                            @if($entry->status === 'terdaftar')
                                                <span class="badge badge-primary" style="font-size: 0.65rem; padding: 0.12rem 0.45rem;">Terdaftar</span>
                                            @elseif($entry->status === 'DNS')
                                                <span class="badge badge-accent" style="font-size: 0.65rem; background-color: #fee2e2; color: #ef4444; padding: 0.12rem 0.45rem;">DNS</span>
                                            @else
                                                <span class="badge badge-accent" style="font-size: 0.65rem; background-color: #fef3c7; color: #d97706; padding: 0.12rem 0.45rem;">DQ</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Row Bawah: Limit vs Catatan Waktu --}}
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; background: rgba(10, 22, 40, 0.6); padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid rgba(91, 168, 232, 0.06);">
                                        <div>
                                            <div style="font-size: 0.68rem; color: var(--text-muted);">Limit Daftar (PB)</div>
                                            <div style="font-family: monospace; font-size: 0.85rem; font-weight: 700; color: var(--accent); margin-top: 0.1rem;">
                                                {{ $entry->seed_time ?: '-' }}
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-size: 0.68rem; color: var(--text-muted);">Catatan Waktu</div>
                                            <div style="font-family: monospace; font-size: 0.92rem; font-weight: 900; color: #f1f5f9; margin-top: 0.1rem;">
                                                @if($entry->result)
                                                    @if($entry->result->status === 'finish')
                                                        {{ $entry->result->time_result }}
                                                    @else
                                                        <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted);">{{ $entry->result->status }}</span>
                                                    @endif
                                                @else
                                                    <span style="font-size: 0.75rem; font-style: italic; font-weight: 400; color: var(--text-muted);">Belum</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div style="text-align: center; padding: 1.25rem; color: var(--text-muted); font-size: 0.8rem; font-style: italic;">
                                    Belum ada atlet yang didaftarkan pada nomor ini.
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            @else
                <div class="comp-hero-card" style="padding: 2.5rem 1rem; text-align: center;">
                    <div style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 0.5rem;">🏊</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--silver-200);">Belum Ada Nomor Lomba</div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem;">Event ini belum memiliki nomor lomba yang didefinisikan.</div>
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════
             MOBILE TAB 2: PER ATLET (Sistem Poin Atlet)
             ══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'atlet'" x-transition style="display: flex; flex-direction: column; gap: 0.875rem;">
            @if(count($athleteView) > 0)
                @php
                    $mTotalBertanding = 0;
                    $mTotalPbMembaik  = 0;
                    $mSumPersentase   = 0;
                    $mCountPersentase = 0;
                    foreach ($athleteView as $av) {
                        if ($av['counted_entries'] > 0) {
                            $mTotalBertanding++;
                            $mSumPersentase += $av['percentage'];
                            $mCountPersentase++;
                        }
                        foreach ($av['entry_results'] as $er) {
                            if (isset($er['direction']) && $er['direction'] === 'faster') {
                                $mTotalPbMembaik++;
                            }
                        }
                    }
                    $mRataPersentase = $mCountPersentase > 0
                        ? round($mSumPersentase / $mCountPersentase, 1)
                        : null;
                @endphp

                {{-- Stat Grid 2x2 Mobile --}}
                <div class="comp-stat-grid">
                    <div class="comp-stat-card">
                        <div class="comp-stat-val" style="color: #38bdf8;">{{ count($athleteView) }}</div>
                        <div class="comp-stat-lbl">Total Atlet</div>
                    </div>
                    <div class="comp-stat-card">
                        <div class="comp-stat-val" style="color: #22c55e;">{{ $mTotalBertanding }}</div>
                        <div class="comp-stat-lbl">Bertanding</div>
                    </div>
                    <div class="comp-stat-card">
                        <div class="comp-stat-val" style="color: #10b981;">{{ $mTotalPbMembaik }}</div>
                        <div class="comp-stat-lbl">PB Membaik</div>
                    </div>
                    <div class="comp-stat-card">
                        <div class="comp-stat-val" style="color: #f59e0b;">
                            {{ $mRataPersentase !== null ? $mRataPersentase . '%' : '-' }}
                        </div>
                        <div class="comp-stat-lbl">Rata-rata Poin</div>
                    </div>
                </div>

                {{-- Search & Filter Bar (1 Baris Fleksibel Sesuai design.md) --}}
                <div>
                    <div class="mobile-search-bar">
                        <div class="mobile-search-input-wrap">
                            <span class="mobile-search-icon">🔍</span>
                            <input
                                type="text"
                                x-model="filterNama"
                                class="mobile-search-input"
                                placeholder="Cari nama atlet...">
                            <button
                                type="button"
                                x-show="filterNama !== ''"
                                @click="filterNama = ''"
                                class="mobile-clear-btn"
                                style="position: absolute; right: 0.75rem; background: none; border: none; color: var(--text-muted); cursor: pointer;">
                                ✕
                            </button>
                        </div>
                        <button
                            type="button"
                            class="mobile-filter-btn"
                            @click="mobileFilterOpen = true"
                            style="cursor: pointer;">
                            <span>⚙️ Filter</span>
                            <span
                                x-show="activeFilterCount() > 0"
                                class="filter-count-badge"
                                x-text="activeFilterCount()">
                            </span>
                        </button>
                    </div>

                    {{-- Active Filter Chips --}}
                    <div
                        class="active-filter-chips"
                        x-show="filterKu !== '' || filterGender !== '' || filterNama !== ''"
                        style="margin-top: 0.5rem; display: flex; gap: 0.35rem; flex-wrap: wrap; align-items: center;">

                        <template x-if="filterKu !== ''">
                            <span class="active-chip">
                                <span>KU: <strong x-text="filterKu"></strong></span>
                                <button type="button" class="chip-remove" @click="filterKu = ''">✕</button>
                            </span>
                        </template>

                        <template x-if="filterGender !== ''">
                            <span class="active-chip">
                                <span>Gender: <strong x-text="filterGender === 'L' ? 'PA' : 'PI'"></strong></span>
                                <button type="button" class="chip-remove" @click="filterGender = ''">✕</button>
                            </span>
                        </template>

                        <template x-if="filterNama !== ''">
                            <span class="active-chip">
                                <span>Nama: "<strong x-text="filterNama"></strong>"</span>
                                <button type="button" class="chip-remove" @click="filterNama = ''">✕</button>
                            </span>
                        </template>

                        <button
                            type="button"
                            class="chip-reset-all"
                            @click="resetFilters()"
                            style="font-size: 0.72rem; color: #f87171; background: none; border: none; cursor: pointer; padding: 0.2rem 0.4rem; text-decoration: underline;">
                            Reset Semua
                        </button>
                    </div>
                </div>

                {{-- Daftar Kartu Atlet Mobile (Vertical Athlete Card Sesuai design.md) --}}
                <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                    @foreach($athleteView as $athleteId => $av)
                        @php
                            $mAthlete      = $av['athlete'];
                            $mCategory     = $av['category'];
                            $mBadgeBg      = match($mCategory) {
                                \App\Services\CompetitionPointService::LABEL_BAIK  => '#16a34a',
                                \App\Services\CompetitionPointService::LABEL_CUKUP => '#d97706',
                                \App\Services\CompetitionPointService::LABEL_GAGAL => '#dc2626',
                                default => '#6b7280',
                            };
                            $mGenderLabel  = $mAthlete->gender === 'L' ? 'PA' : 'PI';
                            $mGenderBadge  = $mAthlete->gender === 'L' ? 'badge-primary' : 'badge-accent';
                            $mAthleteNameLc = strtolower($mAthlete->name);
                        @endphp

                        <div
                            x-show="
                                (filterNama === '' || '{{ $mAthleteNameLc }}'.includes(filterNama.toLowerCase())) &&
                                (filterKu === '' || '{{ $mAthlete->ku }}' === filterKu) &&
                                (filterGender === '' || '{{ $mAthlete->gender }}' === filterGender)
                            "
                            class="athlete-mobile-card"
                            style="background: linear-gradient(160deg, rgba(10,22,40,0.9) 0%, rgba(6,15,34,0.95) 100%); border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; padding: 0;"
                        >
                            {{-- Header Kartu Atlet --}}
                            <div style="padding: 1rem; background: rgba(15, 32, 64, 0.4); border-bottom: 1px solid var(--border-color);">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.65rem; min-width: 0;">
                                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.05rem; color: #fff; flex-shrink: 0; overflow:hidden;">
                                            @if($mAthlete->photo)
                                                <img src="{{ asset('storage/'.$mAthlete->photo) }}" alt="{{ $mAthlete->name }}" style="width:100%; height:100%; object-fit:cover;">
                                            @else
                                                {{ strtoupper(substr($mAthlete->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div style="min-width: 0;">
                                            <a href="{{ route('athletes.show', $mAthlete->id) }}" style="font-size: 0.95rem; font-weight: 800; color: #fff; text-decoration: none; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $mAthlete->name }}
                                            </a>
                                            <div style="display: flex; gap: 0.35rem; margin-top: 0.2rem;">
                                                <span class="badge {{ $mGenderBadge }}" style="padding: 0.08rem 0.35rem; font-size: 0.65rem;">{{ $mGenderLabel }}</span>
                                                <span class="badge" style="background: rgba(142, 184, 216, 0.15); color: var(--silver-200); padding: 0.08rem 0.35rem; font-size: 0.65rem;">KU {{ $mAthlete->ku }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="text-align: right; flex-shrink: 0;">
                                        @if($av['counted_entries'] > 0)
                                            <div style="font-size: 0.7rem; color: var(--text-muted);">Poin</div>
                                            <div style="font-size: 1.05rem; font-weight: 900; color: #f1f5f9; line-height: 1.1;">
                                                {{ $av['total_points'] }} <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;">/ {{ $av['max_points'] }}</span>
                                            </div>
                                            <span style="display: inline-block; background: {{ $mBadgeBg }}; color: #fff; padding: 0.15rem 0.55rem; border-radius: 9999px; font-weight: 700; font-size: 0.68rem; margin-top: 0.2rem;">
                                                @if($mCategory === \App\Services\CompetitionPointService::LABEL_BAIK)✅
                                                @elseif($mCategory === \App\Services\CompetitionPointService::LABEL_CUKUP)⚠️
                                                @else ❌ @endif {{ $mCategory }} ({{ $av['percentage'] }}%)
                                            </span>
                                        @else
                                            <span style="color: var(--text-muted); font-style: italic; font-size: 0.75rem;">Belum bertanding</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Sub-Daftar Hasil Nomor Lomba Atlet --}}
                            <div style="padding: 0.75rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                @foreach($av['entry_results'] as $er)
                                    @php
                                        $mEntryRow  = $er['entry'];
                                        $mEvtRow    = $mEntryRow->competitionEvent;
                                        $mStrokeMap = [
                                            'bebas'    => 'Bebas',
                                            'dada'     => 'Dada',
                                            'kupu'     => 'Kupu-Kupu',
                                            'punggung' => 'Punggung',
                                            'ganti'    => 'Ganti',
                                            'estafet'  => 'Estafet',
                                        ];
                                        $mAcaraName = ($mStrokeMap[$mEvtRow->stroke] ?? ucfirst($mEvtRow->stroke)) . ' ' . $mEvtRow->distance . 'M';
                                        $mIsDnsOrDq = in_array($er['status_label'], ['DNS', 'DQ']);
                                        $mNotCounted = !$er['is_counted'] && $er['points'] === null;
                                    @endphp

                                    <div style="background: rgba(6, 15, 34, 0.7); border: 1px solid rgba(91, 168, 232, 0.08); border-radius: 0.65rem; padding: 0.65rem 0.75rem;">
                                        {{-- Header Baris Acara & Status --}}
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                                            <div style="font-size: 0.82rem; font-weight: 700; color: var(--silver-100);">
                                                🏊 {{ $mAcaraName }}
                                            </div>
                                            <div>
                                                @if($er['status_label'] === 'DNS')
                                                    <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.1rem 0.45rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 700;">DNS</span>
                                                @elseif($er['status_label'] === 'DQ')
                                                    <span style="background: rgba(217, 119, 6, 0.15); color: #d97706; border: 1px solid rgba(217, 119, 6, 0.3); padding: 0.1rem 0.45rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 700;">DQ</span>
                                                @elseif($er['is_debut'])
                                                    <span style="background: rgba(37, 99, 235, 0.15); color: #60a5fa; border: 1px solid rgba(37, 99, 235, 0.3); padding: 0.1rem 0.45rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 700;">Debut</span>
                                                @elseif($er['is_counted'])
                                                    <span style="background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); padding: 0.1rem 0.45rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 700;">Finish</span>
                                                @else
                                                    <span style="color: var(--text-muted); font-size: 0.7rem; font-style: italic;">Belum bertanding</span>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Waktu: PB (Limit) vs Final vs Selisih & Poin --}}
                                        <div style="display: grid; grid-template-columns: repeat(3, 1fr) auto; gap: 0.35rem; align-items: center; border-top: 1px dashed rgba(91, 168, 232, 0.08); padding-top: 0.4rem;">
                                            {{-- PB / Limit --}}
                                            <div>
                                                <div style="font-size: 0.62rem; color: var(--text-muted);">Limit (PB)</div>
                                                <div style="font-family: monospace; font-size: 0.78rem; font-weight: 700; color: var(--accent); margin-top: 0.05rem;">
                                                    @if($er['is_debut'] || $er['limit_cs'] === null)
                                                        <span style="color: var(--text-muted); font-weight: 400;">N/A</span>
                                                    @else
                                                        {{ $mEntryRow->seed_time }}
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Final Result --}}
                                            <div>
                                                <div style="font-size: 0.62rem; color: var(--text-muted);">Hasil</div>
                                                <div style="font-family: monospace; font-size: 0.82rem; font-weight: 800; color: #f1f5f9; margin-top: 0.05rem;">
                                                    @if($mNotCounted)
                                                        <span style="color: var(--text-muted); font-weight: 400; font-size: 0.72rem;">Belum</span>
                                                    @elseif($mIsDnsOrDq)
                                                        <span style="color: var(--text-muted);">—</span>
                                                    @else
                                                        {{ $mEntryRow->result->time_result ?? '—' }}
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Selisih Waktu (Bersih Tanpa Simbol Naik/Turun) --}}
                                            <div>
                                                <div style="font-size: 0.62rem; color: var(--text-muted);">Selisih</div>
                                                <div class="time-clean-diff" style="margin-top: 0.05rem;">
                                                    @if($mNotCounted || $mIsDnsOrDq)
                                                        <span style="color: var(--text-muted); font-weight: 400;">—</span>
                                                    @elseif($er['is_debut'])
                                                        <span style="color: #34d399; font-size: 0.7rem; font-family: inherit;">Baru</span>
                                                    @elseif($er['direction'] === 'faster')
                                                        <span class="time-faster">{{ \App\Services\CompetitionPointService::formatSelisih($er['selisih_cs'], 'faster') }}</span>
                                                    @elseif($er['direction'] === 'slower')
                                                        <span class="time-slower">{{ \App\Services\CompetitionPointService::formatSelisih($er['selisih_cs'], 'slower') }}</span>
                                                    @else
                                                        <span class="time-equal">{{ \App\Services\CompetitionPointService::formatSelisih(0, 'equal') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Poin --}}
                                            <div style="text-align: right; padding-left: 0.4rem;">
                                                <div style="font-size: 0.62rem; color: var(--text-muted);">Poin</div>
                                                <div style="font-weight: 900; font-size: 0.95rem; margin-top: 0.05rem;">
                                                    @if($er['points'] === null)
                                                        <span style="color: var(--text-muted); font-weight: 400;">—</span>
                                                    @elseif($er['points'] >= 10)
                                                        <span style="color: #22c55e;">+{{ $er['points'] }}</span>
                                                    @elseif($er['points'] >= 5)
                                                        <span style="color: #f59e0b;">+{{ $er['points'] }}</span>
                                                    @else
                                                        <span style="color: #ef4444;">{{ $er['points'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Footer Ringkas Kartu Atlet --}}
                            <div style="padding: 0.6rem 1rem; background: rgba(10, 22, 40, 0.8); border-top: 1px solid var(--border-color); font-size: 0.72rem; color: var(--text-muted); display: flex; justify-content: space-between; align-items: center;">
                                <span>{{ $av['counted_entries'] }} nomor dihitung</span>
                                <span style="font-weight: 700; color: var(--silver-200);">Total: {{ $av['total_points'] }} Poin</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="comp-hero-card" style="padding: 2.5rem 1rem; text-align: center;">
                    <div style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 0.5rem;">🎽</div>
                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--silver-200);">Belum Ada Atlet Terdaftar</div>
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem;">Gunakan tombol "Entry Atlet" untuk mendaftarkan atlet pada event ini.</div>
                </div>
            @endif
        </div>

        {{-- 3. Filter Bottom Sheet Modal (Mobile) Sesuai Bagian 2.C design.md --}}
        <div
            class="filter-bottom-sheet-overlay"
            x-show="mobileFilterOpen"
            x-transition.opacity
            @click.self="mobileFilterOpen = false"
            style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(4, 13, 26, 0.78); backdrop-filter: blur(4px);">

            <div
                class="filter-bottom-sheet"
                x-show="mobileFilterOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform translate-y-full"
                x-transition:enter-end="transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="transform translate-y-0"
                x-transition:leave-end="transform translate-y-full"
                style="position: fixed; bottom: 0; left: 0; right: 0; background: #0a1628; border-top: 1px solid rgba(91, 168, 232, 0.2); border-radius: 1.5rem 1.5rem 0 0; padding: 1.25rem 1.25rem 2rem 1.25rem; max-height: 80vh; overflow-y: auto;">

                <div class="bottom-sheet-handle" style="width: 40px; height: 4px; background: rgba(142, 184, 216, 0.3); border-radius: 2px; margin: 0 auto 1.25rem auto;"></div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h3 style="font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0;">⚙️ Filter Performa Atlet</h3>
                    <button type="button" @click="mobileFilterOpen = false" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; padding: 0.25rem;">✕</button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.1rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: var(--silver-200); display: block; margin-bottom: 0.4rem;">
                            Kelompok Umur (KU)
                        </label>
                        <select x-model="filterKu" class="form-input" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem; font-size: 0.875rem;">
                            <option value="">Semua Kelompok Umur</option>
                            @php $mKuList = collect($athleteView)->pluck('athlete.ku')->unique()->sort()->values(); @endphp
                            @foreach($mKuList as $ku)
                                <option value="{{ $ku }}">KU {{ $ku }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 0.8rem; font-weight: 700; color: var(--silver-200); display: block; margin-bottom: 0.4rem;">
                            Jenis Kelamin
                        </label>
                        <select x-model="filterGender" class="form-input" style="width: 100%; padding: 0.75rem; border-radius: 0.75rem; font-size: 0.875rem;">
                            <option value="">Semua (PA &amp; PI)</option>
                            <option value="L">PA (Putera)</option>
                            <option value="P">PI (Puteri)</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                        <button
                            type="button"
                            @click="mobileFilterOpen = false"
                            class="btn btn-primary"
                            style="flex: 1; padding: 0.85rem; border-radius: 0.75rem; font-weight: 700; font-size: 0.9rem; justify-content: center;">
                            Terapkan Filter
                        </button>
                        <button
                            type="button"
                            @click="resetFilters(); mobileFilterOpen = false;"
                            class="btn btn-accent"
                            style="padding: 0.85rem 1.25rem; border-radius: 0.75rem; font-weight: 600; font-size: 0.9rem; justify-content: center;">
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Export Bottom Sheet Modal (Mobile) --}}
        <div
            class="filter-bottom-sheet-overlay"
            x-show="mobileExportOpen"
            x-transition.opacity
            @click.self="mobileExportOpen = false"
            style="display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(4, 13, 26, 0.78); backdrop-filter: blur(4px);">

            <div
                class="filter-bottom-sheet"
                x-show="mobileExportOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="transform translate-y-full"
                x-transition:enter-end="transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="transform translate-y-0"
                x-transition:leave-end="transform translate-y-full"
                style="position: fixed; bottom: 0; left: 0; right: 0; background: #0a1628; border-top: 1px solid rgba(91, 168, 232, 0.2); border-radius: 1.5rem 1.5rem 0 0; padding: 1.25rem 1.25rem 2rem 1.25rem; max-height: 80vh; overflow-y: auto;">

                <div class="bottom-sheet-handle" style="width: 40px; height: 4px; background: rgba(142, 184, 216, 0.3); border-radius: 2px; margin: 0 auto 1.25rem auto;"></div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 800; color: #fff; margin: 0;">📥 Ekspor Formulir Excel Resmi</h3>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0.2rem 0 0 0;">Pilih format formulir standar untuk diunduh</p>
                    </div>
                    <button type="button" @click="mobileExportOpen = false" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; padding: 0.25rem;">✕</button>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="{{ route('competitions.export', ['competition' => $competition->id, 'form' => 'A1']) }}"
                       class="btn"
                       style="background: rgba(22, 163, 74, 0.15); border: 1px solid rgba(22, 163, 74, 0.35); color: #86efac; padding: 0.85rem 1rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; text-decoration: none; font-weight: 700;">
                        <span>📥 Form A1 (Perorangan)</span>
                        <span style="font-size: 0.75rem; opacity: 0.8;">.XLSX ↗</span>
                    </a>

                    <a href="{{ route('competitions.export', ['competition' => $competition->id, 'form' => 'A2']) }}"
                       class="btn"
                       style="background: rgba(22, 163, 74, 0.15); border: 1px solid rgba(22, 163, 74, 0.35); color: #86efac; padding: 0.85rem 1rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; text-decoration: none; font-weight: 700;">
                        <span>📥 Form A2 (Gaya Ganti)</span>
                        <span style="font-size: 0.75rem; opacity: 0.8;">.XLSX ↗</span>
                    </a>

                    <a href="{{ route('competitions.export', ['competition' => $competition->id, 'form' => 'A3']) }}"
                       class="btn"
                       style="background: rgba(22, 163, 74, 0.15); border: 1px solid rgba(22, 163, 74, 0.35); color: #86efac; padding: 0.85rem 1rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: space-between; text-decoration: none; font-weight: 700;">
                        <span>📥 Form A3 (Estafet)</span>
                        <span style="font-size: 0.75rem; opacity: 0.8;">.XLSX ↗</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- 5. Sticky Bottom Action Bar di Mobile --}}
        <div class="mobile-action-bar">
            <a href="{{ route('competitions.index') }}" class="btn-action-mobile outline">
                ⬅ Kembali
            </a>
            <a href="{{ route('competitions.entries.create', $competition->id) }}" class="btn-action-mobile primary">
                👥 Entry Atlet
            </a>
            <a href="{{ route('competitions.results.edit', $competition->id) }}" class="btn-action-mobile" style="background: #f97316; color: #fff;">
                ⏱️ Waktu
            </a>
        </div>

    </div>{{-- /.mobile-only --}}

</div>{{-- /x-data --}}
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
