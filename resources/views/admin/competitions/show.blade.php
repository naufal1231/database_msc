@extends('layouts.admin')

@section('title', 'Detail Event - ' . $competition->name)
@section('header_title', 'Dashboard Event Perlombaan')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;">

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

    <div class="data-card" style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h4 style="font-weight: bold; color: #ffffffff; font-size: 1.05rem; margin: 0;">📊 Ekspor Hasil Lomba Resmi (Excel)</h4>
                <p style="font-size: 0.825rem; color: #ffffffff; margin: 0.25rem 0 0 0;">Ekspor hasil catatan waktu atlet ke format formulir standar A1, A2, atau A3.</p>
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

    <div x-data="{ activeTab: 'nomor' }">

        <div style="display: flex; gap: 0; border-radius: 0.75rem 0.75rem 0 0; overflow: hidden; border: 1px solid var(--border-color); border-bottom: none; width: fit-content;">
            <button
                id="tab-btn-nomor"
                @click="activeTab = 'nomor'"
                :style="activeTab === 'nomor'
                    ? 'background:#0369a1; color:#fff; font-weight:700;'
                    : 'background:var(--bg-light); color:var(--text-muted); font-weight:400;'"
                style="padding: 0.65rem 1.4rem; border: none; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; display:flex; align-items:center; gap:0.4rem;">
                🏊 Per Nomor Lomba
            </button>
            <button
                id="tab-btn-atlet"
                @click="activeTab = 'atlet'"
                :style="activeTab === 'atlet'
                    ? 'background:#0369a1; color:#fff; font-weight:700;'
                    : 'background:var(--bg-light); color:var(--text-muted); font-weight:400;'"
                style="padding: 0.65rem 1.4rem; border: none; cursor: pointer; font-size: 0.9rem; transition: all 0.2s; display:flex; align-items:center; gap:0.4rem; border-left: 1px solid var(--border-color);">
                🎽 Per Atlet
            </button>
        </div>

        {{-- TAB 1: PER NOMOR LOMBA --}}
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

        {{-- TAB 2: PER ATLET --}}
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

                {{-- Ringkasan Event --}}
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

                {{-- Filter --}}
                <div x-data="{ filterNama: '', filterKu: '', filterGender: '' }" style="margin-bottom: 1.5rem;">
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

                    {{-- Daftar kartu atlet --}}
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1.5rem;">
                        @foreach($athleteView as $athleteId => $av)
                            @php
                                $athlete     = $av['athlete'];
                                $category    = $av['category'];
                                $badgeBg     = match($category) {
                                    \App\Services\CompetitionPointService::LABEL_BAIK  => '#16a34a',
                                    \App\Services\CompetitionPointService::LABEL_CUKUP => '#d97706',
                                    \App\Services\CompetitionPointService::LABEL_GAGAL => '#dc2626',
                                    default => '#6b7280',
                                };
                                $genderLabel = $athlete->gender === 'L' ? 'PA' : 'PI';
                                $genderBadge = $athlete->gender === 'L' ? 'badge-primary' : 'badge-accent';
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
                                        <div style="width: 42px; height: 42px; border-radius: 50%; background: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; color: #fff; flex-shrink: 0;">
                                            {{ strtoupper(substr($athlete->name, 0, 1)) }}
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

                                {{-- Tabel entry --}}
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

                                {{-- Footer kartu --}}
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

                            </div>{{-- /kartu --}}
                        @endforeach
                    </div>{{-- /daftar kartu --}}

                </div>{{-- /filter x-data --}}

            @else
                <div style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                    Belum ada atlet yang terdaftar pada event ini.
                </div>
            @endif
        </div>{{-- /tab-atlet --}}

    </div>{{-- /x-data activeTab --}}

</div>{{-- /.outer-flex --}}
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
