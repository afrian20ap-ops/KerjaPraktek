@extends('layouts.app')
@section('title', 'Riwayat Absen')
@section('page-title', 'Riwayat Absen Karyawan')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>

<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item active"><i class="fa-solid fa-calendar-check"></i> Riwayat Absen</a>

<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header" style="flex-wrap: wrap; gap: 1rem;">
        <span class="panel-title">Riwayat Absensi</span>
        <div class="panel-actions" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.absensi') }}" id="filterForm" style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
                
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Tampilan:</label>
                    <select name="view_type" id="view_type" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:150px;">
                        <option value="keseluruhan" {{ $viewType === 'keseluruhan' ? 'selected' : '' }}>Semua Karyawan</option>
                        <option value="individu" {{ $viewType === 'individu' ? 'selected' : '' }}>Per Karyawan</option>
                    </select>
                </div>

                <div id="employeeGroup" style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Karyawan:</label>
                    <input type="text" id="searchInput" list="karyawanList" class="form-control" placeholder="Ketik nama atau NIK..." style="padding:0.35rem 0.75rem;font-size:0.85rem; width:220px;" value="{{ isset($karyawanTerpilih) ? strtoupper($karyawanTerpilih->name) . ' (' . ($karyawanTerpilih->nik ?? '-') . ')' : '' }}">
                    <datalist id="karyawanList">
                        @foreach($semuaKaryawan as $karyawan)
                            <option data-id="{{ $karyawan->id }}" value="{{ strtoupper($karyawan->name) }} ({{ $karyawan->nik ?? '-' }})"></option>
                        @endforeach
                    </datalist>
                    <input type="hidden" name="user_id" id="hiddenUserId" value="{{ isset($karyawanTerpilih) ? $karyawanTerpilih->id : '' }}">
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Periode:</label>
                    <select name="periode_type" id="periode_type" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:150px;">
                        <option value="bulanan" {{ $periodeType === 'bulanan' ? 'selected' : '' }}>Per Bulan</option>
                        <option value="range" {{ $periodeType === 'range' ? 'selected' : '' }}>Rentang Tanggal</option>
                    </select>
                </div>

                <div id="bulananGroup" style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Bulan & Tahun:</label>
                    <input type="month" id="bulan_tahun" name="bulan_tahun" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:160px;" value="{{ $bulanTahun }}">
                </div>

                <div id="rangeGroup" style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Dari:</label>
                    <input type="date" name="dari" id="dari" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:145px;" value="{{ $dari }}">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Sampai:</label>
                    <input type="date" name="sampai" id="sampai" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:145px;" value="{{ $sampai }}">
                </div>

                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Urutkan:</label>
                    <select name="sort_by" id="sort_by" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:180px;">
                        <option value="tanggal_desc" {{ $sortBy === 'tanggal_desc' ? 'selected' : '' }}>Hari/Tanggal (Terbaru)</option>
                        <option value="tanggal_asc" {{ $sortBy === 'tanggal_asc' ? 'selected' : '' }}>Hari/Tanggal (Terlama)</option>
                        <option value="nama_asc" id="sortOptNamaAsc" {{ $sortBy === 'nama_asc' ? 'selected' : '' }}>Nama Karyawan (A-Z)</option>
                        <option value="nama_desc" id="sortOptNamaDesc" {{ $sortBy === 'nama_desc' ? 'selected' : '' }}>Nama Karyawan (Z-A)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Tampilkan</button>
                <button type="button" class="btn btn-outline" style="border-color: var(--primary-500); color: var(--primary-500);" onclick="window.print()"><i class="fa-solid fa-print"></i> Cetak PDF</button>
            </form>
        </div>
    </div>
    
    <style>
        .table-absensi { width: 100%; border-collapse: separate; border-spacing: 0; font-family: 'Outfit', sans-serif; font-size: 0.85rem; }
        .table-absensi th, .table-absensi td { border: 1px solid var(--border-color); padding: 0.5rem 0.75rem; text-align: center; color: var(--text-primary); white-space: nowrap; }
        .table-absensi th { background-color: var(--bg-hover); font-weight: 600; color: var(--text-secondary); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
        .table-absensi tbody tr { transition: all 0.2s ease; }
        .table-absensi tbody tr:hover td { background-color: color-mix(in srgb, var(--primary-50) 40%, transparent); }
        .table-absensi td.name-col { text-align: left; font-weight: 600; position: sticky; left: 45px; background: var(--bg-card); z-index: 10; box-shadow: 2px 0 5px rgba(0,0,0,0.02); }
        .table-absensi th.name-col { position: sticky; left: 45px; z-index: 12; background: var(--bg-hover); box-shadow: 2px 0 5px rgba(0,0,0,0.02); }
        .table-absensi th.no-col { position: sticky; left: 0; z-index: 12; background: var(--bg-hover); width: 45px; }
        .table-absensi td.no-col { position: sticky; left: 0; background: var(--bg-card); z-index: 10; font-weight: 500; color: var(--text-secondary); }
        
        .time-input { width: 100%; min-width: 60px; border: 1px solid transparent; border-radius: var(--border-radius-sm); outline: none; text-align: center; background: transparent; font-family: 'Outfit', monospace; font-size: 0.85rem; font-weight: 500; color: inherit; padding: 0.4rem; transition: all 0.2s ease; }
        .time-input:hover { background: var(--bg-hover); border-color: var(--border-color); }
        .time-input:focus { background: var(--surface); border-color: var(--primary-500); box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-500) 15%, transparent); }
        
        @media print {
            @page {
                size: auto;
                margin: 0mm !important;
            }
            body * { visibility: hidden; }
            .panel, .panel * { visibility: visible; }
            .panel { position: absolute; left: 15mm; top: 15mm; width: calc(100% - 30mm); border: none; box-shadow: none; }
            .panel-actions { display: none; }
            .table-absensi th, .table-absensi td { border: 1px solid #000; color: #000; padding: 0.25rem; font-size: 10px; }
            .table-absensi td.name-col, .table-absensi th.name-col, .table-absensi th.no-col, .table-absensi td.no-col { position: static; box-shadow: none; }
            .time-input { border: none !important; background: transparent !important; }
        }
    </style>

    <div class="table-responsive" style="border-radius: var(--border-radius); border: 1px solid var(--border-color);">
        <table class="table-absensi">
            <thead>
                <tr>
                    <th>NO.</th>
                    @if($viewType === 'keseluruhan')
                        <th>NAMA KARYAWAN</th>
                    @endif
                    <th>TANGGAL</th>
                    <th>JAM MASUK</th>
                    <th>JAM KELUAR</th>
                    <th>STATUS</th>
                    <th>LEMBUR (JAM)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensis as $index => $abs)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    @if($viewType === 'keseluruhan')
                        <td style="text-align: left; font-weight: 600;">
                            {{ $abs->user->name ?? '-' }}
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-family: monospace;">
                                NIK: {{ $abs->user->nik ?? '-' }}
                            </div>
                        </td>
                    @endif
                    <td>{{ \Carbon\Carbon::parse($abs->tanggal)->format('d-M-Y') }}</td>
                    <td>{{ $abs->jam_masuk ? \Carbon\Carbon::parse($abs->jam_masuk)->format('H:i') : '-' }}</td>
                    <td>{{ $abs->jam_keluar ? \Carbon\Carbon::parse($abs->jam_keluar)->format('H:i') : '-' }}</td>
                    <td>
                        @if($abs->status == 'Hadir')
                            <span class="badge success">{{ $abs->status }}</span>
                        @elseif($abs->status == 'Alpa')
                            <span class="badge danger">{{ $abs->status }}</span>
                        @else
                            <span class="badge warning">{{ $abs->status }}</span>
                        @endif
                    </td>
                    @php
                        $realLembur = $abs->jam_lembur;
                        if ($abs->jam_keluar) {
                            $keluarC = \Carbon\Carbon::parse($abs->jam_keluar);
                            $batas   = \Carbon\Carbon::parse('17:00:00');
                            if ($keluarC->hour < 9) {
                                $keluarC->addDay();
                            }
                            if ($keluarC->gt($batas)) {
                                $realLembur = (int) round($keluarC->diffInMinutes($batas) / 60);
                            } else {
                                $realLembur = 0;
                            }
                        }
                    @endphp
                    <td style="font-weight: bold;">{{ $realLembur > 0 ? '+' . $realLembur . ' jam' : '-' }}</td>
                </tr>
                @empty
                <tr>
                    @if($viewType === 'individu')
                        @if(isset($karyawanTerpilih))
                            <td colspan="6" style="text-align:center; padding: 2rem; color:var(--text-muted);">Tidak ada data absensi untuk <strong>{{ $karyawanTerpilih->name }}</strong> pada periode yang dipilih.</td>
                        @else
                            <td colspan="6" style="text-align:center; padding: 2rem; color:var(--text-muted);">Silakan pilih karyawan terlebih dahulu untuk melihat rekapitulasi absensi.</td>
                        @endif
                    @else
                        <td colspan="7" style="text-align:center; padding: 2rem; color:var(--text-muted);">Tidak ada data absensi keseluruhan untuk periode yang dipilih.</td>
                    @endif
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const viewTypeSelect = document.getElementById('view_type');
    const employeeGroup = document.getElementById('employeeGroup');
    const searchInput = document.getElementById('searchInput');
    const hiddenUserId = document.getElementById('hiddenUserId');

    const periodeTypeSelect = document.getElementById('periode_type');
    const bulananGroup = document.getElementById('bulananGroup');
    const rangeGroup = document.getElementById('rangeGroup');
    
    const form = document.getElementById('filterForm');

    const sortOptNamaAsc = document.getElementById('sortOptNamaAsc');
    const sortOptNamaDesc = document.getElementById('sortOptNamaDesc');
    const sortBySelect = document.getElementById('sort_by');

    function toggleViewType() {
        if (viewTypeSelect.value === 'individu') {
            employeeGroup.style.display = 'flex';
            searchInput.required = true;
            
            // Sembunyikan opsi urutkan berdasarkan nama pada mode individu
            if (sortOptNamaAsc) sortOptNamaAsc.style.display = 'none';
            if (sortOptNamaDesc) sortOptNamaDesc.style.display = 'none';
            
            // Reset ke default jika sebelumnya mengurutkan berdasarkan nama
            if (sortBySelect && (sortBySelect.value === 'nama_asc' || sortBySelect.value === 'nama_desc')) {
                sortBySelect.value = 'tanggal_desc';
            }
        } else {
            employeeGroup.style.display = 'none';
            searchInput.required = false;
            searchInput.value = '';
            hiddenUserId.value = '';
            
            // Tampilkan kembali opsi urutkan berdasarkan nama pada mode keseluruhan
            if (sortOptNamaAsc) sortOptNamaAsc.style.display = 'block';
            if (sortOptNamaDesc) sortOptNamaDesc.style.display = 'block';
        }
    }

    function togglePeriodeType() {
        if (periodeTypeSelect.value === 'range') {
            rangeGroup.style.display = 'flex';
            bulananGroup.style.display = 'none';
        } else {
            rangeGroup.style.display = 'none';
            bulananGroup.style.display = 'flex';
        }
    }

    viewTypeSelect.addEventListener('change', toggleViewType);
    periodeTypeSelect.addEventListener('change', togglePeriodeType);

    // Initial state
    toggleViewType();
    togglePeriodeType();

    function findMatch() {
        if (!searchInput.value) return null;
        const val = searchInput.value.toLowerCase();
        let foundId = null;
        let exactMatch = false;

        const options = document.querySelectorAll('#karyawanList option');
        for (let i = 0; i < options.length; i++) {
            if (options[i].value.toLowerCase() === val) {
                foundId = options[i].getAttribute('data-id');
                exactMatch = true;
                break;
            }
        }
        
        if (!exactMatch && val.length > 0) {
            for (let i = 0; i < options.length; i++) {
                if (options[i].value.toLowerCase().includes(val)) {
                    foundId = options[i].getAttribute('data-id');
                    searchInput.value = options[i].value; // auto-complete
                    break;
                }
            }
        }
        return foundId;
    }

    if (searchInput) {
        searchInput.addEventListener('change', function() {
            if (!this.value) {
                hiddenUserId.value = '';
                return;
            }
            const foundId = findMatch();
            if (foundId) {
                hiddenUserId.value = foundId;
            }
        });
    }

    form.addEventListener('submit', function(e) {
        if (viewTypeSelect.value === 'individu') {
            if (searchInput.value) {
                const foundId = findMatch();
                if (foundId) {
                    hiddenUserId.value = foundId;
                } else {
                    e.preventDefault();
                    alert("Karyawan tidak ditemukan. Silakan ketik nama atau NIK dengan benar.");
                }
            } else {
                e.preventDefault();
                alert("Silakan pilih karyawan terlebih dahulu.");
            }
        }
    });

});
</script>
@endpush
