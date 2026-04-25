@extends('layouts.app')
@section('title', 'Data Absensi')
@section('page-title', 'Data Absensi Karyawan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>

<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item active"><i class="fa-solid fa-calendar-check"></i> Data Absensi</a>

<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header" style="flex-wrap: wrap; gap: 1rem;">
        <span class="panel-title">Rekapitulasi Absensi</span>
        <div class="panel-actions" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label style="font-size: 0.85rem; font-weight: 500; color: var(--text-secondary);">Minggu Ke-</label>
                <input type="number" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:70px; text-align:center;" value="74">
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label style="font-size: 0.85rem; font-weight: 500; color: var(--text-secondary);">Dari:</label>
                <input type="date" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:130px;" value="2026-04-04">
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <label style="font-size: 0.85rem; font-weight: 500; color: var(--text-secondary);">Sampai:</label>
                <input type="date" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:130px;" value="2026-04-10">
            </div>
            <button class="btn btn-primary"><i class="fa-solid fa-filter"></i> Tampilkan</button>
            <button class="btn btn-outline" style="border-color: var(--primary-500); color: var(--primary-500);" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak PDF</button>
            <button class="btn btn-primary" onclick="showToast('Data berhasil disimpan!')"><i class="fa-solid fa-save"></i> Simpan</button>
        </div>
    </div>
    
    <style>
        .table-absensi { width: 100%; border-collapse: collapse; font-family: 'Outfit', sans-serif; font-size: 0.85rem; }
        .table-absensi th, .table-absensi td { border: 1px solid var(--border-color); padding: 0.4rem; text-align: center; color: var(--text-primary); white-space: nowrap; }
        .table-absensi th { background-color: var(--bg-hover); font-weight: 600; color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; }
        .table-absensi tbody tr:hover td { background-color: color-mix(in srgb, var(--bg-hover) 40%, transparent); }
        .table-absensi td.name-col { text-align: left; font-weight: 500; position: sticky; left: 0; background: var(--bg-card); z-index: 10; }
        .table-absensi th.name-col { position: sticky; left: 0; z-index: 11; background: var(--bg-hover); }
        
        @media print {
            body * { visibility: hidden; }
            .panel, .panel * { visibility: visible; }
            .panel { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
            .panel-actions { display: none; }
            .table-absensi th, .table-absensi td { border: 1px solid #000; color: #000; }
            .table-absensi td.name-col { position: static; }
        }
    </style>

    <div class="table-responsive" style="max-height: 70vh; overflow-y: auto; overflow-x: auto;">
        <table class="table-absensi">
            <thead>
                <tr>
                    <th rowspan="3" style="width: 40px; position: sticky; left: 0; z-index: 11; background: var(--bg-hover);">NO.</th>
                    <th rowspan="3" class="name-col" style="min-width: 150px;">NAMA PEKERJA</th>
                    <th colspan="4" style="text-align: left; padding-left: 1rem;">MINGGU KE - 74</th>
                    <th colspan="6" style="text-align: center;">DARI TANGGAL : 04 April 2026</th>
                    <th colspan="4" style="text-align: right; padding-right: 1rem;">SAMPAI : 10 April 2026</th>
                </tr>
                <tr>
                    <th colspan="2">SABTU / 04-Apr</th>
                    <th colspan="2">MINGGU / 05-Apr</th>
                    <th colspan="2">SENIN / 06-Apr</th>
                    <th colspan="2">SELASA / 07-Apr</th>
                    <th colspan="2">RABU / 08-Apr</th>
                    <th colspan="2">KAMIS / 09-Apr</th>
                    <th colspan="2">JUM'AT / 10-Apr</th>
                </tr>
                <tr>
                    @for($i = 0; $i < 7; $i++)
                        <th>MASUK</th>
                        <th>KELUAR</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @php
                    $pekerja = [
                        'Sohib', 'Syarif', 'Syafii Lubis', 'Hafiz', 'Syafii Al islami', 
                        'Judin', 'Sulistiono', 'Ridho', 'Rudi', 'Aceng', 
                        'Hannafi', 'Dwi', 'Ade Irwan', 'Nandar', 'Ahmad Sutia', 'Aan', 'Hendra'
                    ];
                @endphp
                
                @foreach($pekerja as $index => $nama)
                <tr>
                    <td style="position: sticky; left: 0; background: var(--bg-card); z-index: 10;">{{ $index + 1 }}</td>
                    <td class="name-col">{{ $nama }}</td>
                    @for($i = 0; $i < 7; $i++)
                        <td style="padding: 0;"><input type="text" value="09:00" style="width: 100%; min-width: 45px; border: none; outline: none; text-align: center; background: transparent; font-family: inherit; font-size: 0.85rem; color: inherit; padding: 0.4rem;"></td>
                        <td style="padding: 0;"><input type="text" value="17:00" style="width: 100%; min-width: 45px; border: none; outline: none; text-align: center; background: transparent; font-family: inherit; font-size: 0.85rem; color: inherit; padding: 0.4rem;"></td>
                    @endfor
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
