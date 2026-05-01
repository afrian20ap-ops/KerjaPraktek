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
                @forelse($absensis as $abs)
                <tr>
                    <td style="font-weight: 500; color: var(--text-primary);">
                        <i class="fa-regular fa-calendar" style="color: var(--text-muted); margin-right: 0.5rem;"></i> 
                        {{ \Carbon\Carbon::parse($abs->tanggal)->locale('id')->isoFormat('D MMMM Y') }}
                    </td>
                    <td>
                        @if($abs->jam_masuk)
                        <div style="background: var(--bg-hover); display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-family: monospace; font-size: 0.95rem;">{{ \Carbon\Carbon::parse($abs->jam_masuk)->format('H:i') }}</div>
                        @else
                        <span style="color: var(--text-muted);">--:--</span>
                        @endif
                    </td>
                    <td>
                        @if($abs->jam_keluar)
                        <div style="background: var(--bg-hover); display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-family: monospace; font-size: 0.95rem;">{{ \Carbon\Carbon::parse($abs->jam_keluar)->format('H:i') }}</div>
                        @else
                        <span style="color: var(--text-muted);">--:--</span>
                        @endif
                    </td>
                    <td>
                        @if($abs->status == 'Hadir')
                            <span class="badge success"><i class="fa-solid fa-check" style="margin-right: 0.25rem;"></i> {{ $abs->status }}</span>
                        @elseif($abs->status == 'Alpa')
                            <span class="badge danger"><i class="fa-solid fa-xmark" style="margin-right: 0.25rem;"></i> {{ $abs->status }}</span>
                        @else
                            <span class="badge warning"><i class="fa-solid fa-exclamation" style="margin-right: 0.25rem;"></i> {{ $abs->status }}</span>
                        @endif
                    </td>
                    <td style="color: var(--text-secondary); font-size: 0.85rem;">
                        <span style="display:inline-flex;align-items:center;gap:0.35rem;background:color-mix(in srgb, var(--success) 10%, transparent);padding:0.25rem 0.5rem;border-radius:4px;color:var(--success);">
                            <i class="fa-solid fa-shield-check"></i> Diverifikasi Supervisi
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-muted);">Belum ada riwayat absensi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
