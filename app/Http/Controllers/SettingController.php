<?php

namespace App\Http\Controllers;

use App\Services\CompetitionPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    protected $settingsFile;

    public function __construct()
    {
        $this->settingsFile = storage_path('app/settings.json');
    }

    /**
     * Get settings data with defaults.
     */
    protected function getSettings()
    {
        $defaults = array_merge([
            'club_name' => 'Marabunta Swimming Club',
            'organizer' => 'Pengurus Cabang Marabunta',
            'address' => 'Kolam Renang Tirtomoyo, Surakarta',
            'phone' => '081234567890',
            'wa_gateway_url' => 'https://api.wa-gateway.msc/v1/send',
            'wa_api_key' => 'msc_gateway_secret_token_2026',
            'wa_status' => 'Terhubung',
        ], CompetitionPointService::defaults());

        if (!File::exists($this->settingsFile)) {
            return $defaults;
        }

        $saved = json_decode(File::get($this->settingsFile), true) ?? [];
        return array_merge($defaults, $saved);
    }

    /**
     * Show Settings page.
     */
    public function index()
    {
        $settings = $this->getSettings();
        return view('super-admin.settings', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            abort(403, 'Hanya Super Admin yang diizinkan mengubah konfigurasi sistem.');
        }

        $validated = $request->validate([
            'club_name' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'wa_gateway_url' => 'nullable|url',
            'wa_api_key' => 'nullable|string',
            'wa_status' => 'required|string',
            // Konfigurasi Poin & Ambang Performa
            'poin_lebih_cepat'     => 'required|integer|min:0',
            'poin_sama'            => 'required|integer|min:0',
            'poin_lebih_lambat'    => 'required|integer|min:0',
            'poin_debut'           => 'required|integer|min:0',
            'performance_good_min' => 'required|numeric|min:0|max:100',
            'performance_fair_min' => 'required|numeric|min:0|max:100',
        ]);

        if ((float) $validated['performance_good_min'] < (float) $validated['performance_fair_min']) {
            return back()->withInput()->withErrors([
                'performance_good_min' => 'Ambang batas kategori "Baik" harus lebih besar atau sama dengan kategori "Cukup".'
            ]);
        }

        $existing = File::exists($this->settingsFile)
            ? (json_decode(File::get($this->settingsFile), true) ?? [])
            : [];

        $allData = array_merge($existing, $validated);

        File::put($this->settingsFile, json_encode($allData, JSON_PRETTY_PRINT));

        return redirect()->back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
