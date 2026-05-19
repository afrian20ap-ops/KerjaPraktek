<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LaporanLapangan;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LaporanController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    // ==========================================
    // ADMIN
    // ==========================================

    public function indexAdmin(Request $request)
    {
        $tanggalDari   = $request->get('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->get('tanggal_sampai', now()->format('Y-m-d'));
        $karyawanId    = $request->get('karyawan_id');
        $status        = $request->get('status');

        $query = LaporanLapangan::with('user')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($karyawanId) $query->where('user_id', $karyawanId);
        if ($status)     $query->where('status', $status);

        $laporan  = $query->orderByDesc('tanggal')->get();
        $karyawan = User::where('role', 'karyawan')->orderBy('name')->get();

        return view('admin.laporan.index', compact('laporan', 'karyawan', 'tanggalDari', 'tanggalSampai'));
    }

    public function approveAdmin(Request $request, $id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        $laporan->update(['status' => 'Disetujui', 'catatan' => null]);
        return redirect()->route('admin.laporan')->with('success', 'Status laporan berhasil diperbarui.');
    }

    // ==========================================
    // SUPERVISI
    // ==========================================

    public function indexSupervisi(Request $request)
    {
        $tanggalDari   = $request->get('tanggal_dari', now()->subDays(30)->format('Y-m-d'));
        $tanggalSampai = $request->get('tanggal_sampai', now()->format('Y-m-d'));
        $karyawanId    = $request->get('karyawan_id');
        $status        = $request->get('status');

        $query = LaporanLapangan::with('user')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($karyawanId) $query->where('user_id', $karyawanId);
        if ($status)     $query->where('status', $status);

        $laporan  = $query->orderByDesc('tanggal')->get();
        $karyawan = User::where('role', 'karyawan')->orderBy('name')->get();

        return view('supervisi.laporan.index', compact('laporan', 'karyawan', 'tanggalDari', 'tanggalSampai'));
    }

    public function approveSupervisi(Request $request, $id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        $laporan->update(['status' => 'Disetujui', 'catatan' => null]);
        return redirect()->route('supervisi.laporan')->with('success', 'Status laporan berhasil diperbarui.');
    }

    // ==========================================
    // DOWNLOAD XLSX  (Admin & Supervisi)
    // ==========================================

    /**
     * Generate & download laporan sebagai XLSX.
     * Pastikan file  scripts/generate_laporan_xlsx.py  ada di root project.
     * Install Python deps: pip install openpyxl requests Pillow
     */
    public function downloadXlsx($id)
    {
        $laporan = LaporanLapangan::with('user')->findOrFail($id);

        $data = [
            'nama'             => $laporan->user->name ?? '-',
            'lokasi'           => $laporan->lokasi ?? '-',
            'tanggal'          => Carbon::parse($laporan->tanggal)->format('d-m-Y'),
            'judul'            => 'LAPORAN INSPEKSI PEKERJAAN',
            'diajukan_oleh'    => 'CV. Garuda Jaya',
            'diperiksa_oleh_1' => 'CV. Titian Mahakarya',
            'diperiksa_oleh_2' => 'PT.',
            'foto_paths'       => $laporan->foto_paths      ?? [],
            'foto_deskripsis'  => $laporan->foto_deskripsis ?? [],
        ];

        $scriptPath = base_path('scripts/generate_laporan_xlsx.py');

        if (!file_exists($scriptPath)) {
            return back()->with('error', 'Script generate_laporan_xlsx.py tidak ditemukan di folder scripts/.');
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        // Gunakan Python dari virtual environment jika ada
        $venvPython = base_path('.venv' . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR . 'python.exe');
        if (!file_exists($venvPython)) {
            // Fallback ke system Python
            $pythonCommand = PHP_OS_FAMILY === 'Windows' ? 'python' : 'python3';
        } else {
            $pythonCommand = $venvPython;
        }

        $command = escapeshellcmd($pythonCommand) . ' ' . escapeshellarg($scriptPath);

        $process = proc_open(
            $command,
            $descriptors,
            $pipes
        );

        if (!is_resource($process)) {
            return back()->with('error', 'Gagal menjalankan generator laporan.');
        }

        // Pass JSON via stdin
        fwrite($pipes[0], json_encode($data, JSON_UNESCAPED_UNICODE));
        fclose($pipes[0]);
        
        $xlsxBytes = stream_get_contents($pipes[1]);
        $errorOut  = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);

        if ($returnCode !== 0 || empty($xlsxBytes)) {
            Log::error('generate_laporan_xlsx error: ' . $errorOut);
            return back()->with('error', 'Gagal membuat file laporan.');
        }

        $filename = 'Laporan_'
            . Str::slug($laporan->user->name ?? 'karyawan')
            . '_' . Carbon::parse($laporan->tanggal)->format('Y-m-d')
            . '.xlsx';

        return response($xlsxBytes, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($xlsxBytes),
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ]);
    }

    // ==========================================
    // KARYAWAN
    // ==========================================

    public function indexKaryawan(Request $request)
    {
        $userId     = session('user_id');
        $bulanTahun = $request->get('bulan_tahun', now()->format('Y-m'));

        $tanggalObj    = Carbon::createFromFormat('Y-m', $bulanTahun);
        $tanggalDari   = $tanggalObj->copy()->startOfMonth()->format('Y-m-d');
        $tanggalSampai = $tanggalObj->copy()->endOfMonth()->format('Y-m-d');

        $laporan = LaporanLapangan::where('user_id', $userId)
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->orderByDesc('tanggal')
            ->get();

        return view('karyawan.laporan.index', compact('laporan', 'tanggalDari', 'tanggalSampai', 'bulanTahun'));
    }

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'lokasi'           => 'required|string|max:255',
            'foto'             => 'required|array|min:1|max:8',
            'foto.*'           => 'image|max:5120',
            'foto_deskripsi'   => 'nullable|array|max:8',
            'foto_deskripsi.*' => 'nullable|string|max:255',
        ]);

        $fotoUrls = $fotoDeskripsis = [];

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $idx => $file) {
                if (!$file || !$file->isValid()) continue;
                $fotoUrls[]       = $this->cloudinary->upload($file);
                $deskText         = $request->input('foto_deskripsi.' . $idx, '');
                $fotoDeskripsis[] = is_string($deskText) ? trim($deskText) : '';
            }
        }

        if (empty($fotoUrls)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Minimal harus ada 1 foto yang valid.'], 422);
            }
            return back()->withErrors(['foto' => 'Minimal harus ada 1 foto yang valid.'])->withInput();
        }

        LaporanLapangan::create([
            'user_id'             => session('user_id'),
            'tanggal'             => $request->tanggal,
            'lokasi'              => $request->lokasi,
            'deskripsi_pekerjaan' => '-',
            'foto_paths'          => $fotoUrls,
            'foto_deskripsis'     => $fotoDeskripsis,
            'status'              => 'Terkirim',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Laporan berhasil dikirim.',
                'redirect' => route('karyawan.laporan'),
            ]);
        }

        return redirect()->route('karyawan.laporan')->with('success', 'Laporan berhasil dikirim.');
    }

    public function editKaryawan($id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        abort_if($laporan->user_id !== session('user_id'), 403);
        abort_if($laporan->status === 'Disetujui', 403, 'Laporan sudah disetujui, tidak dapat diedit.');
        return view('karyawan.laporan.edit', compact('laporan'));
    }

    public function updateKaryawan(Request $request, $id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        abort_if($laporan->user_id !== session('user_id'), 403);
        abort_if($laporan->status === 'Disetujui', 403, 'Laporan sudah disetujui, tidak dapat diedit.');

        $request->validate([
            'tanggal'          => 'required|date',
            'lokasi'           => 'required|string|max:255',
            'foto'             => 'nullable|array|max:8',
            'foto.*'           => 'image|max:5120',
            'foto_deskripsi'   => 'nullable|array|max:8',
            'foto_deskripsi.*' => 'nullable|string|max:255',
            'removed_fotos'    => 'nullable|array',
            'removed_fotos.*'  => 'string',
        ]);

        $currentFotos      = $laporan->foto_paths      ?? [];
        $currentDeskripsis = $laporan->foto_deskripsis ?? [];

        if ($request->filled('removed_fotos')) {
            $removedIndexes = $request->removed_fotos;
            $newFotos = $newDeskripsis = [];
            foreach ($currentFotos as $i => $url) {
                if (!in_array((string)$i, $removedIndexes)) {
                    if (in_array($url, $removedIndexes)) { $this->cloudinary->delete($url); continue; }
                    $newFotos[]      = $url;
                    $newDeskripsis[] = $currentDeskripsis[$i] ?? '';
                } else {
                    $this->cloudinary->delete($url);
                }
            }
            $currentFotos      = $newFotos;
            $currentDeskripsis = $newDeskripsis;
        }

        $remainingSlots = 8 - count($currentFotos);
        if ($request->hasFile('foto')) {
            $uploadedCount = 0;
            foreach ($request->file('foto') as $idx => $file) {
                if ($uploadedCount >= $remainingSlots) break;
                $currentFotos[]      = $this->cloudinary->upload($file);
                $currentDeskripsis[] = $request->input('foto_deskripsi.' . $idx, '');
                $uploadedCount++;
            }
        }

        if ($request->has('existing_deskripsi')) {
            foreach ($request->input('existing_deskripsi', []) as $i => $desk) {
                if (isset($currentDeskripsis[$i])) $currentDeskripsis[$i] = $desk;
            }
        }

        if (empty($currentFotos)) {
            return back()->withErrors(['foto' => 'Minimal harus ada 1 foto.'])->withInput();
        }

        $laporan->update([
            'tanggal'             => $request->tanggal,
            'lokasi'              => $request->lokasi,
            'deskripsi_pekerjaan' => '-',
            'foto_paths'          => $currentFotos,
            'foto_deskripsis'     => $currentDeskripsis,
        ]);

        return redirect()->route('karyawan.laporan')->with('success', 'Laporan berhasil diperbarui.');
    }
}