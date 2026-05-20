@extends('layouts.app')
@section('title', 'Edit Laporan Lapangan')
@section('page-title', 'Edit Laporan Lapangan')

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
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-weight:600; font-size:0.9rem; color:var(--text-secondary); margin-bottom:0.35rem; }
    .form-group input, .form-group textarea {
        width:100%; padding:0.55rem 0.85rem; border:1px solid var(--border-color);
        border-radius:var(--border-radius-sm); background:var(--bg-card); color:var(--text-primary);
        font-family:inherit; font-size:0.93rem; outline:none; transition:border 0.15s;
    }
    .form-group input:focus, .form-group textarea:focus { border-color:var(--primary-500); box-shadow:0 0 0 2px color-mix(in srgb,var(--primary-500) 20%,transparent); }
    .btn-outline { display:inline-flex; align-items:center; justify-content:center; gap:0.5rem; padding:0.65rem 1rem; border:1px solid var(--border-color); border-radius:0.75rem; background:transparent; color:var(--text-primary); text-decoration:none; }
    .btn-outline:hover { background:var(--bg-hover); }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media (max-width:600px) { .form-row { grid-template-columns:1fr; } }

    /* Grid foto edit */
    .foto-edit-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:0.75rem; margin-top:0.5rem; }
    @media(max-width:600px){ .foto-edit-grid { grid-template-columns:repeat(2,1fr); } }

    .foto-edit-slot {
        border:2px dashed var(--border-color); border-radius:0.5rem;
        padding:0.5rem; background:var(--bg-body); display:flex; flex-direction:column; gap:0.4rem;
    }
    .foto-edit-slot.has-foto { border-style:solid; border-color:var(--primary-400); }

    .slot-num { font-size:0.68rem; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
    .slot-preview-wrap { position:relative; aspect-ratio:4/3; border-radius:0.35rem; overflow:hidden; background:#f1f5f9; display:flex; align-items:center; justify-content:center; }
    .slot-preview { width:100%; height:100%; object-fit:cover; display:block; }
    .slot-empty-icon { font-size:1.5rem; color:var(--border-color); }
    .slot-remove { position:absolute; top:4px; right:4px; background:rgba(239,68,68,0.9); color:white; border:none; border-radius:50%; width:22px; height:22px; font-size:0.65rem; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 6px rgba(0,0,0,0.15); }
    .slot-file-input { display:none; }
    .slot-upload-btn { width:100%; padding:0.3rem; font-size:0.72rem; border:1px solid var(--border-color); border-radius:0.3rem; background:var(--bg-card); color:var(--text-secondary); cursor:pointer; text-align:center; transition:background 0.15s; }
    .slot-upload-btn:hover { background:var(--bg-hover); }
    .slot-desc-input { width:100%; padding:0.35rem 0.5rem; border:1px solid var(--border-color); border-radius:0.3rem; background:var(--bg-card); color:var(--text-primary); font-family:inherit; font-size:0.75rem; outline:none; resize:none; min-height:48px; box-sizing:border-box; }
    .slot-desc-input:focus { border-color:var(--primary-500); }
</style>

<div style="max-width:860px;margin:auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;">
        <div>
            <h2 style="margin:0;font-size:1.25rem;color:var(--text-primary);">Edit Laporan Lapangan</h2>
            <p style="margin:0.25rem 0 0;color:var(--text-secondary);font-size:0.95rem;">Ubah laporan sebelum disetujui.</p>
        </div>
        <a href="{{ route('karyawan.laporan') }}" class="btn-outline">Kembali</a>
    </div>

    @if(session('error'))
    <div style="padding:1rem;background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;border-radius:0.75rem;margin-bottom:1rem;">
        {{ session('error') }}
    </div>
    @endif

    <form id="editForm" action="{{ route('karyawan.laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data"
          style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--border-radius);padding:1.5rem;">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group">
                <label><i class="fa-regular fa-calendar"></i> Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $laporan->tanggal->format('Y-m-d')) }}" required>
                @error('tanggal')<span class="text-danger" style="font-size:0.85rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-location-dot"></i> Lokasi <span style="color:var(--danger);">*</span></label>
                <input type="text" name="lokasi" value="{{ old('lokasi', $laporan->lokasi) }}" required>
                @error('lokasi')<span class="text-danger" style="font-size:0.85rem;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-group">
            <label>
                <i class="fa-solid fa-camera"></i> Foto Dokumentasi
                <span style="font-weight:400;color:var(--text-muted);font-size:0.8rem;">(Wajib 2 foto · disarankan rasio 1:1 · klik X untuk hapus · tiap foto wajib ada keterangan)</span>
            </label>

            <div class="foto-edit-grid" id="fotoEditGrid">
                @php
                    $existingFotos = $laporan->foto_paths ?? [];
                    $existingDesks = $laporan->foto_deskripsis ?? [];
                    $totalSlots    = 2;
                @endphp

                @for($i = 0; $i < $totalSlots; $i++)
                @php
                    $isExisting = isset($existingFotos[$i]);
                    $fpath      = $isExisting ? $existingFotos[$i] : null;
                    $fdesc      = $isExisting ? ($existingDesks[$i] ?? '') : '';
                @endphp
                <div class="foto-edit-slot {{ $isExisting ? 'has-foto' : '' }}" id="editSlot{{ $i }}">
                    <div class="slot-num">Foto {{ $i+1 }}</div>
                    <div class="slot-preview-wrap" id="editPreview{{ $i }}">
                        @if($isExisting)
                            <img src="{{ $fpath }}" alt="Foto {{ $i+1 }}" class="slot-preview" id="editImg{{ $i }}" loading="lazy">
                            <button type="button" class="slot-remove" onclick="removeEditSlot({{ $i }})">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            {{-- hidden input untuk URL lama --}}
                            <input type="hidden" name="existing_foto_url[{{ $i }}]" id="existingUrl{{ $i }}" value="{{ $fpath }}">
                        @else
                            <i class="fa-solid fa-image slot-empty-icon" id="editEmptyIcon{{ $i }}"></i>
                        @endif
                    </div>

                    {{-- Input file baru (hanya tampil jika slot kosong) --}}
                    <input type="file" name="foto[]" class="slot-file-input" id="editFileInput{{ $i }}"
                           accept="image/*" onchange="handleEditSlot({{ $i }}, this)"
                           {{ $isExisting ? 'style=display:none' : '' }}>
                    <button type="button" class="slot-upload-btn" id="editUploadBtn{{ $i }}"
                            onclick="document.getElementById('editFileInput{{ $i }}').click()"
                            {{ $isExisting ? 'style=display:none' : '' }}>
                        <i class="fa-solid fa-upload"></i> Pilih Foto
                    </button>

                    {{-- Deskripsi --}}
                    @if($isExisting)
                        {{-- Untuk foto lama: kirim lewat existing_deskripsi[i] --}}
                        <textarea name="existing_deskripsi[{{ $i }}]" class="slot-desc-input"
                                  placeholder="Keterangan foto {{ $i+1 }}..."
                                  id="editDesc{{ $i }}" required>{{ old('existing_deskripsi.' . $i, $fdesc) }}</textarea>
                    @else
                        {{-- Untuk foto baru: kirim lewat foto_deskripsi[] --}}
                        <textarea name="foto_deskripsi[]" class="slot-desc-input"
                                  placeholder="Keterangan foto {{ $i+1 }}..."
                                  id="editDesc{{ $i }}"></textarea>
                    @endif
                </div>
                @endfor
            </div>

            {{-- Container hidden inputs untuk removed fotos --}}
            <div id="removedFotosInputs"></div>

            @error('foto')<span style="color:var(--danger);font-size:0.8rem;">{{ $message }}</span>@enderror
            @error('foto_deskripsi')<span style="color:var(--danger);font-size:0.8rem;">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;padding:0.65rem;font-size:0.95rem;">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
        </button>
    </form>
</div>

<script>
// Track slot state: 'existing' | 'new' | 'empty'
const editSlotState = {};

@foreach($laporan->foto_paths ?? [] as $i => $fp)
editSlotState[{{ $i }}] = 'existing';
@endforeach

function removeEditSlot(idx) {
    const state = editSlotState[idx];
    const slot  = document.getElementById('editSlot' + idx);
    const wrap  = document.getElementById('editPreview' + idx);

    if (state === 'existing') {
        // Tambah hidden input removed_fotos[]
        const urlInput = document.getElementById('existingUrl' + idx);
        if (urlInput) {
            const hi = document.createElement('input');
            hi.type  = 'hidden';
            hi.name  = 'removed_fotos[]';
            hi.value = idx; // kirim index
            document.getElementById('removedFotosInputs').appendChild(hi);
        }
    }

    // Bersihkan preview
    wrap.innerHTML = '<i class="fa-solid fa-image slot-empty-icon"></i>';

    // Tampilkan tombol upload
    const uploadBtn = document.getElementById('editUploadBtn' + idx);
    const fileInput = document.getElementById('editFileInput' + idx);
    if (uploadBtn) uploadBtn.style.display = '';
    if (fileInput) fileInput.style.display = '';

    // Ganti textarea: existing_deskripsi → foto_deskripsi[]
    const descEl = document.getElementById('editDesc' + idx);
    if (descEl) {
        descEl.name     = 'foto_deskripsi[]';
        descEl.value    = '';
        descEl.required = false;
    }

    slot.classList.remove('has-foto');
    editSlotState[idx] = 'empty';
}

// ── Image Compression Helper ─────────────────────────
function compressImage(file, callback) {
    if (file.size < 200 * 1024) {
        callback(file);
        return;
    }
    const reader = new FileReader();
    reader.onload = function(event) {
        const img = new Image();
        img.onload = function() {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;
            const max_size = 1024;
            if (width > height) {
                if (width > max_size) {
                    height *= max_size / width;
                    width = max_size;
                }
            } else {
                if (height > max_size) {
                    width *= max_size / height;
                    height = max_size;
                }
            }
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            canvas.toBlob(function(blob) {
                if (blob) {
                    const compressedFile = new File([blob], file.name.substring(0, file.name.lastIndexOf('.')) + '.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });
                    callback(compressedFile);
                } else {
                    callback(file);
                }
            }, 'image/jpeg', 0.7);
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
}

function handleEditSlot(idx, input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const wrap = document.getElementById('editPreview' + idx);
    const slot = document.getElementById('editSlot' + idx);

    // Tampilkan loader kompresi
    const loader = document.createElement('div');
    loader.className = 'slot-compress-loader';
    loader.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Kompresi...';
    loader.setAttribute('style', 'font-size:0.75rem; color:var(--primary-500); margin-top:0.5rem;');
    wrap.appendChild(loader);

    compressImage(file, function(compressedFile) {
        loader.remove();

        // Assign back to file input using DataTransfer
        try {
            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            input.files = dt.files;
        } catch(e) {
            console.error('DataTransfer not supported', e);
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            wrap.innerHTML = '';

            const img = document.createElement('img');
            img.className = 'slot-preview';
            img.src = e.target.result;
            wrap.appendChild(img);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'slot-remove';
            btn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            btn.onclick = function() { removeNewSlot(idx); };
            wrap.appendChild(btn);

            // Sembunyikan tombol upload
            document.getElementById('editUploadBtn' + idx).style.display = 'none';
            slot.classList.add('has-foto');
            editSlotState[idx] = 'new';

            // Wajibkan deskripsi
            const descEl = document.getElementById('editDesc' + idx);
            if (descEl) { descEl.required = true; descEl.name = 'foto_deskripsi[]'; }
        };
        reader.readAsDataURL(compressedFile);
    });
}

function removeNewSlot(idx) {
    const wrap = document.getElementById('editPreview' + idx);
    const slot = document.getElementById('editSlot' + idx);
    wrap.innerHTML = '<i class="fa-solid fa-image slot-empty-icon"></i>';

    const fileInput = document.getElementById('editFileInput' + idx);
    const uploadBtn = document.getElementById('editUploadBtn' + idx);
    if (fileInput) { fileInput.value = ''; fileInput.style.display = ''; }
    if (uploadBtn) uploadBtn.style.display = '';

    const descEl = document.getElementById('editDesc' + idx);
    if (descEl) { descEl.required = false; descEl.value = ''; }

    slot.classList.remove('has-foto');
    editSlotState[idx] = 'empty';
}

// Validasi sebelum submit
document.getElementById('editForm').addEventListener('submit', function(e) {
    const totalFoto = Object.values(editSlotState).filter(s => s !== 'empty').length;
    if (totalFoto !== 2) {
        e.preventDefault();
        if (typeof showToast === 'function') showToast('Wajib mengupload tepat 2 foto!', 'danger');
        return;
    }
    // Cek semua slot aktif punya deskripsi
    let missing = false;
    for (let i = 0; i < 2; i++) {
        if (editSlotState[i] && editSlotState[i] !== 'empty') {
            const desc = document.getElementById('editDesc' + i);
            if (desc && !desc.value.trim()) { missing = true; break; }
        }
    }
    if (missing) {
        e.preventDefault();
        if (typeof showToast === 'function') showToast('Setiap foto wajib diberi keterangan!', 'warning');
    }
});
</script>
@endsection