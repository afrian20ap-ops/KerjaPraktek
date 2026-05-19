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
        <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Selamat datang, Administrator!</h2>
        <p style="opacity:0.85;font-size:0.95rem;">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="grid-cards">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalKaryawan ?? 0 }}</div>
            <div class="stat-label">Total Karyawan</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <span style="font-size:0.75rem;color:var(--success);font-weight:600;"><i class="fa-solid fa-arrow-trend-up"></i> +{{ $karyawanBaru ?? 0 }} bulan ini</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--success) 15%,transparent),color-mix(in srgb,var(--success) 25%,transparent));color:var(--success);">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $hadirKemarin ?? 0 }}</div>
            <div class="stat-label">Hadir Kemarin</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <span style="font-size:0.75rem;color:var(--success);font-weight:600;">{{ $persentaseHadir ?? 0 }}%</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--primary-500) 15%,transparent),color-mix(in srgb,var(--primary-500) 25%,transparent));color:var(--primary-500);">
            <i class="fa-solid fa-clipboard-list"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalLaporanBulanIni ?? 0 }}</div>
            <div class="stat-label">Laporan Bulan Ini</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <span style="font-size:0.75rem;color:var(--warning);font-weight:600;"><i class="fa-solid fa-clock"></i> {{ $laporanMenunggu ?? 0 }} Menunggu</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--info) 15%,transparent),color-mix(in srgb,var(--info) 25%,transparent));color:var(--info);">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">Rp {{ number_format($totalGaji ?? 0, 0, ',', '.') }}</div>
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
            <span class="panel-title"><i class="fa-solid fa-chart-pie" style="color:var(--primary-500);margin-right:0.5rem;"></i>Status Kehadiran Kemarin</span>
        </div>
        <div style="padding:1.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;justify-content:center;">
            <div style="position:relative;width:100%;max-width:180px;aspect-ratio:1/1;flex-shrink:0;">
                <canvas id="statusChart"></canvas>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;gap:0.75rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;"><span style="width:12px;height:12px;border-radius:3px;background:var(--success);display:inline-block;"></span>Hadir</span>
                    <strong>{{ $hadirKemarin ?? 0 }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.875rem;"><span style="width:12px;height:12px;border-radius:3px;background:var(--danger);display:inline-block;"></span>Tidak Hadir</span>
                    <strong>{{ $tidakHadirKemarin ?? 0 }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Attendance Table -->
<div class="panel">
    <div class="panel-header">
        <span class="panel-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--primary-500);margin-right:0.5rem;"></i>Absensi Kemarin</span>
        <div class="panel-actions">
            <a href="{{ route('admin.absensi') }}" class="btn btn-outline" style="font-size:0.82rem;">Lihat Semua</a>
        </div>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Jam Lembur</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensiTerkini ?? [] as $abs)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">{{ strtoupper(substr($abs->user->name ?? 'U', 0, 1)) }}</div>
                            <span style="font-weight:500;">{{ $abs->user->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td>{{ $abs->jam_masuk ? \Carbon\Carbon::parse($abs->jam_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $abs->jam_keluar ? \Carbon\Carbon::parse($abs->jam_keluar)->format('H:i') : '-' }}</td>
                    <td style="font-weight:600; color:{{ $abs->jam_lembur > 0 ? 'var(--warning)' : 'var(--text-muted)' }};">
                        {{ $abs->jam_lembur > 0 ? '+' . $abs->jam_lembur . ' jam' : '-' }}
                    </td>
                    <td>
                        @if($abs->status == 'Hadir') <span class="badge success">Hadir</span>
                        @else <span class="badge danger">Tidak Hadir</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">Belum ada data absensi kemarin</td>
                </tr>
                @endforelse
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
                    <th>Lokasi</th>
                    <th>Lampiran</th>
                    <th>Waktu Submit</th>
                    <th>Status Approval</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporanTerkini ?? [] as $laporan)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">{{ strtoupper(substr($laporan->user->name ?? 'U', 0, 1)) }}</div>
                            <span style="font-weight:500;">{{ $laporan->user->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td style="color:var(--text-secondary);">{{ \Illuminate\Support\Str::limit($laporan->lokasi, 40) }}</td>
                    <td><i class="fa-solid fa-image" style="color:var(--text-muted);"></i> {{ count($laporan->foto_paths ?? []) }} Foto</td>
                    <td>{{ $laporan->created_at ? \Carbon\Carbon::parse($laporan->created_at)->diffForHumans() : 'Baru saja' }}</td>
                    <td>
                        @if($laporan->status == 'Disetujui') <span class="badge success">Disetujui</span>
                        @elseif($laporan->status == 'Ditolak') <span class="badge danger">Ditolak</span>
                        @elseif($laporan->status == 'Terkirim') <span class="badge warning">Menunggu</span>
                        @else <span style="font-size:0.75rem;padding:0.25rem 0.5rem;background:#e2e8f0;border-radius:99px;">Draft</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">Belum ada laporan lapangan hari ini</td>
                </tr>
                @endforelse
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
            data: {{ json_encode($chartHadir ?? [0,0,0,0,0,0,0,0,0,0,0,0]) }},
            borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)',
            tension: 0.4, fill: true, pointBackgroundColor: '#10b981', pointRadius: 4
        },{
            label: 'Tidak Hadir',
            data: {{ json_encode($chartTidakHadir ?? [0,0,0,0,0,0,0,0,0,0,0,0]) }},
            borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.05)',
            tension: 0.4, fill: true, pointBackgroundColor: '#ef4444', pointRadius: 4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { labels: { color: textColor, font: { family: 'Outfit' } } } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: textColor, font: { family: 'Outfit' } } },
            y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: { family: 'Outfit' } } }
        }
    }
});

// Doughnut Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Hadir','Tidak Hadir'],
        datasets: [{ data: [{{ $hadirKemarin ?? 0 }}, {{ $tidakHadirKemarin ?? 0 }}], backgroundColor: ['#10b981','#ef4444'], borderWidth: 0, hoverOffset: 6 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '70%',
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
