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
<div class="panel">
    <div class="panel-header">
        <span class="panel-title">Laporan Pekerjaan Tim</span>
        <div class="panel-actions">
            <input type="date" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem;" value="{{ date('Y-m-d') }}">
        </div>
    </div>
    <div style="padding:1.5rem;display:grid;grid-template-columns:1fr;gap:1.5rem;">
        <div style="border:1px solid var(--border-color);border-radius:var(--border-radius);padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;margin-bottom:1rem;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:0.75rem;">
                    <div class="avatar" style="width:40px;height:40px;">A</div>
                    <div>
                        <div style="font-weight:600;">Andi Wirawan</div>
                        <div style="font-size:0.8rem;color:var(--text-muted);">Proyek Instalasi Tower - 10:30 AM</div>
                    </div>
                </div>
                <span class="badge warning" id="badge-status" style="height:fit-content;">Menunggu Review</span>
            </div>
            <p style="color:var(--text-secondary);font-size:0.95rem;margin-bottom:1rem;">Pekerjaan pemasangan perangkat pada site A telah selesai dilakukan sesuai standar.</p>
            <div style="display:flex;gap:0.5rem;">
                <div style="width:80px;height:60px;background:var(--bg-hover);border-radius:var(--border-radius-sm);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"><i class="fa-solid fa-image"></i></div>
                <div style="width:80px;height:60px;background:var(--bg-hover);border-radius:var(--border-radius-sm);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"><i class="fa-solid fa-image"></i></div>
            </div>
            <div style="margin-top:1rem;border-top:1px solid var(--border-color);padding-top:1rem;display:flex;gap:0.5rem;">
                <button class="btn btn-outline" style="font-size:0.8rem;color:var(--success);border-color:color-mix(in srgb,var(--success) 30%,transparent);" onclick="updateStatus('terima')"><i class="fa-solid fa-check"></i> Terima</button>
                <button class="btn btn-outline" style="font-size:0.8rem;color:var(--danger);border-color:color-mix(in srgb,var(--danger) 30%,transparent);" onclick="updateStatus('revisi')"><i class="fa-solid fa-rotate-left"></i> Revisi</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus(action) {
    const badge = document.getElementById('badge-status');
    if (action === 'terima') {
        badge.className = 'badge success';
        badge.textContent = 'Disetujui';
        showToast('Laporan berhasil diterima dan diteruskan ke Admin.', 'success');
    } else if (action === 'revisi') {
        badge.className = 'badge danger';
        badge.textContent = 'Perlu Revisi';
        showToast('Permintaan revisi telah dikirim ke Karyawan.', 'warning');
    }
}
</script>
@endpush
@endsection
