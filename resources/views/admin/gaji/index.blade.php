@extends('layouts.app')
@section('title', 'Data Penggajian')
@section('page-title', 'Data Penggajian')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>

<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Data Absensi</a>
<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji') }}" class="nav-item active"><i class="fa-solid fa-money-bill-wave"></i> Data Gaji</a>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Periode Penggajian: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM Y') }}</span>
        <div class="panel-actions">
            <button class="btn btn-primary"><i class="fa-solid fa-calculator"></i> Generate Gaji</button>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Nama Karyawan</th>
                    <th>Gaji Pokok</th>
                    <th>Tunjangan</th>
                    <th>Potongan</th>
                    <th>Total Bersih</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:500;">Budi Santoso</td>
                    <td>Rp 5.000.000</td>
                    <td>Rp 1.500.000</td>
                    <td>Rp 100.000</td>
                    <td><strong>Rp 6.400.000</strong></td>
                    <td><span class="badge success">Dibayar</span></td>
                    <td>
                        <a href="{{ route('admin.gaji.slip') }}" class="btn btn-outline" style="padding:0.35rem 0.75rem;"><i class="fa-solid fa-file-pdf"></i> Slip</a>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight:500;">Siti Rahayu</td>
                    <td>Rp 7.000.000</td>
                    <td>Rp 2.000.000</td>
                    <td>Rp 150.000</td>
                    <td><strong>Rp 8.850.000</strong></td>
                    <td><span class="badge warning">Pending</span></td>
                    <td>
                        <a href="{{ route('admin.gaji.slip') }}" class="btn btn-outline" style="padding:0.35rem 0.75rem;"><i class="fa-solid fa-file-pdf"></i> Slip</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
