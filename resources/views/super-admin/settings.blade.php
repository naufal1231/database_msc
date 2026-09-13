@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('header_title', 'Pengaturan Sistem & Gateway Notifikasi')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- System Settings Form -->
    <div class="data-card">
        <div class="card-header">
            <h3 class="card-title">⚙️ Konfigurasi Profil Klub & WhatsApp API Gateway</h3>
        </div>
        <hr style="margin: 1rem 0; border: none; border-bottom: 1px solid var(--border-color);">

        <form method="POST" action="{{ route('settings.update') }}" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf

            <!-- Section: Profil Klub -->
            <div>
                <h4 style="font-weight: bold; color: var(--primary-hover); margin-bottom: 1rem; border-left: 4px solid var(--primary); padding-left: 0.5rem;">🏢 Profil Utama Klub</h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <div class="form-group">
                        <label for="club_name" class="form-label">Nama Resmi Klub</label>
                        <input type="text" name="club_name" id="club_name" class="form-input" value="{{ old('club_name', $settings['club_name']) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="organizer" class="form-label">Penyelenggara / Pengurus Cabang</label>
                        <input type="text" name="organizer" id="organizer" class="form-input" value="{{ old('organizer', $settings['organizer']) }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-top: 1.25rem;">
                    <div class="form-group">
                        <label for="address" class="form-label">Alamat Kolam Latihan Utama</label>
                        <textarea name="address" id="address" class="form-input" rows="2" required>{{ old('address', $settings['address']) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Nomor Kontak Sekretariat (WA)</label>
                        <input type="text" name="phone" id="phone" class="form-input" value="{{ old('phone', $settings['phone']) }}" required>
                    </div>
                </div>
            </div>

            <!-- Section: WhatsApp Gateway -->
            <div style="margin-top: 1rem;">
                <h4 style="font-weight: bold; color: var(--accent); margin-bottom: 1rem; border-left: 4px solid var(--accent); padding-left: 0.5rem;">💬 WhatsApp Gateway API (Simulasi)</h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <div class="form-group">
                        <label for="wa_gateway_url" class="form-label">WhatsApp Gateway endpoint URL</label>
                        <input type="url" name="wa_gateway_url" id="wa_gateway_url" class="form-input" value="{{ old('wa_gateway_url', $settings['wa_gateway_url']) }}">
                    </div>

                    <div class="form-group">
                        <label for="wa_api_key" class="form-label">API Secret Token Key</label>
                        <input type="password" name="wa_api_key" id="wa_api_key" class="form-input" value="{{ old('wa_api_key', $settings['wa_api_key']) }}">
                    </div>

                    <div class="form-group">
                        <label for="wa_status" class="form-label">Status Gateway</label>
                        <select name="wa_status" id="wa_status" class="form-input">
                            <option value="Terhubung" {{ old('wa_status', $settings['wa_status']) === 'Terhubung' ? 'selected' : '' }}>🟢 Terhubung</option>
                            <option value="Terputus" {{ old('wa_status', $settings['wa_status']) === 'Terputus' ? 'selected' : '' }}>🔴 Terputus / Disable</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="text-align: right; margin-top: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-weight: bold;">
                    💾 Simpan Pengaturan
                </button>
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
