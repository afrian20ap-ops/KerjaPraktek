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
    .week-filter input { padding:0.4rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--border-radius-sm); background:var(--bg-card); color:var(--text-primary); font-family:inherit; font-size:0.85rem; outline:none; }
    .week-filter input:focus { border-color:var(--primary-500); }
    .week-badge { background:var(--primary-50); color:var(--primary-700); border:1px solid var(--primary-200); padding:0.3rem 0.9rem; border-radius:99px; font-size:0.82rem; font-weight:600; }

    .laporan-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--border-radius); padding:1.25rem; margin-bottom:1rem; transition:box-shadow 0.15s; }
    .laporan-card:hover { box-shadow:var(--shadow-md); }
    .section-lbl { font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); margin-bottom:0.5rem; margin-top:0.75rem; }

    .badge-disetujui { background:color-mix(in srgb,var(--success) 15%,transparent); color:var(--success); border:1px solid var(--success); padding:0.25rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:700; }
    .badge-terkirim  { background:color-mix(in srgb,var(--warning) 15%,transparent); color:#b45309; border:1px solid #f59e0b; padding:0.25rem 0.75rem; border-radius:99px; font-size:0.75rem; font-weight:700; }

    /* Grid foto display */
    .foto-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:0.75rem; margin-top:0.5rem; }
    @media(max-width:700px) { .foto-grid { grid-template-columns:repeat(2,1fr); } }
    .foto-item { border-radius:0.5rem; border:1px solid var(--border-color); overflow:hidden; background:var(--bg-body); }
    .foto-item img { width:100%; aspect-ratio:4/3; object-fit:cover; display:block; cursor:pointer; transition:opacity 0.15s; }
    .foto-item img:hover { opacity:0.88; }
    .foto-item-desc { padding:0.4rem 0.6rem; font-size:0.78rem; color:var(--text-secondary); line-height:1.4; border-top:1px solid var(--border-color); background:var(--bg-card); }

    /* Form */
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-weight:600; font-size:0.85rem; color:var(--text-secondary); margin-bottom:0.35rem; }
    .form-group input, .form-group textarea, .form-group select {
        width:100%; padding:0.5rem 0.75rem; border:1px solid var(--border-color);
        border-radius:var(--border-radius-sm); background:var(--bg-card); color:var(--text-primary);
        font-family:inherit; font-size:0.88rem; outline:none; transition:border 0.15s;
    }
    .form-group input:focus, .form-group textarea:focus { border-color:var(--primary-500); box-shadow:0 0 0 2px color-mix(in srgb,var(--primary-500) 20%,transparent); }
    .form-group textarea { resize:vertical; min-height:60px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media (max-width:600px) { .form-row { grid-template-columns:1fr; } }

    /* Slot foto di form */
    .foto-slots { display:grid; grid-template-columns:repeat(4,1fr); gap:0.75rem; margin-top:0.5rem; }
    @media(max-width:600px){ .foto-slots { grid-template-columns:repeat(2,1fr); } }
    .foto-slot {
        border:2px dashed var(--border-color); border-radius:0.5rem;
        padding:0.5rem; background:var(--bg-body); transition:border-color 0.15s;
        display:flex; flex-direction:column; gap:0.4rem;
    }
    .foto-slot:has(.slot-preview) { border-style:solid; border-color:var(--primary-400); }
    .slot-num { font-size:0.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
    .slot-preview-wrap { position:relative; aspect-ratio:4/3; border-radius:0.35rem; overflow:hidden; background:#f1f5f9; display:flex; align-items:center; justify-content:center; }
    .slot-preview { width:100%; height:100%; object-fit:cover; display:block; }
    .slot-empty-icon { font-size:1.5rem; color:var(--border-color); }
    .slot-remove { position:absolute; top:4px; right:4px; background:rgba(239,68,68,0.9); color:white; border:none; border-radius:50%; width:20px; height:20px; font-size:0.6rem; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .slot-file-input { display:none; }
    .slot-upload-btn { width:100%; padding:0.3rem; font-size:0.72rem; border:1px solid var(--border-color); border-radius:0.3rem; background:var(--bg-card); color:var(--text-secondary); cursor:pointer; text-align:center; transition:background 0.15s; }
    .slot-upload-btn:hover { background:var(--bg-hover); }
    .slot-desc-input { width:100%; padding:0.35rem 0.5rem; border:1px solid var(--border-color); border-radius:0.3rem; background:var(--bg-card); color:var(--text-primary); font-family:inherit; font-size:0.75rem; outline:none; resize:none; min-height:48px; }
    .slot-desc-input:focus { border-color:var(--primary-500); }
</style>

{{-- FILTER TANGGAL --}}
<form method="GET" action="{{ route('karyawan.laporan') }}" class="week-filter">
    <label><i class="fa-solid fa-calendar-days" style="color:var(--primary-500);margin-right:0.3rem;"></i>Bulan & Tahun:</label>
    <input type="month" name="bulan_tahun" value="{{ request('bulan_tahun', now()->format('Y-m')) }}" style="width:160px;" onchange="this.form.submit()" required>
    <button type="submit" class="btn btn-primary" style="padding:0.4rem 0.85rem;font-size:0.85rem;"><i class="fa-solid fa-search"></i> Tampilkan</button>
    @php
        $tDari = \Carbon\Carbon::parse($tanggalDari)->locale('id')->isoFormat('D MMM');
        $tSampai = \Carbon\Carbon::parse($tanggalSampai)->locale('id')->isoFormat('D MMM Y');
    @endphp
    <span class="week-badge" id="dateRange">{{ $tDari }} – {{ $tSampai }}</span>
</form>

<div style="display:grid;grid-template-columns:1fr 400px;gap:1.25rem;align-items:start;" class="laporan-layout">

    {{-- KIRI: Daftar Laporan --}}
    <div>
        <div style="font-weight:700;font-size:1rem;margin-bottom:1rem;color:var(--text-primary);">
            <i class="fa-solid fa-list-check" style="color:var(--primary-500);margin-right:0.4rem;"></i>
            Laporan Anda
            <span style="font-size:0.82rem;color:var(--text-muted);font-weight:500;">({{ $laporan->count() }} laporan)</span>
        </div>

        @forelse($laporan as $lp)
        <div class="laporan-card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:0.5rem;margin-bottom:0.75rem;">
                <div style="font-size:0.82rem;color:var(--text-secondary);">
                    <span style="font-weight:600;color:var(--text-primary);">{{ $lp->tanggal->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    @if($lp->lokasi) <br><i class="fa-solid fa-location-dot" style="color:var(--danger);"></i> {{ $lp->lokasi }} @endif
                </div>
                <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                    @if($lp->status === 'Disetujui')
                        <span class="badge-disetujui"><i class="fa-solid fa-check-circle"></i> Disetujui</span>
                    @elseif($lp->status === 'Terkirim')
                        <span class="badge-terkirim"><i class="fa-solid fa-paper-plane"></i> Menunggu</span>
                        <a href="{{ route('karyawan.laporan.edit', $lp->id) }}" class="btn btn-secondary" style="font-size:0.78rem;padding:0.35rem 0.85rem;">Edit</a>
                    @endif
                </div>
            </div>

            {{-- Grid Foto + Deskripsi --}}
            @if(!empty($lp->foto_paths))
            <div class="section-lbl"><i class="fa-solid fa-camera" style="color:var(--primary-500);margin-right:0.3rem;"></i>Dokumentasi Foto ({{ count($lp->foto_paths) }})</div>
            <div class="foto-grid">
                @foreach($lp->foto_paths as $idx => $fpath)
                <div class="foto-item">
                    <img src="{{ $fpath }}" alt="Foto {{ $idx+1 }}"
                         onclick="openLightbox('{{ $fpath }}')" />
                    <div class="foto-item-desc">
                        @if(!empty($lp->foto_deskripsis[$idx]))
                            <i class="fa-solid fa-circle-info" style="color:var(--primary-500);margin-right:0.25rem;font-size:0.7rem;"></i>
                            {{ $lp->foto_deskripsis[$idx] }}
                        @else
                            <span style="color:var(--text-muted);font-style:italic;">Foto {{ $idx+1 }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <div style="text-align:center;padding:3rem 2rem;background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--border-radius);color:var(--text-muted);">
            <i class="fa-solid fa-folder-open" style="font-size:2rem;margin-bottom:0.75rem;display:block;color:var(--border-color);"></i>
            <p style="margin:0;font-size:0.9rem;">Belum ada laporan dari Anda untuk periode ini.</p>
        </div>
        @endforelse
    </div>

    {{-- KANAN: Form Kirim Laporan --}}
    <div class="panel" style="position:sticky;top:1rem;">
        <div class="panel-header">
            <span class="panel-title" style="font-size:0.95rem;"><i class="fa-solid fa-paper-plane" style="color:var(--primary-500);margin-right:0.35rem;"></i>Kirim Laporan Baru</span>
        </div>



        <form id="laporanForm" action="{{ route('karyawan.laporan.store') }}" method="POST" enctype="multipart/form-data" style="padding:1.25rem;">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-regular fa-calendar"></i> Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-location-dot"></i> Lokasi <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="lokasi" placeholder="cth: Gedung A lt.3" required>
                </div>
            </div>

            {{-- 6 SLOT FOTO --}}
            <div class="form-group">
                <label><i class="fa-solid fa-camera"></i> Foto Dokumentasi <span style="color:var(--danger);">*</span>
                    <span style="font-weight:400;color:var(--text-muted);font-size:0.78rem;">(maks 8 foto, tiap foto wajib diberi keterangan)</span>
                </label>
                <div class="foto-slots" id="fotoSlots">
                    @for($i = 0; $i < 8; $i++)
                    <div class="foto-slot" id="slot{{ $i }}">
                        <div class="slot-num">Foto {{ $i+1 }}</div>
                        <div class="slot-preview-wrap" id="preview{{ $i }}">
                            <i class="fa-solid fa-image slot-empty-icon" id="emptyIcon{{ $i }}"></i>
                        </div>
                        <input type="file" name="foto[]" class="slot-file-input" id="fileInput{{ $i }}"
                               accept="image/*" onchange="handleSlotFile({{ $i }}, this)">
                        <button type="button" class="slot-upload-btn" onclick="document.getElementById('fileInput{{ $i }}').click()">
                            <i class="fa-solid fa-upload"></i> Pilih Foto
                        </button>
                        <textarea name="foto_deskripsi[]" class="slot-desc-input"
                                  placeholder="Keterangan foto {{ $i+1 }}..."
                                  id="desc{{ $i }}"
                                  {{ $i === 0 ? 'required' : '' }}></textarea>
                    </div>
                    @endfor
                </div>
                @error('foto') <span style="color:var(--danger);font-size:0.8rem;">{{ $message }}</span> @enderror
                @error('foto_deskripsi') <span style="color:var(--danger);font-size:0.8rem;">{{ $message }}</span> @enderror
                @error('foto_deskripsi.0') <span style="color:var(--danger);font-size:0.8rem;">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn" style="width:100%;padding:0.6rem;font-size:0.9rem;">
                <i class="fa-solid fa-paper-plane"></i> Kirim Laporan
            </button>
        </form>
    </div>
</div>

{{-- Lightbox --}}
<div id="lightboxOverlay" onclick="closeLightbox()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9999;align-items:center;justify-content:center;">
    <img id="lightboxImg" src="" alt="Preview" style="max-width:90vw;max-height:90vh;border-radius:0.5rem;box-shadow:0 8px 40px rgba(0,0,0,0.5);">
    <button onclick="closeLightbox()" style="position:absolute;top:1rem;right:1.25rem;background:rgba(255,255,255,0.15);border:none;color:white;font-size:1.5rem;cursor:pointer;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;">&times;</button>
</div>

<style>
@media (max-width: 900px) {
    .laporan-layout { grid-template-columns: 1fr !important; }
}
#lightboxOverlay { display:none; }
#lightboxOverlay.active { display:flex !important; }
</style>

<script>
// ── Lightbox ──────────────────────────────────────────
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxOverlay').classList.add('active');
}
function closeLightbox() {
    document.getElementById('lightboxOverlay').classList.remove('active');
}

// ── Slot foto logic ───────────────────────────────────
// slotFiles[i] = File object (hanya slot yang ada fotonya)
const slotFiles = {};

function handleSlotFile(idx, input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    slotFiles[idx] = file;

    const wrap = document.getElementById('preview' + idx);
    const icon = document.getElementById('emptyIcon' + idx);

    // Bersihkan preview lama
    const oldImg = wrap.querySelector('img.slot-preview');
    if (oldImg) oldImg.remove();
    const oldBtn = wrap.querySelector('.slot-remove');
    if (oldBtn) oldBtn.remove();

    const reader = new FileReader();
    reader.onload = function(ev) {
        if (icon) icon.style.display = 'none';

        const img = document.createElement('img');
        img.className = 'slot-preview';
        img.src = ev.target.result;
        wrap.appendChild(img);

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'slot-remove';
        btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        btn.onclick = function(e) { e.stopPropagation(); removeSlot(idx); };
        wrap.appendChild(btn);

        document.getElementById('slot' + idx).style.borderStyle = 'solid';
        document.getElementById('slot' + idx).style.borderColor = 'var(--primary-400)';
    };
    reader.readAsDataURL(file);
}

function removeSlot(idx) {
    delete slotFiles[idx];
    const wrap = document.getElementById('preview' + idx);
    const oldImg = wrap.querySelector('img.slot-preview');
    if (oldImg) oldImg.remove();
    const oldBtn = wrap.querySelector('.slot-remove');
    if (oldBtn) oldBtn.remove();
    const icon = document.getElementById('emptyIcon' + idx);
    if (icon) icon.style.display = '';

    document.getElementById('slot' + idx).style.borderStyle = 'dashed';
    document.getElementById('slot' + idx).style.borderColor = 'var(--border-color)';
    document.getElementById('fileInput' + idx).value = '';
}

// ── Submit via FormData (bersih, hanya slot yang ada foto) ──────────────────
document.getElementById('laporanForm').addEventListener('submit', function(e) {
    e.preventDefault(); // selalu intercept

    const filledSlots = Object.keys(slotFiles).map(Number);

    // Validasi: minimal 1 foto
    if (filledSlots.length === 0) {
        showToast('Minimal harus ada 1 foto!', 'danger');
        return;
    }

    // Validasi: setiap foto harus punya keterangan
    let missing = false;
    filledSlots.forEach(function(idx) {
        const desc = document.getElementById('desc' + idx);
        if (!desc || !desc.value.trim()) missing = true;
    });
    if (missing) {
        showToast('Setiap foto wajib diberi keterangan!', 'warning');
        return;
    }

    // Bangun FormData hanya dari slot yang terisi
    const form    = document.getElementById('laporanForm');
    const fd      = new FormData();

    // CSRF
    const csrfToken = form.querySelector('input[name="_token"]').value;
    fd.append('_token', csrfToken);

    // Field biasa
    fd.append('tanggal', form.querySelector('input[name="tanggal"]').value);
    fd.append('lokasi',  form.querySelector('input[name="lokasi"]').value);

    // Hanya slot yang ada fotonya
    filledSlots.sort(function(a, b){ return a - b; }).forEach(function(idx) {
        fd.append('foto[]',           slotFiles[idx]);
        fd.append('foto_deskripsi[]', document.getElementById('desc' + idx).value.trim());
    });

    // Loading state
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';

    fetch(form.action, {
        method: 'POST',
        body: fd,
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(function(res) {
        return res.json().then(function(data) {
            if (data.success && data.redirect) {
                // Sukses: redirect ke halaman laporan
                window.location.href = data.redirect;
            } else {
                // Error dari server
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Laporan';
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'Terjadi kesalahan.');
                showToast(msg, 'danger');
            }
        });
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Laporan';
        showToast('Terjadi kesalahan jaringan. Coba lagi.', 'danger');
        console.error(err);
    });
});
</script>
@endsection
