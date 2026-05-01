@extends('layouts.app')

@section('title', 'Pengaturan Profil')
@section('page-title', 'Pengaturan')

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
<div class="grid-2" style="grid-template-columns: 1fr 2fr;">
    <!-- Side Nav Settings -->
    <div class="panel" style="padding: 0; align-self: start;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="avatar" style="width: 48px; height: 48px; font-size: 1.25rem;">
                    {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                </div>
                <div>
                    <strong style="display: block; font-size: 1.1rem; color: var(--text-primary);">{{ session('user_name', 'Pengguna') }}</strong>
                    <span style="font-size: 0.85rem; color: var(--text-secondary);">{{ ucfirst(session('user_role', 'Karyawan')) }}</span>
                </div>
            </div>
        </div>
        <div style="padding: 1rem;">
            <a href="#umum" class="nav-item active" style="margin-bottom: 0.25rem;"><i class="fa-solid fa-user"></i> Pengaturan Umum</a>
            <a href="#keamanan" class="nav-item" style="margin-bottom: 0.25rem;"><i class="fa-solid fa-shield-halved"></i> Keamanan & Password</a>
            <a href="#notifikasi" class="nav-item" style="margin-bottom: 0.25rem;"><i class="fa-solid fa-bell"></i> Notifikasi</a>
        </div>
    </div>

    <!-- Forms -->
    <div>
        <!-- Form Umum -->
        <div class="panel" id="umum" style="margin-bottom: 2rem;">
            <div class="panel-header">
                <h3 class="panel-title">Pengaturan Umum</h3>
            </div>
            <div class="panel-body">
                <form action="#" method="POST">
                    <div class="form-group">
                        <label class="form-label">Foto Profil</label>
                        <div style="display: flex; align-items: center; gap: 1.5rem;">
                            <div class="avatar" style="width: 80px; height: 80px; font-size: 2rem; border-radius: 50%;">
                                {{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline" style="margin-bottom: 0.5rem;">Ubah Foto</button>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Format JPG, PNG atau GIF. Maks 2MB.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ session('user_name', 'Pengguna') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Akses</label>
                            <input type="email" class="form-control" value="user@garudajaya.com">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" value="+62 812-3456-7890">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat Domisili</label>
                            <input type="text" class="form-control" value="Jakarta, Indonesia">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Form Keamanan -->
        <div class="panel" id="keamanan" style="margin-bottom: 2rem;">
            <div class="panel-header">
                <h3 class="panel-title">Keamanan & Password</h3>
            </div>
            <div class="panel-body">
                <form action="#" method="POST">
                    <div class="form-group">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" placeholder="Masukkan password saat ini">
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control" placeholder="Minimal 8 karakter">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" placeholder="Ulangi password baru">
                        </div>
                    </div>
                    
                    <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
