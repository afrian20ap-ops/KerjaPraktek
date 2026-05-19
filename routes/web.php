<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

Route::get('/', function () {
    return redirect('/login');
});

// Route Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    // Cari user berdasarkan kolom 'username'
    $user = User::where('username', $request->username)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return redirect()->route('login')
            ->with('error', 'Username atau password salah.')
            ->withInput(['username' => $request->username]);
    }

    // Simpan info sesi
    $request->session()->put('logged_in', true);
    $request->session()->put('user_id',   $user->id);
    $request->session()->put('user_role', $user->role);
    $request->session()->put('user_name', $user->name);

    // Redirect sesuai role
    return match ($user->role) {
        'admin'     => redirect('/admin/dashboard'),
        'supervisi' => redirect('/supervisi/dashboard'),
        default     => redirect('/karyawan/dashboard'),
    };
})->name('login.post');

Route::post('/logout', function (Request $request) {
    $request->session()->flush();
    return redirect('/login');
})->name('logout');

// Profile & Settings
Route::get('/profile', function () {
    $user = \App\Models\User::find(session('user_id'));
    return view('profile.index', compact('user'));
})->name('profile');

Route::post('/profile', function (Request $request) {
    $userId = session('user_id');
    if (!$userId) return redirect()->route('login');

    $user = \App\Models\User::findOrFail($userId);

    $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'alamat' => 'nullable|string|max:500',
        'divisi' => 'nullable|string|max:100',
        'jabatan' => 'nullable|string|max:100',
        'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $user->name = $request->name;
    $user->phone = $request->phone;
    $user->alamat = $request->alamat;
    $user->divisi = $request->divisi;
    $user->jabatan = $request->jabatan;

    // Handle foto upload
    if ($request->hasFile('foto')) {
        // Hapus foto lama jika ada
        if ($user->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto);
        }
        $path = $request->file('foto')->store('foto-profil', 'public');
        $user->foto = $path;
    }

    $user->save();
    $request->session()->put('user_name', $request->name);

    return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
})->name('profile.update');

Route::post('/profile/credentials', function (Request $request) {
    $userId = session('user_id');
    if (!$userId) return redirect()->route('login');

    $user = \App\Models\User::findOrFail($userId);

    $request->validate([
        'current_password' => 'required|string',
        'username' => 'required|string|max:50|unique:users,username,' . $user->id,
        'new_password' => 'nullable|string|min:6|confirmed',
    ], [
        'current_password.required' => 'Password lama wajib diisi.',
        'username.required' => 'Username wajib diisi.',
        'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
        'new_password.min' => 'Password baru minimal 6 karakter.',
        'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
    ]);

    // Verifikasi password lama
    if (!Hash::check($request->current_password, $user->password)) {
        return redirect()->route('profile')->withErrors(['current_password' => 'Password lama tidak sesuai.'])->withInput();
    }

    $user->username = $request->username;

    if ($request->filled('new_password')) {
        $user->password = Hash::make($request->new_password);
    }

    $user->save();

    return redirect()->route('profile')->with('success', 'Username & Password berhasil diperbarui.');
})->name('profile.credentials');

Route::get('/settings', function () {
    return redirect()->route('profile');
})->name('settings');

// ==========================================
// ROUTES ADMIN
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $totalKaryawan = \App\Models\User::where('role', 'karyawan')->count();
        $karyawanBaru = \App\Models\User::where('role', 'karyawan')->whereMonth('created_at', now()->month)->count();
        
        $yesterday = now()->subDay()->format('Y-m-d');
        $hadirKemarin = \App\Models\Absensi::where('tanggal', $yesterday)->where('status', 'Hadir')->count();
        $tidakHadirKemarin = max(0, $totalKaryawan - $hadirKemarin);
        
        $persentaseHadir = $totalKaryawan > 0 ? round(($hadirKemarin / $totalKaryawan) * 100) : 0;
        
        // Gaji (Mock or sum from Penggajian if exists)
        $totalGaji = \App\Models\Penggajian::whereMonth('periode_mulai', now()->month)->sum('total_gaji_bersih');
        
        $totalLaporanBulanIni = \App\Models\LaporanLapangan::whereMonth('created_at', now()->month)->count();
        $laporanMenunggu = \App\Models\LaporanLapangan::where('status', 'Terkirim')->count();
        
        $absensiTerkini = \App\Models\Absensi::with('user')->whereDate('tanggal', now()->subDay()->toDateString())->orderBy('created_at', 'desc')->get();
        $laporanTerkini = \App\Models\LaporanLapangan::with('user')->whereDate('tanggal', now()->toDateString())->orderBy('created_at', 'desc')->get();

        $tahun = now()->year;
        $chartHadir      = [];
        $chartTidakHadir = [];
        $activeKaryawanCount = max(1, \App\Models\User::where('role', 'karyawan')->count());
        for ($m = 1; $m <= 12; $m++) {
            $totalHadirBulanIni = \App\Models\Absensi::whereYear('tanggal', $tahun)->whereMonth('tanggal', $m)->where('status', 'Hadir')->count();
            $totalAlpaBulanIni  = \App\Models\Absensi::whereYear('tanggal', $tahun)->whereMonth('tanggal', $m)->where('status', 'Alpa')->count();
            
            // Rata-rata hari hadir dan alpa per karyawan
            $chartHadir[]      = round($totalHadirBulanIni / $activeKaryawanCount, 1);
            $chartTidakHadir[] = round($totalAlpaBulanIni / $activeKaryawanCount, 1);
        }

        
        return view('admin.dashboard', compact(
            'totalKaryawan', 'karyawanBaru', 'hadirKemarin', 'tidakHadirKemarin',
            'persentaseHadir', 'totalGaji', 'absensiTerkini', 'laporanTerkini',
            'totalLaporanBulanIni', 'laporanMenunggu', 'chartHadir', 'chartTidakHadir'
        ));
    })->name('dashboard');

    Route::get('/karyawan', [App\Http\Controllers\KaryawanController::class, 'index'])->name('karyawan');
    Route::post('/karyawan', [App\Http\Controllers\KaryawanController::class, 'store'])->name('karyawan.store');
    Route::put('/karyawan/{id}', [App\Http\Controllers\KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{id}', [App\Http\Controllers\KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    Route::get('/absensi', [App\Http\Controllers\AbsensiController::class, 'indexAdmin'])->name('absensi');

    Route::get('/gaji/slip', [App\Http\Controllers\PenggajianController::class, 'slip'])->name('gaji.slip');
    Route::post('/gaji/slip/{id}', [App\Http\Controllers\PenggajianController::class, 'updateSlip'])->name('gaji.slip.update');

    Route::get('/gaji/rekap', function() {
        return redirect()->route('admin.gaji.slip');
    })->name('gaji.rekap');
    Route::post('/gaji/generate', [App\Http\Controllers\PenggajianController::class, 'generate'])->name('gaji.generate');
    Route::post('/gaji/kasbon', [App\Http\Controllers\PenggajianController::class, 'updateKasbon'])->name('gaji.kasbon');

    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'indexAdmin'])->name('laporan');
    Route::post('/laporan/{id}/approve', [App\Http\Controllers\LaporanController::class, 'approveAdmin'])->name('laporan.approve');

    Route::get('/laporan/{id}/download', [App\Http\Controllers\LaporanController::class, 'downloadXlsx'])
    ->name('laporan.download');
});


// ==========================================
// ROUTES SUPERVISI
// ==========================================
Route::prefix('supervisi')->name('supervisi.')->group(function () {
    Route::get('/dashboard', function () {
        $tim = \App\Models\User::where('role', 'karyawan')->count();
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d');

        $hadirKemarin = \App\Models\Absensi::where('tanggal', $yesterday)->where('status', 'Hadir')->count();
        $tidakHadirKemarin = \App\Models\Absensi::where('tanggal', $yesterday)->where('status', 'Alpa')->count();

        $laporanTertunda = \App\Models\LaporanLapangan::where('status', 'Terkirim')->count();
        $laporanTerkini  = \App\Models\LaporanLapangan::with('user')->whereDate('tanggal', $today)->orderBy('created_at', 'desc')->get();
        $absensiTerkini  = \App\Models\Absensi::with('user')->whereDate('tanggal', $yesterday)->orderBy('created_at', 'desc')->get();

        $tahun = now()->year;
        $chartHadir      = [];
        $chartTidakHadir = [];
        $activeKaryawanCount = max(1, $tim);
        for ($m = 1; $m <= 12; $m++) {
            $totalHadirBulanIni = \App\Models\Absensi::whereYear('tanggal', $tahun)->whereMonth('tanggal', $m)->where('status', 'Hadir')->count();
            $totalAlpaBulanIni  = \App\Models\Absensi::whereYear('tanggal', $tahun)->whereMonth('tanggal', $m)->where('status', 'Alpa')->count();
            
            $chartHadir[]      = round($totalHadirBulanIni / $activeKaryawanCount, 1);
            $chartTidakHadir[] = round($totalAlpaBulanIni / $activeKaryawanCount, 1);
        }

        return view('supervisi.dashboard', compact(
            'tim', 'hadirKemarin', 'tidakHadirKemarin',
            'laporanTertunda', 'laporanTerkini', 'absensiTerkini',
            'chartHadir', 'chartTidakHadir'
        ));
    })->name('dashboard');

    Route::get('/absensi', [App\Http\Controllers\AbsensiController::class, 'indexSupervisi'])->name('absensi');
    Route::post('/absensi', [App\Http\Controllers\AbsensiController::class, 'storeSupervisi'])->name('absensi.store');

    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'indexSupervisi'])->name('laporan');
    Route::post('/laporan/{id}/approve', [App\Http\Controllers\LaporanController::class, 'approveSupervisi'])->name('laporan.approve');

    Route::get('/laporan/{id}/download', [App\Http\Controllers\LaporanController::class, 'downloadXlsx'])
    ->name('laporan.download');
});


// ==========================================
// ROUTES KARYAWAN
// ==========================================
Route::prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', function () {
        $userId = session('user_id', 1);
        $hadirBulanIni = \App\Models\Absensi::where('user_id', $userId)
                            ->whereMonth('tanggal', \Carbon\Carbon::now()->month)
                            ->where('status', 'Hadir')->count();
        
        $laporanKaryawan = \App\Models\LaporanLapangan::where('user_id', $userId)
                            ->whereMonth('created_at', \Carbon\Carbon::now()->month)
                            ->count();
                            
        $totalGaji = \App\Models\Penggajian::where('user_id', $userId)
                        ->whereMonth('periode_mulai', \Carbon\Carbon::now()->month)
                        ->sum('total_gaji_bersih');

        $tahun = now()->year;
        $chartHadir      = [];
        $chartTidakHadir = [];
        for ($m = 1; $m <= 12; $m++) {
            $hadir = \App\Models\Absensi::where('user_id', $userId)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $m)->where('status', 'Hadir')->count();
            $alpa  = \App\Models\Absensi::where('user_id', $userId)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $m)->where('status', 'Alpa')->count();
            
            $chartHadir[]      = $hadir;
            $chartTidakHadir[] = $alpa;
        }

        $yesterday = \Carbon\Carbon::yesterday()->format('Y-m-d');
        $absensiKemarin = \App\Models\Absensi::with('user')->where('user_id', $userId)->where('tanggal', $yesterday)->get();
        $laporanTerkini = \App\Models\LaporanLapangan::with('user')->where('user_id', $userId)->whereDate('tanggal', \Carbon\Carbon::today()->format('Y-m-d'))->orderBy('created_at', 'desc')->get();

        return view('karyawan.dashboard', compact('hadirBulanIni', 'laporanKaryawan', 'totalGaji', 'chartHadir', 'chartTidakHadir', 'absensiKemarin', 'laporanTerkini'));
    })->name('dashboard');

    Route::get('/absensi', [App\Http\Controllers\AbsensiController::class, 'indexKaryawan'])->name('absensi');

    Route::get('/gaji', function () {
        return view('karyawan.gaji.index');
    })->name('gaji');

    Route::get('/gaji/slip', [App\Http\Controllers\PenggajianController::class, 'slipKaryawan'])->name('gaji.slip');

    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'indexKaryawan'])->name('laporan');
    Route::get('/laporan/{id}/edit', [App\Http\Controllers\LaporanController::class, 'editKaryawan'])->name('laporan.edit');
    Route::put('/laporan/{id}', [App\Http\Controllers\LaporanController::class, 'updateKaryawan'])->name('laporan.update');
    Route::post('/laporan', [App\Http\Controllers\LaporanController::class, 'storeKaryawan'])->name('laporan.store');
});