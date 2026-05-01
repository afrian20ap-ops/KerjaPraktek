<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanLapangan;
use App\Models\User;
use Carbon\Carbon;

class LaporanController extends Controller
{
    // ============================
    // ADMIN
    // ============================

    public function indexAdmin(Request $request)
    {
        // Filter per minggu, default minggu ini
        $minggu = $request->query('minggu', Carbon::now()->weekOfYear);
        $tahun  = $request->query('tahun',  Carbon::now()->year);

        // Hitung range tanggal dari nomor minggu
        $mulai  = Carbon::now()->setISODate($tahun, $minggu)->startOfWeek();
        $akhir  = Carbon::now()->setISODate($tahun, $minggu)->endOfWeek();

        $laporan = LaporanLapangan::with('user')
            ->whereBetween('tanggal', [$mulai->format('Y-m-d'), $akhir->format('Y-m-d')])
            ->orderBy('tanggal', 'desc')
            ->get();

        // Daftar minggu yang tersedia untuk dropdown
        $mingguList = LaporanLapangan::selectRaw('WEEK(tanggal, 1) as minggu, YEAR(tanggal) as tahun')
            ->groupBy('minggu', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('minggu', 'desc')
            ->get();

        return view('admin.laporan.index', compact('laporan', 'minggu', 'tahun', 'mulai', 'akhir', 'mingguList'));
    }

    public function approveAdmin(Request $request, $id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        $laporan->status = 'Disetujui';
        $laporan->save();

        return redirect()->back()->with('success', 'Laporan berhasil disetujui!');
    }

    // ============================
    // SUPERVISI
    // ============================

    public function indexSupervisi(Request $request)
    {
        $minggu = $request->query('minggu', Carbon::now()->weekOfYear);
        $tahun  = $request->query('tahun',  Carbon::now()->year);

        $mulai = Carbon::now()->setISODate($tahun, $minggu)->startOfWeek();
        $akhir = Carbon::now()->setISODate($tahun, $minggu)->endOfWeek();

        // Ambil SEMUA laporan karyawan
        $laporan = LaporanLapangan::with('user')
            ->whereBetween('tanggal', [$mulai->format('Y-m-d'), $akhir->format('Y-m-d')])
            ->orderBy('tanggal', 'desc')
            ->get();

        $mingguList = LaporanLapangan::selectRaw('WEEK(tanggal, 1) as minggu, YEAR(tanggal) as tahun')
            ->groupBy('minggu', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('minggu', 'desc')
            ->get();

        return view('supervisi.laporan.index', compact('laporan', 'minggu', 'tahun', 'mulai', 'akhir', 'mingguList'));
    }

    public function approveSupervisi(Request $request, $id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        $laporan->status = 'Disetujui';
        $laporan->save();

        return redirect()->back()->with('success', 'Laporan berhasil disetujui!');
    }

    // ============================
    // KARYAWAN
    // ============================

    public function indexKaryawan(Request $request)
    {
        $minggu = $request->query('minggu', Carbon::now()->weekOfYear);
        $tahun  = $request->query('tahun',  Carbon::now()->year);

        $mulai = Carbon::now()->setISODate($tahun, $minggu)->startOfWeek();
        $akhir = Carbon::now()->setISODate($tahun, $minggu)->endOfWeek();

        $userId = 1; // Dummy auth

        $laporan = LaporanLapangan::with('user')
            ->where('user_id', $userId)
            ->whereBetween('tanggal', [$mulai->format('Y-m-d'), $akhir->format('Y-m-d')])
            ->orderBy('tanggal', 'desc')
            ->get();

        $mingguList = LaporanLapangan::where('user_id', $userId)
            ->selectRaw('WEEK(tanggal, 1) as minggu, YEAR(tanggal) as tahun')
            ->groupBy('minggu', 'tahun')
            ->orderBy('tahun', 'desc')
            ->orderBy('minggu', 'desc')
            ->get();

        return view('karyawan.laporan.index', compact('laporan', 'minggu', 'tahun', 'mulai', 'akhir', 'mingguList'));
    }

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'tanggal'             => 'required|date',
            'lokasi'              => 'nullable|string|max:255',
            'deskripsi_pekerjaan' => 'required|string',
            'kendala'             => 'nullable|string',
            'solusi'              => 'nullable|string',
            'foto'                => 'nullable|image|max:4096',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('laporan', 'public');
        }

        $tanggal = Carbon::parse($request->tanggal);
        $userId  = 1; // Dummy auth

        LaporanLapangan::create([
            'user_id'             => $userId,
            'tanggal'             => $request->tanggal,
            'minggu_ke'           => $tanggal->weekOfYear,
            'lokasi'              => $request->lokasi,
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            'kendala'             => $request->kendala,
            'solusi'              => $request->solusi,
            'foto_path'           => $fotoPath,
            'status'              => 'Terkirim',
        ]);

        return redirect()
            ->route('karyawan.laporan', ['minggu' => $tanggal->weekOfYear, 'tahun' => $tanggal->year])
            ->with('success', 'Laporan lapangan berhasil dikirim!');
    }
}
