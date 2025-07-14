<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Menampilkan form edit profil
    public function edit()
    {
        $user = auth()->user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        return view('dashboard.profile.edit', compact('user', 'profile'));
    }

    // Memperbarui profil
    public function update(Request $request)
    {
        $user = auth()->user();

         // Cek apakah user sudah login
         if (!$user) {
             return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
         }
   // Validasi input
         $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Ambil profil dari database
        $profile = UserProfile::where('user_id', $user->id)->first();

        // Jika profil tidak ditemukan, buat baru hanya jika diperlukan
        if (!$profile) {
            $profile = new UserProfile();
            $profile->user_id = $user->id;
        }

        // Handle upload avatar
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($profile->avatar && file_exists(public_path($profile->avatar))) {
                unlink(public_path($profile->avatar));
            }

            // Simpan avatar baru
            $imageName = time() . '.' . $request->avatar->extension();
            $request->avatar->move(public_path('images/avatars'), $imageName);
            $profile->avatar = 'images/avatars/' . $imageName;
        }

        // Update field profil
        $profile->name = $request->input('name');
        $profile->phone = $request->input('phone');
        $profile->address = $request->input('address');

        // Simpan (jika belum ada, akan insert, jika sudah ada, akan update)
        $profile->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function showChangePasswordForm()
{
    return view('dashboard.profile.change_password');
}

public function changePassword(Request $request)
{
    $user = auth()->user();

    // Validasi input
    $request->validate([
        'current_password' => 'required',
        'new_password' => ['required', 'confirmed', Password::defaults()],
    ]);

    // Cek apakah password lama cocok
    if (!Hash::check($request->input('current_password'), $user->password)) {
        return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
    }

    // Update password di database
    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'password' => Hash::make($request->input('new_password')),
            'updated_at' => now()
        ]);

    return redirect()->back()->with('success', 'Password berhasil diperbarui.');
}
    }