@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item active" id="nav-dashboard">
    <i class="fa-solid fa-house"></i> Dashboard
</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item" id="nav-karyawan">
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
<!-- Welcome Banner -->
<div class="welcome-banner" style="background:linear-gradient(135deg,var(--primary-600) 0%,var(--primary-500) 60%,var(--primary-400) 100%);border-radius:var(--border-radius-lg);padding:2rem 2.5rem;margin-bottom:2rem;color:white;position:relative;overflow:hidden;">
    <div style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.07);"></div>
    <div style="position:absolute;right:60px;bottom:-60px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.05);"></div>
    <div style="position:relative;z-index:1;">
        <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Selamat datang, Administrator! 👋</h2>
        <p style="opacity:0.85;font-size:0.95rem;">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid-cards">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">128</div>
            <div class="stat-label">Total Karyawan</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <span style="font-size:0.75rem;color:var(--success);font-weight:600;"><i class="fa-solid fa-arrow-trend-up"></i> +4 bulan ini</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--success) 15%,transparent),color-mix(in srgb,var(--success) 25%,transparent));color:var(--success);">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">115</div>
            <div class="stat-label">Hadir Hari Ini</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <span style="font-size:0.75rem;color:var(--success);font-weight:600;">89.8%</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--warning) 15%,transparent),color-mix(in srgb,var(--warning) 25%,transparent));color:var(--warning);">
            <i class="fa-solid fa-user-clock"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">8</div>
            <div class="stat-label">Terlambat Hari Ini</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <span style="font-size:0.75rem;color:var(--warning);font-weight:600;">6.2%</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--info) 15%,transparent),color-mix(in srgb,var(--info) 25%,transparent));color:var(--info);">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">Rp 245 Jt</div>
            <div class="stat-label">Total Gaji Bulan Ini</div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-bottom:2rem;">
    <!-- Chart Kehadiran -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-chart-line" style="color:var(--primary-500);margin-right:0.5rem;"></i>Grafik Kehadiran Bulanan</span>
            <div class="panel-actions">
                <select class="form-control" style="padding:0.35rem 0.75rem;font-size:0.8rem;width:auto;" aria-label="Pilih Tahun">
                    <option>2026</option>
                    <option>2025</option>
                </select>
            </div>
        </div>
        <div style="padding:1.5rem;">
            <canvas id="attendanceChart" height="220"></canvas>
        </div>
    </div>

    <!-- Chart Status Absensi -->
    <div class="panel">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-chart-pie" style="color:var(--primary-500);margin-right:0.5rem;"></i>Status Kehadiran Hari Ini</span>
        </div>
        <div style="padding:1.5rem;display:flex;align-items:center;gap:2rem;">
            <canvas id="statusChart" width="200" height="200" style="max-width:180px;"></canvas>
            <div style="flex:1;display:flex;flex-direction:column;gap:0.75rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;"><span style="width:12px;height:12px;border-radius:3px;background:var(--success);display:inline-block;"></span>Hadir</span>
                    <strong>115</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;"><span style="width:12px;height:12px;border-radius:3px;background:var(--warning);display:inline-block;"></span>Terlambat</span>
                    <strong>8</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;"><span style="width:12px;height:12px;border-radius:3px;background:var(--danger);display:inline-block;"></span>Tidak Hadir</span>
                    <strong>5</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Attendance Table -->
<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--primary-500);margin-right:0.5rem;"></i>Absensi Terkini</span>
        <div class="panel-actions">
            <a href="{{ route('admin.absensi') }}" class="btn btn-outline" style="font-size:0.82rem;">Lihat Semua</a>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Jabatan</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                $rows = [
                    ['Budi Santoso','Teknisi','07:55','17:02','Hadir'],
                    ['Siti Rahayu','Admin','08:01','17:00','Hadir'],
                    ['Ahmad Fauzi','Supervisor','09:15','17:30','Terlambat'],
                    ['Dewi Anggraini','Marketing','07:58','17:05','Hadir'],
                    ['Rizky Pratama','HRD','08:00','-','Hadir'],
                    ['Eko Wahyudi','Keuangan','-','-','Tidak Hadir'],
                ];
                @endphp
                @foreach($rows as $r)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">{{ strtoupper(substr($r[0],0,1)) }}</div>
                            <span style="font-weight:500;">{{ $r[0] }}</span>
                        </div>
                    </td>
                    <td style="color:var(--text-secondary);">{{ $r[1] }}</td>
                    <td>{{ $r[2] }}</td>
                    <td>{{ $r[3] }}</td>
                    <td>
                        @if($r[4]==='Hadir') <span class="badge success">Hadir</span>
                        @elseif($r[4]==='Terlambat') <span class="badge warning">Terlambat</span>
                        @else <span class="badge danger">Tidak Hadir</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
<!-- Recent Field Reports -->
<div class="panel" style="margin-top: 2rem;">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-clipboard-list" style="color:var(--primary-500);margin-right:0.5rem;"></i> Ringkasan Laporan Lapangan</span>
        <div class="panel-actions">
            <a href="{{ route('admin.laporan') }}" class="btn btn-outline" style="font-size:0.82rem;">Lihat Detail</a>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Proyek / Tugas</th>
                    <th>Lampiran</th>
                    <th>Waktu Submit</th>
                    <th>Status Approval</th>
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
                    <td><i class="fa-solid fa-image" style="color:var(--text-muted);"></i> 2 Foto</td>
                    <td>Hari ini, 10:30 AM</td>
                    <td><span class="badge success">Disetujui Supervisi</span></td>
                </tr>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">D</div>
                            <span style="font-weight:500;">Deni Pratama</span>
                        </div>
                    </td>
                    <td style="color:var(--text-secondary);">Perbaikan Jaringan Area B</td>
                    <td><i class="fa-solid fa-image" style="color:var(--text-muted);"></i> 1 Foto</td>
                    <td>Kemarin, 15:45 PM</td>
                    <td><span class="badge success">Disetujui Supervisi</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
const gridColor = isDark ? 'rgba(255,255,255,0.07)' : 'rgba(0,0,0,0.07)';
const textColor = isDark ? '#94a3b8' : '#475569';

// Line Chart
new Chart(document.getElementById('attendanceChart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'],
        datasets: [{
            label: 'Hadir',
            data: [118,115,121,114,119,112,125,120,116,122,115,128],
            borderColor: '#f43f5e', backgroundColor: 'rgba(244,63,94,0.1)',
            tension: 0.4, fill: true, pointBackgroundColor: '#f43f5e', pointRadius: 4
        },{
            label: 'Tidak Hadir',
            data: [10,13,7,14,9,16,3,8,12,6,13,0],
            borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.05)',
            tension: 0.4, fill: true, pointBackgroundColor: '#3b82f6', pointRadius: 4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { color: textColor, font: { family: 'Outfit' } } } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: textColor, font: { family: 'Outfit' } } },
            y: { grid: { color: gridColor }, ticks: { color: textColor, font: { family: 'Outfit' } } }
        }
    }
});

// Doughnut Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Hadir','Terlambat','Tidak Hadir'],
        datasets: [{ data: [115, 8, 5], backgroundColor: ['#10b981','#f59e0b','#ef4444'], borderWidth: 0, hoverOffset: 6 }]
    },
    options: {
        responsive: true, cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
