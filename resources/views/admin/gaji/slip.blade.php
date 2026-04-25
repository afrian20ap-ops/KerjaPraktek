@extends('layouts.app')
@section('title', 'Summary Report Penggajian')
@section('page-title', 'Summary Report Penggajian')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>

<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Data Absensi</a>
<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item active"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="header-left">
            <span class="panel-title" style="font-weight: 700; font-size: 1.25rem;">SUMMARY REPORT</span>
            <div class="filter-actions">
                <label style="font-size: 0.85rem; font-weight: 500; color: var(--text-secondary);">Karyawan:</label>
                <select class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:200px;">
                    <option value="Sohib">Sohib (EMP-001)</option>
                    <option value="Syarif">Syarif (EMP-002)</option>
                    <option value="Syafii">Syafii Lubis (EMP-003)</option>
                    <option value="Hafiz">Hafiz (EMP-004)</option>
                </select>
                <button class="btn btn-primary" style="padding: 0.35rem 0.75rem;"><i class="fa-solid fa-search"></i> Tampilkan</button>
            </div>
        </div>
        <div class="panel-actions">
            <button class="btn btn-outline" style="border-color: var(--primary-500); color: var(--primary-500);" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak / Export PDF</button>
            <button class="btn btn-primary" onclick="showToast('Data slip berhasil disimpan!')"><i class="fa-solid fa-save"></i> Simpan</button>
        </div>
    </div>
    
    <div style="padding: 1.5rem;">
        <!-- Header Informasi Karyawan -->
        <div class="info-karyawan-grid">
            <div>
                <div style="display: flex;"><div style="width: 120px;">NAMA</div><div>: <strong>SOHIB</strong></div></div>
                <div style="display: flex;"><div style="width: 120px;">BASIC</div><div>: 175.000</div></div>
                <div style="display: flex;"><div style="width: 120px;">UANG LEMBUR</div><div>: 17.500</div></div>
            </div>
            <div>
                <div style="display: flex;"><div style="width: 120px;">UANG MAKAN</div><div>: 20.000</div></div>
            </div>
            <div>
                <div style="display: flex; background: yellow; color: black; padding: 2px 5px; width: max-content; font-weight: 700;">
                    <div style="width: 100px;">Total GAJI</div><div>: 2.232.500</div>
                </div>
                <div style="display: flex;"><div style="width: 100px;">KASBON</div><div>: 0</div></div>
                <div style="display: flex;"><div style="width: 100px;">PERIODE</div><div>: 28-Mar-26 s/d 10-Apr-26</div></div>
            </div>
        </div>

        <!-- Tabel Detail Absensi dan Gaji -->
        <style>
            .table-report { width: 100%; border-collapse: collapse; font-family: monospace; font-size: 0.9rem; }
            .table-report th, .table-report td { border: 1px solid var(--text-primary); padding: 0.5rem; text-align: center; color: var(--text-primary); }
            .table-report th { background-color: var(--bg-hover); font-weight: 700; }
            .table-report tbody tr td { border-bottom: 1px dotted var(--text-primary); }
            .table-report tfoot th { border: 1px solid var(--text-primary); border-top: 2px solid var(--text-primary); font-weight: 700; text-align: center; padding: 0.75rem; }
            
            @media print {
                body * { visibility: hidden; }
                .panel, .panel * { visibility: visible; }
                .panel { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
                .panel-actions { display: none; }
            }
        </style>
        
        <div class="table-responsive">
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
                    @php
                        $data = [
                            ['SABTU', '28-Mar-26', '-', '-', '-', '-', '-', '-', '-', '-'],
                            ['MINGGU', '29-Mar-26', '-', '-', '-', '-', '-', '-', '-', '-'],
                            ['SENIN', '30-Mar-26', '-', '-', '-', '-', '-', '-', '-', '-'],
                            ['SELASA', '31-Mar-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['RABU', '01-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['KAMIS', '02-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['JUMAT', '03-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['SABTU', '04-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['MINGGU', '05-Apr-26', '09:00', '17:00', '1,5', '262.500', '-', '-', '20.000', '282.500'],
                            ['SENIN', '06-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['SELASA', '07-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['RABU', '08-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['KAMIS', '09-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                            ['JUMAT', '10-Apr-26', '09:00', '17:00', '1', '175.000', '-', '-', '20.000', '195.000'],
                        ];
                    @endphp
                    
                    @foreach($data as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row[0] }}</td>
                        <td>{{ $row[1] }}</td>
                        <td>{{ $row[2] }}</td>
                        <td>{{ $row[3] }}</td>
                        <td style="font-weight: bold;">{{ $row[4] }}</td>
                        <td>{{ $row[5] }}</td>
                        <td style="font-weight: bold;">{{ $row[6] }}</td>
                        <td>{{ $row[7] }}</td>
                        <td>{{ $row[8] }}</td>
                        <td>{{ $row[9] === '195.000' || $row[9] === '282.500' ? '0' : '-' }}</td>
                        <td style="font-weight: bold;">{{ $row[9] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: center;">TOTAL</th>
                        <th>11,5</th>
                        <th>2.012.500</th>
                        <th>0</th>
                        <th>-</th>
                        <th>220.000</th>
                        <th>-</th>
                        <th>2.232.500</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
