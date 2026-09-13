@extends('layouts.admin')

@section('title', 'Perlombaan & Event')
@section('header_title', 'Manajemen Event Perlombaan')

@section('content')
<div class="data-card">
    <div class="card-header">
        <h3 class="card-title">Daftar Event Perlombaan Resmi</h3>
        <a href="{{ route('competitions.create') }}" class="btn btn-primary" style="max-width: 220px;">
            ➕ Buat Event Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60px; text-align: center;">NO</th>
                    <th>NAMA EVENT</th>
                    <th>PENYELENGGARA</th>
                    <th>LOKASI</th>
                    <th>TANGGAL EVENT</th>
                    <th style="text-align: center;">STATUS</th>
                    <th style="width: 220px; text-align: center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($competitions as $index => $comp)
                    <tr>
                        <td style="text-align: center; font-weight: 600; color: var(--text-muted);">
                            {{ $index + 1 }}
                        </td>
                        <td style="font-weight: 700; color: var(--primary-hover);">
                            <a href="{{ route('competitions.show', $comp->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $comp->name }}
                            </a>
                        </td>
                        <td>{{ $comp->organizer }}</td>
                        <td>{{ $comp->location }}</td>
                        <td>
                            @if($comp->start_date->equalTo($comp->end_date))
                                {{ $comp->start_date->format('d/m/Y') }}
                            @else
                                {{ $comp->start_date->format('d/m/Y') }} - {{ $comp->end_date->format('d/m/Y') }}
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($comp->status === 'akan_datang')
                                <span class="badge badge-primary">Akan Datang</span>
                            @elseif($comp->status === 'berlangsung')
                                <span class="badge badge-accent">Berlangsung</span>
                            @else
                                <span class="badge badge-success">Selesai</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 0.35rem; justify-content: center;">
                                <a href="{{ route('competitions.show', $comp->id) }}" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem; background-color: #0284c7;">
                                    Kelola Event
                                </a>
                                <form action="{{ route('competitions.destroy', $comp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini? Seluruh data pendaftaran atlet dan catatan waktu di event ini akan terhapus permanen.');" style="display: inline;">
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
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">
                            Belum ada event perlombaan yang dibuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
