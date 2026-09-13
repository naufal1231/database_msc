@extends('layouts.admin')

@section('title', 'Catat Waktu - ' . $competition->name)
@section('header_title', 'Catatan Waktu Perlombaan')

@section('content')
<div class="data-card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Catat Hasil Waktu: {{ $competition->name }}</h3>
            <p class="club-subtitle">Input hasil waktu (format 6 digit: menit.detik.desimal) per nomor lomba.</p>
        </div>
        <a href="{{ route('competitions.show', $competition->id) }}" class="form-link">
            ◀ Kembali ke Event
        </a>
    </div>
    <hr style="margin-bottom: 2rem; border: none; border-bottom: 1px solid var(--border-color);">

    @if($events->count() > 0)
        <form method="POST" action="{{ route('competitions.results.store', $competition->id) }}">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 3rem; margin-bottom: 2.5rem;">
                @foreach($events as $evt)
                    @php
                        $evtEntries = isset($entries[$evt->id]) ? $entries[$evt->id] : collect();
                    @endphp

                    <div style="background: #0369a1; border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.25rem;">
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: #ffffffff; border-bottom: 1px dashed var(--border-color); padding-bottom: 0.5rem; margin-bottom: 1rem;">
                            Gaya {{ ucfirst($evt->stroke) }} - {{ $evt->distance }} Meter
                            <small style="font-weight: 400; font-size: 0.8rem; color: #ffffffff; text-transform: uppercase; margin-left: 0.5rem;">
                                ({{ $evt->category === 'perorangan' ? 'Perorangan' : 'Estafet Mix' }})
                            </small>
                        </h4>

                        @if($evtEntries->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px; text-align: center;">NO</th>
                                            <th>NAMA ATLET</th>
                                            <th style="text-align: center; width: 100px;">KU</th>
                                            <th style="text-align: center; width: 100px;">PA/PI</th>
                                            <th style="text-align: center; width: 160px;">STATUS HARI-H</th>
                                            <th style="text-align: center; width: 220px;">WAKTU (MM.SS.CC)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evtEntries as $index => $entry)
                                            @php
                                                $resStatus = old("results.{$entry->id}.status", $entry->result ? $entry->result->status : 'finish');
                                                $resTime = old("results.{$entry->id}.time_result", $entry->result ? $entry->result->time_result : '');

                                                if ($resStatus !== 'finish' && empty($resTime)) {
                                                    $resTime = '';
                                                }
                                            @endphp
                                            <tr>
                                                <td style="text-align: center; font-weight: 600; color: var(--text-muted);">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td style="font-weight: 700; color: var(--text-dark);">
                                                    {{ $entry->athlete->name }}
                                                </td>
                                                <td style="text-align: center;">KU {{ $entry->athlete->ku }}</td>
                                                <td style="text-align: center;">
                                                    <span class="badge {{ $entry->athlete->gender === 'L' ? 'badge-primary' : 'badge-accent' }}" style="padding: 0.15rem 0.35rem; font-size: 0.7rem;">
                                                        {{ $entry->athlete->gender === 'L' ? 'PA' : 'PI' }}
                                                    </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <select name="results[{{ $entry->id }}][status]" class="form-input status-select" data-entry-id="{{ $entry->id }}" style="padding: 0.4rem 0.75rem; font-size: 0.85rem;" required>
                                                        <option value="finish" {{ $resStatus === 'finish' ? 'selected' : '' }}>FINISH</option>
                                                        <option value="DNS" {{ $resStatus === 'DNS' ? 'selected' : '' }}>DNS (No Show)</option>
                                                        <option value="DQ" {{ $resStatus === 'DQ' ? 'selected' : '' }}>DQ (Disqualified)</option>
                                                    </select>
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="text"
                                                           name="results[{{ $entry->id }}][time_result]"
                                                           id="time_{{ $entry->id }}"
                                                           class="form-input time-mask"
                                                           placeholder="99.99.99"
                                                           style="padding: 0.4rem 0.75rem; font-family: monospace; font-size: 1rem; text-align: center; font-weight: 700; letter-spacing: 0.05em;"
                                                           value="{{ $resTime }}"
                                                           {{ $resStatus !== 'finish' ? 'disabled' : 'required' }}>
                                                    @error("results.{$entry->id}.time_result")
                                                        <span style="color: #ef4444; font-size: 0.75rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div style="text-align: center; padding: 1.5rem; color: var(--text-muted); font-style: italic; font-size: 0.85rem;">
                                Belum ada atlet yang didaftarkan pada nomor ini.
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary" style="max-width: 250px;">
                💾 Simpan Catatan Waktu
            </button>
        </form>
    @else
        <div style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
            Belum ada nomor lomba yang didefinisikan untuk event ini.
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup Status change triggers (Enable/Disable time inputs)
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const entryId = this.dataset.entryId;
                const timeInput = document.getElementById('time_' + entryId);

                if (this.value !== 'finish') {
                    timeInput.value = '';
                    timeInput.setAttribute('disabled', 'disabled');
                    timeInput.removeAttribute('required');
                } else {
                    timeInput.removeAttribute('disabled');
                    timeInput.setAttribute('required', 'required');
                }
            });
        });

        // Time Input Mask for standard format: 00.32.94 (MM.SS.CC)
        document.querySelectorAll('.time-mask').forEach(input => {
            input.addEventListener('input', function(e) {
                // Remove anything that isn't a number
                let val = this.value.replace(/[^0-9]/g, '');

                // Limit to maximum 6 digits
                if (val.length > 6) {
                    val = val.substr(0, 6);
                }

                // Add dots dynamically
                let formatted = '';
                if (val.length > 0) {
                    formatted += val.substr(0, 2);
                }
                if (val.length > 2) {
                    formatted += '.' + val.substr(2, 2);
                }
                if (val.length > 4) {
                    formatted += '.' + val.substr(4, 2);
                }

                this.value = formatted;
            });
        });
    });
</script>
@endsection
