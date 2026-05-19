@extends('layouts.app')
@section('title', 'Laporan Lapangan')
@section('page-title', 'Laporan Lapangan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>
<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Data Absensi</a>
<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item active"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<style>
    .week-filter { display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap; background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:1rem 1.25rem; margin-bottom:1.25rem; }
    .week-filter label { font-weight:600; font-size:0.85rem; color:var(--text-secondary); white-space:nowrap; }
    .week-filter select, .week-filter input { padding:0.4rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--border-radius-sm); background:var(--bg-card); color:var(--text-primary); font-family:inherit; font-size:0.85rem; outline:none; }
    .week-filter select:focus, .week-filter input:focus { border-color:var(--primary-500); }
    .week-badge { background:var(--primary-50); color:var(--primary-700); border:1px solid var(--primary-200); padding:0.3rem 0.9rem; border-radius:99px; font-size:0.82rem; font-weight:600; }

    .stat-row { display:grid; grid-template-columns: repeat(auto-fit, minmax(160px,1fr)); gap:1rem; margin-bottom:1.25rem; }
    .stat-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:1.1rem 1.25rem; }
    .stat-num { font-size:1.8rem; font-weight:800; line-height:1; }
    .stat-lbl { font-size:0.78rem; color:var(--text-secondary); font-weight:500; margin-top:0.25rem; }

    .laporan-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:1.25rem; margin-bottom:1rem; transition: box-shadow 0.15s; }
    .laporan-card:hover { box-shadow: var(--shadow-md); }
    .laporan-meta { display:flex; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:0.85rem; }
    .laporan-body p { font-size:0.88rem; color:var(--text-primary); line-height:1.6; margin-bottom:0.5rem; }
    .laporan-body .section-lbl { font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); margin-bottom:0.25rem; }

    .badge-disetujui { background:color-mix(in srgb,var(--success) 15%,transparent); color:var(--success); border:1px solid var(--success); padding:0.25rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:700; }
    .badge-terkirim  { background:color-mix(in srgb,var(--warning) 15%,transparent); color:#b45309; border:1px solid #f59e0b; padding:0.25rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:700; }
    .badge-draft     { background:color-mix(in srgb,var(--text-muted) 10%,transparent); color:var(--text-muted); border:1px solid var(--border-color); padding:0.25rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:700; }

    .foto-grid-admin { display:grid; grid-template-columns:repeat(3,1fr); gap:0.6rem; margin-top:0.5rem; }
    @media(max-width:700px){ .foto-grid-admin { grid-template-columns:repeat(2,1fr); } }
    .foto-item-admin { border-radius:0.45rem; border:1px solid var(--border-color); overflow:hidden; background:var(--bg-body); }
    .foto-item-admin img { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; cursor:pointer; transition:opacity 0.15s; }
    .foto-item-admin img:hover { opacity:0.85; }
    .foto-item-desc-admin { padding:0.35rem 0.55rem; font-size:0.75rem; color:var(--text-secondary); line-height:1.4; border-top:1px solid var(--border-color); background:var(--bg-card); }
    .foto-item-dl { padding:0.25rem 0.55rem; background:var(--bg-card); border-top:1px solid var(--border-color); }
    .foto-item-dl a { font-size:0.7rem; color:var(--primary-600); text-decoration:none; }
    .foto-item-dl a:hover { text-decoration:underline; }
</style>

{{-- FILTER TANGGAL --}}
<form method="GET" action="{{ route('admin.laporan') }}" class="week-filter" onsubmit="return validateDateRange(this)">
    <label><i class="fa-solid fa-calendar-days" style="color:var(--primary-500);margin-right:0.3rem;"></i>Dari:</label>
    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari', now()->startOfMonth()->format('Y-m-d')) }}" style="width:140px;" required>
    <label>Sampai:</label>
    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai', now()->format('Y-m-d')) }}" style="width:140px;" required>
    <button type="submit" class="btn btn-primary" style="padding:0.4rem 0.85rem;font-size:0.85rem;"><i class="fa-solid fa-search"></i> Tampilkan</button>
    <span class="week-badge" id="dateRange">{{ \Carbon\Carbon::parse(request('tanggal_dari', now()->startOfMonth()->format('Y-m-d')))->locale('id')->isoFormat('D MMM') }} – {{ \Carbon\Carbon::parse(request('tanggal_sampai', now()->format('Y-m-d')))->locale('id')->isoFormat('D MMM Y') }}</span>
</form>
<script>
function validateDateRange(form) {
    const tanggalDari = new Date(form.tanggal_dari.value);
    const tanggalSampai = new Date(form.tanggal_sampai.value);
    const diffDays = Math.ceil(Math.abs(tanggalSampai - tanggalDari) / (1000 * 60 * 60 * 24));
    if (diffDays > 31) { showToast('Rentang tanggal maksimal 31 hari. Anda memilih ' + diffDays + ' hari.', 'warning'); return false; }
    if (tanggalSampai < tanggalDari) { showToast('Tanggal sampai harus lebih besar atau sama dengan tanggal dari.', 'danger'); return false; }
    return true;
}
document.addEventListener('DOMContentLoaded', function() {
    const tanggalDari = document.querySelector('input[name="tanggal_dari"]');
    const tanggalSampai = document.querySelector('input[name="tanggal_sampai"]');
    function updateDateRange() {
        if (tanggalDari.value && tanggalSampai.value) {
            const formatter = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short' });
            const formatterYear = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            document.getElementById('dateRange').textContent = formatter.format(new Date(tanggalDari.value)) + ' – ' + formatterYear.format(new Date(tanggalSampai.value));
        }
    }
    tanggalDari.addEventListener('change', updateDateRange);
    tanggalSampai.addEventListener('change', updateDateRange);
});
</script>

{{-- STATISTIK --}}
@php
    $total     = $laporan->count();
    $disetujui = $laporan->where('status','Disetujui')->count();
    $menunggu  = $laporan->where('status','Terkirim')->count();
@endphp
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-num" style="color:var(--primary-600);">{{ $total }}</div>
        <div class="stat-lbl">Total Laporan</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:var(--success);">{{ $disetujui }}</div>
        <div class="stat-lbl">Disetujui</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#b45309;">{{ $menunggu }}</div>
        <div class="stat-lbl">Menunggu Persetujuan</div>
    </div>
</div>

{{-- DAFTAR LAPORAN --}}
@forelse($laporan as $lp)
<div class="laporan-card">
    <div class="laporan-meta">
        <div class="avatar" style="width:38px;height:38px;font-size:0.9rem;flex-shrink:0;background:var(--primary-100);color:var(--primary-700);">
            {{ strtoupper(substr($lp->user->name,0,1)) }}
        </div>
        <div>
            <div style="font-weight:700;color:var(--text-primary);">{{ $lp->user->name }}</div>
            <div style="font-size:0.78rem;color:var(--text-secondary);">{{ $lp->user->divisi ?? 'Supervisi' }}</div>
        </div>
        <div style="margin-left:auto;display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <span style="font-size:0.82rem;color:var(--text-secondary);">
                <i class="fa-regular fa-calendar"></i> {{ $lp->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </span>
            @if($lp->lokasi)
            <span style="font-size:0.82rem;color:var(--text-secondary);">
                <i class="fa-solid fa-location-dot" style="color:var(--danger);"></i> {{ $lp->lokasi }}
            </span>
            @endif
            @if($lp->status === 'Disetujui')
                <span class="badge-disetujui"><i class="fa-solid fa-check-circle"></i> Disetujui</span>
            @elseif($lp->status === 'Terkirim')
                <span class="badge-terkirim"><i class="fa-solid fa-paper-plane"></i> Menunggu</span>
            @else
                <span class="badge-draft"><i class="fa-solid fa-pen"></i> Draft</span>
            @endif
        </div>
    </div>

    <div class="laporan-body">
        @if(!empty($lp->foto_paths))
        <div class="section-lbl"><i class="fa-solid fa-camera" style="color:var(--primary-500);margin-right:0.3rem;"></i>Dokumentasi Foto ({{ count($lp->foto_paths) }})</div>
        <div class="foto-grid-admin">
            @foreach($lp->foto_paths as $idx => $fpath)
            <div class="foto-item-admin">
                <img src="{{ $fpath }}" alt="Foto {{ $idx+1 }}" onclick="openLightboxAdmin('{{ $fpath }}')" />
                <div class="foto-item-desc-admin">
                    @if(!empty($lp->foto_deskripsis[$idx]))
                        <i class="fa-solid fa-circle-info" style="color:var(--primary-500);margin-right:0.2rem;font-size:0.68rem;"></i>
                        {{ $lp->foto_deskripsis[$idx] }}
                    @else
                        <span style="color:var(--text-muted);font-style:italic;">Foto {{ $idx+1 }}</span>
                    @endif
                </div>
                <div class="foto-item-dl">
                    <a href="{{ str_replace('/upload/', '/upload/fl_attachment/', $fpath) }}">
                        <i class="fa-solid fa-download"></i> Download
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- TOMBOL AKSI --}}
    <div style="margin-top:0.85rem;padding-top:0.85rem;border-top:1px dashed var(--border-color);display:flex;justify-content:flex-end;align-items:center;gap:0.6rem;">
        <a href="{{ route('admin.laporan.download', $lp->id) }}"
           class="btn btn-outline"
           style="padding:0.35rem 0.9rem;font-size:0.82rem;border-color:#16a34a;color:#16a34a;display:inline-flex;align-items:center;gap:0.4rem;">
            <i class="fa-solid fa-file-excel"></i> Unduh Laporan
        </a>
        @if($lp->status !== 'Disetujui')
        <form action="{{ route('admin.laporan.approve', $lp->id) }}" method="POST" style="margin:0;">
            @csrf
            <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.85rem;font-size:0.82rem;">
                <i class="fa-solid fa-check"></i> Setujui Laporan
            </button>
        </form>
        @endif
    </div>
</div>
@empty
<div style="text-align:center;padding:4rem 2rem;background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--border-radius);color:var(--text-muted);">
    <i class="fa-solid fa-folder-open" style="font-size:2.5rem;margin-bottom:1rem;display:block;color:var(--border-color);"></i>
    <p style="font-size:1rem;margin-bottom:0.25rem;">Tidak ada laporan untuk periode ini.</p>
    <p style="font-size:0.85rem;">Laporan akan muncul setelah supervisi mengirimkan laporan lapangan.</p>
</div>
@endforelse

{{-- Lightbox --}}
<div id="lightboxAdminOverlay" onclick="closeLightboxAdmin()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;align-items:center;justify-content:center;">
    <img id="lightboxAdminImg" src="" alt="Preview" style="max-width:90vw;max-height:90vh;border-radius:0.5rem;box-shadow:0 8px 40px rgba(0,0,0,0.5);">
    <button onclick="closeLightboxAdmin()" style="position:absolute;top:1rem;right:1.25rem;background:rgba(255,255,255,0.15);border:none;color:white;font-size:1.5rem;cursor:pointer;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;">&times;</button>
</div>
<style>#lightboxAdminOverlay.active { display:flex !important; }</style>
<script>
function openLightboxAdmin(src) {
    document.getElementById('lightboxAdminImg').src = src;
    document.getElementById('lightboxAdminOverlay').classList.add('active');
}
function closeLightboxAdmin() {
    document.getElementById('lightboxAdminOverlay').classList.remove('active');
}
</script>
@endsection