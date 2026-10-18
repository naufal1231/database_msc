@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('header_title', 'Pengaturan Sistem & Gateway Notifikasi')

@section('content')
@php
    $isSuperAdmin = auth()->user()->role === 'super_admin';
@endphp

<div style="display: flex; flex-direction: column; gap: 2rem;">

    @if(!$isSuperAdmin)
        <div class="alert" style="background-color: #0f172a; border: 1px solid #0284c7; color: #38bdf8; padding: 0.85rem 1.25rem; border-radius: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.25rem;">ℹ️</span>
            <div>
                <strong>Mode Lihat Saja (Read-Only)</strong>
                <p style="margin: 0.2rem 0 0 0; font-size: 0.85rem; color: #cbd5e1;">
                    Anda masuk sebagai Pelatih. Seluruh konfigurasi sistem dan sistem poin hanya dapat diubah oleh Pelatih Kepala (Super Admin).
                </p>
            </div>
        </div>
    @endif

    <!-- System Settings Form -->
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">⚙️ Konfigurasi Sistem Klub &amp; Parameter Lomba</h3>
        </div>
        <hr style="margin: 1rem 0; border: none; border-bottom: 1px solid var(--border-color);">

        <form method="POST" action="{{ route('settings.update') }}" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf

            <!-- Section 1: Profil Klub -->
            <div>
                <h4 style="font-weight: bold; color: var(--primary-hover); margin-bottom: 1rem; border-left: 4px solid var(--primary); padding-left: 0.5rem;">🏢 Profil Utama Klub</h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <div class="form-group">
                        <label for="club_name" class="form-label">Nama Resmi Klub</label>
                        <input type="text" name="club_name" id="club_name" class="form-input" value="{{ old('club_name', $settings['club_name']) }}" {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                    </div>

                    <div class="form-group">
                        <label for="organizer" class="form-label">Penyelenggara / Pengurus Cabang</label>
                        <input type="text" name="organizer" id="organizer" class="form-input" value="{{ old('organizer', $settings['organizer']) }}" {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-top: 1.25rem;">
                    <div class="form-group">
                        <label for="address" class="form-label">Alamat Kolam Latihan Utama</label>
                        <textarea name="address" id="address" class="form-input" rows="2" {{ !$isSuperAdmin ? 'disabled' : '' }} required>{{ old('address', $settings['address']) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor Kontak Sekretariat (WA)</label>
                        <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone', $settings['phone']) }}" {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                    </div>
                </div>
            </div>

            <!-- Section 2: WhatsApp Gateway -->
            <div style="margin-top: 0.5rem;">
                <h4 style="font-weight: bold; color: var(--accent); margin-bottom: 1rem; border-left: 4px solid var(--accent); padding-left: 0.5rem;">💬 WhatsApp Gateway API (Simulasi)</h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <div class="form-group">
                        <label for="wa_gateway_url" class="form-label">WhatsApp Gateway endpoint URL</label>
                        <input type="url" name="wa_gateway_url" id="wa_gateway_url" class="form-input" value="{{ old('wa_gateway_url', $settings['wa_gateway_url']) }}" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                    </div>

                    <div class="form-group">
                        <label for="wa_api_key" class="form-label">API Secret Token Key</label>
                        <input type="password" name="wa_api_key" id="wa_api_key" class="form-input" value="{{ old('wa_api_key', $settings['wa_api_key']) }}" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                    </div>

                    <div class="form-group">
                        <label for="wa_status" class="form-label">Status Gateway</label>
                        <select name="wa_status" id="wa_status" class="form-input" {{ !$isSuperAdmin ? 'disabled' : '' }}>
                            <option value="Terhubung" {{ old('wa_status', $settings['wa_status']) === 'Terhubung' ? 'selected' : '' }}>🟢 Terhubung</option>
                            <option value="Terputus" {{ old('wa_status', $settings['wa_status']) === 'Terputus' ? 'selected' : '' }}>🔴 Terputus / Disable</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Konfigurasi Sistem Poin & Ambang Performa -->
            <div style="margin-top: 0.5rem;">
                <h4 style="font-weight: bold; color: #38bdf8; margin-bottom: 0.5rem; border-left: 4px solid #0284c7; padding-left: 0.5rem;">
                    🏊‍♂️ Sistem Poin &amp; Ambang Performa Perlombaan
                </h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    Konfigurasi bobot poin per nomor lomba dan persentase ambang batas evaluasi atlet yang diterapkan otomatis pada Dashboard Event (Tampilan Per Atlet).
                </p>

                {{-- Aturan Poin --}}
                <div style="font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    Aturan Bobot Poin Per Nomor Lomba:
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                    <div class="form-group">
                        <label for="poin_lebih_cepat" class="form-label">
                            <span style="color: #22c55e; font-weight: 700;">Lebih Cepat</span> dari Limit (PB)
                        </label>
                        <input type="number" min="0" max="100" name="poin_lebih_cepat" id="poin_lebih_cepat" class="form-input"
                            value="{{ old('poin_lebih_cepat', $settings['poin_lebih_cepat'] ?? 10) }}"
                            {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Default: 10 poin</small>
                    </div>

                    <div class="form-group">
                        <label for="poin_sama" class="form-label">
                            <span style="color: #94a3b8; font-weight: 700;">= Sama</span> dengan Limit (PB)
                        </label>
                        <input type="number" min="0" max="100" name="poin_sama" id="poin_sama" class="form-input"
                            value="{{ old('poin_sama', $settings['poin_sama'] ?? 5) }}"
                            {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Default: 5 poin</small>
                    </div>

                    <div class="form-group">
                        <label for="poin_lebih_lambat" class="form-label">
                            <span style="color: #ef4444; font-weight: 700;">Lebih Lambat</span> dari Limit (PB)
                        </label>
                        <input type="number" min="0" max="100" name="poin_lebih_lambat" id="poin_lebih_lambat" class="form-input"
                            value="{{ old('poin_lebih_lambat', $settings['poin_lebih_lambat'] ?? 0) }}"
                            {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Default: 0 poin</small>
                    </div>

                    <div class="form-group">
                        <label for="poin_debut" class="form-label">
                            <span style="color: #38bdf8; font-weight: 700;">★ Debut</span> (Tanpa Limit / PB Baru)
                        </label>
                        <input type="number" min="0" max="100" name="poin_debut" id="poin_debut" class="form-input"
                            value="{{ old('poin_debut', $settings['poin_debut'] ?? 10) }}"
                            {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Default: 10 poin</small>
                    </div>
                </div>

                {{-- Ambang Kategori Performa --}}
                <div style="font-size: 0.82rem; font-weight: 600; color: #cbd5e1; margin-top: 1.25rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    Ambang Batas Evaluasi Performa Atlet:
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <div class="form-group">
                        <label for="performance_good_min" class="form-label">
                            Minimal Kategori <span style="color: #22c55e; font-weight: 700;">Baik</span> (%)
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="performance_good_min" id="performance_good_min" class="form-input"
                            value="{{ old('performance_good_min', $settings['performance_good_min'] ?? 70) }}"
                            {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Persentase poin &ge; nilai ini dikategorikan <strong>Baik</strong> (Default: 70%)</small>
                    </div>

                    <div class="form-group">
                        <label for="performance_fair_min" class="form-label">
                            Minimal Kategori <span style="color: #f59e0b; font-weight: 700;">Cukup</span> (%)
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="performance_fair_min" id="performance_fair_min" class="form-input"
                            value="{{ old('performance_fair_min', $settings['performance_fair_min'] ?? 50) }}"
                            {{ !$isSuperAdmin ? 'disabled' : '' }} required>
                        <small style="color: var(--text-muted); font-size: 0.75rem;">Persentase poin &ge; nilai ini (dan &lt; Baik) dikategorikan <strong>Cukup</strong>. Di bawahnya <strong>Gagal</strong> (Default: 50%)</small>
                    </div>
                </div>
            </div>

            <div style="text-align: right; margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                @if($isSuperAdmin)
                    <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-weight: bold;">
                        💾 Simpan Pengaturan
                    </button>
                @else
                    <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">
                        🔒 Tombol simpan dinonaktifkan untuk akun Pelatih.
                    </span>
                @endif
            </div>
        </form>
    </div>

    <!-- Default Events Reference Card -->
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">🏊‍♂️ Master Referensi Nomor Lomba Standar Klub</h3>
        </div>
        <hr style="margin-bottom: 1.5rem; border: none; border-bottom: 1px solid var(--border-color);">
        <p style="margin-bottom: 1.25rem;">Berikut adalah 11 nomor lomba renang standar internal klub MSC yang otomatis digenerate saat opsi **"Otomatis Buat Nomor Lomba Standar Klub"** dicentang pada form pembuatan event perlombaan:</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div style="background-color: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 1rem;">
                <h4 style="font-weight: bold; color: var(--primary-hover); margin: 0 0 0.5rem 0;">Perorangan Gaya Bebas</h4>
                <ul style="padding-left: 1.25rem; margin: 0; line-height: 1.6; font-size: 0.9rem;">
                    <li>Gaya Bebas 50m</li>
                    <li>Gaya Bebas 100m</li>
                    <li>Gaya Bebas 200m</li>
                    <li>Gaya Bebas 400m</li>
                    <li>Gaya Bebas 800m</li>
                    <li>Gaya Bebas 1500m</li>
                </ul>
            </div>

            <div style="background-color: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 1rem;">
                <h4 style="font-weight: bold; color: var(--primary-hover); margin: 0 0 0.5rem 0;">Perorangan Gaya Punggung</h4>
                <ul style="padding-left: 1.25rem; margin: 0; line-height: 1.6; font-size: 0.9rem;">
                    <li>Gaya Punggung 50m</li>
                    <li>Gaya Punggung 100m</li>
                    <li>Gaya Punggung 200m</li>
                </ul>
            </div>

            <div style="background-color: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 1rem;">
                <h4 style="font-weight: bold; color: var(--primary-hover); margin: 0 0 0.5rem 0;">Perorangan Gaya Kupu - Kupu</h4>
                <ul style="padding-left: 1.25rem; margin: 0; line-height: 1.6; font-size: 0.9rem;">
                    <li>Gaya Kupu - Kupu 50m</li>
                    <li>Gaya Kupu - Kupu 100m</li>
                    <li>Gaya Kupu - Kupu 200m</li>
                </ul>
            </div>

            <div style="background-color: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 1rem;">
                <h4 style="font-weight: bold; color: var(--primary-hover); margin: 0 0 0.5rem 0;">Gaya Dada</h4>
                <ul style="padding-left: 1.25rem; margin: 0; line-height: 1.6; font-size: 0.9rem;">
                    <li>Gaya Dada 50m</li>
                    <li>Gaya Dada 100m</li>
                    <li>Gaya Dada 200m</li>
                </ul>
            </div>

            <div style="background-color: var(--bg-light); border: 1px solid var(--border-color); border-radius: 0.5rem; padding: 1rem;">
                <h4 style="font-weight: bold; color: var(--primary-hover); margin: 0 0 0.5rem 0;">Gaya Ganti</h4>
                <ul style="padding-left: 1.25rem; margin: 0; line-height: 1.6; font-size: 0.9rem;">
                    <li>Gaya Ganti 200m</li>
                    <li>Gaya Ganti 400m</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
