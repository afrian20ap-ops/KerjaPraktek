@extends('layouts.app')
@section('title', 'Laporan Lapangan')
@section('page-title', 'Laporan Lapangan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('supervisi.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('supervisi.absensi') }}" class="nav-item"><i class="fa-solid fa-user-clock"></i> Absensi Karyawan</a>
<a href="{{ route('supervisi.laporan') }}" class="nav-item active"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
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
</style>

{{-- FILTER MINGGU --}}
<form method="GET" action="{{ route('supervisi.laporan') }}" class="week-filter">
    <label><i class="fa-solid fa-calendar-week" style="color:var(--primary-500);margin-right:0.3rem;"></i>Minggu ke-:</label>
    <input type="number" name="minggu" value="{{ $minggu }}" min="1" max="53" style="width:70px;text-align:center;">
    <label>Tahun:</label>
    <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2099" style="width:90px;text-align:center;">
    <button type="submit" class="btn btn-primary" style="padding:0.4rem 0.85rem;font-size:0.85rem;"><i class="fa-solid fa-search"></i> Tampilkan</button>
    <span class="week-badge">{{ $mulai->locale('id')->isoFormat('D MMM') }} – {{ $akhir->locale('id')->isoFormat('D MMM Y') }}</span>
    
    @if($mingguList->count())
    <div style="margin-left:auto;display:flex;align-items:center;gap:0.5rem;">
        <label>Riwayat:</label>
        <select onchange="window.location=this.value" style="min-width:180px;">
            <option value="">-- Pilih Minggu --</option>
            @foreach($mingguList as $m)
            <option value="{{ route('supervisi.laporan', ['minggu'=>$m->minggu,'tahun'=>$m->tahun]) }}"
                    {{ $m->minggu == $minggu && $m->tahun == $tahun ? 'selected' : '' }}>
                Minggu {{ $m->minggu }} / {{ $m->tahun }}
            </option>
            @endforeach
        </select>
    </div>
    @endif
</form>

{{-- STATISTIK --}}
@php
    $total     = $laporan->count();
    $disetujui = $laporan->where('status','Disetujui')->count();
    $menunggu  = $laporan->where('status','Terkirim')->count();
@endphp
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-num" style="color:var(--primary-600);">{{ $total }}</div>
        <div class="stat-lbl">Total Laporan Tim</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:var(--success);">{{ $disetujui }}</div>
        <div class="stat-lbl">Telah Disetujui</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color:#b45309;">{{ $menunggu }}</div>
        <div class="stat-lbl">Menunggu Persetujuan</div>
    </div>
</div>

@if(session('success'))
<div style="padding:0.85rem 1.25rem;background:var(--success);color:white;border-radius:var(--border-radius);margin-bottom:1rem;font-weight:500;">
    <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- DAFTAR LAPORAN --}}
@forelse($laporan as $lp)
<div class="laporan-card">
    <div class="laporan-meta">
        <div class="avatar" style="width:38px;height:38px;font-size:0.9rem;flex-shrink:0;background:var(--primary-100);color:var(--primary-700);">
            {{ strtoupper(substr($lp->user->name,0,1)) }}
        </div>
        <div>
            <div style="font-weight:700;color:var(--text-primary);">{{ $lp->user->name }}</div>
            <div style="font-size:0.78rem;color:var(--text-secondary);">{{ $lp->user->divisi ?? 'Karyawan' }}</div>
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
        <div class="section-lbl">Deskripsi Pekerjaan</div>
        <p>{{ $lp->deskripsi_pekerjaan }}</p>

        @if($lp->kendala)
        <div class="section-lbl">Kendala</div>
        <p>{{ $lp->kendala }}</p>
        @endif

        @if($lp->solusi)
        <div class="section-lbl">Solusi</div>
        <p>{{ $lp->solusi }}</p>
        @endif

        @if($lp->foto_path)
        <div style="margin-top:0.75rem;">
            <a href="{{ asset('storage/' . $lp->foto_path) }}" download="Foto_Laporan_{{ \Carbon\Carbon::parse($lp->tanggal)->format('Ymd') }}_{{ $lp->user->name }}.jpg" class="btn btn-outline" style="padding:0.3rem 0.75rem;font-size:0.82rem;color:var(--primary-600);border-color:var(--primary-500);">
                <i class="fa-solid fa-download"></i> Download Foto Lampiran
            </a>
        </div>
        @endif
    </div>

    @if($lp->status !== 'Disetujui')
    <div style="margin-top:0.85rem;padding-top:0.85rem;border-top:1px dashed var(--border-color);display:flex;justify-content:flex-end;">
        <form action="{{ route('supervisi.laporan.approve', $lp->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.85rem;font-size:0.82rem;">
                <i class="fa-solid fa-check"></i> Setujui Laporan
            </button>
        </form>
    </div>
    @endif
</div>
@empty
<div style="text-align:center;padding:4rem 2rem;background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--border-radius);color:var(--text-muted);">
    <i class="fa-solid fa-folder-open" style="font-size:2.5rem;margin-bottom:1rem;display:block;color:var(--border-color);"></i>
    <p style="font-size:1rem;margin-bottom:0.25rem;">Tidak ada laporan dari tim minggu ini.</p>
    <p style="font-size:0.85rem;">Laporan akan muncul setelah karyawan mengirimkan laporan lapangan.</p>
</div>
@endforelse
@endsection
