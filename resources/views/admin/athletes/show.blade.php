@extends('layouts.admin')

@section('title', 'Detail Atlet - ' . $athlete->name)
@section('header_title', 'Profil Detail Atlet')

@section('content')

{{-- ═══════════════════════════════════════
     MOBILE LAYOUT
═══════════════════════════════════════ --}}
<div class="mobile-only mobile-has-action-bar" style="display: flex; flex-direction: column; gap: 0.875rem;">

    {{-- ── Profile Hero Card ─────────────────── --}}
    <div class="data-card" style="padding: 0; overflow: hidden;">
        {{-- Top accent bar --}}
        <div style="height: 4px; background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);"></div>
        <div class="m-profile-hero">
            @if($athlete->photo)
                <img src="{{ asset('storage/' . $athlete->photo) }}"
                     alt="{{ $athlete->name }}"
                     style="width: 90px; height: 90px; border-radius: 50%; object-fit: cover;
                            border: 3px solid var(--primary);
                            box-shadow: 0 0 0 3px rgba(38,132,212,0.25);">
            @else
                <div style="width: 90px; height: 90px; border-radius: 50%;
                            background: var(--navy-700); border: 3px solid var(--glass-border);
                            display: flex; align-items: center; justify-content: center; font-size: 2.5rem;">
                    👤
                </div>
            @endif

            <div>
                <div class="m-profile-name">{{ $athlete->name }}</div>
                <div class="m-profile-sub">KU {{ $athlete->ku }}</div>
            </div>

            <div class="m-profile-badges">
                <span class="m-chip {{ $athlete->gender === 'L' ? 'm-chip-pa' : 'm-chip-pi' }}" style="font-size: 0.78rem; padding: 0.25rem 0.75rem;">
                    {{ $athlete->gender === 'L' ? '♂ PA (Putera)' : '♀ PI (Puteri)' }}
                </span>
                <span class="m-chip m-chip-blue" style="font-size: 0.78rem; padding: 0.25rem 0.75rem;">
                    {{ $athlete->currentClass->name }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Accordion: Data Pribadi ───────────── --}}
    <div class="m-section-accordion open">
        <div class="m-section-header">
            <span class="m-section-header-title">📋 Data Pribadi</span>
            <span class="m-section-chevron">▾</span>
        </div>
        <div class="m-section-body">
            <div class="m-section-body-inner">
                <div class="m-info-row">
                    <span class="m-info-row-label">Tanggal Lahir</span>
                    <span class="m-info-row-value">{{ $athlete->birth_date->format('d F Y') }}</span>
                </div>
                <div class="m-info-row">
                    <span class="m-info-row-label">Bergabung</span>
                    <span class="m-info-row-value">{{ $athlete->join_date->format('d F Y') }}</span>
                </div>
                <div class="m-info-row">
                    <span class="m-info-row-label">Kelas Aktif</span>
                    <span class="m-info-row-value" style="color: var(--primary);">{{ $athlete->currentClass->name }}</span>
                </div>
                <div class="m-info-row">
                    <span class="m-info-row-label">Orang Tua</span>
                    <span class="m-info-row-value">
                        @if($athlete->parentUser)
                            {{ $athlete->parentUser->name }}<br>
                            <small style="color: var(--text-muted); font-weight: 400;">{{ $athlete->parentUser->phone }}</small>
                        @else
                            <span style="color: var(--text-muted); font-style: italic; font-weight: 400;">Belum ditautkan</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Accordion: Personal Best ──────────── --}}
    @if($personalBests->count() > 0)
    <div class="m-section-accordion open">
        <div class="m-section-header">
            <span class="m-section-header-title">🏆 Personal Best</span>
            <span class="m-section-chevron">▾</span>
        </div>
        <div class="m-section-body">
            <div class="m-section-body-inner">
                @foreach($personalBests as $pb)
                <div class="m-info-row" style="flex-direction: column; align-items: flex-start; gap: 0.3rem;">
                    <span style="font-size: 0.78rem; color: var(--text-muted); font-weight: 600;">
                        Gaya {{ ucfirst($pb['stroke']) }} {{ $pb['distance'] }}m
                        &nbsp;<span style="opacity: 0.6;">({{ $pb['category'] === 'perorangan' ? 'Perorangan' : 'Estafet Mix' }})</span>
                    </span>
                    <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                        <span style="font-family: monospace; font-size: 1rem; font-weight: 700; color: var(--primary-hover);">
                            {{ $pb['time_result'] }}
                        </span>
                        <span style="font-size: 0.72rem; color: var(--text-muted); text-align: right;">
                            {{ $pb['competition_name'] }}<br>
                            <span style="opacity: 0.8;">{{ date('d/m/Y', strtotime($pb['competition_date'])) }}</span>
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── Accordion: Grafik Progres ─────────── --}}
    @if($personalBests->count() > 0)
    <div class="m-section-accordion">
        <div class="m-section-header">
            <span class="m-section-header-title">📈 Grafik Progres</span>
            <span class="m-section-chevron">▾</span>
        </div>
        <div class="m-section-body">
            <div class="m-section-body-inner">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="chart_event_selector_mobile" class="form-label" style="font-size: 0.78rem;">
                        Pilih Nomor Lomba
                    </label>
                    <select id="chart_event_selector_mobile" class="form-input" style="padding: 0.5rem 1rem;">
                        @foreach($personalBests as $pb)
                            <option value="{{ $pb['event_key'] }}">{{ $pb['event_label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="height: 220px; position: relative;">
                    <canvas id="progressChartMobile"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Accordion: Riwayat Kelas ──────────── --}}
    <div class="m-section-accordion">
        <div class="m-section-header">
            <span class="m-section-header-title">⏳ Riwayat Kelas</span>
            <span class="m-section-chevron">▾</span>
        </div>
        <div class="m-section-body">
            <div class="m-section-body-inner">
                @forelse($athlete->classHistories as $history)
                <div class="m-info-row" style="flex-direction: column; align-items: flex-start; gap: 0.3rem;">
                    <span style="font-weight: 700; color: var(--primary-hover); font-size: 0.85rem;">
                        {{ $history->swimClass->name }}
                    </span>
                    <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
                        <span style="font-size: 0.75rem; color: var(--text-muted);">
                            {{ $history->start_date->format('d/m/Y') }} →
                            @if($history->end_date)
                                {{ $history->end_date->format('d/m/Y') }}
                            @else
                                <span class="m-chip m-chip-blue" style="font-size: 0.65rem; padding: 0.1rem 0.45rem;">Aktif</span>
                            @endif
                        </span>
                        <span style="font-size: 0.72rem; color: var(--text-muted);">
                            oleh {{ $history->changedBy->name }}
                        </span>
                    </div>
                    @if($history->notes)
                    <span style="font-size: 0.75rem; color: var(--text-light-muted); font-style: italic;">
                        {{ $history->notes }}
                    </span>
                    @endif
                </div>
                @empty
                    <div style="text-align: center; padding: 1.5rem; color: var(--text-muted); font-style: italic; font-size: 0.85rem;">
                        Riwayat kelas tidak ditemukan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bottom spacer for action bar --}}
    <div style="height: 0.5rem;"></div>
</div>

{{-- ── Sticky Action Bar (mobile) ──────────── --}}
<div class="mobile-action-bar">
    <a href="{{ route('athletes.index') }}" class="btn btn-primary"
       style="background-color: var(--text-muted); color: #fff;">
        ◀ Kembali
    </a>
    <a href="{{ route('athletes.edit', $athlete->id) }}" class="btn btn-primary"
       style="background-color: #ffb700; color: #fff;">
        📝 Edit
    </a>
    @if($athlete->parentUser && $athlete->parentUser->phone)
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $athlete->parentUser->phone) }}"
       target="_blank" rel="noopener"
       class="btn btn-primary"
       style="background-color: #25D366; color: #fff;">
        💬 WA
    </a>
    @endif
</div>


{{-- ═══════════════════════════════════════
     DESKTOP LAYOUT (unchanged)
═══════════════════════════════════════ --}}
<div style="display: flex; flex-direction: column; gap: 2rem;" class="desktop-only">

    {{-- Profile Card --}}
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">Profil Atlet</h3>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('event-history.show', $athlete->id) }}" class="btn btn-primary" style="background-color: #0369a1; color: #fff; max-width: 150px;">
                    📊 Riwayat Event
                </a>
                <a href="{{ route('athletes.edit', $athlete->id) }}" class="btn btn-primary" style="background-color: #ffb700; color: #fff; max-width: 120px;">
                    📝 Edit
                </a>
                <a href="{{ route('athletes.index') }}" class="btn btn-primary" style="background-color: var(--text-muted); color: #fff; max-width: 120px;">
                    ◀ Kembali
                </a>
            </div>
        </div>
        <hr style="margin-bottom: 2rem; border: none; border-bottom: 1px solid var(--border-color);">

        <div class="profile-grid">
            {{-- Photo column --}}
            <div class="profile-photo-card">
                @if($athlete->photo)
                    <img src="{{ asset('storage/' . $athlete->photo) }}" alt="Foto {{ $athlete->name }}" class="athlete-photo-lg">
                @else
                    <div style="width: 180px; height: 180px; border-radius: 1rem; background-color: #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 5rem; color: #a0aec0; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                        👤
                    </div>
                @endif
                <div style="margin-top: 1rem;">
                    <span class="badge {{ $athlete->gender === 'L' ? 'badge-primary' : 'badge-accent' }}" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;">
                        {{ $athlete->gender === 'L' ? 'PA (Putera)' : 'PI (Puteri)' }}
                    </span>
                </div>
            </div>

            {{-- Details column --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div style="border-left: 4px solid var(--primary); padding-left: 1rem; margin-bottom: 0.5rem;">
                    <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--primary-hover);">{{ $athlete->name }}</h2>
                    <span style="font-size: 1rem; color: var(--text-muted); font-weight: 600;">KU {{ $athlete->ku }}</span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Tanggal Lahir</span>
                        <span class="info-value">{{ $athlete->birth_date->format('d F Y') }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Kelas Latihan Saat Ini</span>
                        <span class="info-value" style="color: var(--primary);">{{ $athlete->currentClass->name }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Tanggal Bergabung</span>
                        <span class="info-value">{{ $athlete->join_date->format('d F Y') }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Orang Tua / Wali</span>
                        <span class="info-value">
                            @if($athlete->parentUser)
                                {{ $athlete->parentUser->name }} <small style="color: var(--text-muted); font-weight: 400;">({{ $athlete->parentUser->phone }})</small>
                            @else
                                <span style="color: var(--text-muted); font-style: italic; font-weight: 400;">Belum ditautkan</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PB and Progress Section --}}
    @if($personalBests->count() > 0)
    <div class="grid-pb-chart" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
        {{-- Personal Best Card --}}
        <div class="data-card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">🏆 Catatan Waktu Terbaik (Personal Best)</h3>
            </div>
            <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">
            <div class="table-responsive">
                <table class="table" style="font-size: 0.9rem;">
                    <thead>
                        <tr>
                            <th>NOMOR LOMBA</th>
                            <th style="text-align: center; width: 110px;">WAKTU (PB)</th>
                            <th>EVENT & TANGGAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personalBests as $pb)
                            <tr>
                                <td style="font-weight: 700; color: var(--primary-hover);">
                                    Gaya {{ ucfirst($pb['stroke']) }} - {{ $pb['distance'] }}m
                                    <br><small style="color: var(--text-muted); font-weight: 400; text-transform: uppercase;">({{ $pb['category'] === 'perorangan' ? 'Perorangan' : 'Estafet Mix' }})</small>
                                </td>
                                <td style="text-align: center; font-family: monospace; font-weight: 700; font-size: 1rem; color: var(--primary-hover);">
                                    {{ $pb['time_result'] }}
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $pb['competition_name'] }}</span>
                                    <br><small style="color: var(--text-muted);">{{ date('d/m/Y', strtotime($pb['competition_date'])) }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Progress Chart Card --}}
        <div class="data-card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">📈 Grafik Progres Perkembangan Waktu</h3>
            </div>
            <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="chart_event_selector" class="form-label" style="font-size: 0.85rem;">Pilih Nomor Lomba</label>
                <select id="chart_event_selector" class="form-input" style="padding: 0.5rem 1rem;">
                    @foreach($personalBests as $pb)
                        <option value="{{ $pb['event_key'] }}">{{ $pb['event_label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div style="height: 250px; position: relative;">
                <canvas id="progressChart"></canvas>
            </div>
        </div>
    </div>
    @else
    <div class="data-card" style="text-align: center; padding: 3rem 2rem; background-color: #f8fafc; border: 1px dashed var(--border-color);">
        <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">⏱️</div>
        <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem;">Belum Ada Data Personal Best</h4>
        <p style="font-size: 0.875rem; color: var(--text-light-muted); max-width: 400px; margin: 0 auto; line-height: 1.5;">
            Tabel waktu terbaik dan grafik perkembangan akan muncul setelah atlet ini memiliki riwayat hasil perlombaan.
        </p>
    </div>
    @endif

    {{-- Class History Card --}}
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">⏳ Riwayat Kenaikan & Perpindahan Kelas</h3>
        </div>
        <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 200px;">KELAS LATIHAN</th>
                        <th style="width: 150px;">TANGGAL MULAI</th>
                        <th style="width: 150px;">TANGGAL SELESAI</th>
                        <th>CATATAN PELATIH</th>
                        <th style="width: 180px;">DICATAT OLEH</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($athlete->classHistories as $history)
                        <tr>
                            <td style="font-weight: 700; color: var(--primary-hover);">
                                {{ $history->swimClass->name }}
                            </td>
                            <td>{{ $history->start_date->format('d/m/Y') }}</td>
                            <td>
                                @if($history->end_date)
                                    {{ $history->end_date->format('d/m/Y') }}
                                @else
                                    <span class="badge badge-success" style="font-size: 0.7rem;">Aktif Saat Ini</span>
                                @endif
                            </td>
                            <td>
                                {{ $history->notes ?? '-' }}
                            </td>
                            <td style="font-weight: 500;">
                                {{ $history->changedBy->name }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted); font-style: italic;">
                                Riwayat kelas tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($personalBests->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);

        function formatCentiseconds(totalCs) {
            const minutes = Math.floor(totalCs / 6000);
            const rem = totalCs % 6000;
            const seconds = Math.floor(rem / 100);
            const centiseconds = Math.round(rem % 100);
            return String(minutes).padStart(2, '0') + '.' + 
                   String(seconds).padStart(2, '0') + '.' + 
                   String(centiseconds).padStart(2, '0');
        }

        function buildChart(canvasId, selectorId) {
            const selector = document.getElementById(selectorId);
            const canvas   = document.getElementById(canvasId);
            if (!selector || !canvas) return;

            const ctx = canvas.getContext('2d');
            let chart = null;

            function update(eventKey) {
                const rawPoints = chartData[eventKey] || [];
                const points = [...rawPoints].sort((a, b) => new Date(a.competition_date) - new Date(b.competition_date));
                const labels = points.map(p => p.competition_name + ' (' + new Date(p.competition_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: '2-digit'}) + ')');
                const dataValues = points.map(p => p.centiseconds / 100);

                if (chart) chart.destroy();

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Catatan Waktu (Detik)',
                            data: dataValues,
                            borderColor: '#0284c7',
                            borderWidth: 3,
                            pointBackgroundColor: '#f97316',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            tension: 0.15,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                reverse: true,
                                ticks: {
                                    callback: function(value) { return formatCentiseconds(value * 100); }
                                },
                                title: { display: true, text: 'Waktu (MM.SS.CC)' }
                            },
                            x: { grid: { display: false } }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const valueInCs = context.raw * 100;
                                        const origRecord = points[context.dataIndex];
                                        return ' Waktu: ' + formatCentiseconds(valueInCs) + ' (Peringkat ' + (origRecord.rank || '-') + ')';
                                    }
                                }
                            },
                            legend: { display: false }
                        }
                    }
                });
            }

            if (selector.value) {
                update(selector.value);
                selector.addEventListener('change', function () { update(this.value); });
            }
        }

        // Desktop chart
        buildChart('progressChart', 'chart_event_selector');
        // Mobile chart
        buildChart('progressChartMobile', 'chart_event_selector_mobile');

        // ── Accordion toggle ──────────────────────
        document.querySelectorAll('.m-section-header').forEach(function (header) {
            header.addEventListener('click', function () {
                const accordion = this.closest('.m-section-accordion');
                const wasOpen = accordion.classList.contains('open');
                accordion.classList.toggle('open');
                // If chart accordion just opened, trigger resize to paint chart
                const canvas = accordion.querySelector('canvas');
                if (canvas && !wasOpen) {
                    setTimeout(function() { window.dispatchEvent(new Event('resize')); }, 360);
                }
            });
        });
    });
</script>
@else
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.m-section-header').forEach(function (header) {
            header.addEventListener('click', function () {
                this.closest('.m-section-accordion').classList.toggle('open');
            });
        });
    });
</script>
@endif
@endsection
