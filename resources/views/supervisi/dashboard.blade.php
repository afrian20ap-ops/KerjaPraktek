@extends('layouts.app')
@section('title', 'Dashboard Supervisi')
@section('page-title', 'Dashboard Supervisi')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('supervisi.dashboard') }}" class="nav-item active"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('supervisi.absensi') }}" class="nav-item"><i class="fa-solid fa-user-clock"></i> Absensi Karyawan</a>
<a href="{{ route('supervisi.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="welcome-banner" style="background:linear-gradient(135deg,var(--primary-600) 0%,var(--primary-500) 60%,var(--primary-400) 100%);border-radius:var(--border-radius-lg);padding:2rem 2.5rem;margin-bottom:2rem;color:white;position:relative;overflow:hidden;">
    <div style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.07);"></div>
    <div style="position:relative;z-index:1;">
        <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Halo, Supervisi! 👋</h2>
        <p style="opacity:0.85;font-size:0.95rem;">Pantau kehadiran tim dan laporan operasional lapangan hari ini.</p>
    </div>
</div>

<div class="grid-cards">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">24</div>
            <div class="stat-label">Anggota Tim</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--success) 15%,transparent),color-mix(in srgb,var(--success) 25%,transparent));color:var(--success);">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">20</div>
            <div class="stat-label">Hadir Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--warning) 15%,transparent),color-mix(in srgb,var(--warning) 25%,transparent));color:var(--warning);">
            <i class="fa-solid fa-clipboard-question"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">2</div>
            <div class="stat-label">Laporan Tertunda</div>
        </div>
    </div>
</div>


<!-- Laporan Terkini -->
<div class="panel" style="margin-top: 2rem;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-clipboard-list" style="color:var(--primary-500);margin-right:0.5rem;"></i> Laporan Lapangan Terkini</span>
        <div class="panel-actions">
            <a href="{{ route('supervisi.laporan') }}" class="btn btn-outline" style="font-size:0.82rem;">Lihat Semua</a>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Tugas / Proyek</th>
                    <th>Waktu Submit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">A</div>
                            <span style="font-weight:500;">Andi Wirawan</span>
                        </div>
                    </td>
                    <td style="color:var(--text-secondary);">Proyek Instalasi Tower</td>
                    <td>10:30 AM</td>
                    <td><span class="badge warning">Menunggu Review</span></td>
                </tr>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">B</div>
                            <span style="font-weight:500;">Budi Santoso</span>
                        </div>
                    </td>
                    <td style="color:var(--text-secondary);">Maintenance Server</td>
                    <td>09:15 AM</td>
                    <td><span class="badge success">Disetujui</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
