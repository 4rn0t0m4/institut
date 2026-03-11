<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $banner = json_decode(Setting::get('sticky_banner', '{}'), true) ?? [];
        $banner = array_merge(['active' => false, 'text' => '', 'link' => '', 'link_label' => ''], $banner);

        return view('admin.announcement.index', compact('banner'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'banner_text'       => 'nullable|string|max:300',
            'banner_link'       => 'nullable|url|max:500',
            'banner_link_label' => 'nullable|string|max:100',
        ]);

        Setting::set('sticky_banner', json_encode([
            'active'     => $request->boolean('banner_active'),
            'text'       => $validated['banner_text'] ?? '',
            'link'       => $validated['banner_link'] ?? '',
            'link_label' => $validated['banner_link_label'] ?? '',
        ]), 'announcement');

        return redirect()->route('admin.announcement.index')->with('success', 'Barre d\'annonce mise à jour.');
    }
}
