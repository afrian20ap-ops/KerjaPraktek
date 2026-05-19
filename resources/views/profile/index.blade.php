@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Pengguna')

@section('sidebar-nav')
    @if(session('user_role') == 'admin')
        <span class="nav-label">Menu Utama</span>
        <a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>
        <span class="nav-label" style="margin-top:1rem;">Absensi</span>
        <a href="{{ route('admin.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Data Absensi</a>
        <span class="nav-label" style="margin-top:1rem;">Penggajian</span>
        <a href="{{ route('admin.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
        <span class="nav-label" style="margin-top:1rem;">Operasional</span>
        <a href="{{ route('admin.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
    @elseif(session('user_role') == 'supervisi')
        <span class="nav-label">Menu Utama</span>
        <a href="{{ route('supervisi.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="nav-label" style="margin-top:1rem;">Operasional</span>
        <a href="{{ route('supervisi.absensi') }}" class="nav-item"><i class="fa-solid fa-user-clock"></i> Absensi Karyawan</a>
        <a href="{{ route('supervisi.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
    @else
        <span class="nav-label">Menu Utama</span>
        <a href="{{ route('karyawan.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
        <span class="nav-label" style="margin-top:1rem;">Personal</span>
        <a href="{{ route('karyawan.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Riwayat Absensi</a>
        <a href="{{ route('karyawan.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
        <span class="nav-label" style="margin-top:1rem;">Operasional</span>
        <a href="{{ route('karyawan.laporan') }}" class="nav-item"><i class="fa-solid fa-camera"></i> Laporan Lapangan</a>
    @endif
@endsection

@section('content')
<!-- Header Profil -->
<div class="panel" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
    <!-- Cover Background -->
    <div style="height: 150px; background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-400) 100%); position: relative;">
        <!-- Dekorasi Cover -->
        <div style="position: absolute; right: 10%; top: -20px; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.1);"></div>
        <div style="position: absolute; right: 20%; top: 50px; width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
    </div>
    
    <!-- Profil Info -->
    <div style="padding: 0 2rem 2rem 2rem; position: relative;">
        <div style="display: flex; align-items: flex-end; gap: 1.5rem; margin-top: -50px; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <!-- Avatar Besar -->
            <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--surface); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; color: var(--primary-500); border: 4px solid var(--surface); box-shadow: var(--shadow-md); z-index: 1;">
                {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
            </div>
            
            <div style="flex: 1; padding-bottom: 0.5rem; z-index: 1;">
                <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">{{ session('user_name', 'Pengguna') }}</h2>
                <div style="display: flex; align-items: center; gap: 1rem; color: var(--text-secondary); font-size: 0.95rem;">
                    <span><i class="fa-solid fa-briefcase" style="margin-right: 0.35rem;"></i> {{ ucfirst(session('user_role', 'Karyawan')) }}</span>
                    <span><i class="fa-solid fa-envelope" style="margin-right: 0.35rem;"></i> user@garudajaya.com</span>
                    <span><i class="fa-solid fa-location-dot" style="margin-right: 0.35rem;"></i> Jakarta, Indonesia</span>
                </div>
            </div>

            <div style="padding-bottom: 0.5rem; z-index: 1;">
                <!-- Edit Profil moved to bottom section -->
            </div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem; align-items: start;">
    <!-- Informasi Personal -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-regular fa-address-card" style="color: var(--primary-500); margin-right: 0.5rem;"></i> Informasi Personal</span>
        </div>
        <div class="panel-body" style="padding: 0 1.5rem 1.5rem 1.5rem;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary);">Nomor Induk Karyawan</span>
                    <strong style="color: var(--text-primary);">EMP-{{ rand(1000, 9999) }}</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary);">Nama Lengkap</span>
                    <strong style="color: var(--text-primary);">{{ session('user_name', 'Pengguna') }}</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary);">Email Akses</span>
                    <strong style="color: var(--text-primary);">user@garudajaya.com</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary);">Nomor Telepon</span>
                    <strong style="color: var(--text-primary);">+62 812-3456-7890</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 1rem 0;">
                    <span style="color: var(--text-secondary);">Tanggal Bergabung</span>
                    <strong style="color: var(--text-primary);">12 Januari 2024</strong>
                </li>
            </ul>
        </div>
    </div>

    <!-- Informasi Pekerjaan -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-briefcase" style="color: var(--primary-500); margin-right: 0.5rem;"></i> Informasi Pekerjaan</span>
        </div>
        <div class="panel-body" style="padding: 0 1.5rem 1.5rem 1.5rem;">
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary);">Role Sistem</span>
                    <span class="badge primary">{{ ucfirst(session('user_role', 'Karyawan')) }}</span>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary);">Departemen</span>
                    <strong style="color: var(--text-primary);">Operasional Lapangan</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary);">Status Karyawan</span>
                    <span class="badge success">Aktif</span>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary);">Level Basic</span>
                    <strong style="color: var(--text-primary);">Skill</strong>
                </li>
                <li style="display: flex; justify-content: space-between; padding: 1rem 0;">
                    <span style="color: var(--text-secondary);">Atasan Langsung</span>
                    <strong style="color: var(--text-primary);">Bpk. Joko Susilo</strong>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Form Edit Profil -->
<div class="panel" style="margin-top: 2rem;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-pen" style="color: var(--primary-500); margin-right: 0.5rem;"></i> Edit Profil</span>
    </div>
    <div class="panel-body" style="padding: 1.5rem;">
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="name" value="{{ session('user_name', 'Pengguna') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Akses</label>
                    <input type="email" class="form-control" name="email" value="user@garudajaya.com" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" name="phone" value="+62 812-3456-7890">
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat Domisili</label>
                    <input type="text" class="form-control" name="address" value="Jakarta, Indonesia">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
