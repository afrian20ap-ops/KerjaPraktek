<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = User::where('role', '!=', 'admin')->orderBy('name', 'asc')->get();
        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'username'            => 'required|string|max:50|unique:users,username|alpha_dash',
            'nik'                 => 'required|string|unique:users,nik',
            'role'                => 'required|in:karyawan,supervisi',
            'divisi'              => 'nullable|string',
            'jabatan'             => 'nullable|string',
            'gaji_pokok_harian'   => 'required|numeric',
            'uang_makan_harian'   => 'required|numeric',
            'uang_lembur_per_jam' => 'required|numeric',
            'password'            => 'required|string|min:6',
        ]);

        User::create([
            'name'                => $request->name,
            'username'            => $request->username,
            'nik'                 => $request->nik,
            'role'                => $request->role,
            'divisi'              => $request->divisi,
            'jabatan'             => $request->jabatan,
            'gaji_pokok_harian'   => $request->gaji_pokok_harian,
            'uang_makan_harian'   => $request->uang_makan_harian,
            'uang_lembur_per_jam' => $request->uang_lembur_per_jam,
            'password'            => Hash::make($request->password),
        ]);

        return redirect()->route('admin.karyawan')->with('success', 'Data Karyawan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'                => 'required|string|max:255',
            'username'            => 'required|string|max:50|unique:users,username,' . $id . '|alpha_dash',
            'nik'                 => 'required|string|unique:users,nik,' . $id,
            'role'                => 'required|in:karyawan,supervisi',
            'divisi'              => 'nullable|string',
            'jabatan'             => 'nullable|string',
            'gaji_pokok_harian'   => 'required|numeric',
            'uang_makan_harian'   => 'required|numeric',
            'uang_lembur_per_jam' => 'required|numeric',
        ]);

        $user->name                = $request->name;
        $user->username            = $request->username;
        $user->nik                 = $request->nik;
        $user->role                = $request->role;
        $user->divisi              = $request->divisi;
        $user->jabatan             = $request->jabatan;
        $user->gaji_pokok_harian   = $request->gaji_pokok_harian;
        $user->uang_makan_harian   = $request->uang_makan_harian;
        $user->uang_lembur_per_jam = $request->uang_lembur_per_jam;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.karyawan')->with('success', 'Data Karyawan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.karyawan')->with('success', 'Data Karyawan berhasil dihapus!');
    }
}