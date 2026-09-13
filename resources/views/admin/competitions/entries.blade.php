@extends('layouts.admin')

@section('title', 'Pendaftaran Event - ' . $competition->name)
@section('header_title', 'Pendaftaran Atlet & Nomor Lomba')

@section('content')
<div class="data-card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Pendaftaran Event: {{ $competition->name }}</h3>
            <p class="club-subtitle">Pilih nomor lomba untuk setiap atlet di bawah ini.</p>
        </div>
        <a href="{{ route('competitions.show', $competition->id) }}" class="form-link">
            ◀ Kembali ke Event
        </a>
    </div>
    <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">

    <!-- Filter Bar (Reload Page) -->
    <form method="GET" action="{{ route('competitions.entries.create', $competition->id) }}" class="filter-bar" style="margin-bottom: 2rem; background-color: var(--bg-light); padding: 1rem; border-radius: 0.75rem;">
        <div class="form-group" style="margin-bottom: 0; width: 220px;">
            <label for="class_id" class="form-label" style="font-size: 0.8rem;">Filter Kelas Latihan</label>
            <select name="class_id" id="class_id" class="form-input">
                <option value="">-- Semua Kelas --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0; width: 180px;">
            <label for="ku" class="form-label" style="font-size: 0.8rem;">Filter KU (Tahun Lahir)</label>
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
                Filter Atlet
            </button>
            <a href="{{ route('competitions.entries.create', $competition->id) }}" class="btn btn-accent" style="padding: 0.75rem 1.25rem; background-color: var(--text-muted); color: #fff;">
                Reset
            </a>
        </div>
    </form>

    @if($events->count() > 0)
        @if($athletes->count() > 0)
            <form method="POST" action="{{ route('competitions.entries.store', $competition->id) }}">
                @csrf

                <div class="table-responsive" style="margin-bottom: 2rem;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NAMA ATLET & DETAIL</th>
                                @foreach($events as $evt)
                                    <th style="font-size: 0.75rem; text-align: center; padding: 0.75rem 0.4rem; border-left: 1px solid var(--border-color); white-space: normal; min-width: 75px; max-width: 95px; vertical-align: middle;">
                                        Gaya {{ ucfirst($evt->stroke) }} ({{ $evt->distance }}M)
                                    </th>
                                @endforeach
                        </thead>
                        <tbody>
                            @foreach($athletes as $athlete)
                                <!-- Register Displayed Athlete IDs -->
                                <input type="hidden" name="displayed_athlete_ids[]" value="{{ $athlete->id }}">

                                <tr>
                                    <td>
                                        <div style="font-weight: 700; color: var(--primary-hover);">{{ $athlete->name }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            KU {{ $athlete->ku }} | {{ $athlete->gender === 'L' ? 'PA' : 'PI' }} | {{ $athlete->currentClass->name }}
                                        </div>
                                    </td>
                                    @foreach($events as $evt)
                                        <td style="text-align: center; border-left: 1px solid var(--border-color); background-color: rgba(10, 22, 40, 0.4); vertical-align: top; padding: 0.75rem 0.5rem;">
                                            @php
                                                $eventKey = $evt->stroke . '_' . $evt->distance . '_' . $evt->category;
                                                $pbTime = isset($athletePbs[$athlete->id][$eventKey]) ? $athletePbs[$athlete->id][$eventKey] : null;

                                                $isChecked = isset($existingEntries[$athlete->id]) && $existingEntries[$athlete->id]->contains('competition_event_id', $evt->id);

                                                // Disable checkbox if result is already inputted
                                                $hasResult = false;
                                                $existingEntry = null;
                                                if ($isChecked) {
                                                    $existingEntry = $existingEntries[$athlete->id]->firstWhere('competition_event_id', $evt->id);
                                                    $hasResult = $existingEntry && $existingEntry->result !== null;
                                                }

                                                $initialValue = $existingEntry ? $existingEntry->seed_time : ($pbTime ?? '');
                                            @endphp

                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.4rem;">
                                                @if($hasResult)
                                                    <!-- If result already exists, keep checkbox checked and locked/disabled to prevent deletion of results -->
                                                    <input type="hidden" name="entries[{{ $athlete->id }}][]" value="{{ $evt->id }}">
                                                    <input type="checkbox" checked disabled style="cursor: not-allowed; width: 1.25rem; height: 1.25rem; accent-color: var(--text-muted);">

                                                    <span style="font-family: monospace; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); display: block; margin-top: 0.2rem;">
                                                        {{ $existingEntry->seed_time ?: '-' }}
                                                    </span>
                                                    <input type="hidden" name="seed_times[{{ $athlete->id }}][{{ $evt->id }}]" value="{{ $existingEntry->seed_time }}">
                                                @else
                                                    <input type="checkbox"
                                                           name="entries[{{ $athlete->id }}][]"
                                                           value="{{ $evt->id }}"
                                                           id="chk_{{ $athlete->id }}_{{ $evt->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           onchange="toggleSeedInput(this, {{ $athlete->id }}, {{ $evt->id }})"
                                                           style="cursor: pointer; width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">

                                                    <div id="seed_container_{{ $athlete->id }}_{{ $evt->id }}"
                                                         style="{{ $isChecked ? 'display: block;' : 'display: none;' }} margin-top: 0.35rem; width: 85px;">

                                                        <input type="text"
                                                               name="seed_times[{{ $athlete->id }}][{{ $evt->id }}]"
                                                               id="seed_input_{{ $athlete->id }}_{{ $evt->id }}"
                                                               class="form-input time-mask"
                                                               placeholder="00.00.00"
                                                               value="{{ $initialValue }}"
                                                               style="padding: 0.25rem 0.4rem; font-family: monospace; font-size: 0.8rem; text-align: center; font-weight: 700; border-radius: 0.4rem; border-color: rgba(91, 168, 232, 0.2);"
                                                               {{ $isChecked ? 'required' : 'disabled' }}>

                                                        @if($pbTime)
                                                            <small style="color: #4ade80; font-size: 0.65rem; font-weight: 800; display: block; margin-top: 0.15rem; line-height: 1.1;">
                                                                Auto PB
                                                            </small>
                                                        @else
                                                            <small class="limit-req-label" style="color: #f87171; font-size: 0.65rem; font-weight: 800; display: block; margin-top: 0.15rem; line-height: 1.1;">
                                                                Wajib Limit
                                                            </small>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary" style="max-width: 250px;">
                    Simpan Pendaftaran (Entry)
                </button>
            </form>
        @else
            <div style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                Tidak ada atlet yang sesuai dengan filter kelas/KU di atas.
            </div>
        @endif
    @else
        <div style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
            Belum ada nomor lomba yang dibuka di event ini. Harap tambahkan nomor lomba terlebih dahulu.
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function toggleSeedInput(checkbox, athleteId, eventId) {
        const container = document.getElementById(`seed_container_${athleteId}_${eventId}`);
        const input = document.getElementById(`seed_input_${athleteId}_${eventId}`);
        if (checkbox.checked) {
            container.style.display = 'block';
            input.removeAttribute('disabled');
            input.setAttribute('required', 'required');
            input.focus();
        } else {
            container.style.display = 'none';
            input.setAttribute('disabled', 'disabled');
            input.removeAttribute('required');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        function applyMask(input) {
            input.addEventListener('input', function(e) {
                let val = this.value.replace(/[^0-9]/g, '');
                if (val.length > 6) {
                    val = val.substr(0, 6);
                }
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
        }

        document.querySelectorAll('.time-mask').forEach(input => {
            applyMask(input);
        });
    });
</script>
@endsection
