@extends('layouts.admin')

@section('title', 'Daftar Atlet')
@section('header_title', 'Manajemen Data Atlet')

@section('content')
<div class="data-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Atlet Marabunta Swimming Club</h3>
        <a href="{{ route('athletes.create') }}" class="btn btn-primary" style="max-width: 200px;">
            ➕ Tambah Atlet Baru
        </a>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('athletes.index') }}" class="filter-bar" style="margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
            <label for="search" class="form-label" style="font-size: 0.8rem;">Cari Nama Atlet</label>
            <input type="text" name="search" id="search" class="form-input" placeholder="Ketik nama..." value="{{ request('search') }}">
        </div>

        <div class="form-group" style="margin-bottom: 0; width: 180px;">
            <label for="class_id" class="form-label" style="font-size: 0.8rem;">Kelas Latihan</label>
            <select name="class_id" id="class_id" class="form-input">
                <option value="">-- Semua Kelas --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 0; width: 150px;">
            <label for="ku" class="form-label" style="font-size: 0.8rem;">Kelompok Umur (KU)</label>
            <select name="ku" id="ku" class="form-input">
                <option value="">-- Semua KU --</option>
                @foreach($kus as $kuYear)
                    <option value="{{ $kuYear }}" {{ request('ku') == $kuYear ? 'selected' : '' }}>
                        KU {{ $kuYear }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.25rem;">
                Filter
            </button>
            <a href="{{ route('athletes.index') }}" class="btn btn-accent" style="padding: 0.75rem 1.25rem; background-color: var(--text-muted); color: #fff;">
                Reset
            </a>
        </div>
    </form>

    <!-- Athletes Table -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">NO</th>
                    <th style="width: 80px; text-align: center;">FOTO</th>
                    <th>NAMA ATLET</th>
                    <th>TGL LAHIR</th>
                    <th style="text-align: center;">KU</th>
                    <th style="text-align: center;">PA/PI</th>
                    <th>KELAS</th>
                    <th>ORANG TUA / WALI</th>
                    <th style="width: 220px; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($athletes as $index => $athlete)
                    <tr>
                        <td style="text-align: center; font-weight: 600; color: var(--text-muted);">
                            {{ $athletes->firstItem() + $index }}
                        </td>
                        <td style="text-align: center;">
                            @if($athlete->photo)
                                <img src="{{ asset('storage/' . $athlete->photo) }}" alt="Foto Atlet" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-color);">
                            @else
                                <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #e2e8f0; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; color: #a0aec0; border: 1px solid var(--border-color);">
                                    👤
                                </div>
                            @endif
                        </td>
                        <td style="font-weight: 700; color: var(--primary-hover);">
                            <a href="{{ route('athletes.show', $athlete->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $athlete->name }}
                            </a>
                        </td>
                        <td>{{ $athlete->birth_date->format('d/m/Y') }}</td>
                        <td style="text-align: center; font-weight: 600;">
                            KU {{ $athlete->ku }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge {{ $athlete->gender === 'L' ? 'badge-primary' : 'badge-accent' }}" style="padding: 0.2rem 0.4rem;">
                                {{ $athlete->gender === 'L' ? 'PA' : 'PI' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-success" style="background-color: var(--bg-light); color: var(--primary-hover); border: 1px solid rgba(2, 132, 199, 0.2);">
                                {{ $athlete->currentClass->name }}
                            </span>
                        </td>
                        <td>
                            @if($athlete->parentUser)
                                <span style="font-weight: 500;">{{ $athlete->parentUser->name }}</span>
                                <br><small style="color: var(--text-muted);">{{ $athlete->parentUser->phone }}</small>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">Belum ditautkan</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                <a href="{{ route('athletes.show', $athlete->id) }}" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; background-color: #0284c7;">
                                    Detail
                                </a>
                                <a href="{{ route('athletes.edit', $athlete->id) }}" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; background-color: #ffb700; color: #fff;">
                                    Edit
                                </a>
                                <form action="{{ route('athletes.destroy', $athlete->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus atlet ini? Seluruh data riwayat kelas dan hasil lomba atlet ini juga akan terhapus permanen.');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-accent" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                            Data atlet tidak ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
        {{ $athletes->links() }}
    </div>
</div>
@endsection
