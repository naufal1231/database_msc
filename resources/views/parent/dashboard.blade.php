@extends('layouts.parent')

@section('title', 'Dashboard Orang Tua')
@section('header_title', 'Dashboard Pemantauan Progres Atlet')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;">

    <!-- Children Selector Tab -->
    @if($children->count() > 1)
        <div class="data-card" style="margin-bottom: 0; padding: 1.25rem;">
            <label class="form-label" style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 0.75rem; display: block;">Pilih Data Anak Anda:</label>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                @foreach($children as $child)
                    <a href="{{ route('parent.dashboard', ['child_id' => $child->id]) }}" 
                       style="padding: 0.55rem 1.25rem; font-size: 0.875rem; border-radius: 0.65rem; text-decoration: none; font-weight: 700; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 0.4rem;
                              {{ $selectedChild->id === $child->id 
                                  ? 'background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%); color: #fff; box-shadow: 0 4px 12px var(--primary-glow);'
                                  : 'background: rgba(2, 132, 199, 0.06); color: var(--text-muted); border: 1px solid var(--border-color);' }}">
                        🏊 {{ $child->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Notifications Feed -->
    @if($notifications->count() > 0)
        <div class="data-card" style="margin-bottom: 0; background-color: #f8fafc; border: 1px solid var(--border-color);">
            <div class="card-header" style="padding-bottom: 0.5rem;">
                <h3 class="card-title" style="font-size: 1.1rem; color: var(--primary-hover);">🔔 Notifikasi & Aktivitas Terbaru</h3>
            </div>
            <hr style="margin: 0.5rem 0 0.75rem 0; border: none; border-bottom: 1px solid var(--border-color);">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($notifications as $notif)
                    <div style="padding: 0.75rem 1rem; background-color: #ffffff; border-radius: 0.5rem; border-left: 4px solid var(--primary); box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 0.25rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.775rem; color: var(--text-muted); font-weight: bold;">
                            <span style="color: var(--primary-hover);">{{ $notif->title }}</span>
                            <span>{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-dark); line-height: 1.45;">{{ $notif->message }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($selectedChild)
        <!-- Child Profile Info Card -->
        <div class="data-card" style="margin-bottom: 0;">
            <div class="card-header">
                <h3 class="card-title">Profil Atlet Anak</h3>
                <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                    <a href="{{ route('parent.event-history.show', $selectedChild->id) }}"
                       style="padding: 0.45rem 1rem; border-radius: 0.65rem; font-size: 0.82rem; font-weight: 700; text-decoration: none; background: rgba(91,168,232,0.12); border: 1px solid rgba(91,168,232,0.25); color: #5ba8e8; transition: all 0.2s ease;"
                       onmouseover="this.style.background='rgba(91,168,232,0.22)'" onmouseout="this.style.background='rgba(91,168,232,0.12)'">
                        📊 Riwayat Perlombaan
                    </a>
                    <span class="badge badge-success" style="background-color: var(--bg-light); color: var(--primary); border: 1px solid rgba(2, 132, 199, 0.2);">
                        {{ $selectedChild->currentClass->name }}
                    </span>
                </div>
            </div>
            <hr style="margin: 1.25rem 0; border: none; border-bottom: 1px solid var(--border-color);">

            <div class="profile-grid">
                <!-- Photo column -->
                <div class="profile-photo-card">
                    @if($selectedChild->photo)
                        <img src="{{ asset('storage/' . $selectedChild->photo) }}" alt="Foto {{ $selectedChild->name }}" class="athlete-photo-lg">
                    @else
                        <div style="width: 150px; height: 150px; border-radius: 1rem; background-color: #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 4rem; color: #a0aec0; border: 3px solid #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            👤
                        </div>
                    @endif
                    <div style="margin-top: 1rem;">
                        <span class="badge {{ $selectedChild->gender === 'L' ? 'badge-primary' : 'badge-accent' }}" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">
                            {{ $selectedChild->gender === 'L' ? 'PA (Putera)' : 'PI (Puteri)' }}
                        </span>
                    </div>
                </div>

                <!-- Details column -->
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div>
                        <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--primary-hover); margin: 0;">{{ $selectedChild->name }}</h2>
                        <span style="font-size: 0.95rem; color: var(--text-muted); font-weight: 600;">KU {{ $selectedChild->ku }}</span>
                    </div>

                    <div class="info-grid" style="gap: 1rem;">
                        <div class="info-item">
                            <span class="info-label">Tanggal Lahir</span>
                            <span class="info-value">{{ $selectedChild->birth_date->format('d F Y') }}</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Tanggal Bergabung Klub</span>
                            <span class="info-value">{{ $selectedChild->join_date->format('d F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
               <!-- PB and Progress Graph Section - Only visible when data exists -->
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
                                <th>EVENT &amp; TANGGAL</th>
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
        <div class="data-card" style="text-align: center; padding: 3rem 2rem; background-color: #f8fafc; border: 1px dashed var(--border-color); margin-bottom: 0;">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">⏱️</div>
            <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem;">Belum Ada Catatan Waktu Terbaik</h4>
            <p style="font-size: 0.875rem; color: var(--text-light-muted); max-width: 440px; margin: 0 auto; line-height: 1.5;">
                Tabel Personal Best dan grafik perkembangan akan muncul setelah <strong>{{ $selectedChild->name }}</strong> memiliki riwayat hasil perlombaan yang tercatat.
            </p>
        </div>
        @endif
        </div>

        <!-- Class History Card -->
        <div class="data-card">
            <div class="card-header">
                <h3 class="card-title">⏳ Riwayat Kenaikan & Perpindahan Kelas Latihan</h3>
            </div>
            <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 200px;">KELAS LATIHAN</th>
                            <th style="width: 150px;">TANGGAL MULAI</th>
                            <th style="width: 150px;">TANGGAL SELESAI</th>
                            <th>CATATAN KEMAJUAN PELATIH</th>
                            <th style="width: 180px;">DICATAT OLEH</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selectedChild->classHistories as $history)
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
                                    Riwayat perpindahan kelas tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Case: No children mapped to this parent user -->
        <div class="data-card" style="background-color: #fffbeb; border: 1px solid #fef3c7; text-align: center; padding: 4rem 2rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">⚠️</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #b45309; margin-bottom: 0.5rem;">Akun Anda Belum Ditautkan dengan Profil Atlet Anak</h3>
            <p style="color: #b45309; max-width: 500px; margin: 0 auto; line-height: 1.5;">
                Harap hubungi <strong>Pelatih Kepala / Pelatih Kelas Anda</strong> di sekretariat Marabunta Swimming Club untuk menautkan profil atlet anak Anda ke alamat email akun ini: <strong>{{ auth()->user()->email }}</strong>.
            </p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
@if($selectedChild && $personalBests->count() > 0)
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
            const dataValues = points.map(p => p.centiseconds / 100);

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

        // Initialize chart
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
