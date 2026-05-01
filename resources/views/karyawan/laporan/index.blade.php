@extends('layouts.app')
@section('title', 'Laporan Lapangan')
@section('page-title', 'Laporan Lapangan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('karyawan.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<span class="nav-label" style="margin-top:1rem;">Personal</span>
<a href="{{ route('karyawan.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Riwayat Absensi</a>
<a href="{{ route('karyawan.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('karyawan.laporan') }}" class="nav-item active"><i class="fa-solid fa-camera"></i> Laporan Lapangan</a>
@endsection

@section('content')
<style>
    .week-filter { display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap; background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:1rem 1.25rem; margin-bottom:1.25rem; }
    .week-filter label { font-weight:600; font-size:0.85rem; color:var(--text-secondary); white-space:nowrap; }
    .week-filter input, .week-filter select { padding:0.4rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--border-radius-sm); background:var(--bg-card); color:var(--text-primary); font-family:inherit; font-size:0.85rem; outline:none; }
    .week-filter input:focus, .week-filter select:focus { border-color:var(--primary-500); }
    .week-badge { background:var(--primary-50); color:var(--primary-700); border:1px solid var(--primary-200); padding:0.3rem 0.9rem; border-radius:99px; font-size:0.82rem; font-weight:600; }

    .laporan-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:1.25rem; margin-bottom:1rem; transition:box-shadow 0.15s; }
    .laporan-card:hover { box-shadow:var(--shadow-md); }
    .section-lbl { font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); margin-bottom:0.25rem; margin-top:0.75rem; }
    
    .badge-disetujui { background:color-mix(in srgb,var(--success) 15%,transparent); color:var(--success); border:1px solid var(--success); padding:0.25rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:700; }
    .badge-terkirim  { background:color-mix(in srgb,var(--warning) 15%,transparent); color:#b45309; border:1px solid #f59e0b; padding:0.25rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:700; }

    /* Form kirim laporan */
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-weight:600; font-size:0.85rem; color:var(--text-secondary); margin-bottom:0.35rem; }
    .form-group input, .form-group textarea, .form-group select {
        width:100%; padding:0.5rem 0.75rem; border:1px solid var(--border-color);
        border-radius:var(--border-radius-sm); background:var(--bg-card); color:var(--text-primary);
        font-family:inherit; font-size:0.88rem; outline:none; transition:border 0.15s;
    }
    .form-group input:focus, .form-group textarea:focus { border-color:var(--primary-500); box-shadow:0 0 0 2px color-mix(in srgb,var(--primary-500) 20%,transparent); }
    .form-group textarea { resize:vertical; min-height:80px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media (max-width:600px) { .form-row { grid-template-columns:1fr; } }
</style>

{{-- FILTER MINGGU --}}
<form method="GET" action="{{ route('karyawan.laporan') }}" class="week-filter">
    <label><i class="fa-solid fa-calendar-week" style="color:var(--primary-500);margin-right:0.3rem;"></i>Minggu ke-:</label>
    <input type="number" name="minggu" value="{{ $minggu }}" min="1" max="53" style="width:70px;text-align:center;">
    <label>Tahun:</label>
    <input type="number" name="tahun" value="{{ $tahun }}" min="2020" max="2099" style="width:90px;text-align:center;">
    <button type="submit" class="btn btn-primary" style="padding:0.4rem 0.85rem;font-size:0.85rem;"><i class="fa-solid fa-search"></i> Tampilkan</button>
    <span class="week-badge">{{ $mulai->locale('id')->isoFormat('D MMM') }} – {{ $akhir->locale('id')->isoFormat('D MMM Y') }}</span>

    @if($mingguList->count())
    <div style="margin-left:auto;display:flex;align-items:center;gap:0.5rem;">
        <select onchange="window.location=this.value" style="min-width:180px;">
            <option value="">-- Riwayat Minggu --</option>
            @foreach($mingguList as $m)
            <option value="{{ route('karyawan.laporan', ['minggu'=>$m->minggu,'tahun'=>$m->tahun]) }}"
                    {{ $m->minggu == $minggu && $m->tahun == $tahun ? 'selected' : '' }}>
                Minggu {{ $m->minggu }} / {{ $m->tahun }}
            </option>
            @endforeach
        </select>
    </div>
    @endif
</form>

<div style="display:grid;grid-template-columns:1fr 380px;gap:1.25rem;align-items:start;" class="laporan-layout">

    {{-- KIRI: Daftar Laporan Minggu Ini --}}
    <div>
        <div style="font-weight:700;font-size:1rem;margin-bottom:1rem;color:var(--text-primary);">
            <i class="fa-solid fa-list-check" style="color:var(--primary-500);margin-right:0.4rem;"></i>
            Laporan Anda — Minggu {{ $minggu }} / {{ $tahun }}
            <span style="font-size:0.82rem;color:var(--text-muted);font-weight:500;">({{ $laporan->count() }} laporan)</span>
        </div>

        @forelse($laporan as $lp)
        <div class="laporan-card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.75rem;">
                <div style="font-size:0.82rem;color:var(--text-secondary);">
                    <span style="font-weight:600;color:var(--text-primary);">{{ $lp->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    @if($lp->lokasi) <br><i class="fa-solid fa-location-dot" style="color:var(--danger);"></i> {{ $lp->lokasi }} @endif
                </div>
                @if($lp->status === 'Disetujui')
                    <span class="badge-disetujui"><i class="fa-solid fa-check-circle"></i> Disetujui</span>
                @else
                    <span class="badge-terkirim"><i class="fa-solid fa-paper-plane"></i> Menunggu</span>
                @endif
            </div>

            <div class="section-lbl">Deskripsi Pekerjaan</div>
            <p style="font-size:0.88rem;color:var(--text-primary);line-height:1.6;margin-bottom:0;">{{ $lp->deskripsi_pekerjaan }}</p>

            @if($lp->kendala)
            <div class="section-lbl">Kendala</div>
            <p style="font-size:0.88rem;color:var(--text-primary);line-height:1.6;margin-bottom:0;">{{ $lp->kendala }}</p>
            @endif

            @if($lp->solusi)
            <div class="section-lbl">Solusi</div>
            <p style="font-size:0.88rem;color:var(--text-primary);line-height:1.6;margin-bottom:0;">{{ $lp->solusi }}</p>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:3rem 2rem;background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--border-radius);color:var(--text-muted);">
            <i class="fa-solid fa-folder-open" style="font-size:2rem;margin-bottom:0.75rem;display:block;color:var(--border-color);"></i>
            <p style="margin:0;font-size:0.9rem;">Belum ada laporan dari Anda untuk minggu ini.</p>
        </div>
        @endforelse
    </div>

    {{-- KANAN: Form Kirim Laporan --}}
    <div class="panel" style="position:sticky;top:1rem;">
        <div class="panel-header">
            <span class="panel-title" style="font-size:0.95rem;"><i class="fa-solid fa-paper-plane" style="color:var(--primary-500);margin-right:0.35rem;"></i>Kirim Laporan Baru</span>
        </div>

        @if(session('success'))
        <div style="padding:0.75rem 1rem;background:var(--success);color:white;font-size:0.85rem;border-bottom:1px solid var(--border-color);">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('karyawan.laporan.store') }}" method="POST" enctype="multipart/form-data" style="padding:1.25rem;">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-regular fa-calendar"></i> Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-location-dot"></i> Lokasi</label>
                    <input type="text" name="lokasi" placeholder="cth: Gedung A lantai 3">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-clipboard"></i> Deskripsi Pekerjaan <span style="color:var(--danger);">*</span></label>
                <textarea name="deskripsi_pekerjaan" placeholder="Jelaskan pekerjaan yang dilakukan hari ini..." required></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-triangle-exclamation"></i> Kendala (opsional)</label>
                <textarea name="kendala" placeholder="Kendala yang ditemui..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-lightbulb"></i> Solusi (opsional)</label>
                <textarea name="solusi" placeholder="Solusi yang diterapkan..."></textarea>
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-camera"></i> Foto (opsional)</label>
                <input type="file" name="foto" accept="image/*" style="padding:0.3rem 0.5rem;font-size:0.82rem;">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:0.6rem;font-size:0.9rem;">
                <i class="fa-solid fa-paper-plane"></i> Kirim Laporan
            </button>
        </form>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .laporan-layout { grid-template-columns: 1fr !important; }
}
</style>
@endsection
