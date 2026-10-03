<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = Setting::create([]);
        }

        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::first();

        if (!$setting) {
            $setting = Setting::create([]);
        }

        $data = $request->validate([

            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_website' => 'nullable|string|max:255',
            'company_address' => 'nullable|string',

            'timezone' => 'required|string',
            'date_format' => 'required|string',

            'sender_name' => 'nullable|string|max:255',
            'sender_email' => 'nullable|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',

            'email_notifications' => 'nullable',

            'company_logo' => 'nullable|image|max:2048',

        ]);

        if ($request->hasFile('company_logo')) {

            if (
                $setting->company_logo &&
                Storage::disk('public')->exists($setting->company_logo)
            ) {
                Storage::disk('public')->delete($setting->company_logo);
            }

            $data['company_logo'] = $request
                ->file('company_logo')
                ->store('company-logo', 'public');
        }

        $data['email_notifications'] =
            $request->has('email_notifications');

        

        try {

    $setting->update($data);

    return back()->with(
        'success',
        'Settings updated successfully.'
    );

} catch (\Exception $e) {

    

}

        return back()->with(
            'success',
            'Settings updated successfully.'
        );
    }
}