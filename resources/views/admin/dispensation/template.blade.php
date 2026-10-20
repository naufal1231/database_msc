@extends('layouts.admin')

@section('title', 'Template Dispensasi')
@section('header_title', 'Kelola Template Dispensasi')

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 800px;">

    {{-- Status Template --}}
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">📄 Status Template Aktif</h3>
            <a href="{{ route('admin.dispensation.index') }}" class="form-link">◀ Kembali ke Daftar</a>
        </div>
        <hr style="margin: 1rem 0; border: none; border-bottom: 1px solid var(--border-color);">

        @if($templateExists)
            <div style="background: linear-gradient(135deg, rgba(34,197,94,0.1), rgba(22,163,74,0.05)); border: 1px solid rgba(34,197,94,0.3); border-radius: 0.75rem; padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 2.5rem;">📋</span>
                <div>
                    <div style="font-weight: 700; color: #15803d; font-size: 1rem;">Template Aktif</div>
                    <div style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.2rem;">
                        <strong>{{ $templateInfo['filename'] ?? 'template.docx' }}</strong>
                    </div>
                    @if($templateInfo['uploaded_at'] ?? null)
                        <div style="color: var(--text-light-muted); font-size: 0.8rem; margin-top: 0.2rem;">
                            Diupload: {{ \Carbon\Carbon::parse($templateInfo['uploaded_at'])->format('d/m/Y H:i') }}
                            ({{ \Carbon\Carbon::parse($templateInfo['uploaded_at'])->diffForHumans() }})
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div style="background: linear-gradient(135deg, rgba(239,68,68,0.08), rgba(220,38,38,0.04)); border: 1px solid rgba(239,68,68,0.25); border-radius: 0.75rem; padding: 1.25rem; display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 2.5rem;">⚠️</span>
                <div>
                    <div style="font-weight: 700; color: #dc2626;">Belum Ada Template</div>
                    <div style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.2rem;">
                        Upload template .docx di bawah untuk mengaktifkan fitur dispensasi.
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Form Upload --}}
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">⬆️ {{ $templateExists ? 'Ganti' : 'Upload' }} Template</h3>
        </div>
        <hr style="margin: 1rem 0; border: none; border-bottom: 1px solid var(--border-color);">

        <form method="POST" action="{{ route('admin.dispensation.template.upload') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="template_file" class="form-label">
                    File Template .docx <span style="color: #ef4444;">*</span>
                </label>
                <input type="file" name="template" id="template_file" class="form-input"
                       accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                       required style="padding: 0.6rem;">
                <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-muted);">
                    Ukuran maksimal: 10 MB. Hanya format .docx yang diterima.
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 0.5rem;">
                ⬆️ Upload Template
            </button>
        </form>
    </div>

    {{-- Panduan Placeholder --}}
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">📌 Placeholder Wajib dalam Template</h3>
        </div>
        <hr style="margin: 1rem 0; border: none; border-bottom: 1px solid var(--border-color);">

        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem;">
            Template .docx <strong>wajib</strong> mengandung semua placeholder berikut. Gunakan format <code style="background: rgba(2,132,199,0.08); padding: 0.15rem 0.4rem; border-radius: 0.3rem; font-family: monospace;">${nama_placeholder}</code> di dalam dokumen Word.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.75rem;">
            @foreach($requiredPlaceholders as $ph)
                <div style="background: rgba(2,132,199,0.06); border: 1px solid rgba(2,132,199,0.15); border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <code style="font-size: 0.9rem; font-weight: 700; color: var(--primary-hover); font-family: monospace;">${{ '{' . $ph . '}' }}</code>
                    <div style="margin-top: 0.25rem; font-size: 0.78rem; color: var(--text-muted);">
                        @switch($ph)
                            @case('nama_atlet') Nama lengkap atlet @break
                            @case('kelas') Kelas sekolah atlet @break
                            @case('no_absen') Nomor absen di sekolah @break
                            @case('nama_kejuaraan') Nama kompetisi @break
                            @case('tanggal') Tanggal kompetisi (bahasa Indonesia) @break
                            @case('tempat') Lokasi kompetisi @break
                        @endswitch
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 1.5rem; padding: 1rem 1.25rem; background: rgba(249,115,22,0.07); border: 1px solid rgba(249,115,22,0.2); border-radius: 0.75rem;">
            <strong style="color: #c2410c; font-size: 0.875rem;">📝 Contoh Isi Template:</strong>
            <pre style="margin: 0.75rem 0 0; font-size: 0.82rem; line-height: 1.7; white-space: pre-wrap; color: var(--text-dark); font-family: 'Inter', sans-serif;">Kepada Yth.
Bapak/Ibu Wali Kelas <strong>${kelas}</strong>
di tempat

Dengan hormat,
Kami dari Marabunta Swimming Club menerangkan bahwa:

    Nama   : <strong>${nama_atlet}</strong>
    Kelas  : <strong>${kelas}</strong>
    No Absen: <strong>${no_absen}</strong>

akan mengikuti <strong>${nama_kejuaraan}</strong> yang diselenggarakan pada
tanggal <strong>${tanggal}</strong> bertempat di <strong>${tempat}</strong>.

Bersama surat ini kami mohon izin agar siswa tersebut dapat
meninggalkan kegiatan belajar mengajar pada hari yang bersangkutan.</pre>
        </div>
    </div>
</div>
@endsection
