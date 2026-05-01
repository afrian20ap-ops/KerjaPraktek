@extends('layouts.app')
@section('title', 'Data Karyawan')
@section('page-title', 'Kelola Data Karyawan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item active"><i class="fa-solid fa-users"></i> Data Karyawan</a>
<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Data Absensi</a>
<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <span class="panel-title"><i class="fa-solid fa-users" style="color:var(--primary-500);margin-right:0.5rem;"></i> Daftar Karyawan & Supervisi</span>
        <button type="button" class="btn btn-primary" onclick="openModal('addModal')" style="padding:0.4rem 1rem;">
            <i class="fa-solid fa-user-plus"></i> Tambah Pegawai
        </button>
    </div>

    @if(session('success'))
    <div style="padding:1rem 1.5rem; background:var(--success); color:white; font-weight:500;">
        <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div style="padding:1rem 1.5rem; background:var(--danger); color:white; font-weight:500;">
        <i class="fa-solid fa-triangle-exclamation"></i> Terjadi kesalahan saat menyimpan data.
        <ul style="margin-top:0.5rem; padding-left:1.5rem; font-size:0.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NAMA & NIK</th>
                    <th>ROLE & DIVISI</th>
                    <th>BASIC / HR</th>
                    <th>MAKAN / HR</th>
                    <th>LEMBUR / JM</th>
                    <th style="text-align:center;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawan as $index => $k)
                <tr>
                    <td style="color:var(--text-muted);">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight:700;color:var(--text-primary);">{{ $k->name }}</div>
                        <div style="font-size:0.8rem;color:var(--text-secondary);font-family:monospace;">NIK: {{ $k->nik ?? '-' }}</div>
                    </td>
                    <td>
                        <div>
                            @if($k->role == 'supervisi')
                                <span class="badge warning" style="font-size:0.7rem;padding:0.2rem 0.5rem;">Supervisi</span>
                            @else
                                <span class="badge success" style="font-size:0.7rem;padding:0.2rem 0.5rem;">Karyawan</span>
                            @endif
                        </div>
                        <div style="font-size:0.8rem;color:var(--text-secondary);margin-top:0.25rem;">{{ $k->divisi ?? '-' }}</div>
                    </td>
                    <td style="font-family:monospace;font-weight:600;">{{ number_format($k->gaji_pokok_harian, 0, ',', '.') }}</td>
                    <td style="font-family:monospace;color:var(--text-secondary);">{{ number_format($k->uang_makan_harian, 0, ',', '.') }}</td>
                    <td style="font-family:monospace;color:var(--text-secondary);">{{ number_format($k->uang_lembur_per_jam, 0, ',', '.') }}</td>
                    <td style="text-align:center;">
                        <button type="button" class="btn btn-outline" style="padding:0.25rem 0.5rem;font-size:0.8rem;margin-right:0.25rem;" onclick="openEditModal({{ $k->id }}, '{{ addslashes($k->name) }}', '{{ $k->email }}', '{{ $k->nik }}', '{{ $k->role }}', '{{ $k->divisi }}', '{{ $k->jabatan }}', {{ $k->gaji_pokok_harian }}, {{ $k->uang_makan_harian }}, {{ $k->uang_lembur_per_jam }})">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                        <form action="{{ route('admin.karyawan.destroy', $k->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus pegawai ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="background:var(--danger);color:white;padding:0.25rem 0.5rem;font-size:0.8rem;border:none;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:3rem 1rem;color:var(--text-muted);">Belum ada data pegawai terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="addModal" class="custom-modal-overlay">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h3><i class="fa-solid fa-user-plus" style="color:var(--primary-500);margin-right:0.5rem;"></i> Tambah Pegawai</h3>
            <button type="button" class="close-btn" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.karyawan.store') }}" method="POST">
            @csrf
            <div class="custom-modal-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email (Untuk Login)</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password Login</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Role / Hak Akses</label>
                    <select name="role" class="form-control" required>
                        <option value="karyawan">Karyawan</option>
                        <option value="supervisi">Supervisi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" name="nik" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Divisi</label>
                    <input type="text" name="divisi" class="form-control">
                </div>
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control">
                </div>
                <div style="grid-column:1 / -1;margin-top:0.5rem;margin-bottom:0.25rem;">
                    <strong style="color:var(--primary-600);font-size:0.9rem;">PENGATURAN GAJI (Rp)</strong>
                    <hr style="border:none;border-top:1px dashed var(--border-color);margin-top:0.5rem;">
                </div>
                <div class="form-group">
                    <label>Gaji Pokok / Hari</label>
                    <input type="number" name="gaji_pokok_harian" class="form-control" value="0" required>
                </div>
                <div class="form-group">
                    <label>Uang Makan / Hari</label>
                    <input type="number" name="uang_makan_harian" class="form-control" value="0" required>
                </div>
                <div class="form-group">
                    <label>Uang Lembur / Jam</label>
                    <input type="number" name="uang_lembur_per_jam" class="form-control" value="0" required>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="editModal" class="custom-modal-overlay">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h3><i class="fa-solid fa-pen" style="color:var(--primary-500);margin-right:0.5rem;"></i> Edit Pegawai</h3>
            <button type="button" class="close-btn" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="custom-modal-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email (Untuk Login)</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password Baru <small style="color:var(--text-muted);">(opsional)</small></label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                </div>
                <div class="form-group">
                    <label>Role / Hak Akses</label>
                    <select name="role" id="edit_role" class="form-control" required>
                        <option value="karyawan">Karyawan</option>
                        <option value="supervisi">Supervisi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>NIK</label>
                    <input type="text" name="nik" id="edit_nik" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Divisi</label>
                    <input type="text" name="divisi" id="edit_divisi" class="form-control">
                </div>
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" id="edit_jabatan" class="form-control">
                </div>
                <div style="grid-column:1 / -1;margin-top:0.5rem;margin-bottom:0.25rem;">
                    <strong style="color:var(--primary-600);font-size:0.9rem;">PENGATURAN GAJI (Rp)</strong>
                    <hr style="border:none;border-top:1px dashed var(--border-color);margin-top:0.5rem;">
                </div>
                <div class="form-group">
                    <label>Gaji Pokok / Hari</label>
                    <input type="number" name="gaji_pokok_harian" id="edit_gaji" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Uang Makan / Hari</label>
                    <input type="number" name="uang_makan_harian" id="edit_makan" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Uang Lembur / Jam</label>
                    <input type="number" name="uang_lembur_per_jam" id="edit_lembur" class="form-control" required>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Data</button>
            </div>
        </form>
    </div>
</div>

<style>
/* CSS Modal (Overlay) */
.custom-modal-overlay { display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.6); z-index:99999; align-items:center; justify-content:center; }
.custom-modal-overlay.show { display:flex !important; }
.custom-modal-content { background:var(--bg-card); width:100%; max-width:650px; margin:auto; border-radius:var(--border-radius-lg); box-shadow:0 25px 50px -12px rgba(0,0,0,0.5); overflow:hidden; position:relative; }
.custom-modal-header { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; }
.custom-modal-header h3 { margin:0; font-size:1.1rem; }
.close-btn { background:none; border:none; font-size:1.25rem; color:var(--text-muted); cursor:pointer; }
.custom-modal-body { padding:1.5rem; max-height:calc(100vh - 160px); overflow-y:auto; }
.custom-modal-footer { padding:1.25rem 1.5rem; border-top:1px solid var(--border-color); background:var(--bg-hover); display:flex; justify-content:flex-end; gap:0.75rem; }

.form-group { margin-bottom:0.5rem; }
.form-group label { display:block; font-size:0.85rem; font-weight:600; color:var(--text-secondary); margin-bottom:0.35rem; }
.form-control { width:100%; padding:0.5rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--border-radius-sm); font-family:inherit; font-size:0.9rem; background:var(--bg-card); color:var(--text-primary); }
</style>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('show');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}
function openEditModal(id, name, email, nik, role, divisi, jabatan, basic, makan, lembur) {
    document.getElementById('editForm').action = '/admin/karyawan/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_nik').value = nik;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_divisi').value = divisi;
    document.getElementById('edit_jabatan').value = jabatan;
    document.getElementById('edit_gaji').value = basic;
    document.getElementById('edit_makan').value = makan;
    document.getElementById('edit_lembur').value = lembur;
    
    openModal('editModal');
}
</script>
@endsection
