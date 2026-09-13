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

        <!-- Status Event -->
        <div class="form-group">
            <label for="status" class="form-label">Status Awal Event *</label>
            <select name="status" id="status" class="form-input" required>
                <option value="akan_datang" {{ old('status') === 'akan_datang' ? 'selected' : '' }}>Akan Datang</option>
                <option value="berlangsung" {{ old('status') === 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
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
@endsection
