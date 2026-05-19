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
        $userId    = $request->query('user_id');
        $dateFrom  = $request->query('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo    = $request->query('date_to',   Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Pastikan format tanggal valid
        try {
            $periodeMulai = Carbon::createFromFormat('Y-m-d', $dateFrom)->format('Y-m-d');
            $periodeAkhir = Carbon::createFromFormat('Y-m-d', $dateTo)->format('Y-m-d');
        } catch (\Exception $e) {
            $periodeMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
            $periodeAkhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        // Pastikan date_from tidak lebih besar dari date_to
        if ($periodeMulai > $periodeAkhir) {
            $temp = $periodeMulai;
            $periodeMulai = $periodeAkhir;
            $periodeAkhir = $temp;
        }

        // Ambil semua karyawan untuk dropdown pencarian
        $semuaKaryawan = User::where('role', 'karyawan')->orderBy('name', 'asc')->get();

        // Jika tidak ada user_id, tampilkan state kosong
        if (!$userId) {
            return view('admin.gaji.slip', compact('semuaKaryawan', 'periodeMulai', 'periodeAkhir'))->with('belumPilih', true);
        }

        $user = User::findOrFail($userId);

        // Langsung query absensi berdasarkan rentang tanggal yang dipilih
        $absensis = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeAkhir])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Cari penggajian jika ada (untuk referensi kasbon, tapi tidak wajib)
        $penggajian = Penggajian::where('user_id', $userId)
            ->where('periode_mulai', $periodeMulai)
            ->where('periode_akhir', $periodeAkhir)
            ->first();

        return view('admin.gaji.slip', compact('user', 'absensis', 'semuaKaryawan', 'userId', 'periodeMulai', 'periodeAkhir', 'penggajian'));
    }

    public function updateSlip(Request $request, $id)
    {
        $penggajian = Penggajian::findOrFail($id);
        
        $grandGajiPokok = 0;
        $grandUangLembur = 0;
        $grandUangMakan = 0;
        $grandKasbon = 0;
        $grandJamLembur = 0;

        if ($request->has('absensi')) {
            foreach ($request->absensi as $absId => $data) {
                $abs = Absensi::find($absId);
                if ($abs) {
                    $abs->nominal_basic = $data['basic'];
                    $abs->nominal_lembur = $data['lembur'];
                    $abs->nominal_makan = $data['makan'];
                    $abs->nominal_kasbon = $data['kasbon'];
                    
                    if (isset($data['jam_lembur'])) {
                        $abs->jam_lembur = $data['jam_lembur'];
                    }
                    
                    $abs->save();

                    $grandGajiPokok += $data['basic'];
                    $grandUangLembur += $data['lembur'];
                    $grandUangMakan += $data['makan'];
                    $grandKasbon += $data['kasbon'];
                    if (isset($data['jam_lembur'])) {
                        $grandJamLembur += $data['jam_lembur'];
                    }
                }
            }
        }

        $penggajian->total_kehadiran_hari = $penggajian->total_kehadiran_hari; // Tetap
        $penggajian->total_jam_lembur   = $grandJamLembur;
        $penggajian->total_gaji_pokok   = $grandGajiPokok;
        $penggajian->total_uang_lembur  = $grandUangLembur;
        $penggajian->total_uang_makan   = $grandUangMakan;
        $penggajian->kasbon             = $grandKasbon;
        $penggajian->total_gaji_bersih  = ($grandGajiPokok + $grandUangLembur + $grandUangMakan) - $grandKasbon;
        $penggajian->save();

        return redirect()->route('admin.gaji.slip', [
            'user_id'   => $penggajian->user_id,
            'date_from' => $penggajian->periode_mulai,
            'date_to'   => $penggajian->periode_akhir,
        ])->with('success', 'Rincian Slip Gaji berhasil diperbarui!');
    }

    public function slipKaryawan(Request $request)
    {
        // Gunakan user_id dari session
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // Baca rentang tanggal dari request, default: bulan berjalan
        $dateFrom = $request->query('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo   = $request->query('date_to',   Carbon::now()->endOfMonth()->format('Y-m-d'));

        try {
            $periodeMulai = Carbon::createFromFormat('Y-m-d', $dateFrom)->format('Y-m-d');
            $periodeAkhir = Carbon::createFromFormat('Y-m-d', $dateTo)->format('Y-m-d');
        } catch (\Exception $e) {
            $periodeMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
            $periodeAkhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        // Pastikan date_from tidak lebih besar dari date_to
        if ($periodeMulai > $periodeAkhir) {
            $temp = $periodeMulai;
            $periodeMulai = $periodeAkhir;
            $periodeAkhir = $temp;
        }

        // Langsung query absensi berdasarkan rentang tanggal yang dipilih
        $absensis = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$periodeMulai, $periodeAkhir])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Cari penggajian jika ada (untuk referensi)
        $penggajian = Penggajian::with('user')
            ->where('user_id', $userId)
            ->where('periode_mulai', $periodeMulai)
            ->where('periode_akhir', $periodeAkhir)
            ->first();

        return view('karyawan.gaji.slip', compact('user', 'absensis', 'periodeMulai', 'periodeAkhir', 'penggajian'));
    }
}
