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
<style>
    .profile-avatar {
        width: 120px; height: 120px; border-radius: 50%; object-fit: cover;
        border: 4px solid var(--surface); box-shadow: var(--shadow-md); z-index: 1;
        background: var(--surface); display: flex; align-items: center; justify-content: center;
        font-size: 3rem; font-weight: 700; color: var(--primary-500); position: relative; overflow: hidden;
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .profile-avatar-overlay {
        position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex;
        align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s;
        cursor: pointer; border-radius: 50%;
    }
    .profile-avatar:hover .profile-avatar-overlay { opacity: 1; }
    .profile-info-item {
        display: flex; justify-content: space-between; padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    .profile-info-item:last-child { border-bottom: none; }
    .profile-info-label { color: var(--text-secondary); }
    .profile-info-value { color: var(--text-primary); font-weight: 600; text-align: right; }
</style>

{{-- Notifikasi Global --}}
@if(session('success'))
<div style="padding: 0.85rem 1rem; background: color-mix(in srgb, var(--success) 15%, transparent); color: var(--success); border: 1px solid color-mix(in srgb, var(--success) 30%, transparent); border-radius: var(--border-radius); margin-bottom: 1.25rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="padding: 0.85rem 1rem; background: color-mix(in srgb, var(--danger) 15%, transparent); color: var(--danger); border: 1px solid color-mix(in srgb, var(--danger) 30%, transparent); border-radius: var(--border-radius); margin-bottom: 1.25rem; font-weight: 500;">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <ul style="margin: 0.25rem 0 0 1.25rem; padding: 0;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Header Profil -->
<div class="panel" style="padding: 0; overflow: hidden; margin-bottom: 2rem;">
    <div style="height: 150px; background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-400) 100%); position: relative;">
        <div style="position: absolute; right: 10%; top: -20px; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.1);"></div>
        <div style="position: absolute; right: 20%; top: 50px; width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
    </div>
    <div style="padding: 0 2rem 2rem 2rem; position: relative;">
        <div style="display: flex; align-items: flex-end; gap: 1.5rem; margin-top: -50px; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <!-- Avatar -->
            <div class="profile-avatar" id="avatarContainer">
                @if($user && $user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto Profil" id="avatarPreview">
                @else
                    <span id="avatarInitial">{{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}</span>
                @endif
                @if(session('user_role') == 'admin')
                <label for="fotoInput" class="profile-avatar-overlay">
                    <i class="fa-solid fa-camera" style="color: white; font-size: 1.5rem;"></i>
                </label>
                @endif
            </div>

            <div style="flex: 1; padding-bottom: 0.5rem; z-index: 1;">
                <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem;">{{ $user->name ?? session('user_name', 'Pengguna') }}</h2>
                <div style="display: flex; align-items: center; gap: 1rem; color: var(--text-secondary); font-size: 0.95rem; flex-wrap: wrap;">
                    <span><i class="fa-solid fa-briefcase" style="margin-right: 0.35rem;"></i> {{ ucfirst(session('user_role', 'Karyawan')) }}</span>
                    @if($user && $user->divisi)
                        <span><i class="fa-solid fa-building" style="margin-right: 0.35rem;"></i> {{ $user->divisi }}</span>
                    @endif
                    @if($user && $user->phone)
                        <span><i class="fa-solid fa-phone" style="margin-right: 0.35rem;"></i> {{ $user->phone }}</span>
                    @endif
                    @if($user && $user->alamat)
                        <span><i class="fa-solid fa-location-dot" style="margin-right: 0.35rem;"></i> {{ $user->alamat }}</span>
                    @endif
                </div>
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
                <li class="profile-info-item">
                    <span class="profile-info-label">Nomor Induk Karyawan</span>
                    <strong class="profile-info-value">{{ $user->nik ?? '-' }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Nama Lengkap</span>
                    <strong class="profile-info-value">{{ $user->name ?? '-' }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Username</span>
                    <strong class="profile-info-value">{{ $user->username ?? '-' }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Nomor Telepon</span>
                    <strong class="profile-info-value">{{ $user->phone ?? '-' }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Alamat Domisili</span>
                    <strong class="profile-info-value">{{ $user->alamat ?? '-' }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Tanggal Bergabung</span>
                    <strong class="profile-info-value">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</strong>
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
                <li class="profile-info-item">
                    <span class="profile-info-label">Role Sistem</span>
                    <span class="badge primary">{{ ucfirst($user->role ?? 'Karyawan') }}</span>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Divisi</span>
                    <strong class="profile-info-value">{{ $user->divisi ?? '-' }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Jabatan</span>
                    <strong class="profile-info-value">{{ $user->jabatan ?? '-' }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Gaji Pokok / Hari</span>
                    <strong class="profile-info-value">Rp {{ number_format($user->gaji_pokok_harian ?? 0, 0, ',', '.') }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Uang Makan / Hari</span>
                    <strong class="profile-info-value">Rp {{ number_format($user->uang_makan_harian ?? 0, 0, ',', '.') }}</strong>
                </li>
                <li class="profile-info-item">
                    <span class="profile-info-label">Uang Lembur / Jam</span>
                    <strong class="profile-info-value">Rp {{ number_format($user->uang_lembur_per_jam ?? 0, 0, ',', '.') }}</strong>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Form Edit Profil -->
@if(session('user_role') == 'admin')
<div class="panel" style="margin-top: 2rem;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-pen" style="color: var(--primary-500); margin-right: 0.5rem;"></i> Edit Profil</span>
    </div>
    <div class="panel-body" style="padding: 1.5rem;">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Foto Upload -->
            <div style="margin-bottom: 1.5rem;">
                <label class="form-label">Foto Profil</label>
                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                    <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; background: var(--bg-hover); display: flex; align-items: center; justify-content: center; border: 2px solid var(--border-color);">
                        @if($user && $user->foto)
                            <img src="{{ asset('storage/' . $user->foto) }}" alt="Preview" id="fotoPreviewSmall" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fa-solid fa-user" style="font-size: 2rem; color: var(--text-muted);" id="fotoIconSmall"></i>
                            <img src="" alt="Preview" id="fotoPreviewSmall" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        @endif
                    </div>
                    <div>
                        <input type="file" name="foto" id="fotoInput" accept="image/jpeg,image/png,image/webp" style="display: none;" onchange="previewFoto(this)">
                        <button type="button" onclick="document.getElementById('fotoInput').click()" class="btn btn-outline" style="padding: 0.4rem 1rem; font-size: 0.85rem;">
                            <i class="fa-solid fa-upload" style="margin-right: 0.35rem;"></i> Pilih Foto
                        </button>
                        <small style="color: var(--text-muted); font-size: 0.78rem; display: block; margin-top: 0.35rem;">Format: JPG, PNG, WebP. Maks 2MB.</small>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Contoh: 0812-3456-7890">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Divisi</label>
                    <input type="text" class="form-control" name="divisi" value="{{ old('divisi', $user->divisi ?? '') }}" placeholder="Contoh: Teknisi (Skill)">
                </div>
                <div class="form-group">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" value="{{ old('jabatan', $user->jabatan ?? '') }}" placeholder="Contoh: Staff">
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Alamat Domisili</label>
                    <input type="text" class="form-control" name="alamat" value="{{ old('alamat', $user->alamat ?? '') }}" placeholder="Contoh: Jakarta, Indonesia">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save" style="margin-right:0.35rem;"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ===== UBAH USERNAME & PASSWORD ===== --}}
@if(session('user_role') == 'admin' && isset($user))
<div class="panel" style="margin-top: 2rem;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-shield-halved" style="color: var(--primary-500); margin-right: 0.5rem;"></i> Ubah Username & Password</span>
    </div>
    <div class="panel-body" style="padding: 1.5rem;">
        <form action="{{ route('profile.credentials') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Username Saat Ini</label>
                    <input type="text" class="form-control" name="username" value="{{ old('username', $user->username) }}" required autocomplete="off">
                    <small style="color: var(--text-muted); font-size: 0.78rem; margin-top: 0.25rem; display: block;">Username digunakan untuk login ke sistem.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Lama <span style="color:var(--danger);">*</span></label>
                    <div style="position:relative;">
                        <input type="password" class="form-control" name="current_password" id="currentPassword" required placeholder="Masukkan password lama" style="padding-right: 2.5rem;">
                        <button type="button" onclick="togglePassword('currentPassword', this)" style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-muted); cursor:pointer; padding:0;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Password Baru</label>
                    <div style="position:relative;">
                        <input type="password" class="form-control" name="new_password" id="newPassword" placeholder="Kosongkan jika tidak ingin mengubah" minlength="6" style="padding-right: 2.5rem;">
                        <button type="button" onclick="togglePassword('newPassword', this)" style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-muted); cursor:pointer; padding:0;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <small style="color: var(--text-muted); font-size: 0.78rem; margin-top: 0.25rem; display: block;">Minimal 6 karakter. Kosongkan jika hanya ingin ubah username.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div style="position:relative;">
                        <input type="password" class="form-control" name="new_password_confirmation" id="confirmPassword" placeholder="Ulangi password baru" style="padding-right: 2.5rem;">
                        <button type="button" onclick="togglePassword('confirmPassword', this)" style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--text-muted); cursor:pointer; padding:0;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-key" style="margin-right:0.35rem;"></i> Simpan Kredensial</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Update small preview in form
            const previewSmall = document.getElementById('fotoPreviewSmall');
            const iconSmall = document.getElementById('fotoIconSmall');
            if (previewSmall) {
                previewSmall.src = e.target.result;
                previewSmall.style.display = 'block';
            }
            if (iconSmall) iconSmall.style.display = 'none';

            // Update avatar in header
            const container = document.getElementById('avatarContainer');
            const existingImg = container.querySelector('img#avatarPreview');
            const initial = document.getElementById('avatarInitial');
            if (existingImg) {
                existingImg.src = e.target.result;
            } else {
                if (initial) initial.style.display = 'none';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.id = 'avatarPreview';
                img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:50%;';
                container.insertBefore(img, container.firstChild);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
