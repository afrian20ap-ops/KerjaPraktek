@extends('layouts.app')
@section('title', 'Absensi Tim')
@section('page-title', 'Absensi Tim')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('supervisi.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('supervisi.absensi') }}" class="nav-item active"><i class="fa-solid fa-user-clock"></i> Absensi Karyawan</a>
<a href="{{ route('supervisi.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')

    <div class="panel">
        <div class="panel-header">
            <div class="header-left">
                <span class="panel-title">Input Absensi Harian Tim</span>
                <div class="filter-actions">
                    <input type="date" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:150px;" value="{{ date('Y-m-d') }}">
                    <button class="btn btn-primary" style="padding: 0.35rem 0.75rem;"><i class="fa-solid fa-search"></i> Tampilkan</button>
                </div>
            </div>
            <div class="panel-actions">
                <button class="btn btn-success" style="background: var(--success); color: white; border: none; padding: 0.5rem 1rem;" onclick="showToast('Data absensi berhasil disimpan!')"><i class="fa-solid fa-save"></i> Simpan Semua Absensi</button>
            </div>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama Anggota Tim</th>
                        <th>Basic</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">S</div>
                                <span style="font-weight:500;">Sohib</span>
                            </div>
                        </td>
                        <td>Skill</td>
                        <td style="padding: 0;"><input type="time" value="09:00" class="form-control" style="border:none; border-radius:0; background:transparent;"></td>
                        <td style="padding: 0;"><input type="time" value="17:00" class="form-control" style="border:none; border-radius:0; background:transparent;"></td>
                        <td>
                            <select class="form-control" style="border:none; border-radius:0; background:transparent;">
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">S</div>
                                <span style="font-weight:500;">Syarif</span>
                            </div>
                        </td>
                        <td>Skill</td>
                        <td style="padding: 0;"><input type="time" value="" class="form-control" style="border:none; border-radius:0; background:transparent;"></td>
                        <td style="padding: 0;"><input type="time" value="" class="form-control" style="border:none; border-radius:0; background:transparent;"></td>
                        <td>
                            <select class="form-control" style="border:none; border-radius:0; background:transparent;">
                                <option value="alpa" selected>Alpa</option>
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
