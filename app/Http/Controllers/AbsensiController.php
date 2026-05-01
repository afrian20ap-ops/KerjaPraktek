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
        $absensis = Absensi::with('user')->orderBy('tanggal', 'desc')->limit(100)->get();
        // Kelompokkan berdasarkan tanggal jika diperlukan, namun untuk contoh kita kirim semua
        return view('admin.absensi.index', compact('absensis'));
    }

    public function indexSupervisi(Request $request)
    {
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
                $totalHari = $status === 'Hadir' ? 1.0 : 0;
                $uangMakan = $status === 'Hadir';

                // Hitung lembur otomatis
                $jamLembur = 0;
                if ($jamKeluar && in_array($status, ['Hadir', 'Terlambat'])) {
                    $keluar      = Carbon::parse($jamKeluar);
                    $batasNormal = Carbon::parse('17:00:00');
                    if ($keluar->gt($batasNormal)) {
                        $jamLembur = $keluar->diffInHours($batasNormal);
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
        // Asumsi auth dummy user ID 1
        $userId = 1; 
        $absensis = Absensi::where('user_id', $userId)->orderBy('tanggal', 'desc')->limit(30)->get();
        return view('karyawan.absensi.index', compact('absensis'));
    }
}
