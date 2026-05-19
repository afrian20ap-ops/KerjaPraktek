@extends('layouts.app')
@section('title', 'Slip Gaji Detail')
@section('page-title', 'Slip Gaji Detail')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('karyawan.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Personal</span>
<a href="{{ route('karyawan.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Riwayat Absensi</a>
<a href="{{ route('karyawan.gaji.slip') }}" class="nav-item active"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('karyawan.laporan') }}" class="nav-item"><i class="fa-solid fa-camera"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header" style="flex-wrap: wrap; gap: 0.75rem;">
        <div class="header-left">
            <span class="panel-title" style="font-weight: 700; font-size: 1.25rem;">SUMMARY REPORT</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            {{-- Filter Periode --}}
            <form class="filter-actions" action="{{ route('karyawan.gaji.slip') }}" method="GET" style="display:flex; gap:0.5rem; align-items:center;">
                <label style="font-size:0.82rem; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Dari:</label>
                <input type="date" name="date_from" class="form-control" style="padding:0.35rem 0.75rem; font-size:0.85rem; width:145px;" value="{{ $periodeMulai ?? date('Y-m-01') }}">
                <label style="font-size:0.82rem; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Sampai:</label>
                <input type="date" name="date_to" class="form-control" style="padding:0.35rem 0.75rem; font-size:0.85rem; width:145px;" value="{{ $periodeAkhir ?? date('Y-m-t') }}">
                <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.75rem;"><i class="fa-solid fa-search"></i> Tampilkan</button>
            </form>
            <button class="btn btn-outline no-print" style="border-color: var(--primary-500); color: var(--primary-500);" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak / Export PDF</button>
        </div>
    </div>
    
    <div style="padding: 1.5rem;">
        <!-- Header Informasi Karyawan -->
        <!-- Header Informasi Karyawan -->
        <style>
            .info-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--border-radius); padding: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 2rem; box-shadow: var(--shadow-sm); }
            .info-group { display: flex; flex-direction: column; gap: 0.75rem; }
            .info-row { display: flex; justify-content: space-between; font-size: 0.9rem; padding-bottom: 0.5rem; border-bottom: 1px dashed var(--border-color); }
            .info-label { color: var(--text-secondary); font-weight: 500; font-size: 0.85rem; }
            .info-value { color: var(--text-primary); font-weight: 600; text-align: right; }
            .info-highlight { background: color-mix(in srgb, var(--success) 10%, transparent); color: var(--success); padding: 0.75rem; border-radius: var(--border-radius-sm); font-size: 1.1rem; font-weight: 800; border: 1px solid color-mix(in srgb, var(--success) 30%, transparent); display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
            
            .table-report { width: 100%; border-collapse: collapse; font-family: 'Outfit', sans-serif; font-size: 0.85rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); overflow: hidden; }
            .table-report th, .table-report td { border: 1px solid var(--border-color); padding: 0.65rem 0.4rem; text-align: center; color: var(--text-primary); }
            .table-report th { background-color: var(--bg-hover); font-weight: 600; color: var(--text-secondary); font-size: 0.75rem; letter-spacing: 0.5px; }
            .table-report tbody tr:hover td { background-color: color-mix(in srgb, var(--primary-50) 40%, transparent); }
            .table-report tfoot th { background: color-mix(in srgb, var(--primary-50) 60%, transparent); color: var(--primary-700); font-weight: 700; border-top: 2px solid var(--primary-200); font-size: 0.9rem; }
            
            @media print {
                body * { visibility: hidden; }
                .panel, .panel * { visibility: visible; }
                .panel { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
                .panel-actions, .filter-actions { display: none; }
                .info-card { box-shadow: none; border: 2px solid #000; gap: 1rem; }
                .info-highlight { background: none; color: #000; border: 2px solid #000; }
                .table-report th, .table-report td { border: 1px solid #000; color: #000; }
                .table-report tfoot th { border-top: 2px solid #000; color: #000; }
            }
        </style>

        @if(isset($user))
        @php
            // Pre-calculate all values before rendering
            $grandHari = 0; $grandJamLembur = 0; $grandGaji = 0;
            $grandLembur = 0; $grandMakan = 0; $grandKasbon = 0; $grandJumlah = 0;
            $rowData = [];
            foreach ($absensis as $abs) {
                $realJamLembur = 0;
                if ($abs->jam_masuk && $abs->jam_keluar) {
                    $keluarC = \Carbon\Carbon::parse($abs->jam_keluar);
                    $batas = \Carbon\Carbon::parse($abs->jam_masuk)->setTime(17, 0, 0);
                    if ($keluarC->gt($batas)) {
                        $realJamLembur = (int) floor(abs($keluarC->diffInMinutes($batas)) / 60);
                    }
                }
                $realTotalHari = $abs->total_hari;
                if (\Carbon\Carbon::parse($abs->tanggal)->isSunday() && $abs->status === 'Hadir') {
                    $realTotalHari = 1.5;
                }
                $basicDefault  = $realTotalHari * $user->gaji_pokok_harian;
                $lemburDefault = $realJamLembur * $user->uang_lembur_per_jam;
                $makanDefault  = $abs->dapat_uang_makan ? $user->uang_makan_harian : 0;
                $valBasic  = $abs->nominal_basic  !== null ? $abs->nominal_basic  : $basicDefault;
                if (\Carbon\Carbon::parse($abs->tanggal)->isSunday() && $valBasic == $user->gaji_pokok_harian) {
                    $valBasic = $basicDefault;
                }
                $valLembur = $abs->nominal_lembur !== null ? $abs->nominal_lembur : $lemburDefault;
                $valMakan  = $abs->nominal_makan  !== null ? $abs->nominal_makan  : $makanDefault;
                $valKasbon = $abs->nominal_kasbon !== null ? $abs->nominal_kasbon : 0;
                $jumlah    = ($valBasic + $valLembur + $valMakan) - $valKasbon;
                $grandHari += $realTotalHari; $grandJamLembur += $realJamLembur;
                $grandGaji += $valBasic; $grandLembur += $valLembur;
                $grandMakan += $valMakan; $grandKasbon += $valKasbon; $grandJumlah += $jumlah;
                $rowData[] = compact('abs', 'realJamLembur', 'realTotalHari', 'valBasic', 'valLembur', 'valMakan', 'valKasbon', 'jumlah');
            }
        @endphp
        <div class="info-card">
            <div class="info-group">
                <div class="info-row"><span class="info-label">NAMA KARYAWAN</span><span class="info-value" style="text-transform: uppercase;">{{ $user->name ?? '-' }}</span></div>
                <div class="info-row"><span class="info-label">BASIC / HARI</span><span class="info-value">Rp {{ number_format($user->gaji_pokok_harian, 0, ',', '.') }}</span></div>
                <div class="info-row"><span class="info-label">UANG LEMBUR / JAM</span><span class="info-value">Rp {{ number_format($user->uang_lembur_per_jam, 0, ',', '.') }}</span></div>
                <div class="info-row"><span class="info-label">UANG MAKAN / HARI</span><span class="info-value">Rp {{ number_format($user->uang_makan_harian, 0, ',', '.') }}</span></div>
            </div>
            <div class="info-group">
                <div class="info-highlight">
                    <span style="font-size:0.85rem; font-weight:600; color:var(--text-secondary);">TOTAL GAJI DITERIMA</span>
                    <span>Rp {{ number_format($grandJumlah, 0, ',', '.') }}</span>
                </div>
                <div class="info-row"><span class="info-label">TOTAL KASBON</span><span class="info-value" style="color:var(--danger);">{{ $grandKasbon > 0 ? 'Rp '.number_format($grandKasbon, 0, ',', '.') : '-' }}</span></div>
                <div class="info-row"><span class="info-label">PERIODE</span><span class="info-value">{{ \Carbon\Carbon::parse($periodeMulai)->format('d-M-y') }} s/d {{ \Carbon\Carbon::parse($periodeAkhir)->format('d-M-y') }}</span></div>
            </div>
        </div>
        
        <div class="table-responsive" style="border-radius: var(--border-radius); border: 1px solid var(--border-color);">
            <table class="table-report">
                <thead>
                    <tr>
                        <th rowspan="2">NO</th>
                        <th rowspan="2">HARI KERJA</th>
                        <th rowspan="2">TANGGAL</th>
                        <th colspan="2">JAM DATANG &<br>PULANG</th>
                        <th rowspan="2">TOTAL<br>HARI</th>
                        <th rowspan="2">TOTAL<br>GAJI</th>
                        <th rowspan="2">JAM<br>LEMBUR</th>
                        <th rowspan="2">TOTAL<br>LEMBUR</th>
                        <th rowspan="2">UANG<br>MAKAN</th>
                        <th rowspan="2">KASBON</th>
                        <th rowspan="2">JUMLAH</th>
                    </tr>
                    <tr>
                        <th>IN</th>
                        <th>OUT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rowData as $index => $row)
                    <tr>
                        <td style="color:var(--text-secondary);">{{ $index + 1 }}</td>
                        <td style="font-weight:500; text-transform: uppercase;">{{ \Carbon\Carbon::parse($row['abs']->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                        <td style="color:var(--text-secondary);">{{ \Carbon\Carbon::parse($row['abs']->tanggal)->format('d-M-y') }}</td>
                        <td>{{ $row['abs']->jam_masuk ? \Carbon\Carbon::parse($row['abs']->jam_masuk)->format('H:i') : '-' }}</td>
                        <td>{{ $row['abs']->jam_keluar ? \Carbon\Carbon::parse($row['abs']->jam_keluar)->format('H:i') : '-' }}</td>
                        <td style="font-weight: 700; color:var(--primary-600);">{{ $row['realTotalHari'] }}</td>
                        <td>{{ number_format($row['valBasic'], 0, ',', '.') }}</td>
                        <td style="font-weight: 700;">{{ $row['realJamLembur'] ?: '-' }}</td>
                        <td>{{ $row['valLembur'] > 0 ? number_format($row['valLembur'], 0, ',', '.') : '-' }}</td>
                        <td>{{ $row['valMakan'] > 0 ? number_format($row['valMakan'], 0, ',', '.') : '-' }}</td>
                        <td style="color:var(--danger);">{{ $row['valKasbon'] > 0 ? number_format($row['valKasbon'], 0, ',', '.') : '-' }}</td>
                        <td style="font-weight: 800; color:var(--text-primary);">{{ number_format($row['jumlah'], 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" style="text-align:center; padding:2rem;">Tidak ada data absensi untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right; padding-right: 1rem;">GRAND TOTAL</th>
                        <th>{{ number_format($grandHari, 1, ',', '.') }}</th>
                        <th>{{ number_format($grandGaji, 0, ',', '.') }}</th>
                        <th>{{ $grandJamLembur }}</th>
                        <th>{{ number_format($grandLembur, 0, ',', '.') }}</th>
                        <th>{{ number_format($grandMakan, 0, ',', '.') }}</th>
                        <th style="color:var(--danger);">{{ $grandKasbon > 0 ? number_format($grandKasbon, 0, ',', '.') : '-' }}</th>
                        <th style="font-size:1.1rem;">{{ number_format($grandJumlah, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div style="text-align:center; padding:4rem 2rem; background:var(--bg-card); border-radius:var(--border-radius); border:1px solid var(--border-color);">
            <i class="fa-solid fa-file-invoice-dollar" style="font-size:3rem; color:var(--text-muted); margin-bottom:1rem;"></i>
            <h3 style="color:var(--text-primary); margin-bottom:0.5rem;">Slip Gaji Belum Tersedia</h3>
            <p style="color:var(--text-secondary);">Gaji Anda untuk periode
                <strong>{{ \Carbon\Carbon::parse($periodeMulai ?? now()->startOfMonth())->format('d M Y') }}</strong>
                s/d
                <strong>{{ \Carbon\Carbon::parse($periodeAkhir ?? now()->endOfMonth())->format('d M Y') }}</strong>
                belum di-generate oleh admin. Silakan pilih periode lain atau cek kembali nanti.
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
