@extends('layouts.admin')

@section('title', 'Detail Atlet - ' . $athlete->name)
@section('header_title', 'Profil Detail Atlet')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- Profile Card -->
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
            <!-- Photo column -->
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

            <!-- Details column -->
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

    <!-- PB and Progress Section -->
    @if($personalBests->count() > 0)
    <div class="grid-pb-chart" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
        <!-- Personal Best Card -->
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

        <!-- Progress Chart Card -->
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

    <!-- Class History Card -->
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
        const selector = document.getElementById('chart_event_selector');
        const ctx = document.getElementById('progressChart').getContext('2d');
        
        let progressChart = null;

        function formatCentiseconds(totalCs) {
            const minutes = Math.floor(totalCs / 6000);
            const rem = totalCs % 6000;
            const seconds = Math.floor(rem / 100);
            const centiseconds = Math.round(rem % 100);
            
            return String(minutes).padStart(2, '0') + '.' + 
                   String(seconds).padStart(2, '0') + '.' + 
                   String(centiseconds).padStart(2, '0');
        }

        function updateChart(eventKey) {
            const rawPoints = chartData[eventKey] || [];
            
            // Sort chronologically by date
            const points = [...rawPoints].sort((a, b) => new Date(a.competition_date) - new Date(b.competition_date));
            
            const labels = points.map(p => p.competition_name + ' (' + new Date(p.competition_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: '2-digit'}) + ')');
            const dataValues = points.map(p => p.centiseconds / 100); // convert to seconds for chart plotting

            if (progressChart) {
                progressChart.destroy();
            }

            progressChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Catatan Waktu (Detik)',
                        data: dataValues,
                        borderColor: '#0284c7',
                        // backgroundColor: 'rgba(2, 132, 199, 0.1)',
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
                             reverse: true,  // Waktu tercepat di atas, terlambat di bawah
                             ticks: {
                                 callback: function(value) {
                                     return formatCentiseconds(value * 100);
                                 }
                             },
                             title: {
                                 display: true,
                                 text: 'Waktu (MM.SS.CC)'
                             }
                         },
                        x: {
                            grid: {
                                display: false
                            }
                        }
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
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Initialize with first value
        if (selector && selector.value) {
            updateChart(selector.value);
            selector.addEventListener('change', function() {
                updateChart(this.value);
            });
        }
    });
</script>
@endif
@endsection
