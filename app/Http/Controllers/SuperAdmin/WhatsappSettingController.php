<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Models\WhatsappSetting;
use App\Http\Controllers\Controller;

class WhatsappSettingController extends Controller
{
    //
        public function index()
    {
        $setting = WhatsappSetting::firstOrCreate([]);
        return view('superadmin.whatsapp-settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string|max:255',
            'sender_phone' => 'required|string|max:20',
            'delay_between_messages' => 'required|integer|min:1|max:30',
            'service_provider' => 'required|in:fontee,zaviago,restqa'
        ]);

        $setting = WhatsappSetting::firstOrCreate([]);
        $setting->update($request->all());

        return redirect()->back()->with('success', 'Pengaturan WhatsApp berhasil diperbarui.');
    }
}
