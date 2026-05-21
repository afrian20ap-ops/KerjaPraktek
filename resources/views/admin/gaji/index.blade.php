@extends('layouts.app')
@section('title', 'Data Penggajian')
@section('page-title', 'Data Penggajian')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>

<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Riwayat Absen</a>
<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item active"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">

    <div class="panel-header">
        <span class="panel-title">Periode Penggajian: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}</span>
        <div class="panel-actions">
            <form action="{{ route('admin.gaji.generate') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-calculator"></i> Generate Gaji</button>
            </form>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Kehadiran</th>
                    <th>Gaji Pokok & Makan</th>
                    <th>Uang Lembur</th>
                    <th>Kasbon</th>
                    <th>Total Bersih</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penggajians as $gaji)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;background:var(--primary-100);color:var(--primary-700);">{{ substr($gaji->user->name, 0, 1) }}</div>
                            <div>
                                <div style="font-weight:600;color:var(--text-primary);">{{ $gaji->user->name }}</div>
                                <div style="font-size:0.8rem;color:var(--text-secondary);">{{ $gaji->user->divisi ?? 'Staff' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $gaji->total_kehadiran_hari }} Hari</td>
                    <td><span style="color:var(--success);">+ Rp {{ number_format($gaji->total_gaji_pokok + $gaji->total_uang_makan, 0, ',', '.') }}</span></td>
                    <td><span style="color:var(--success);">+ Rp {{ number_format($gaji->total_uang_lembur, 0, ',', '.') }}</span></td>
                    <td>
                        <span style="color:var(--danger); cursor:pointer; text-decoration: underline dashed;" onclick="editKasbon({{ $gaji->id }}, {{ $gaji->kasbon }})">
                            - Rp {{ number_format($gaji->kasbon, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        <div style="background:var(--primary-50);color:var(--primary-700);padding:0.4rem 0.75rem;border-radius:var(--border-radius-sm);display:inline-block;font-weight:700;border:1px solid var(--primary-100);">
                            Rp {{ number_format($gaji->total_gaji_bersih, 0, ',', '.') }}
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.gaji.slip') }}?id={{ $gaji->id }}" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.85rem;"><i class="fa-solid fa-file-pdf" style="color:var(--danger);"></i> Slip</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data penggajian. Silakan klik "Generate Gaji" terlebih dahulu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Hidden Form for Updating Kasbon -->
<form id="kasbonForm" action="{{ route('admin.gaji.kasbon') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="penggajian_id" id="kasbon_penggajian_id">
    <input type="hidden" name="kasbon" id="kasbon_value">
</form>

<script>
    function editKasbon(id, oldKasbon) {
        let kasbon = prompt("Masukkan nilai kasbon (contoh: 100000):", oldKasbon);
        if (kasbon !== null && !isNaN(kasbon)) {
            document.getElementById('kasbon_penggajian_id').value = id;
            document.getElementById('kasbon_value').value = kasbon;
            document.getElementById('kasbonForm').submit();
        } else if (kasbon !== null) {
            showToast("Harap masukkan angka yang valid.", "warning");
        }
    }
</script>
@endsection
