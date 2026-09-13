@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Dashboard Pelatih Kelas')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- Welcome Card -->
    <div class="data-card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3 class="card-title">Selamat Datang</h3>
        </div>
        <p>Halo <strong>{{ auth()->user()->name }}</strong>, selamat bekerja! Gunakan menu di sebelah kiri untuk mengelola data atlet, event lomba, dan menyetujui akun orang tua.</p>
    </div>

    <!-- Stats Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
        <div class="data-card" style="margin-bottom: 0; background-color: #f0f9ff; border: 1px solid #bae6fd; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🏊‍♂️</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: #0369a1;">{{ $stats['athletes_count'] }}</div>
            <div style="font-size: 0.85rem; color: #0369a1; font-weight: 600; text-transform: uppercase; margin-top: 0.25rem;">Atlet Aktif</div>
        </div>

        <div class="data-card" style="margin-bottom: 0; background-color: #f0fdf4; border: 1px solid #bbf7d0; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🏫</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: #15803d;">{{ $stats['classes_count'] }}</div>
            <div style="font-size: 0.85rem; color: #15803d; font-weight: 600; text-transform: uppercase; margin-top: 0.25rem;">Kelas Latihan</div>
        </div>

        <div class="data-card" style="margin-bottom: 0; background-color: #fffbeb; border: 1px solid #fef3c7; text-align: center;">
            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🏆</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: #b45309;">{{ $stats['competitions_count'] }}</div>
            <div style="font-size: 0.85rem; color: #b45309; font-weight: 600; text-transform: uppercase; margin-top: 0.25rem;">Event Perlombaan</div>
        </div>
    </div>

    <!-- Parent Approval Grid -->
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">⏳ Persetujuan Pendaftaran Akun Orang Tua ({{ $pendingParents->count() }})</h3>
        </div>
        <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px; text-align: center;">NO</th>
                        <th>NAMA ORANG TUA / WALI</th>
                        <th>EMAIL</th>
                        <th>NO. TELEPON / WA</th>
                        <th>TANGGAL DAFTAR</th>
                        <th style="width: 220px; text-align: center;">AKSI VERIFIKASI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingParents as $index => $parent)
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: var(--text-muted);">
                                {{ $index + 1 }}
                            </td>
                            <td style="font-weight: 700; color: var(--text-dark);">
                                {{ $parent->name }}
                            </td>
                            <td>{{ $parent->email }}</td>
                            <td>{{ $parent->phone ?? 'Tidak Diisi' }}</td>
                            <td>{{ $parent->created_at->format('d/m/Y H:i') }}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                    <form action="{{ route('parents.approve', $parent->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" style="background-color: #16a34a; font-size: 0.8rem; padding: 0.4rem 0.8rem; color: #fff;">
                                            Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('parents.reject', $parent->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak dan menghapus pendaftaran akun orang tua ini?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-accent" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--text-muted); font-style: italic;">
                                Tidak ada permohonan pendaftaran akun orang tua baru saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
