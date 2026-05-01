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
    public function index()
    {
        $penggajians = Penggajian::with('user')->get();
        return view('admin.gaji.index', compact('penggajians'));
    }

    public function generate()
    {
        $periodeMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $periodeAkhir = Carbon::now()->endOfMonth()->format('Y-m-d');

        $users = User::all();
        foreach($users as $user) {
            $existing = Penggajian::where('user_id', $user->id)
                ->where('periode_mulai', $periodeMulai)
                ->first();
                
            $kasbon = $existing ? $existing->kasbon : 0;
            
            $this->hitungGaji($user->id, $periodeMulai, $periodeAkhir, $kasbon);
        }

        return redirect()->route('admin.gaji.slip')->with('success', 'Gaji berhasil digenerate untuk seluruh karyawan.');
    }

    public function updateKasbon(Request $request)
    {
        $request->validate([
            'penggajian_id' => 'required|exists:penggajians,id',
            'kasbon' => 'required|numeric'
        ]);

        $penggajian = Penggajian::findOrFail($request->penggajian_id);
        
        $this->hitungGaji(
            $penggajian->user_id, 
            $penggajian->periode_mulai, 
            $penggajian->periode_akhir, 
            $request->kasbon
        );

        return redirect()->back()->with('success', 'Data Kasbon berhasil diupdate.');
    }

    public function slip(Request $request)
    {
        $userId = $request->query('user_id');

        $periodeMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $periodeAkhir = Carbon::now()->endOfMonth()->format('Y-m-d');

        // Jika tidak ada user_id, pilih karyawan pertama
        if (!$userId) {
            $firstUser = User::where('role', '!=', 'admin')->orderBy('name', 'asc')->first();
            if (!$firstUser) {
                return view('admin.gaji.slip');
            }
            $userId = $firstUser->id;
        }

        $user = User::findOrFail($userId);

        // Cari penggajian bulan ini
        $penggajian = Penggajian::with('user')->where('user_id', $userId)
            ->where('periode_mulai', $periodeMulai)
            ->where('periode_akhir', $periodeAkhir)
            ->first();

        if (!$penggajian) {
            $penggajian = $this->hitungGaji($userId, $periodeMulai, $periodeAkhir, 0);
            $penggajian->load('user');
        }
        
        $absensis = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeAkhir])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Ambil semua karyawan untuk dropdown pencarian
        $semuaKaryawan = User::where('role', '!=', 'admin')->orderBy('name', 'asc')->get();

        return view('admin.gaji.slip', compact('penggajian', 'absensis', 'semuaKaryawan'));
    }

    public function updateSlip(Request $request, $id)
    {
        $penggajian = Penggajian::findOrFail($id);
        
        $grandGajiPokok = 0;
        $grandUangLembur = 0;
        $grandUangMakan = 0;
        $grandKasbon = 0;

        if ($request->has('absensi')) {
            foreach ($request->absensi as $absId => $data) {
                $abs = Absensi::find($absId);
                if ($abs) {
                    $abs->nominal_basic = $data['basic'];
                    $abs->nominal_lembur = $data['lembur'];
                    $abs->nominal_makan = $data['makan'];
                    $abs->nominal_kasbon = $data['kasbon'];
                    $abs->save();

                    $grandGajiPokok += $data['basic'];
                    $grandUangLembur += $data['lembur'];
                    $grandUangMakan += $data['makan'];
                    $grandKasbon += $data['kasbon'];
                }
            }
        }

        $penggajian->total_gaji_pokok   = $grandGajiPokok;
        $penggajian->total_uang_lembur  = $grandUangLembur;
        $penggajian->total_uang_makan   = $grandUangMakan;
        $penggajian->kasbon             = $grandKasbon;
        $penggajian->total_gaji_bersih  = ($grandGajiPokok + $grandUangLembur + $grandUangMakan) - $grandKasbon;
        $penggajian->save();

        return redirect()->route('admin.gaji.slip', ['user_id' => $penggajian->user_id])
                         ->with('success', 'Rincian Slip Gaji berhasil diperbarui!');
    }

    public function slipKaryawan(Request $request)
    {
        // Karena tidak ada sistem auth yang fix, kita asumsikan karyawan melihat slip terakhirnya
        // Idealnya: $userId = auth()->id();
        $userId = 1; // dummy user id untuk testing
        $penggajian = Penggajian::with('user')->where('user_id', $userId)->latest()->first();
        
        if (!$penggajian) {
            // fallback for dummy UI if no payroll generated yet
            return view('karyawan.gaji.slip');
        }

        $absensis = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$penggajian->periode_mulai, $penggajian->periode_akhir])
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('karyawan.gaji.slip', compact('penggajian', 'absensis'));
    }
}
