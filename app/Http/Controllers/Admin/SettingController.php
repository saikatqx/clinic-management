<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        // Always work with a single row
        $setting = Setting::first();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        // Validate all fields you have on the page
        $validated = $request->validate([
            'clinic_name'      => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'mobile'           => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'location_link'    => 'nullable|url|max:2048',

            // Socials
            'facebook_url'     => 'nullable|url|max:2048',
            'instagram_url'    => 'nullable|url|max:2048',
            'youtube_url'      => 'nullable|url|max:2048',
            'linkedin_url'     => 'nullable|url|max:2048',

            // Files
            'favicon'          => 'nullable|image|mimes:png,jpg,jpeg,ico|max:512',   // 512 KB
            'clinic_logo'      => 'nullable|image|mimes:jpg,jpeg,png,svg|max:1024', // 1 MB
        ]);

        // Ensure we always update the first (and only) settings row
        $setting = Setting::first() ?? new Setting();

        // Prepare mass-assignable fields
        $data = [
            'clinic_name'     => $validated['clinic_name'],
            'email'           => $validated['email'] ?? null,
            'mobile'          => $validated['mobile'] ?? null,
            'address'         => $validated['address'] ?? null,
            'location_link'   => $validated['location_link'] ?? null,

            // Socials
            'facebook'    => $validated['facebook_url'] ?? null,
            'instagram'   => $validated['instagram_url'] ?? null,
            'youtube'     => $validated['youtube_url'] ?? null,
            'linkedin'    => $validated['linkedin_url'] ?? null,
        ];

        // Uploads directory
        $dir = public_path('images/settings');
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // Handle favicon upload (delete old file if present)
        if ($request->hasFile('favicon')) {
            if (!empty($setting->favicon) && file_exists($dir . '/' . $setting->favicon)) {
                @unlink($dir . '/' . $setting->favicon);
            }
            $faviconName = time() . '_favicon.' . $request->file('favicon')->extension();
            $request->file('favicon')->move($dir, $faviconName);
            $data['favicon'] = $faviconName;
        }

        // Handle logo upload (delete old file if present)
        if ($request->hasFile('clinic_logo')) {
            if (!empty($setting->clinic_logo) && file_exists($dir . '/' . $setting->clinic_logo)) {
                @unlink($dir . '/' . $setting->clinic_logo);
            }
            $logoName = time() . '_logo.' . $request->file('clinic_logo')->extension();
            $request->file('clinic_logo')->move($dir, $logoName);
            $data['clinic_logo'] = $logoName;
        }

        // Save (create vs update)
        $setting->fill($data);
        $setting->save();

        return back()->with('success', 'Settings updated successfully!');
    }
}
