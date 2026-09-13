@extends('layouts.admin')

@section('title', 'Pendaftaran Atlet Baru')
@section('header_title', 'Pendaftaran Atlet Baru')

@section('content')
<div class="data-card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Formulir Pendaftaran Atlet</h3>
        <a href="{{ route('athletes.index') }}" class="form-link">
            ◀ Kembali ke Daftar
        </a>
    </div>
    <hr style="margin-bottom: 2rem; border: none; border-bottom: 1px solid var(--border-color);">

    <form method="POST" action="{{ route('athletes.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- Nama Lengkap -->
        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap Atlet *</label>
            <input type="text" name="name" id="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Ketik nama lengkap atlet...">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Tanggal Lahir -->
            <div class="form-group">
                <label for="birth_date" class="form-label">Tanggal Lahir *</label>
                <input type="date" name="birth_date" id="birth_date" class="form-input @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" required>
            </div>

            <!-- KU (Auto-generate via JS) -->
            <div class="form-group">
                <label class="form-label">Kelompok Umur (KU) *</label>
                <input type="text" id="ku_display" class="form-input" style="background-color: #e2e8f0; cursor: not-allowed;" readonly placeholder="Terhitung otomatis...">
                <small style="color: var(--text-muted);">KU dihitung otomatis dari tahun lahir.</small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Jenis Kelamin -->
            <div class="form-group">
                <label for="gender" class="form-label">Jenis Kelamin (Kategori) *</label>
                <select name="gender" id="gender" class="form-input" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>PA (Putera)</option>
                    <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>PI (Puteri)</option>
                </select>
            </div>

            <!-- Kelas Latihan -->
            <div class="form-group">
                <label for="current_class_id" class="form-label">Kelas Latihan Saat Ini *</label>
                <select name="current_class_id" id="current_class_id" class="form-input" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('current_class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} - {{ $class->description }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Tanggal Bergabung -->
            <div class="form-group">
                <label for="join_date" class="form-label">Tanggal Bergabung *</label>
                <input type="date" name="join_date" id="join_date" class="form-input @error('join_date') is-invalid @enderror" value="{{ old('join_date', date('Y-m-d')) }}" required>
            </div>

            <!-- Orang Tua / Wali -->
            <div class="form-group">
                <label for="parent_user_id" class="form-label">Tautkan Orang Tua / Wali (Opsional)</label>
                <select name="parent_user_id" id="parent_user_id" class="form-input">
                    <option value="">-- Pilih Akun Orang Tua --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_user_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }} ({{ $parent->phone ?? 'Tanpa Kontak' }})
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted);">Hanya menampilkan akun orang tua yang aktif.</small>
            </div>
        </div>

        <!-- Foto Atlet -->
        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="photo" class="form-label">Foto Atlet (Opsional)</label>
            <input type="file" name="photo" id="photo" class="form-input" accept="image/*">
            <small style="color: var(--text-muted);">Format: jpeg, png, jpg. Maks: 2MB.</small>
        </div>

        <button type="submit" class="btn btn-primary">
            Daftarkan Atlet
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const birthDateInput = document.getElementById('birth_date');
        const kuDisplay = document.getElementById('ku_display');

        function calculateKU() {
            const dateVal = birthDateInput.value;
            if (dateVal) {
                const year = new Date(dateVal).getFullYear();
                kuDisplay.value = 'KU ' + year;
            } else {
                kuDisplay.value = '';
            }
        }

        // Run on load if date is already set
        calculateKU();

        // Run on change
        birthDateInput.addEventListener('change', calculateKU);
    });
</script>
@endsection
