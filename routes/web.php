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
    $request->session()->put('user_foto', $user->foto);

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
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/credentials', [App\Http\Controllers\ProfileController::class, 'credentials'])->name('profile.credentials');

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
    Route::post('/gaji/slip/save', [App\Http\Controllers\PenggajianController::class, 'saveSlip'])->name('gaji.slip.save');
    Route::post('/gaji/slip/{id}', [App\Http\Controllers\PenggajianController::class, 'updateSlip'])->name('gaji.slip.update');

    Route::get('/gaji/rekap', function() {
        return redirect()->route('admin.gaji.slip');
    })->name('gaji.rekap');
    Route::post('/gaji/generate', [App\Http\Controllers\PenggajianController::class, 'generate'])->name('gaji.generate');
    Route::post('/gaji/kasbon', [App\Http\Controllers\PenggajianController::class, 'updateKasbon'])->name('gaji.kasbon');

    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'indexAdmin'])->name('laporan');
    Route::post('/laporan/{id}/approve', [App\Http\Controllers\LaporanController::class, 'approveAdmin'])->name('laporan.approve');

    Route::get('/laporan/{id}/download', [App\Http\Controllers\LaporanController::class, 'downloadXlsx'])->name('laporan.download');
    Route::post('/laporan/download-bulk', [App\Http\Controllers\LaporanController::class, 'downloadBulkXlsx'])->name('laporan.downloadBulk');
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

    Route::get('/laporan/{id}/download', [App\Http\Controllers\LaporanController::class, 'downloadXlsx'])->name('laporan.download');
    Route::post('/laporan/download-bulk', [App\Http\Controllers\LaporanController::class, 'downloadBulkXlsx'])->name('laporan.downloadBulk');
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