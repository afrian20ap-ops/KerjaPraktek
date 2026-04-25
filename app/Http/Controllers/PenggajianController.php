<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Absensi;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenggajianController extends Controller
{
    /**
     * Hitung gaji berdasarkan absensi dan rate karyawan
     */
    public function hitungGaji($userId, $periodeMulai, $periodeAkhir, $kasbon = 0)
    {
        $karyawan = User::findOrFail($userId);
        
        $absensis = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeAkhir])
            ->get();

        $totalHari = 0;
        $totalJamLembur = 0;
        $totalUangMakan = 0;

        foreach ($absensis as $absensi) {
            $totalHari += $absensi->total_hari;
            $totalJamLembur += $absensi->jam_lembur;
            
            if ($absensi->dapat_uang_makan) {
                $totalUangMakan += $karyawan->uang_makan_harian;
            }
        }

        $totalGajiPokok = $totalHari * $karyawan->gaji_pokok_harian;
        $totalUangLembur = $totalJamLembur * $karyawan->uang_lembur_per_jam;

        $totalGajiBersih = $totalGajiPokok + $totalUangLembur + $totalUangMakan - $kasbon;

        // Simpan atau update rekap penggajian
        $penggajian = Penggajian::updateOrCreate(
            [
                'user_id' => $userId,
                'periode_mulai' => $periodeMulai,
                'periode_akhir' => $periodeAkhir,
            ],
            [
                'total_kehadiran_hari' => $totalHari,
                'total_jam_lembur' => $totalJamLembur,
                'total_gaji_pokok' => $totalGajiPokok,
                'total_uang_lembur' => $totalUangLembur,
                'total_uang_makan' => $totalUangMakan,
                'kasbon' => $kasbon,
                'total_gaji_bersih' => $totalGajiBersih,
            ]
        );

        return $penggajian;
    }
}
