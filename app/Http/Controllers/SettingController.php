<?php

namespace App\Http\Controllers;

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
     * Get settings data.
     */
    protected function getSettings()
    {
        if (!File::exists($this->settingsFile)) {
            // Default configuration
            return [
                'club_name' => 'Marabunta Swimming Club',
                'organizer' => 'Pengurus Cabang Marabunta',
                'address' => 'Kolam Renang Tirtomoyo, Surakarta',
                'phone' => '081234567890',
                'wa_gateway_url' => 'https://api.wa-gateway.msc/v1/send',
                'wa_api_key' => 'msc_gateway_secret_token_2026',
                'wa_status' => 'Terhubung',
            ];
        }

        return json_decode(File::get($this->settingsFile), true);
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
        $validated = $request->validate([
            'club_name' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'wa_gateway_url' => 'nullable|url',
            'wa_api_key' => 'nullable|string',
            'wa_status' => 'required|string',
        ]);

        File::put($this->settingsFile, json_encode($validated, JSON_PRETTY_PRINT));

        return redirect()->back()->with('success', 'Pengaturan sistem profil klub berhasil disimpan.');
    }
}
