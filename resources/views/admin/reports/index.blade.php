@extends('layouts.admin')

@section('title', 'Laporan Kemajuan Atlet')
@section('header_title', 'Laporan Rekap Performa & Kemajuan Atlet')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.75rem;">
    <!-- Filter Card -->
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">🔍 Filter Rekap Laporan</h3>
        </div>
        <hr style="margin: 0 0 1.25rem; border: none; border-bottom: 1px solid var(--glass-border);">
        
        <form method="GET" action="{{ route('reports.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="class_id" class="form-label">Kelas Latihan</label>
                <select name="class_id" id="class_id" class="form-input">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="ku" class="form-label">Kelompok Umur (KU)</label>
                <select name="ku" id="ku" class="form-input">
                    <option value="">Semua KU</option>
                    @foreach($kus as $k)
                        <option value="{{ $k }}" {{ request('ku') == $k ? 'selected' : '' }}>
                            KU {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="gender" class="form-label">Kategori Gender</label>
                <select name="gender" id="gender" class="form-input">
                    <option value="">Semua Gender</option>
                    <option value="L" {{ request('gender') === 'L' ? 'selected' : '' }}>PA (Putera)</option>
                    <option value="P" {{ request('gender') === 'P' ? 'selected' : '' }}>PI (Puteri)</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: flex-end; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 100px; padding: 0.75rem 1rem;">
                    Tampilkan
                </button>
                <a href="{{ route('reports.index', array_merge(request()->all(), ['print' => '1'])) }}" 
                   target="_blank" 
                   class="btn btn-primary" 
                   style="flex: 1; min-width: 100px; padding: 0.75rem 1rem; background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); box-shadow: 0 4px 14px rgba(22, 163, 74, 0.25); text-decoration: none;">
                    🖨️ Cetak
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table Card -->
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">📊 Tabel Rekap Kemajuan Atlet</h3>
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">
                Klik nama atlet untuk melihat best time semua nomor lomba
            </span>
        </div>
        <hr style="margin: 0 0 1.25rem; border: none; border-bottom: 1px solid var(--glass-border);">

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">NO</th>
                        <th>NAMA ATLET</th>
                        <th style="text-align: center; width: 90px;">KU</th>
                        <th style="text-align: center; width: 80px;">PA/PI</th>
                        <th>KELAS SEKARANG</th>
                        <th style="text-align: center; width: 130px;">LOMBA DIIKUTI</th>
                        <th style="text-align: center; width: 130px;">STATUS PB</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $index => $row)
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: var(--text-muted);">
                                {{ $index + 1 }}
                            </td>
                            <td>
                                <button 
                                    class="athlete-name-btn"
                                    onclick="showAthleteModal({{ $index }})"
                                    title="Klik untuk melihat best time">
                                    {{ $row['name'] }}
                                </button>
                            </td>
                            <td style="text-align: center; color: var(--text-muted);">KU {{ $row['ku'] }}</td>
                            <td style="text-align: center;">
                                <span class="badge {{ $row['gender'] === 'PA' ? 'badge-primary' : 'badge-accent' }}">
                                    {{ $row['gender'] }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: var(--silver-200);">{{ $row['class_name'] }}</td>
                            <td style="text-align: center; font-weight: 700;">{{ $row['races_count'] }} kali</td>
                            <td style="text-align: center;">
                                @if(count($row['personal_bests']) > 0)
                                    <span class="badge badge-success">
                                        {{ count($row['personal_bests']) }} nomor
                                    </span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">Belum ada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                                Tidak ada data atlet yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ─── MODAL BEST TIME ATLET ─── -->
<div class="modal-overlay" id="pbModal" onclick="closePbModal(event)">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title" id="modalAthleteNameTitle">Nama Atlet</div>
                <div class="modal-subtitle" id="modalAthleteSubtitle">Best Time Semua Nomor Lomba</div>
            </div>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body" id="modalBodyContent">
            <!-- Filled by JS -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // All report data passed from PHP
    const reportData = @json($reportData);

    function showAthleteModal(index) {
        const athlete = reportData[index];
        const modal = document.getElementById('pbModal');
        const titleEl = document.getElementById('modalAthleteNameTitle');
        const subtitleEl = document.getElementById('modalAthleteSubtitle');
        const bodyEl = document.getElementById('modalBodyContent');

        titleEl.textContent = '🏊 ' + athlete.name;
        subtitleEl.textContent = 'KU ' + athlete.ku + ' · ' + athlete.gender + ' · ' + athlete.class_name;

        if (!athlete.personal_bests || athlete.personal_bests.length === 0) {
            bodyEl.innerHTML = `
                <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                    <div style="font-size: 2rem; margin-bottom: 0.75rem;">⏱️</div>
                    <p style="font-style: italic; font-size: 0.875rem;">Belum ada catatan waktu finish untuk atlet ini.</p>
                </div>
            `;
        } else {
            let rows = athlete.personal_bests.map(pb => `
                <tr>
                    <td style="font-weight: 700; color: var(--silver-200);">${pb.event_label}</td>
                    <td style="text-align: center; font-family: monospace; font-weight: 800; font-size: 1rem; color: #93c5fd; letter-spacing: 0.05em;">${pb.time_result}</td>
                    <td style="font-size: 0.8rem; color: var(--text-muted);">${pb.competition_name}<br><small>${pb.competition_date}</small></td>
                </tr>
            `).join('');

            bodyEl.innerHTML = `
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NOMOR LOMBA</th>
                                <th style="text-align: center; width: 130px;">WAKTU TERBAIK</th>
                                <th>DARI EVENT</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            `;
        }

        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('pbModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function closePbModal(event) {
        if (event.target === document.getElementById('pbModal')) {
            closeModal();
        }
    }

    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endsection
