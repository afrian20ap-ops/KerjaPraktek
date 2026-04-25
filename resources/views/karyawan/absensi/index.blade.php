@extends('layouts.app')
@section('title', 'Riwayat Absensi')
@section('page-title', 'Riwayat Absensi')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('karyawan.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Personal</span>
<a href="{{ route('karyawan.absensi') }}" class="nav-item active"><i class="fa-solid fa-calendar-check"></i> Riwayat Absensi</a>
<a href="{{ route('karyawan.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('karyawan.laporan') }}" class="nav-item"><i class="fa-solid fa-camera"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="header-left">
            <span class="panel-title">Riwayat Kehadiran Anda</span>
        </div>
        <div class="panel-actions">
            <div class="filter-actions">
                <input type="month" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem;" value="{{ date('Y-m') }}">
                <button class="btn btn-primary" style="padding: 0.35rem 0.75rem;"><i class="fa-solid fa-search"></i></button>
            </div>
        </div>
    </div>
    <div style="padding: 1rem 1.5rem; background: var(--bg-hover); border-bottom: 1px solid var(--border-color); font-size: 0.85rem; color: var(--text-secondary);">
        <i class="fa-solid fa-circle-info" style="color: var(--info);"></i> Data absensi di bawah ini diisi dan diverifikasi langsung oleh <strong>Supervisi</strong>.
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ \Carbon\Carbon::now()->subDays(1)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    <td>09:00</td>
                    <td>17:00</td>
                    <td><span class="badge success">Hadir</span></td>
                    <td style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-solid fa-check-circle" style="color: var(--success);"></i> Diverifikasi Supervisi</td>
                </tr>
                <tr>
                    <td>{{ \Carbon\Carbon::now()->subDays(2)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td><span class="badge danger">Alpa</span></td>
                    <td style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-solid fa-check-circle" style="color: var(--success);"></i> Diverifikasi Supervisi</td>
                </tr>
                <tr>
                    <td>{{ \Carbon\Carbon::now()->subDays(3)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    <td>09:00</td>
                    <td>17:01</td>
                    <td><span class="badge success">Hadir</span></td>
                    <td style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-solid fa-check-circle" style="color: var(--success);"></i> Diverifikasi Supervisi</td>
                </tr>
                <tr>
                    <td>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td><span class="badge warning">Menunggu</span></td>
                    <td style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-regular fa-clock" style="color: var(--warning);"></i> Belum Diabsen Supervisi</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
