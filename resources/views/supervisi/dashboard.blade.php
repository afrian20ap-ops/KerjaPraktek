@extends('layouts.app')
@section('title', 'Dashboard Supervisi')
@section('page-title', 'Dashboard Supervisi')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('supervisi.dashboard') }}" class="nav-item active"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('supervisi.absensi') }}" class="nav-item"><i class="fa-solid fa-user-clock"></i> Absensi Karyawan</a>
<a href="{{ route('supervisi.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="welcome-banner" style="background:linear-gradient(135deg,var(--primary-600) 0%,var(--primary-500) 60%,var(--primary-400) 100%);border-radius:var(--border-radius-lg);padding:2rem 2.5rem;margin-bottom:2rem;color:white;position:relative;overflow:hidden;">
    <div style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.07);"></div>
    <div style="position:relative;z-index:1;">
        <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:0.25rem;">Halo, Supervisi! 👋</h2>
        <p style="opacity:0.85;font-size:0.95rem;">Pantau kehadiran tim dan laporan operasional lapangan hari ini.</p>
    </div>
</div>

<div class="grid-cards">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $tim }}</div>
            <div class="stat-label">Anggota Tim</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--success) 15%,transparent),color-mix(in srgb,var(--success) 25%,transparent));color:var(--success);">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $hadirHariIni }}</div>
            <div class="stat-label">Hadir Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,color-mix(in srgb,var(--warning) 15%,transparent),color-mix(in srgb,var(--warning) 25%,transparent));color:var(--warning);">
            <i class="fa-solid fa-clipboard-question"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $laporanTertunda }}</div>
            <div class="stat-label">Laporan Tertunda</div>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-top: 2rem; align-items: start;">
    <!-- Grafik Kehadiran Bulanan -->
    <div class="panel" style="margin-top: 0;">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-chart-line" style="color:var(--primary-500);margin-right:0.5rem;"></i>Grafik Kehadiran Bulanan</span>
            <div class="panel-actions">
                <select class="form-control" style="padding:0.25rem 0.5rem;font-size:0.8rem;border-radius:4px;border:1px solid var(--border-color);background:var(--bg-card);color:var(--text-primary);">
                    <option>{{ now()->year }}</option>
                    <option>{{ now()->year - 1 }}</option>
                </select>
            </div>
        </div>
        <div style="padding:1.5rem;">
            <canvas id="attendanceChart" height="220"></canvas>
        </div>
    </div>

    <!-- Laporan Terkini -->
    <div class="panel" style="margin-top: 0;">
        <div class="panel-header">
            <span class="panel-title"><i class="fa-solid fa-clipboard-list" style="color:var(--primary-500);margin-right:0.5rem;"></i> Laporan Lapangan Terkini</span>
            <div class="panel-actions">
                <a href="{{ route('supervisi.laporan') }}" class="btn btn-outline" style="font-size:0.82rem;">Lihat Semua</a>
            </div>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Lokasi</th>
                        <th>Waktu Submit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporanTerkini as $lp)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="avatar" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">
                                    {{ strtoupper(substr($lp->user->name, 0, 1)) }}
                                </div>
                                <span style="font-weight:500;">{{ $lp->user->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary);">
                            {{ \Illuminate\Support\Str::limit($lp->lokasi, 40) }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($lp->created_at)->format('H:i') }}</td>
                        <td>
                            @if($lp->status == 'Disetujui')
                                <span class="badge success">Disetujui</span>
                            @elseif($lp->status == 'Terkirim')
                                <span class="badge warning">Menunggu Review</span>
                            @else
                                <span class="badge" style="background:var(--bg-hover);color:var(--text-secondary);border:1px solid var(--border-color);">Draft</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted);">
                            Belum ada laporan terkini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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

// Doughnut Chart statusChart has been removed
</script>
@endpush
