@extends('layouts.admin')

@section('title', 'Absensi Harian Atlet')
@section('header_title', 'Absensi Harian')

@section('styles')
<style>
    /* ─── ATTENDANCE SPECIFIC STYLES ─── */
    .attendance-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    @media (max-width: 900px) {
        .attendance-stat-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 500px) {
        .attendance-stat-grid { grid-template-columns: 1fr; }
    }

    .stat-mini {
        background: linear-gradient(160deg, rgba(12, 24, 48, 0.85) 0%, rgba(6, 15, 34, 0.9) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
    }
    .stat-mini::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(200,216,236,0.2) 50%, transparent);
    }
    .stat-mini-icon {
        font-size: 1.6rem;
        width: 42px;
        text-align: center;
        flex-shrink: 0;
    }
    .stat-mini-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.75px;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }
    .stat-mini-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--silver-200);
        letter-spacing: -0.02em;
        line-height: 1;
    }

    .stat-mini.hadir { border-color: rgba(74, 222, 128, 0.2); }
    .stat-mini.izin  { border-color: rgba(251, 146, 60, 0.2); }
    .stat-mini.sakit { border-color: rgba(96, 165, 250, 0.2); }
    .stat-mini.alpha { border-color: rgba(248, 113, 113, 0.2); }

    .badge-hadir  { background: rgba(74,222,128,0.12); color: #4ade80; border-color: rgba(74,222,128,0.25); }
    .badge-izin   { background: rgba(251,146,60,0.12); color: #fb923c; border-color: rgba(251,146,60,0.25); }
    .badge-sakit  { background: rgba(96,165,250,0.12); color: #60a5fa; border-color: rgba(96,165,250,0.25); }
    .badge-alpha  { background: rgba(248,113,113,0.12); color: #f87171; border-color: rgba(248,113,113,0.25); }

    .input-section {
        background: rgba(10, 22, 40, 0.5);
        border: 1px solid var(--glass-border);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }
    .input-section-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--silver-200);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        letter-spacing: -0.01em;
    }
    .input-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1.2fr 2fr auto;
        gap: 1rem;
        align-items: end;
    }
    @media (max-width: 900px) {
        .input-row { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 560px) {
        .input-row { grid-template-columns: 1fr; }
    }

    .btn-sm-custom {
        padding: 0.45rem 0.85rem;
        font-size: 0.78rem;
        border-radius: 0.6rem;
        width: auto;
    }
    .btn-outline-secondary {
        background: rgba(200,216,236,0.06);
        border: 1px solid var(--glass-border);
        color: var(--text-muted);
    }
    .btn-outline-secondary:hover {
        background: rgba(200,216,236,0.12);
        color: var(--silver-200);
        transform: none;
        box-shadow: none;
    }
    .btn-teal {
        background: linear-gradient(135deg, rgba(20, 184, 166, 0.9), rgba(13, 148, 136, 0.9));
        color: #fff;
        box-shadow: 0 4px 12px rgba(20, 184, 166, 0.2);
    }
    .btn-teal:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(20, 184, 166, 0.3);
    }
    .empty-state {
        text-align: center;
        padding: 3.5rem 2rem;
        color: var(--text-muted);
    }
    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    .empty-state-text {
        font-size: 0.9rem;
        font-style: italic;
    }
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(4,13,26,0.85);
        backdrop-filter: blur(6px);
        z-index: 200;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .modal-backdrop-custom.active {
        opacity: 1;
        pointer-events: auto;
    }
    .modal-card {
        background: linear-gradient(160deg, rgba(12,24,48,0.98) 0%, rgba(6,15,34,0.99) 100%);
        border: 1px solid var(--glass-border);
        border-radius: 1.25rem;
        width: 100%;
        max-width: 480px;
        box-shadow: 0 40px 80px rgba(0,0,0,0.6), inset 0 1px 0 rgba(200,216,236,0.1);
        transform: translateY(16px);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .modal-backdrop-custom.active .modal-card {
        transform: translateY(0);
    }
    .page-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }
</style>
@endsection

@section('content')

{{-- ─── Filter & Header Card ─── --}}
<div class="data-card">
    <div class="card-header">
        <div>
            <h3 class="card-title">📅 Absensi Harian Atlet</h3>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">
                Catat dan pantau kehadiran atlet per sesi latihan
            </p>
        </div>
        <div class="page-actions">
            <a href="{{ route('attendance.recap') }}" class="btn btn-teal btn-sm-custom">
                📊 Lihat Rekap Bulanan
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('attendance.index') }}">
        <div class="filter-bar">
            <div class="form-group filter-input" style="margin-bottom: 0;">
                <label for="swim_class_id" class="form-label">Kelas Latihan</label>
                <select name="swim_class_id" id="swim_class_id" class="form-input" onchange="this.form.submit()">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group filter-input" style="margin-bottom: 0;">
                <label for="date" class="form-label">Tanggal</label>
                <input type="date" name="date" id="date" class="form-input"
                       value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>
</div>

@if($selectedClass)

    {{-- ─── Mini Stats ─── --}}
    @php
        $countHadir  = $attendances->where('status', 'hadir')->count();
        $countIzin   = $attendances->where('status', 'izin')->count();
        $countSakit  = $attendances->where('status', 'sakit')->count();
        $countAlpha  = $attendances->where('status', 'tanpa_keterangan')->count();
    @endphp
    <div class="attendance-stat-grid">
        <div class="stat-mini hadir">
            <div class="stat-mini-icon">✅</div>
            <div>
                <div class="stat-mini-label">Hadir</div>
                <div class="stat-mini-value">{{ $countHadir }}</div>
            </div>
        </div>
        <div class="stat-mini izin">
            <div class="stat-mini-icon">📨</div>
            <div>
                <div class="stat-mini-label">Izin</div>
                <div class="stat-mini-value">{{ $countIzin }}</div>
            </div>
        </div>
        <div class="stat-mini sakit">
            <div class="stat-mini-icon">🤒</div>
            <div>
                <div class="stat-mini-label">Sakit</div>
                <div class="stat-mini-value">{{ $countSakit }}</div>
            </div>
        </div>
        <div class="stat-mini alpha">
            <div class="stat-mini-icon">❌</div>
            <div>
                <div class="stat-mini-label">Tanpa Keterangan</div>
                <div class="stat-mini-value">{{ $countAlpha }}</div>
            </div>
        </div>
    </div>

    {{-- ─── Attendance Table ─── --}}
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">
                Daftar Kehadiran —
                <span style="color: var(--primary); font-weight: 600;">
                    {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') ?? $selectedDate }}
                </span>
            </h3>
            <span style="font-size: 0.8rem; color: var(--text-muted);">
                {{ $attendances->count() }} catatan tercatat
            </span>
        </div>

        @if($attendances->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Atlet</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th>Dicatat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                            <tr>
                                <td style="color: var(--text-muted); font-size: 0.8rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <div style="font-weight: 700; color: var(--silver-200);">
                                        {{ $attendance->athlete->name }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $attendance->swimClass->name }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'hadir'             => ['class' => 'hadir',  'label' => '✅ Hadir'],
                                            'izin'              => ['class' => 'izin',   'label' => '📨 Izin'],
                                            'sakit'             => ['class' => 'sakit',  'label' => '🤒 Sakit'],
                                            'tanpa_keterangan'  => ['class' => 'alpha',  'label' => '❌ Tanpa Ket.'],
                                        ];
                                        $s = $statusMap[$attendance->status] ?? ['class' => 'alpha', 'label' => ucfirst($attendance->status)];
                                    @endphp
                                    <span class="badge badge-{{ $s['class'] }}">{{ $s['label'] }}</span>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-muted); max-width: 200px;">
                                    {{ $attendance->note ?? '—' }}
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-light-muted);">
                                    {{ $attendance->createdBy->name }}
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm-custom"
                                            onclick="openEditModal({{ $attendance->id }}, '{{ $attendance->status }}', '{{ addslashes($attendance->note ?? '') }}')"
                                            title="Edit status kehadiran">
                                        ✏️ Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-text">Belum ada catatan absensi untuk tanggal ini.</div>
                <p style="font-size: 0.8rem; color: var(--text-light-muted); margin-top: 0.5rem;">
                    Gunakan form di bawah untuk menambahkan absensi.
                </p>
            </div>
        @endif
    </div>

    {{-- ─── Input Absensi Baru ─── --}}
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">➕ Input Absensi Baru</h3>
        </div>

        <form method="POST" action="{{ route('attendance.store') }}" id="storeAttendanceForm">
            @csrf
            <input type="hidden" name="swim_class_id" value="{{ $selectedClass }}">

            <div class="input-row">
                {{-- Atlet --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="athlete_id" class="form-label">Atlet</label>
                    @php
                        $selectedClassObj = $classes->where('id', $selectedClass)->first();
                        $athletesInClass = $selectedClassObj ? $selectedClassObj->athletes : collect();
                    @endphp
                    <select name="athlete_id" id="athlete_id" class="form-input" required>
                        <option value="">-- Pilih Atlet --</option>
                        @foreach($athletesInClass as $athlete)
                            <option value="{{ $athlete->id }}">{{ $athlete->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tanggal --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="attendance_date" class="form-label">Tanggal</label>
                    <input type="date" name="attendance_date" id="attendance_date"
                           class="form-input" value="{{ $selectedDate }}" required>
                </div>

                {{-- Status --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="status" class="form-label">Status Kehadiran</label>
                    <select name="status" id="status" class="form-input" required>
                        <option value="hadir">✅ Hadir</option>
                        <option value="izin">📨 Izin</option>
                        <option value="sakit">🤒 Sakit</option>
                        <option value="tanpa_keterangan">❌ Tanpa Keterangan</option>
                    </select>
                </div>

                {{-- Catatan --}}
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="note" class="form-label">Catatan <span id="note-optional" style="color: var(--text-light-muted); font-weight: 400;">(opsional)</span></label>
                    <input type="text" name="note" id="note" class="form-input"
                           placeholder="Contoh: Sakit demam, surat izin terlampir...">
                </div>

                {{-- Submit --}}
                <div style="padding-bottom: 0;">
                    <button type="submit" class="btn btn-primary" style="white-space: nowrap; width: auto; padding: 0.75rem 1.5rem;">
                        💾 Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>

@else
    {{-- ─── No Class Selected ─── --}}
    <div class="data-card">
        <div class="empty-state" style="padding: 4rem 2rem;">
            <div class="empty-state-icon">📌</div>
            <p style="font-size: 1rem; font-weight: 700; color: var(--silver-200); margin-bottom: 0.5rem;">
                Pilih Kelas Terlebih Dahulu
            </p>
            <div class="empty-state-text">
                Gunakan filter di atas untuk memilih kelas dan tanggal absensi yang ingin dicatat.
            </div>
        </div>
    </div>
@endif

{{-- ─── Edit Modal ─── --}}
<div class="modal-backdrop-custom" id="editModalBackdrop" onclick="closeEditModal(event)">
    <div class="modal-card" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div class="modal-title">✏️ Edit Status Kehadiran</div>
                <div class="modal-subtitle">Perbarui catatan absensi atlet</div>
            </div>
            <button class="modal-close" onclick="closeEditModalBtn()" title="Tutup">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" id="editAttendanceForm">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="edit_status" class="form-label">Status Kehadiran</label>
                    <select name="status" id="edit_status" class="form-input" required>
                        <option value="hadir">✅ Hadir</option>
                        <option value="izin">📨 Izin</option>
                        <option value="sakit">🤒 Sakit</option>
                        <option value="tanpa_keterangan">❌ Tanpa Keterangan</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_note" class="form-label">Catatan</label>
                    <input type="text" name="note" id="edit_note" class="form-input"
                           placeholder="Catatan tambahan (opsional)">
                </div>
                <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="closeEditModalBtn()">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ─── Note Field Validation ───
    document.getElementById('status')?.addEventListener('change', function() {
        const noteField = document.getElementById('note');
        const noteOptional = document.getElementById('note-optional');
        if (this.value === 'izin' || this.value === 'sakit') {
            noteField.placeholder = 'Wajib diisi — contoh: Surat keterangan dokter';
            noteField.setAttribute('required', 'required');
            noteOptional.textContent = '(wajib)';
            noteOptional.style.color = '#f87171';
        } else {
            noteField.placeholder = 'Contoh: Sakit demam, surat izin terlampir...';
            noteField.removeAttribute('required');
            noteOptional.textContent = '(opsional)';
            noteOptional.style.color = 'var(--text-light-muted)';
        }
    });

    // ─── Edit Modal ───
    function openEditModal(attendanceId, currentStatus, currentNote) {
        const backdrop = document.getElementById('editModalBackdrop');
        const form = document.getElementById('editAttendanceForm');
        form.action = `/admin/attendance/${attendanceId}`;
        document.getElementById('edit_status').value = currentStatus;
        document.getElementById('edit_note').value = currentNote;
        backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal(event) {
        if (event.target === document.getElementById('editModalBackdrop')) {
            closeEditModalBtn();
        }
    }

    function closeEditModalBtn() {
        document.getElementById('editModalBackdrop').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditModalBtn();
    });
</script>
@endsection