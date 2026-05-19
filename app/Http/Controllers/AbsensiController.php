<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $bulanTahun = $request->query('bulan_tahun', Carbon::now()->format('Y-m'));
        $tanggalObj = Carbon::createFromFormat('Y-m', $bulanTahun);
        $dari   = $tanggalObj->copy()->startOfMonth()->format('Y-m-d');
        $sampai = $tanggalObj->copy()->endOfMonth()->format('Y-m-d');
        
        $userId = $request->query('user_id');

        $semuaKaryawan = User::where('role', 'karyawan')->orderBy('name')->get();

        if ($userId) {
            $absensis = Absensi::with('user')
                ->where('user_id', $userId)
                ->whereBetween('tanggal', [$dari, $sampai])
                ->orderBy('tanggal', 'desc')
                ->get();
            $karyawanTerpilih = User::find($userId);
        } else {
            $absensis = collect();
            $karyawanTerpilih = null;
        }

        return view('admin.absensi.index', compact('absensis', 'dari', 'sampai', 'semuaKaryawan', 'karyawanTerpilih', 'bulanTahun'));
    }

    public function indexSupervisi(Request $request)
    {
        // Auto-checkout for yesterday if current time is >= 08:59
        $now = Carbon::now();
        if ($now->hour > 8 || ($now->hour == 8 && $now->minute >= 59)) {
            $yesterday = $now->copy()->subDay()->format('Y-m-d');
            $unclosed = Absensi::where('tanggal', $yesterday)
                ->where('status', 'Hadir')
                ->whereNull('jam_keluar')
                ->get();
            
            foreach ($unclosed as $abs) {
                $abs->jam_keluar = '08:59:00';
                $abs->jam_lembur = 16; // 08:59 -> >= 30 menit = 16 jam
                $abs->save();
            }
        }

        // Ambil tanggal dari query param, default hari ini
        $tanggal = $request->query('tanggal', Carbon::now()->format('Y-m-d'));
        $users   = User::where('role', 'karyawan')->get();

        $absensis = [];
        foreach ($users as $user) {
            $abs = Absensi::where('user_id', $user->id)->where('tanggal', $tanggal)->first();
            $absensis[$user->id] = $abs;
        }

        return view('supervisi.absensi.index', compact('users', 'tanggal', 'absensis'));
    }

    public function storeSupervisi(Request $request)
    {
        $tanggal = $request->tanggal ?? Carbon::now()->format('Y-m-d');
        $data    = $request->absensi;

        if ($data) {
            foreach ($data as $userId => $val) {
                $status    = $val['status'];
                $jamMasuk  = isset($val['jam_masuk'])  && $val['jam_masuk']  !== '' ? $val['jam_masuk']  : null;
                $jamKeluar = isset($val['jam_keluar']) && $val['jam_keluar'] !== '' ? $val['jam_keluar'] : null;

                // Hanya Hadir atau Alpa
                $isSunday  = Carbon::parse($tanggal)->isSunday();
                $totalHari = $status === 'Hadir' ? ($isSunday ? 1.5 : 1.0) : 0;
                $uangMakan = $status === 'Hadir';

                // Hitung lembur otomatis
                $jamLembur = 0;
                if ($jamKeluar && $status === 'Hadir') {
                    $keluar      = Carbon::parse($jamKeluar);
                    $batasNormal = Carbon::parse('17:00:00');
                    
                    if ($keluar->hour < 9) {
                        $keluar->addDay();
                    }
                    
                    if ($keluar->gt($batasNormal)) {
                        $jamLembur = (int) round($keluar->diffInMinutes($batasNormal) / 60);
                    }
                }

                Absensi::updateOrCreate(
                    ['user_id' => $userId, 'tanggal' => $tanggal],
                    [
                        'jam_masuk'       => $jamMasuk,
                        'jam_keluar'      => $jamKeluar,
                        'status'          => $status,
                        'total_hari'      => $totalHari,
                        'jam_lembur'      => $jamLembur,
                        'dapat_uang_makan'=> $uangMakan,
                    ]
                );
            }
        }

        return redirect()
            ->route('supervisi.absensi', ['tanggal' => $tanggal])
            ->with('success', 'Absensi tanggal ' . Carbon::parse($tanggal)->format('d M Y') . ' berhasil disimpan!');
    }

    public function indexKaryawan(Request $request)
    {
        $userId = session('user_id'); 
        $absensis = Absensi::where('user_id', $userId)->orderBy('tanggal', 'desc')->limit(30)->get();
        return view('karyawan.absensi.index', compact('absensis'));
    }
}
