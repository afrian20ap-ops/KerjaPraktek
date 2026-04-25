@extends('layouts.app')

@section('title', 'Data Karyawan')
@section('page-title', 'Data Karyawan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item" id="nav-dashboard">
    <i class="fa-solid fa-house"></i> Dashboard
</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item active" id="nav-karyawan">
    <i class="fa-solid fa-users"></i> Data Karyawan
</a>

<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item" id="nav-absensi">
    <i class="fa-solid fa-calendar-check"></i> Data Absensi
</a>
<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item" id="nav-rekap-gaji">
    <i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji
</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item" id="nav-laporan">
    <i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan
</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Daftar Karyawan</span>
        <div class="panel-actions">
            <button class="btn btn-primary" data-modal-target="modal-tambah"><i class="fa-solid fa-plus"></i> Tambah Karyawan</button>
        </div>
    </div>
    <div class="filter-bar">
        <input type="text" class="form-control filter-input" placeholder="Cari nama atau NIK...">
        <select class="form-control filter-select">
            <option value="">Semua Basic</option>
            <option value="Skill">Skill</option>
            <option value="Semi-Skill">Semi-Skill</option>
            <option value="Helper">Helper</option>
        </select>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>Basic</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>EMP-001</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">B</div>
                            <span style="font-weight:500;">Budi Santoso</span>
                        </div>
                    </td>
                    <td>Skill</td>
                    <td><span class="badge primary">Karyawan</span></td>
                    <td>
                        <button class="btn btn-outline" style="padding:0.35rem 0.75rem;" data-modal-target="modal-edit"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-outline" style="padding:0.35rem 0.75rem;color:var(--danger);border-color:color-mix(in srgb,var(--danger) 30%,transparent);"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>EMP-002</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">S</div>
                            <span style="font-weight:500;">Siti Rahayu</span>
                        </div>
                    </td>
                    <td>Semi-Skill</td>
                    <td><span class="badge warning">Supervisi</span></td>
                    <td>
                        <button class="btn btn-outline" style="padding:0.35rem 0.75rem;" data-modal-target="modal-edit"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-outline" style="padding:0.35rem 0.75rem;color:var(--danger);border-color:color-mix(in srgb,var(--danger) 30%,transparent);"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div class="modal-overlay" id="modal-tambah">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Tambah Data Karyawan</h3>
            <button class="modal-close" data-modal-close>&times;</button>
        </div>
        <div class="modal-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">NIK</label>
                    <input type="text" class="form-control" placeholder="Contoh: EMP-001">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" placeholder="Masukkan nama lengkap">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="Email untuk login">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" placeholder="Password login">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Role Akses</label>
                    <select class="form-control">
                        <option value="karyawan">Karyawan</option>
                        <option value="supervisi">Supervisi</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Basic</label>
                    <select class="form-control">
                        <option value="Skill">Skill</option>
                        <option value="Semi-Skill">Semi-Skill</option>
                        <option value="Helper">Helper</option>
                    </select>
                </div>
            </div>
            <div style="border-top: 1px solid var(--border-color); margin: 1rem 0; padding-top: 1rem;">
                <h4 style="margin-bottom: 1rem; font-size: 0.95rem; color: var(--text-secondary);">Data Penggajian</h4>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Gaji Pokok (Harian)</label>
                        <input type="number" class="form-control" placeholder="Contoh: 175000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Uang Makan</label>
                        <input type="number" class="form-control" placeholder="Contoh: 20000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Uang Lembur / Jam</label>
                        <input type="number" class="form-control" placeholder="Contoh: 17500">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" data-modal-close>Batal</button>
            <button class="btn btn-primary" data-modal-close>Simpan Data</button>
        </div>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Edit Data Karyawan</h3>
            <button class="modal-close" data-modal-close>&times;</button>
        </div>
        <div class="modal-body">
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">NIK</label>
                    <input type="text" class="form-control" value="EMP-001" readonly style="background: var(--bg-hover);">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" value="Budi Santoso">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" value="budi@example.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru (Opsional)</label>
                    <input type="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Role Akses</label>
                    <select class="form-control">
                        <option value="karyawan" selected>Karyawan</option>
                        <option value="supervisi">Supervisi</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Basic</label>
                    <select class="form-control">
                        <option value="Skill" selected>Skill</option>
                        <option value="Semi-Skill">Semi-Skill</option>
                        <option value="Helper">Helper</option>
                    </select>
                </div>
            </div>
            <div style="border-top: 1px solid var(--border-color); margin: 1rem 0; padding-top: 1rem;">
                <h4 style="margin-bottom: 1rem; font-size: 0.95rem; color: var(--text-secondary);">Data Penggajian</h4>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Gaji Pokok (Harian)</label>
                        <input type="number" class="form-control" value="175000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Uang Makan</label>
                        <input type="number" class="form-control" value="20000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Uang Lembur / Jam</label>
                        <input type="number" class="form-control" value="17500">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" data-modal-close>Batal</button>
            <button class="btn btn-primary" data-modal-close>Simpan Perubahan</button>
        </div>
    </div>
</div>
@endsection
