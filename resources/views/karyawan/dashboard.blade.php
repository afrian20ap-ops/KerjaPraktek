@extends('layouts.app')
@section('title', 'Dashboard Karyawan')
@section('page-title', 'Dashboard Pribadi')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('karyawan.dashboard') }}" class="nav-item active"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Personal</span>
<a href="{{ route('karyawan.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Riwayat Absensi</a>
<a href="{{ route('karyawan.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('karyawan.laporan') }}" class="nav-item"><i class="fa-solid fa-camera"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="welcome-banner" style="background:linear-gradient(135deg,var(--primary-600) 0%,var(--primary-500) 60%,var(--primary-400) 100%);border-radius:var(--border-radius-lg);padding:2rem 2.5rem;margin-bottom:2rem;color:white;position:relative;overflow:hidden;">
    <div style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.07);"></div>
    <div style="position:relative;z-index:1;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1.5rem;">
        <div>
            <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Halo, Karyawan!</h2>
            <p style="opacity:0.85;font-size:0.95rem;">Jangan lupa melakukan absensi hari ini.</p>
        </div>
        <div style="background:rgba(255,255,255,0.2);backdrop-filter:blur(10px);padding:1.5rem;border-radius:var(--border-radius);text-align:center;">
            <div style="font-size:2rem;font-weight:800;margin-bottom:0.5rem;font-family:monospace;" id="clock">00:00:00</div>
            <button class="btn" style="background:white;color:var(--primary-600);width:100%;font-weight:700;box-shadow:var(--shadow);"><i class="fa-solid fa-fingerprint"></i> Absen Masuk</button>
        </div>
    </div>
</div>



<div class="grid-cards">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--success) 15%,transparent),color-mix(in srgb,var(--success) 25%,transparent));color:var(--success);">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $hadirBulanIni }}</div>
            <div class="stat-label">Hadir Bulan Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--warning) 15%,transparent),color-mix(in srgb,var(--warning) 25%,transparent));color:var(--warning);">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $terlambatBulanIni }}</div>
            <div class="stat-label">Terlambat</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID');
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endpush
