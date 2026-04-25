@extends('layouts.app')
@section('title', 'Laporan Lapangan')
@section('page-title', 'Laporan Lapangan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>
<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Data Absensi</a>
<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item active"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="header-left">
            <span class="panel-title">Semua Laporan Lapangan</span>
            <div class="filter-actions">
                <input type="date" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem;" value="{{ date('Y-m-d') }}">
                <select class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem;">
                    <option>Semua Karyawan</option>
                    <option>Andi Wirawan</option>
                </select>
                <button class="btn btn-primary" style="padding:0.35rem 0.75rem;"><i class="fa-solid fa-search"></i></button>
            </div>
        </div>
    </div>
    <div style="padding:1.5rem;display:grid;grid-template-columns:1fr;gap:1.5rem;">
        <div style="border:1px solid var(--border-color);border-radius:var(--border-radius);padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="avatar" style="width:40px;height:40px;">A</div>
                    <div>
                        <div style="font-weight:600;">Andi Wirawan</div>
                        <div style="font-size:0.8rem;color:var(--text-muted);">10:30 AM</div>
                    </div>
                </div>
                <span class="badge success" style="height:fit-content;">Disetujui Supervisi</span>
            </div>
            <p style="color:var(--text-secondary);font-size:0.95rem;margin-bottom:1rem;">Pekerjaan pemasangan perangkat pada site A telah selesai dilakukan sesuai standar.</p>
            <div style="display:flex;gap:0.5rem;margin-bottom:1rem;">
                <div style="width:80px;height:60px;background:var(--bg-hover);border-radius:var(--border-radius-sm);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"><i class="fa-solid fa-image"></i></div>
                <div style="width:80px;height:60px;background:var(--bg-hover);border-radius:var(--border-radius-sm);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"><i class="fa-solid fa-image"></i></div>
            </div>
            <div style="border-top:1px solid var(--border-color);padding-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                <button class="btn btn-primary" style="font-size:0.85rem;" onclick="showToast('File foto laporan mulai diunduh!')"><i class="fa-solid fa-download"></i> Unduh Foto Bukti</button>
                <button class="btn btn-outline" style="font-size:0.85rem;" onclick="showToast('Laporan PDF mulai diunduh!')"><i class="fa-solid fa-file-pdf"></i> Unduh Laporan PDF</button>
            </div>
        </div>
    </div>
</div>
@endsection
