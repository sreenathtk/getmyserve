<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = Setting::allKeyed();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'email'     => 'nullable|email|max:255',
            'phone'     => 'nullable|string|max:50',
            'address'   => 'nullable|string|max:500',
            'facebook'  => 'nullable|url|max:255',
            'twitter'   => 'nullable|url|max:255',
            'linkedin'  => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'vat_rate'  => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        Cache::forget('site_settings');

        return redirect()->route('admin.settings.edit')
            ->with('success', 'Settings saved successfully.');
    }
}
