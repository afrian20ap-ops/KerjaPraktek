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
        .table-absensi { width: 100%; border-collapse: separate; border-spacing: 0; font-family: 'Outfit', sans-serif; font-size: 0.85rem; }
        .table-absensi th, .table-absensi td { border: 1px solid var(--border-color); padding: 0.5rem 0.75rem; text-align: center; color: var(--text-primary); white-space: nowrap; }
        .table-absensi th { background-color: var(--bg-hover); font-weight: 600; color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
        .table-absensi tbody tr { transition: all 0.2s ease; }
        .table-absensi tbody tr:hover td { background-color: color-mix(in srgb, var(--primary-50) 40%, transparent); }
        .table-absensi td.name-col { text-align: left; font-weight: 600; position: sticky; left: 45px; background: var(--bg-card); z-index: 10; box-shadow: 2px 0 5px rgba(0,0,0,0.02); }
        .table-absensi th.name-col { position: sticky; left: 45px; z-index: 12; background: var(--bg-hover); box-shadow: 2px 0 5px rgba(0,0,0,0.02); }
        .table-absensi th.no-col { position: sticky; left: 0; z-index: 12; background: var(--bg-hover); width: 45px; }
        .table-absensi td.no-col { position: sticky; left: 0; background: var(--bg-card); z-index: 10; font-weight: 500; color: var(--text-secondary); }
        
        .time-input { width: 100%; min-width: 60px; border: 1px solid transparent; border-radius: var(--border-radius-sm); outline: none; text-align: center; background: transparent; font-family: 'Outfit', monospace; font-size: 0.85rem; font-weight: 500; color: inherit; padding: 0.4rem; transition: all 0.2s ease; }
        .time-input:hover { background: var(--bg-hover); border-color: var(--border-color); }
        .time-input:focus { background: var(--surface); border-color: var(--primary-500); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-500) 15%, transparent); }
        
        @media print {
            body * { visibility: hidden; }
            .panel, .panel * { visibility: visible; }
            .panel { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
            .panel-actions { display: none; }
            .table-absensi th, .table-absensi td { border: 1px solid #000; color: #000; padding: 0.25rem; font-size: 10px; }
            .table-absensi td.name-col, .table-absensi th.name-col, .table-absensi th.no-col, .table-absensi td.no-col { position: static; box-shadow: none; }
            .time-input { border: none !important; background: transparent !important; }
        }
    </style>

    <div class="table-responsive" style="border-radius: var(--border-radius); border: 1px solid var(--border-color);">
        <table class="table-absensi">
            <thead>
                <tr>
                    <th>NO.</th>
                    <th style="text-align:left;">NAMA PEKERJA</th>
                    <th>TANGGAL</th>
                    <th>JAM MASUK</th>
                    <th>JAM KELUAR</th>
                    <th>STATUS</th>
                    <th>LEMBUR (JAM)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensis as $index => $abs)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align:left; font-weight: 600;">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:28px;height:28px;font-size:0.7rem;flex-shrink:0;">{{ strtoupper(substr($abs->user->name,0,1)) }}</div>
                            <span>{{ $abs->user->name }}</span>
                        </div>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($abs->tanggal)->format('d-M-Y') }}</td>
                    <td>{{ $abs->jam_masuk ? \Carbon\Carbon::parse($abs->jam_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $abs->jam_keluar ? \Carbon\Carbon::parse($abs->jam_keluar)->format('H:i') : '-' }}</td>
                    <td>
                        @if($abs->status == 'Hadir')
                            <span class="badge success">{{ $abs->status }}</span>
                        @elseif($abs->status == 'Alpa')
                            <span class="badge danger">{{ $abs->status }}</span>
                        @else
                            <span class="badge warning">{{ $abs->status }}</span>
                        @endif
                    </td>
                    <td style="font-weight: bold;">{{ $abs->jam_lembur > 0 ? $abs->jam_lembur : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 2rem;">Belum ada data absensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
