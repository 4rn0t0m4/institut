<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'google_analytics_id' => Setting::get('google_analytics_id', ''),
            'google_ads_id' => Setting::get('google_ads_id', ''),
            'analytics_property_id' => Setting::get('analytics_property_id', ''),
        ];

        $credentialsPath = storage_path('app/analytics/service-account-credentials.json');
        $credentialsExist = file_exists($credentialsPath);

        return view('admin.settings.index', compact('settings', 'credentialsExist'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'google_analytics_id' => 'nullable|string|max:50',
            'google_ads_id' => 'nullable|string|max:50',
            'analytics_property_id' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '', 'tracking');
        }

        // Sync property ID to .env for Spatie Analytics
        $this->updateEnv('ANALYTICS_PROPERTY_ID', $validated['analytics_property_id'] ?? '');

        return redirect()->route('admin.settings.index')->with('success', 'Paramètres mis à jour.');
    }

    private function updateEnv(string $key, string $value): void
    {
        // Sanitize: only allow alphanumeric key, strip newlines from value
        if (! preg_match('/^[A-Z_]+$/', $key)) {
            return;
        }
        $value = str_replace(["\n", "\r"], '', $value);

        $path = base_path('.env');
        $content = file_get_contents($path);

        $escapedKey = preg_quote($key, '/');
        if (preg_match("/^{$escapedKey}=.*/m", $content)) {
            $content = preg_replace("/^{$escapedKey}=.*/m", "{$key}={$value}", $content);
        } else {
            $content .= "\n{$key}={$value}";
        }

        // Atomic write: write to temp file then rename
        $tmpPath = $path.'.tmp';
        file_put_contents($tmpPath, $content, LOCK_EX);
        rename($tmpPath, $path);
    }
}
