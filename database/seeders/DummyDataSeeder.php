<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Absensi;
use App\Models\Penggajian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Karyawan Dummy
        $karyawan = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'nik' => 'EMP-' . rand(1000, 9999),
            'divisi' => 'Teknisi (Skill)',
            'jabatan' => 'Staff',
            'gaji_pokok_harian' => 175000,
            'uang_makan_harian' => 20000,
            'uang_lembur_per_jam' => 17500,
        ]);

        // Buat data absensi selama 5 hari terakhir di bulan ini
        $today = Carbon::now();
        $absensiHari = 5;
        
        for ($i = $absensiHari; $i >= 1; $i--) {
            $date = $today->copy()->subDays($i);
            // Skip hari minggu (opsional)
            if ($date->isSunday()) continue;
            
            Absensi::create([
                'user_id' => $karyawan->id,
                'tanggal' => $date->format('Y-m-d'),
                'jam_masuk' => '09:00:00',
                'jam_keluar' => '17:00:00',
                'status' => 'Hadir',
                'total_hari' => 1.0,
                'jam_lembur' => rand(0, 2),
                'dapat_uang_makan' => true,
            ]);
        }

        // Jalankan Controller Penggajian Logic secara manual atau biarkan ini kosong
        // karena admin akan klik generate.
        // Tapi sesuai instruksi, kita juga generate dummy Penggajian (Slip Gaji) 1 kali.
        
        $periodeMulai = $today->copy()->startOfMonth()->format('Y-m-d');
        $periodeAkhir = $today->copy()->endOfMonth()->format('Y-m-d');
        
        $absensis = Absensi::where('user_id', $karyawan->id)->get();
        $totalHari = 0;
        $totalLembur = 0;
        $uangMakan = 0;

        foreach ($absensis as $abs) {
            $totalHari += $abs->total_hari;
            $totalLembur += $abs->jam_lembur;
            if ($abs->dapat_uang_makan) {
                $uangMakan += $karyawan->uang_makan_harian;
            }
        }

        $gajiPokok = $totalHari * $karyawan->gaji_pokok_harian;
        $uangLembur = $totalLembur * $karyawan->uang_lembur_per_jam;
        $kasbon = 50000; // Contoh ada kasbon 50 ribu
        
        Penggajian::create([
            'user_id' => $karyawan->id,
            'periode_mulai' => $periodeMulai,
            'periode_akhir' => $periodeAkhir,
            'total_kehadiran_hari' => $totalHari,
            'total_jam_lembur' => $totalLembur,
            'total_gaji_pokok' => $gajiPokok,
            'total_uang_lembur' => $uangLembur,
            'total_uang_makan' => $uangMakan,
            'kasbon' => $kasbon,
            'total_gaji_bersih' => ($gajiPokok + $uangLembur + $uangMakan) - $kasbon,
        ]);
    }
}
