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
<style>
    .date-bar {
        display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
        background: var(--bg-card); border: 1px solid var(--border-color);
        border-radius: var(--border-radius); padding: 1rem 1.25rem; margin-bottom: 1.25rem;
    }
    .date-bar label { font-weight: 600; font-size: 0.85rem; color: var(--text-secondary); white-space: nowrap; }
    .date-bar input[type="date"] {
        padding: 0.45rem 0.85rem; border: 1px solid var(--border-color);
        border-radius: var(--border-radius-sm); background: var(--bg-card);
        color: var(--text-primary); font-family: inherit; font-size: 0.9rem;
        cursor: pointer; outline: none; transition: border 0.15s;
    }
    .date-bar input[type="date"]:focus { border-color: var(--primary-500); }
    .date-badge { background: var(--primary-50); color: var(--primary-700); border: 1px solid var(--primary-200); padding: 0.3rem 0.9rem; border-radius: 99px; font-size: 0.82rem; font-weight: 600; }
    .day-nav { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .day-btn { padding: 0.3rem 0.75rem; border-radius: 99px; border: 1px solid var(--border-color); font-size: 0.78rem; font-weight: 600; cursor: pointer; transition: all 0.15s; background: var(--bg-card); color: var(--text-secondary); text-decoration: none; }
    .day-btn:hover { border-color: var(--primary-400); color: var(--primary-600); }
    .day-btn.today { background: var(--primary-500); color: white; border-color: var(--primary-500); }

    .summary-bar { display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
    .summary-chip { display: flex; align-items: center; gap: 0.5rem; padding: 0.45rem 1rem; border-radius: 99px; font-size: 0.82rem; font-weight: 700; border: 1.5px solid; }

    .abs-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .abs-table th { padding: 0.85rem 1rem; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); background: var(--bg-hover); border-bottom: 1px solid var(--border-color); text-align: left; }
    .abs-table td { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    .abs-table tbody tr:last-child td { border-bottom: none; }
    .abs-table tbody tr:hover td { background: color-mix(in srgb, var(--primary-50) 25%, transparent); }

    .toggle-wrap { display: flex; background: var(--bg-hover); border: 1px solid var(--border-color); border-radius: 99px; padding: 2px; gap: 2px; width: fit-content; }
    .toggle-btn { padding: 0.3rem 0.85rem; border-radius: 99px; border: none; cursor: pointer; font-size: 0.78rem; font-weight: 700; transition: all 0.15s; background: transparent; color: var(--text-secondary); }
    .toggle-btn.on-hadir { background: var(--success); color: white; }
    .toggle-btn.on-alpa  { background: var(--danger);  color: white; }

    .time-clean { width: 88px; border: 1px solid var(--border-color); border-radius: var(--border-radius-sm); padding: 0.35rem 0.5rem; background: var(--bg-card); color: var(--text-primary); font-family: monospace; font-size: 0.88rem; text-align: center; outline: none; transition: border 0.15s; }
    .time-clean:focus { border-color: var(--primary-500); box-shadow: 0 0 0 2px color-mix(in srgb, var(--primary-500) 20%, transparent); }
    .time-clean:disabled { opacity: 0.35; cursor: not-allowed; background: var(--bg-hover); }
</style>

{{-- DATE PICKER --}}
<form method="GET" action="{{ route('supervisi.absensi') }}" class="date-bar">
    <label><i class="fa-solid fa-calendar-day" style="color:var(--primary-500);margin-right:0.35rem;"></i>Tanggal:</label>
    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()">
    <span class="date-badge">{{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
    <button type="submit" class="btn btn-outline" style="margin-left:auto;padding:0.35rem 0.75rem;font-size:0.82rem;"><i class="fa-solid fa-arrow-right"></i> Tampilkan</button>
</form>

{{-- SUMMARY --}}
@php
    $jmlHadir = collect($absensis)->filter(fn($a) => $a && $a->status === 'Hadir')->count();
    $jmlAlpa  = collect($absensis)->filter(fn($a) => $a && $a->status === 'Alpa')->count();
@endphp
<div class="summary-bar">
    <div class="summary-chip" style="background:color-mix(in srgb,var(--success) 12%,transparent);color:var(--success);border-color:var(--success);">
        <i class="fa-solid fa-check-circle"></i> Hadir: {{ $jmlHadir }}
    </div>
    <div class="summary-chip" style="background:color-mix(in srgb,var(--danger) 12%,transparent);color:var(--danger);border-color:var(--danger);">
        <i class="fa-solid fa-times-circle"></i> Alpa: {{ $jmlAlpa }}
    </div>
    <div class="summary-chip" style="background:color-mix(in srgb,var(--text-muted) 10%,transparent);color:var(--text-muted);border-color:var(--border-color);">
        <i class="fa-solid fa-users"></i> Total: {{ $users->count() }} karyawan
    </div>
</div>

{{-- FORM --}}
<div class="panel">
    <form action="{{ route('supervisi.absensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

        <div class="panel-header">
            <span class="panel-title">Input Absensi — {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }}</span>
            <div class="panel-actions">
                <button type="button" onclick="tandaiSemua('Hadir')" class="btn btn-outline" style="border-color:var(--success);color:var(--success);padding:0.35rem 0.85rem;font-size:0.82rem;">
                    <i class="fa-solid fa-check-double"></i> Semua Hadir
                </button>
                <button type="button" onclick="setJamSemua('09:00','17:00')" class="btn btn-outline" style="padding:0.35rem 0.85rem;font-size:0.82rem;">
                    <i class="fa-regular fa-clock"></i> Set Jam Default
                </button>
                <button type="submit" class="btn btn-primary" style="padding:0.35rem 1rem;">
                    <i class="fa-solid fa-save"></i> Simpan Absensi
                </button>
            </div>
        </div>

        @if(session('success'))
        <div style="padding:0.85rem 1.25rem;background:var(--success);color:white;border-bottom:1px solid var(--border-color);font-weight:500;">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <div class="table-responsive">
            <table class="abs-table">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Nama Karyawan</th>
                        <th style="width:160px;">Status</th>
                        <th style="width:115px;">Jam Masuk</th>
                        <th style="width:115px;">Jam Keluar</th>
                        <th style="width:110px;">Lembur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $no => $user)
                    @php
                        $abs    = $absensis[$user->id] ?? null;
                        $status = $abs ? $abs->status : 'Hadir';
                        $masuk  = $abs && $abs->jam_masuk  ? substr($abs->jam_masuk,  0, 5) : '09:00';
                        $keluar = $abs && $abs->jam_keluar ? substr($abs->jam_keluar, 0, 5) : '17:00';
                        $lembur = $abs ? (int)$abs->jam_lembur : 0;
                        $isAlpa = $status === 'Alpa';
                    @endphp
                    <tr>
                        <td style="color:var(--text-muted);font-weight:600;font-size:0.82rem;">{{ $no + 1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div class="avatar" style="width:36px;height:36px;font-size:0.85rem;flex-shrink:0;background:var(--primary-100);color:var(--primary-700);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-primary);">{{ $user->name }}</div>
                                    <div style="font-size:0.75rem;color:var(--text-secondary);">{{ $user->nik ?? '-' }} · {{ $user->divisi ?? 'Staff' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="hidden" name="absensi[{{ $user->id }}][status]" id="status-{{ $user->id }}" value="{{ $status }}">
                            <div class="toggle-wrap">
                                <button type="button" onclick="setStatus({{ $user->id }}, 'Hadir')"
                                        id="btn-hadir-{{ $user->id }}"
                                        class="toggle-btn {{ !$isAlpa ? 'on-hadir' : '' }}">
                                    <i class="fa-solid fa-check"></i> Hadir
                                </button>
                                <button type="button" onclick="setStatus({{ $user->id }}, 'Alpa')"
                                        id="btn-alpa-{{ $user->id }}"
                                        class="toggle-btn {{ $isAlpa ? 'on-alpa' : '' }}">
                                    <i class="fa-solid fa-times"></i> Alpa
                                </button>
                            </div>
                        </td>
                        <td>
                            <input type="time" name="absensi[{{ $user->id }}][jam_masuk]"
                                   id="masuk-{{ $user->id }}"
                                   value="{{ $isAlpa ? '' : $masuk }}"
                                   class="time-clean" {{ $isAlpa ? 'disabled' : '' }}>
                        </td>
                        <td>
                            <input type="time" name="absensi[{{ $user->id }}][jam_keluar]"
                                   id="keluar-{{ $user->id }}"
                                   value="{{ $isAlpa ? '' : $keluar }}"
                                   class="time-clean" {{ $isAlpa ? 'disabled' : '' }}
                                   oninput="hitungLembur({{ $user->id }})">
                        </td>
                        <td>
                            <span id="lembur-{{ $user->id }}"
                                  style="font-weight:700;font-size:0.85rem;color:{{ $lembur > 0 ? 'var(--warning)' : 'var(--text-muted)' }};">
                                {{ $lembur > 0 ? '+'.$lembur.' jam' : '-' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted);">Belum ada karyawan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="padding:1rem 1.25rem;display:flex;justify-content:flex-end;border-top:1px solid var(--border-color);">
            <button type="submit" class="btn btn-primary" style="padding:0.5rem 1.5rem;font-size:0.95rem;">
                <i class="fa-solid fa-save"></i> Simpan Semua Absensi
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function setStatus(userId, status) {
    document.getElementById('status-' + userId).value = status;
    const isAlpa = status === 'Alpa';

    document.getElementById('btn-hadir-' + userId).className = 'toggle-btn' + (!isAlpa ? ' on-hadir' : '');
    document.getElementById('btn-alpa-'  + userId).className = 'toggle-btn' + ( isAlpa ? ' on-alpa'  : '');

    const masuk  = document.getElementById('masuk-'  + userId);
    const keluar = document.getElementById('keluar-' + userId);
    masuk.disabled  = isAlpa;
    keluar.disabled = isAlpa;

    if (isAlpa) {
        masuk.value = keluar.value = '';
        const el = document.getElementById('lembur-' + userId);
        if (el) { el.textContent = '-'; el.style.color = 'var(--text-muted)'; }
    } else {
        if (!masuk.value)  masuk.value  = '09:00';
        if (!keluar.value) keluar.value = '17:00';
    }
}

function hitungLembur(userId) {
    const keluar   = document.getElementById('keluar-' + userId)?.value;
    const lemburEl = document.getElementById('lembur-' + userId);
    if (!keluar || !lemburEl) return;

    const [h, m]   = keluar.split(':').map(Number);
    const totalMnt = h * 60 + m;
    const batasMnt = 17 * 60;

    if (totalMnt > batasMnt) {
        const jam = Math.floor((totalMnt - batasMnt) / 60);
        lemburEl.textContent = jam > 0 ? '+' + jam + ' jam' : '-';
        lemburEl.style.color = 'var(--warning)';
    } else {
        lemburEl.textContent = '-';
        lemburEl.style.color = 'var(--text-muted)';
    }
}

function tandaiSemua(status) {
    document.querySelectorAll('[id^="status-"]').forEach(el => {
        setStatus(parseInt(el.id.replace('status-', '')), status);
    });
}

function setJamSemua(jamMasuk, jamKeluar) {
    document.querySelectorAll('[id^="masuk-"]').forEach(el => {
        if (!el.disabled) el.value = jamMasuk;
    });
    document.querySelectorAll('[id^="keluar-"]').forEach(el => {
        if (!el.disabled) {
            el.value = jamKeluar;
            hitungLembur(el.id.replace('keluar-', ''));
        }
    });
}
</script>
@endpush
