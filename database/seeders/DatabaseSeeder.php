<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Absensi;
use App\Http\Controllers\PenggajianController;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $sohib = User::create([
            'name' => 'SOHIB',
            'email' => 'sohib@example.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'nik' => 'EMP-001',
            'gaji_pokok_harian' => 175000,
            'uang_makan_harian' => 20000,
            'uang_lembur_per_jam' => 17500,
        ]);

        $dates = [
            ['tanggal' => '2026-03-31', 'hari' => 1],
            ['tanggal' => '2026-04-01', 'hari' => 1],
            ['tanggal' => '2026-04-02', 'hari' => 1],
            ['tanggal' => '2026-04-03', 'hari' => 1],
            ['tanggal' => '2026-04-04', 'hari' => 1],
            ['tanggal' => '2026-04-05', 'hari' => 1.5], // Minggu
            ['tanggal' => '2026-04-06', 'hari' => 1],
            ['tanggal' => '2026-04-07', 'hari' => 1],
            ['tanggal' => '2026-04-08', 'hari' => 1],
            ['tanggal' => '2026-04-09', 'hari' => 1],
            ['tanggal' => '2026-04-10', 'hari' => 1],
        ];

        foreach ($dates as $d) {
            Absensi::create([
                'user_id' => $sohib->id,
                'tanggal' => $d['tanggal'],
                'jam_masuk' => '09:00:00',
                'jam_keluar' => '17:00:00',
                'status' => 'Hadir',
                'total_hari' => $d['hari'],
                'jam_lembur' => 0,
                'dapat_uang_makan' => true,
            ]);
        }

        $controller = new PenggajianController();
        $controller->hitungGaji($sohib->id, '2026-03-28', '2026-04-10', 50000);
    }
}
