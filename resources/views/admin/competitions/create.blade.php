@extends('layouts.admin')

@section('title', 'Buat Event Baru')
@section('header_title', 'Buat Event Baru')

@section('content')
<div class="data-card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Formulir Event Perlombaan</h3>
        <a href="{{ route('competitions.index') }}" class="form-link">
            ◀ Batal & Kembali
        </a>
    </div>
    <hr style="margin-bottom: 2rem; border: none; border-bottom: 1px solid var(--border-color);">

    <form method="POST" action="{{ route('competitions.store') }}">
        @csrf

        <!-- Nama Event -->
        <div class="form-group">
            <label for="name" class="form-label">Nama Event Lomba *</label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}" required placeholder="Contoh: IAOC 2026">
        </div>

        <!-- Penyelenggara -->
        <div class="form-group">
            <label for="organizer" class="form-label">Penyelenggara *</label>
            <input type="text" name="organizer" id="organizer" class="form-input" value="{{ old('organizer') }}" required placeholder="Contoh: PRSI">
        </div>

        <!-- Lokasi -->
        <div class="form-group">
            <label for="location" class="form-label">Lokasi Kolam Renang *</label>
            <input type="text" name="location" id="location" class="form-input" value="{{ old('location') }}" required placeholder="Contoh: AQUATIC CENTER JAKARTA">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Tanggal Mulai -->
            <div class="form-group">
                <label for="start_date" class="form-label">Tanggal Mulai *</label>
                <input type="date" name="start_date" id="start_date" class="form-input" value="{{ old('start_date', date('Y-m-d')) }}" required>
            </div>

            <!-- Tanggal Selesai -->
            <div class="form-group">
                <label for="end_date" class="form-label">Tanggal Selesai *</label>
                <input type="date" name="end_date" id="end_date" class="form-input" value="{{ old('end_date', date('Y-m-d')) }}" required>
            </div>
        </div>

        <!-- Status Event (Otomatis Berdasarkan Tanggal) -->
        <div class="form-group">
            <label for="status" class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                <span>Status Perlombaan *</span>
                <span style="font-size: 0.8rem; font-weight: normal; color: var(--primary-hover);">⚡ Ditentukan Otomatis dari Tanggal</span>
            </label>
            <select name="status" id="status" class="form-input" required style="background-color: var(--bg-light); font-weight: 600;">
                <option value="akan_datang" {{ old('status') === 'akan_datang' ? 'selected' : '' }}>⏳ Akan Datang</option>
                <option value="berlangsung" {{ old('status') === 'berlangsung' ? 'selected' : '' }}>🔥 Berlangsung</option>
                <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>✅ Selesai</option>
            </select>
            <div id="status-info-box" style="margin-top: 0.5rem; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; display: flex; align-items: center; gap: 0.6rem; transition: all 0.3s ease;">
                <span id="status-icon" style="font-size: 1.1rem;">📅</span>
                <span id="status-keterangan">Keterangan status akan disesuaikan otomatis dengan tanggal yang dipilih.</span>
            </div>
        </div>

        <!-- Auto Create Default Events -->
        <div class="form-group" style="margin-bottom: 2rem; background-color: var(--bg-light); padding: 1rem; border-radius: 0.75rem; border: 1px dashed rgba(2, 132, 199, 0.3);">
            <label for="auto_create_events" class="form-checkbox" style="font-weight: 600; color: var(--primary-hover);">
                <input type="checkbox" name="auto_create_events" id="auto_create_events" value="1" checked>
                <span>Otomatis Buat Nomor Lomba Standar Klub</span>
            </label>
            <p style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-muted); padding-left: 1.5rem;">
                Mencakup Standart nomor lomba untuk Gaya Bebas, Gaya Dada, Gaya Kupu, Gaya Punggung, Gaya Ganti.
            </p>
        </div>

        <button type="submit" class="btn btn-primary">
            Buat Event & Lanjutkan
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const statusSelect = document.getElementById('status');
    const statusInfoBox = document.getElementById('status-info-box');
    const statusIcon = document.getElementById('status-icon');
    const statusKeterangan = document.getElementById('status-keterangan');

    function getTodayString() {
        const d = new Date();
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function updateStatus() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const today = getTodayString();

        if (!startDate || !endDate) return;

        // Jika end_date lebih awal dari start_date, otomatis sesuaikan end_date
        if (endDate < startDate) {
            endDateInput.value = startDate;
        }

        const validEndDate = endDateInput.value;

        if (today < startDate) {
            statusSelect.value = 'akan_datang';
            if (statusInfoBox) {
                statusInfoBox.style.background = '#e0f2fe';
                statusInfoBox.style.color = '#0369a1';
                statusInfoBox.style.border = '1px solid #bae6fd';
                statusIcon.textContent = '⏳';
                statusKeterangan.innerHTML = 'Keterangan: Diinput <strong>sebelum tanggal berlangsung</strong>, otomatis berstatus <strong>Akan Datang</strong>.';
            }
        } else if (today > validEndDate) {
            statusSelect.value = 'selesai';
            if (statusInfoBox) {
                statusInfoBox.style.background = '#dcfce7';
                statusInfoBox.style.color = '#15803d';
                statusInfoBox.style.border = '1px solid #bbf7d0';
                statusIcon.textContent = '✅';
                statusKeterangan.innerHTML = 'Keterangan: Tanggal <strong>telah melewati jadwal pertandingan</strong>, otomatis berstatus <strong>Selesai</strong>.';
            }
        } else {
            statusSelect.value = 'berlangsung';
            if (statusInfoBox) {
                statusInfoBox.style.background = '#ffedd5';
                statusInfoBox.style.color = '#c2410c';
                statusInfoBox.style.border = '1px solid #fed7aa';
                statusIcon.textContent = '🔥';
                statusKeterangan.innerHTML = 'Keterangan: Tanggal <strong>sesuai dengan jadwal pertandingan</strong>, otomatis berstatus <strong>Berlangsung</strong>.';
            }
        }
    }

    startDateInput.addEventListener('change', updateStatus);
    startDateInput.addEventListener('input', updateStatus);
    endDateInput.addEventListener('change', updateStatus);
    endDateInput.addEventListener('input', updateStatus);

    updateStatus();
});
</script>
@endsection
