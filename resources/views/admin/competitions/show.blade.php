@extends('layouts.admin')

@section('title', 'Detail Event - ' . $competition->name)
@section('header_title', 'Dashboard Event Perlombaan')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- Event Info Header -->
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
                <span class="info-value">
                    @if($competition->status === 'akan_datang')
                        <span class="badge badge-primary">Akan Datang</span>
                    @elseif($competition->status === 'berlangsung')
                        <span class="badge badge-accent">Berlangsung</span>
                    @else
                        <span class="badge badge-success">Selesai</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Export Excel Panel Card -->
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

    <!-- Race Events and Results -->
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">🏊‍♂️ Nomor Lomba & Hasil Catatan Waktu</h3>
        </div>
        <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">

        <!-- List Nomor Lomba -->
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

                        <!-- Entries and Results table under this event -->
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
                                            <td style="text-align: center; font-weight: 600; color: var(--text-muted);">
                                                {{ $index + 1 }}
                                            </td>
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
                                            <td style="text-align: center; font-family: monospace; font-weight: 700; font-size: 1.05rem; color: var(--primary-hover);">
                                                @if($entry->result)
                                                    @if($entry->result->status === 'finish')
                                                        {{ $entry->result->time_result }}
                                                    @else
                                                        <span style="color: var(--text-muted); font-weight: 400; font-family: inherit; font-size: 0.9rem;">
                                                            {{ $entry->result->status }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span style="color: var(--text-muted); font-weight: 400; font-family: inherit; font-size: 0.9rem; font-style: italic;">
                                                        Belum bertanding
                                                    </span>
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
</div>
@endsection
