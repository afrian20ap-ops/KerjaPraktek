<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::find(session('user_id'));
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        if (session('user_role') !== 'admin') {
            return redirect()->back()->withErrors('Hanya Admin yang dapat mengubah profil.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'divisi' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $user = User::find(session('user_id'));

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $path = $request->file('foto')->store('profile_photos', 'public');
            $user->foto = $path;
        }

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->divisi = $request->divisi;
        $user->jabatan = $request->jabatan;
        $user->alamat = $request->alamat;
        
        $user->save();

        session(['user_name' => $user->name, 'user_foto' => $user->foto]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function credentials(Request $request)
    {
        if (session('user_role') !== 'admin') {
            return redirect()->back()->withErrors('Hanya Admin yang dapat mengubah kredensial.');
        }

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . session('user_id'),
            'current_password' => 'required',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $user = User::find(session('user_id'));

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->username = $request->username;

        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Kredensial berhasil diperbarui.');
    }
}
