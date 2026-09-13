@extends('layouts.admin')

@section('title', 'Ubah Data Atlet')
@section('header_title', 'Ubah Data Atlet')

@section('content')
<div class="data-card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title">Edit Profil: {{ $athlete->name }}</h3>
        <a href="{{ route('athletes.show', $athlete->id) }}" class="form-link">
            ◀ Batal & Kembali
        </a>
    </div>
    <hr style="margin-bottom: 2rem; border: none; border-bottom: 1px solid var(--border-color);">

    <form method="POST" action="{{ route('athletes.update', $athlete->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Nama Lengkap -->
        <div class="form-group">
            <label for="name" class="form-label">Nama Lengkap Atlet *</label>
            <input type="text" name="name" id="name" class="form-input @error('name') is-invalid @enderror" value="{{ old('name', $athlete->name) }}" required placeholder="Ketik nama lengkap atlet...">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Tanggal Lahir -->
            <div class="form-group">
                <label for="birth_date" class="form-label">Tanggal Lahir *</label>
                <input type="date" name="birth_date" id="birth_date" class="form-input @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $athlete->birth_date->format('Y-m-d')) }}" required>
            </div>

            <!-- KU (Auto-generate via JS) -->
            <div class="form-group">
                <label class="form-label">Kelompok Umur (KU) *</label>
                <input type="text" id="ku_display" class="form-input" style="background-color: #e2e8f0; cursor: not-allowed;" readonly placeholder="Terhitung otomatis...">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Jenis Kelamin -->
            <div class="form-group">
                <label for="gender" class="form-label">Jenis Kelamin (Kategori) *</label>
                <select name="gender" id="gender" class="form-input" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ old('gender', $athlete->gender) === 'L' ? 'selected' : '' }}>PA (Putera)</option>
                    <option value="P" {{ old('gender', $athlete->gender) === 'P' ? 'selected' : '' }}>PI (Puteri)</option>
                </select>
            </div>

            <!-- Kelas Latihan -->
            <div class="form-group">
                <label for="current_class_id" class="form-label">Kelas Latihan Saat Ini *</label>
                <select name="current_class_id" id="current_class_id" class="form-input" required>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('current_class_id', $athlete->current_class_id) == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} - {{ $class->description }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Catatan Perpindahan Kelas (Hanya muncul via JS jika kelas diubah) -->
        <div id="class-change-notes-container" class="form-group" style="display: none; background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.25rem;">
            <label for="class_change_notes" class="form-label" style="color: #b45309; font-weight: 700;">📝 Catatan Perpindahan Kelas *</label>
            <textarea name="class_change_notes" id="class_change_notes" rows="3" class="form-input" placeholder="Tulis alasan kenaikan/perpindahan kelas latihan atlet ini..."></textarea>
            <small style="color: #b45309;">Anda memindahkan kelas atlet. Riwayat kelas lama akan otomatis diakhiri dan riwayat kelas baru akan dibuat bersama catatan ini.</small>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <!-- Tanggal Bergabung -->
            <div class="form-group">
                <label for="join_date" class="form-label">Tanggal Bergabung *</label>
                <input type="date" name="join_date" id="join_date" class="form-input @error('join_date') is-invalid @enderror" value="{{ old('join_date', $athlete->join_date->format('Y-m-d')) }}" required>
            </div>

            <!-- Orang Tua / Wali -->
            <div class="form-group">
                <label for="parent_user_id" class="form-label">Tautkan Orang Tua / Wali (Opsional)</label>
                <select name="parent_user_id" id="parent_user_id" class="form-input">
                    <option value="">-- Pilih Akun Orang Tua --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_user_id', $athlete->parent_user_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }} ({{ $parent->phone ?? 'Tanpa Kontak' }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Foto Atlet -->
        <div class="form-group" style="margin-bottom: 2rem;">
            <label for="photo" class="form-label">Foto Atlet (Opsional)</label>
            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 0.5rem;">
                @if($athlete->photo)
                    <img src="{{ asset('storage/' . $athlete->photo) }}" alt="Foto Lama" style="width: 60px; height: 60px; border-radius: 0.5rem; object-fit: cover; border: 1px solid var(--border-color);">
                @else
                    <div style="width: 60px; height: 60px; border-radius: 0.5rem; background-color: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #a0aec0; border: 1px solid var(--border-color);">
                        👤
                    </div>
                @endif
                <div style="flex: 1;">
                    <input type="file" name="photo" id="photo" class="form-input" accept="image/*">
                    <small style="color: var(--text-muted);">Pilih file baru jika ingin mengganti foto yang sudah ada.</small>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            Simpan Perubahan
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const birthDateInput = document.getElementById('birth_date');
        const kuDisplay = document.getElementById('ku_display');
        const classSelect = document.getElementById('current_class_id');
        const originalClassId = "{{ $athlete->current_class_id }}";
        const notesContainer = document.getElementById('class-change-notes-container');
        const notesTextarea = document.getElementById('class_change_notes');

        // KU calculation
        function calculateKU() {
            const dateVal = birthDateInput.value;
            if (dateVal) {
                const year = new Date(dateVal).getFullYear();
                kuDisplay.value = 'KU ' + year;
            } else {
                kuDisplay.value = '';
            }
        }

        calculateKU();
        birthDateInput.addEventListener('change', calculateKU);

        // Class Change Notes display logic
        classSelect.addEventListener('change', function() {
            if (this.value != originalClassId) {
                notesContainer.style.display = 'block';
                notesTextarea.setAttribute('required', 'required');
            } else {
                notesContainer.style.display = 'none';
                notesTextarea.removeAttribute('required');
            }
        });
    });
</script>
@endsection
