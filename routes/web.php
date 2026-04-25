<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route Login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function () {
    // Dummy login action
    return redirect('/admin/dashboard');
})->name('login.post');

Route::get('/logout', function () {
    return redirect('/login');
})->name('logout');


// ==========================================
// ROUTES ADMIN
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/karyawan', function () {
        return view('admin.karyawan.index');
    })->name('karyawan');

    Route::get('/absensi', function () {
        return view('admin.absensi.index');
    })->name('absensi');

    Route::get('/gaji/slip', function () {
        return view('admin.gaji.slip');
    })->name('gaji.slip');

    Route::get('/gaji/rekap', function () {
        return view('admin.gaji.index');
    })->name('gaji.rekap');

    Route::get('/laporan', function () {
        return view('admin.laporan.index');
    })->name('laporan');
});


// ==========================================
// ROUTES SUPERVISI
// ==========================================
Route::prefix('supervisi')->name('supervisi.')->group(function () {
    Route::get('/dashboard', function () {
        return view('supervisi.dashboard');
    })->name('dashboard');

    Route::get('/absensi', function () {
        return view('supervisi.absensi.index');
    })->name('absensi');

    Route::get('/laporan', function () {
        return view('supervisi.laporan.index');
    })->name('laporan');
});


// ==========================================
// ROUTES KARYAWAN
// ==========================================
Route::prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', function () {
        return view('karyawan.dashboard');
    })->name('dashboard');

    Route::get('/absensi', function () {
        return view('karyawan.absensi.index');
    })->name('absensi');

    Route::get('/gaji', function () {
        return view('karyawan.gaji.index');
    })->name('gaji');

    Route::get('/gaji/slip', function () {
        return view('karyawan.gaji.slip');
    })->name('gaji.slip');

    Route::get('/laporan', function () {
        return view('karyawan.laporan.index');
    })->name('laporan');
});
