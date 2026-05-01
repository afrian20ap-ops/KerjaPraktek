<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Illuminate\Http\Request $request) {
    // Dummy login action: set a session so we can simulate login state
    $request->session()->put('logged_in', true);
    $request->session()->put('user_role', 'admin');
    $request->session()->put('user_name', 'Adminstrator');
    
    return redirect('/admin/dashboard');
})->name('login.post');

Route::post('/logout', function (Illuminate\Http\Request $request) {
    // Dummy logout action: clear session
    $request->session()->flush();
    return redirect('/login');
})->name('logout');

// Profile & Settings
Route::get('/profile', function () {
    return view('profile.index');
})->name('profile');

Route::get('/settings', function () {
    return view('profile.settings');
})->name('settings');

// ==========================================
// ROUTES ADMIN
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
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
});


// ==========================================
// ROUTES SUPERVISI
// ==========================================
Route::prefix('supervisi')->name('supervisi.')->group(function () {
    Route::get('/dashboard', function () {
        $tim = \App\Models\User::where('role', 'karyawan')->count();
        $hadirHariIni = \App\Models\Absensi::where('tanggal', \Carbon\Carbon::today()->format('Y-m-d'))
                            ->where('status', 'Hadir')->count();
        $laporanTertunda = \App\Models\LaporanLapangan::where('status', 'Terkirim')->count();
        $laporanTerkini = \App\Models\LaporanLapangan::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        return view('supervisi.dashboard', compact('tim', 'hadirHariIni', 'laporanTertunda', 'laporanTerkini'));
    })->name('dashboard');

    Route::get('/absensi', [App\Http\Controllers\AbsensiController::class, 'indexSupervisi'])->name('absensi');
    Route::post('/absensi', [App\Http\Controllers\AbsensiController::class, 'storeSupervisi'])->name('absensi.store');

    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'indexSupervisi'])->name('laporan');
    Route::post('/laporan/{id}/approve', [App\Http\Controllers\LaporanController::class, 'approveSupervisi'])->name('laporan.approve');
});


// ==========================================
// ROUTES KARYAWAN
// ==========================================
Route::prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', function () {
        $userId = 1; // Dummy auth
        $hadirBulanIni = \App\Models\Absensi::where('user_id', $userId)
                            ->whereMonth('tanggal', \Carbon\Carbon::now()->month)
                            ->where('status', 'Hadir')->count();
        
        $terlambatBulanIni = \App\Models\Absensi::where('user_id', $userId)
                            ->whereMonth('tanggal', \Carbon\Carbon::now()->month)
                            ->where('status', 'Terlambat')->count();

        return view('karyawan.dashboard', compact('hadirBulanIni', 'terlambatBulanIni'));
    })->name('dashboard');

    Route::get('/absensi', [App\Http\Controllers\AbsensiController::class, 'indexKaryawan'])->name('absensi');

    Route::get('/gaji', function () {
        return view('karyawan.gaji.index');
    })->name('gaji');

    Route::get('/gaji/slip', [App\Http\Controllers\PenggajianController::class, 'slipKaryawan'])->name('gaji.slip');

    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'indexKaryawan'])->name('laporan');
    Route::post('/laporan', [App\Http\Controllers\LaporanController::class, 'storeKaryawan'])->name('laporan.store');
});
