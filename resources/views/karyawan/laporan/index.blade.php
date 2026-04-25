@extends('layouts.app')
@section('title', 'Laporan Lapangan')
@section('page-title', 'Laporan Lapangan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('karyawan.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Personal</span>
<a href="{{ route('karyawan.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Riwayat Absensi</a>
<a href="{{ route('karyawan.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('karyawan.laporan') }}" class="nav-item active"><i class="fa-solid fa-camera"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header">
        <div class="header-left">
            <span class="panel-title">Upload Laporan Baru</span>
        </div>
    </div>
    <div style="padding:1.5rem;">
        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Judul Laporan / Proyek</label>
                <input type="text" class="form-control" placeholder="Contoh: Instalasi Kabel Fiber Optik Area B">
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan Pekerjaan</label>
                <textarea class="form-control" rows="4" placeholder="Tulis rincian pekerjaan yang dilakukan..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Upload Foto / Bukti</label>
                <input type="file" class="form-control" accept="image/*" multiple>
            </div>
            <button type="button" class="btn btn-primary" onclick="showToast('Laporan berhasil diunggah!')"><i class="fa-solid fa-upload"></i> Kirim Laporan</button>
        </form>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Riwayat Laporan Saya</span>
    </div>
    <div style="padding:1.5rem;display:grid;grid-template-columns:1fr;gap:1.5rem;">
        <div style="border:1px solid var(--border-color);border-radius:var(--border-radius);padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:1rem;">
                <div style="font-weight:600;">Proyek Instalasi Tower</div>
                <span class="badge success" style="height:fit-content;">Diterima Supervisi</span>
            </div>
            <p style="color:var(--text-secondary);font-size:0.95rem;margin-bottom:1rem;">Pekerjaan pemasangan perangkat pada site A telah selesai dilakukan sesuai standar.</p>
            <div style="display:flex;gap:0.5rem;">
                <div style="width:80px;height:60px;background:var(--bg-hover);border-radius:var(--border-radius-sm);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"><i class="fa-solid fa-image"></i></div>
            </div>
        </div>
    </div>
</div>
@endsection
