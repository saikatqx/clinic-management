<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'favicon' => 'nullable|image|mimes:png,ico|max:512',
            'clinic_logo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        ]);

        $setting = Setting::firstOrNew();

        $data = $request->only(['clinic_name', 'address', 'mobile', 'email', 'location_link']);

        // Favicon
        if ($request->hasFile('favicon')) {
            if ($setting->favicon && file_exists(public_path('images/settings/' . $setting->favicon))) {
                unlink(public_path('images/settings/' . $setting->favicon));
            }
            $faviconName = time() . '_favicon.' . $request->favicon->extension();
            $request->favicon->move(public_path('images/settings'), $faviconName);
            $data['favicon'] = $faviconName;
        }

        // Logo
        if ($request->hasFile('clinic_logo')) {
            if ($setting->clinic_logo && file_exists(public_path('images/settings/' . $setting->clinic_logo))) {
                unlink(public_path('images/settings/' . $setting->clinic_logo));
            }
            $logoName = time() . '_logo.' . $request->clinic_logo->extension();
            $request->clinic_logo->move(public_path('images/settings'), $logoName);
            $data['clinic_logo'] = $logoName;
        }

        $setting->updateOrCreate(['id' => $setting->id ?? 1], $data);

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}

