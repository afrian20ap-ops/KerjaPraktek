@extends('layouts.app')
@section('title', 'Slip Gaji')
@section('page-title', 'Slip Gaji')

@section('sidebar-nav')
<span class="nav-label">Menu Utama</span>
<a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fa-solid fa-house"></i> Dashboard</a>
<a href="{{ route('admin.karyawan') }}" class="nav-item"><i class="fa-solid fa-users"></i> Data Karyawan</a>

<span class="nav-label" style="margin-top:1rem;">Absensi</span>
<a href="{{ route('admin.absensi') }}" class="nav-item"><i class="fa-solid fa-calendar-check"></i> Data Absensi</a>
<span class="nav-label" style="margin-top:1rem;">Penggajian</span>
<a href="{{ route('admin.gaji.slip') }}" class="nav-item active"><i class="fa-solid fa-file-invoice-dollar"></i> Slip Gaji</a>
<span class="nav-label" style="margin-top:1rem;">Operasional</span>
<a href="{{ route('admin.laporan') }}" class="nav-item"><i class="fa-solid fa-clipboard-list"></i> Laporan Lapangan</a>
@endsection

@section('content')

<style>
    .info-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 2rem;
        margin-bottom: 1.5rem;
    }
    .info-group { display: flex; flex-direction: column; gap: 0.6rem; }
    .info-row { display: flex; justify-content: space-between; font-size: 0.88rem; padding-bottom: 0.45rem; border-bottom: 1px dashed var(--border-color); }
    .info-label { color: var(--text-secondary); font-weight: 500; font-size: 0.82rem; }
    .info-value { color: var(--text-primary); font-weight: 700; text-align: right; }
    .info-highlight {
        background: color-mix(in srgb, var(--success) 10%, transparent);
        color: var(--success);
        padding: 0.85rem 1rem;
        border-radius: var(--border-radius-sm);
        font-size: 1.05rem;
        font-weight: 800;
        border: 1px solid color-mix(in srgb, var(--success) 30%, transparent);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .table-report { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    .table-report th, .table-report td { border: 1px solid var(--border-color); padding: 0.6rem 0.4rem; text-align: center; color: var(--text-primary); white-space: nowrap; }
    .table-report th { background: var(--bg-hover); font-weight: 600; color: var(--text-secondary); font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase; }
    .table-report tbody tr:hover td { background: color-mix(in srgb, var(--primary-50) 35%, transparent); }
    .table-report tfoot th { background: color-mix(in srgb, var(--primary-100) 50%, transparent); color: var(--primary-700); font-weight: 700; border-top: 2px solid var(--primary-300); font-size: 0.85rem; }
    .input-rupiah {
        width: 90px;
        text-align: right;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        padding: 0.3rem 0.5rem;
        background: var(--bg-card);
        color: var(--text-primary);
        font-family: monospace;
        font-size: 0.82rem;
        transition: border 0.15s;
    }
    .input-rupiah:focus { border-color: var(--primary-500); outline: none; box-shadow: 0 0 0 2px color-mix(in srgb, var(--primary-500) 20%, transparent); }
    .alert-success { padding: 0.85rem 1rem; background: var(--success); color: white; border-radius: var(--border-radius); margin-bottom: 1rem; font-weight: 500; }

    @media print {
        .no-print, .panel-header form, .panel-actions { display: none !important; }
        body * { visibility: hidden; }
        .panel, .panel * { visibility: visible; }
        .panel { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; }
        .table-report th, .table-report td { border: 1px solid #000 !important; color: #000 !important; }
        .table-report tfoot th { border-top: 2px solid #000 !important; }
        .input-rupiah { border: none !important; background: transparent !important; font-weight: 700; }
        .info-card { border: 1px solid #000 !important; }
        .info-highlight { background: none !important; color: #000 !important; border: 1px solid #000 !important; }
    }
</style>

<div class="panel">
    {{-- ===== HEADER: Filter + Generate ===== --}}
    <div class="panel-header" style="flex-wrap: wrap; gap: 0.75rem;">
        <div class="header-left">
            <span class="panel-title" style="font-weight: 700; font-size: 1.2rem;">SUMMARY REPORT</span>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            {{-- Pilih Karyawan --}}
            <form class="filter-actions" action="{{ route('admin.gaji.slip') }}" method="GET" style="display:flex;gap:0.5rem;align-items:center;">
                <label style="font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Karyawan:</label>
                <input type="text" id="searchInput" list="karyawanList" class="form-control" placeholder="Ketik nama atau NIK..." style="padding:0.35rem 0.75rem;font-size:0.85rem; min-width:200px;" value="{{ isset($user) ? strtoupper($user->name) . ' (' . ($user->nik ?? '-') . ')' : '' }}">
                <datalist id="karyawanList">
                    @foreach($semuaKaryawan as $karyawan)
                        <option data-id="{{ $karyawan->id }}" value="{{ strtoupper($karyawan->name) }} ({{ $karyawan->nik ?? '-' }})"></option>
                    @endforeach
                </datalist>
                <input type="hidden" name="user_id" id="hiddenUserId" value="{{ $userId ?? '' }}">
                <label style="font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap; margin-left: 0.5rem;">Dari:</label>
                <input type="date" name="date_from" id="dateFrom" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:145px;" value="{{ $periodeMulai ?? date('Y-m-01') }}">
                <label style="font-size: 0.82rem; font-weight: 600; color: var(--text-secondary); white-space:nowrap;">Sampai:</label>
                <input type="date" name="date_to" id="dateTo" class="form-control" style="padding:0.35rem 0.75rem;font-size:0.85rem; width:145px;" value="{{ $periodeAkhir ?? date('Y-m-t') }}">
                <button type="submit" class="btn btn-primary" style="padding:0.35rem 0.75rem;"><i class="fa-solid fa-search"></i> Tampilkan</button>
            </form>

            {{-- Cetak --}}
            <button onclick="window.print()" class="btn btn-outline no-print" style="border-color:var(--primary-500);color:var(--primary-500);padding:0.35rem 0.85rem;">
                <i class="fa-solid fa-print"></i> Cetak / Export PDF
            </button>
        </div>
    </div>

    <div style="padding: 0 1.5rem 1.5rem;">


        @if(isset($user))
        {{-- ===== INFO CARD ===== --}}
        <div class="info-card">
            <div class="info-group">
                <div class="info-row"><span class="info-label">NAMA KARYAWAN</span><span class="info-value" style="text-transform:uppercase;">{{ $user->name }}</span></div>
                <div class="info-row"><span class="info-label">BASIC / HARI</span><span class="info-value">Rp {{ number_format($user->gaji_pokok_harian, 0, ',', '.') }}</span></div>
                <div class="info-row"><span class="info-label">UANG LEMBUR / JAM</span><span class="info-value">Rp {{ number_format($user->uang_lembur_per_jam, 0, ',', '.') }}</span></div>
                <div class="info-row"><span class="info-label">UANG MAKAN / HARI</span><span class="info-value">Rp {{ number_format($user->uang_makan_harian, 0, ',', '.') }}</span></div>
            </div>
            <div class="info-group">
                <div class="info-highlight">
                    <span style="font-size:0.82rem;font-weight:600;color:var(--text-secondary);">TOTAL GAJI DITERIMA</span>
                    <span id="header-total-gaji">Rp 0</span>
                </div>
                <div class="info-row"><span class="info-label">TOTAL KASBON</span><span class="info-value" style="color:var(--danger);" id="header-total-kasbon">-</span></div>
                <div class="info-row"><span class="info-label">PERIODE</span><span class="info-value">{{ \Carbon\Carbon::parse($periodeMulai)->format('d-M-y') }} s/d {{ \Carbon\Carbon::parse($periodeAkhir)->format('d-M-y') }}</span></div>
            </div>
        </div>

        {{-- ===== FORM EDIT RINCIAN PER HARI ===== --}}
        <form action="{{ $penggajian ? route('admin.gaji.slip.update', $penggajian->id) : '#' }}" method="POST">
            @csrf
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;" class="no-print">
                <span style="font-size:0.85rem;color:var(--text-secondary);">
                    <i class="fa-solid fa-circle-info" style="color:var(--primary-500);"></i>
                    Klik kolom untuk mengedit nilai <strong>Basic, Jam Lembur, Lembur (Rp), Makan, dan Kasbon</strong> per hari.
                </span>
                <button type="submit" class="btn btn-primary" style="padding:0.4rem 1rem;">
                    <i class="fa-solid fa-save"></i> Simpan Perubahan
                </button>
            </div>

            <div class="table-responsive" style="border-radius:var(--border-radius);border:1px solid var(--border-color);overflow:auto;">
                <table class="table-report">
                    <thead>
                        <tr>
                            <th rowspan="2">NO</th>
                            <th rowspan="2">HARI KERJA</th>
                            <th rowspan="2">TANGGAL</th>
                            <th colspan="2">JAM DATANG & PULANG</th>
                            <th rowspan="2">TOTAL<br>HARI</th>
                            <th rowspan="2">TOTAL GAJI<br>(Rp)</th>
                            <th rowspan="2">JAM<br>LEMBUR</th>
                            <th rowspan="2">TOTAL LEMBUR<br>(Rp)</th>
                            <th rowspan="2">UANG MAKAN<br>(Rp)</th>
                            <th rowspan="2">KASBON<br>(Rp)</th>
                            <th rowspan="2">JUMLAH</th>
                        </tr>
                        <tr>
                            <th>IN</th>
                            <th>OUT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandHari = 0; $grandJamLembur = 0;
                            $grandGaji = 0; $grandLembur = 0; $grandMakan = 0;
                            $grandKasbon = 0; $grandJumlah = 0;
                        @endphp

                        @forelse($absensis as $index => $abs)
                        @php
                            $realJamLembur = $abs->jam_lembur;
                            if ($abs->jam_keluar) {
                                $keluarC = \Carbon\Carbon::parse($abs->jam_keluar);
                                $batas = \Carbon\Carbon::parse('17:00:00');
                                if ($keluarC->gt($batas)) {
                                    $realJamLembur = (int) floor(abs($keluarC->diffInMinutes($batas)) / 60);
                                }
                            }

                            $realTotalHari = $abs->total_hari;
                            if (\Carbon\Carbon::parse($abs->tanggal)->isSunday() && $abs->status === 'Hadir') {
                                $realTotalHari = 1.5;
                            }

                            $basicDefault  = $realTotalHari * $user->gaji_pokok_harian;
                            $lemburDefault = $realJamLembur * $user->uang_lembur_per_jam;
                            $makanDefault  = $abs->dapat_uang_makan ? $user->uang_makan_harian : 0;

                            $valBasic  = $abs->nominal_basic  !== null ? $abs->nominal_basic  : $basicDefault;
                            
                            // Koreksi otomatis jika di database masih tersimpan perhitungan lama (1x gaji pokok padahal hari Minggu)
                            if (\Carbon\Carbon::parse($abs->tanggal)->isSunday() && $valBasic == $user->gaji_pokok_harian) {
                                $valBasic = $basicDefault;
                            }

                            $valLembur = $abs->nominal_lembur !== null ? $abs->nominal_lembur : $lemburDefault;
                            $valMakan  = $abs->nominal_makan  !== null ? $abs->nominal_makan  : $makanDefault;
                            $valKasbon = $abs->nominal_kasbon !== null ? $abs->nominal_kasbon : 0;
                            $jumlah    = ($valBasic + $valLembur + $valMakan) - $valKasbon;

                            $grandHari   += $realTotalHari;
                            $grandJamLembur += $realJamLembur;
                            $grandGaji   += $valBasic;
                            $grandLembur += $valLembur;
                            $grandMakan  += $valMakan;
                            $grandKasbon += $valKasbon;
                            $grandJumlah += $jumlah;
                        @endphp
                        <tr>
                            <td style="color:var(--text-muted);">{{ $index + 1 }}</td>
                            <td style="font-weight:600;text-transform:uppercase;color:var(--primary-600);">{{ \Carbon\Carbon::parse($abs->tanggal)->locale('id')->isoFormat('dddd') }}</td>
                            <td style="color:var(--text-secondary);">{{ \Carbon\Carbon::parse($abs->tanggal)->format('d-M-y') }}</td>
                            <td>{{ $abs->jam_masuk  ? substr($abs->jam_masuk, 0, 5)  : '-' }}</td>
                            <td>{{ $abs->jam_keluar ? substr($abs->jam_keluar, 0, 5) : '-' }}</td>
                            <td style="font-weight:700;color:var(--primary-600);">{{ $realTotalHari }}</td>
                            <td><input type="number" name="absensi[{{ $abs->id }}][basic]"  value="{{ (int)$valBasic }}"  class="input-rupiah"></td>
                            <td><input type="number" name="absensi[{{ $abs->id }}][jam_lembur]" value="{{ $realJamLembur ?: 0 }}" class="input-jam" data-rate="{{ $user->uang_lembur_per_jam }}" style="width: 50px; text-align: center; border: 1px solid var(--border-color); border-radius: 4px; padding: 0.3rem;" min="0" step="1"></td>
                            <td><input type="number" name="absensi[{{ $abs->id }}][lembur]" value="{{ (int)$valLembur }}" class="input-rupiah"></td>
                            <td><input type="number" name="absensi[{{ $abs->id }}][makan]"  value="{{ (int)$valMakan }}"  class="input-rupiah"></td>
                            <td><input type="number" name="absensi[{{ $abs->id }}][kasbon]" value="{{ (int)$valKasbon }}" class="input-rupiah" style="color:var(--danger);"></td>
                            <td style="font-weight:800;" class="jumlah-col">{{ number_format($jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" style="text-align:center;padding:2rem;color:var(--text-muted);">
                                Tidak ada data absensi untuk periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align:right;padding-right:1rem;">GRAND TOTAL</th>
                            <th>{{ number_format($grandHari, 1, ',', '.') }}</th>
                            <th id="footer-grand-gaji">{{ number_format($grandGaji, 0, ',', '.') }}</th>
                            <th id="footer-grand-jam-lembur">{{ $grandJamLembur }}</th>
                            <th id="footer-grand-lembur">{{ number_format($grandLembur, 0, ',', '.') }}</th>
                            <th id="footer-grand-makan">{{ number_format($grandMakan, 0, ',', '.') }}</th>
                            <th style="color:var(--danger);" id="footer-grand-kasbon">{{ $grandKasbon > 0 ? number_format($grandKasbon, 0, ',', '.') : '-' }}</th>
                            <th style="font-size:0.95rem;color:var(--success);" id="footer-grand-jumlah">{{ number_format($grandJumlah, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>

        @else
        {{-- ===== STATE KOSONG ===== --}}
        <div class="info-card" style="opacity: 0.6; pointer-events: none;">
            <div class="info-group">
                <div class="info-row"><span class="info-label">NAMA KARYAWAN</span><span class="info-value">-</span></div>
                <div class="info-row"><span class="info-label">BASIC / HARI</span><span class="info-value">Rp 0</span></div>
                <div class="info-row"><span class="info-label">UANG LEMBUR / JAM</span><span class="info-value">Rp 0</span></div>
                <div class="info-row"><span class="info-label">UANG MAKAN / HARI</span><span class="info-value">Rp 0</span></div>
            </div>
            <div class="info-group">
                <div class="info-highlight">
                    <span style="font-size:0.82rem;font-weight:600;color:var(--text-secondary);">TOTAL GAJI DITERIMA</span>
                    <span id="header-total-gaji">Rp 0</span>
                </div>
                <div class="info-row"><span class="info-label">TOTAL KASBON</span><span class="info-value" style="color:var(--danger);">-</span></div>
                <div class="info-row"><span class="info-label">PERIODE</span><span class="info-value">-</span></div>
            </div>
        </div>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;" class="no-print">
            <span style="font-size:0.85rem;color:var(--text-secondary);">
                <i class="fa-solid fa-circle-info" style="color:var(--primary-500);"></i>
                Silakan pilih karyawan terlebih dahulu untuk melihat dan mengedit rincian gaji.
            </span>
            <button type="button" class="btn btn-primary" style="padding:0.4rem 1rem; opacity: 0.5; cursor: not-allowed;">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>

        <div class="table-responsive" style="border-radius:var(--border-radius);border:1px solid var(--border-color);overflow:auto; opacity: 0.6;">
            <table class="table-report">
                <thead>
                    <tr>
                        <th rowspan="2">NO</th>
                        <th rowspan="2">HARI KERJA</th>
                        <th rowspan="2">TANGGAL</th>
                        <th colspan="2">JAM DATANG & PULANG</th>
                        <th rowspan="2">TOTAL<br>HARI</th>
                        <th rowspan="2">TOTAL GAJI<br>(Rp)</th>
                        <th rowspan="2">JAM<br>LEMBUR</th>
                        <th rowspan="2">TOTAL LEMBUR<br>(Rp)</th>
                        <th rowspan="2">UANG MAKAN<br>(Rp)</th>
                        <th rowspan="2">KASBON<br>(Rp)</th>
                        <th rowspan="2">JUMLAH</th>
                    </tr>
                    <tr>
                        <th>IN</th>
                        <th>OUT</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="12" style="text-align:center;padding:2rem;color:var(--text-muted);">
                            Belum ada karyawan yang dipilih.
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align:right;padding-right:1rem;">GRAND TOTAL</th>
                        <th>0</th>
                        <th>0</th>
                        <th>0</th>
                        <th>0</th>
                        <th>0</th>
                        <th style="color:var(--danger);">-</th>
                        <th style="font-size:0.95rem;color:var(--success);">0</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.input-rupiah');

    function calculateGrandTotals() {
        let totalGaji = 0;
        let totalLembur = 0;
        let totalMakan = 0;
        let totalKasbon = 0;
        let totalJumlah = 0;
        let totalJamLembur = 0;

        document.querySelectorAll('tbody tr').forEach(function(row) {
            const cols = row.querySelectorAll('.input-rupiah');
            if(cols.length === 4) {
                const basic  = parseFloat(cols[0].value) || 0;
                const lembur = parseFloat(cols[1].value) || 0;
                const makan  = parseFloat(cols[2].value) || 0;
                const kasbon = parseFloat(cols[3].value) || 0;
                const jamLembur = parseFloat(row.querySelector('.input-jam')?.value) || 0;
                
                totalGaji += basic;
                totalLembur += lembur;
                totalMakan += makan;
                totalKasbon += kasbon;
                totalJamLembur += jamLembur;
                totalJumlah += (basic + lembur + makan - kasbon);
            }
        });

        // Update footer
        if(document.getElementById('footer-grand-gaji')) document.getElementById('footer-grand-gaji').textContent = totalGaji.toLocaleString('id-ID');
        if(document.getElementById('footer-grand-jam-lembur')) document.getElementById('footer-grand-jam-lembur').textContent = totalJamLembur;
        if(document.getElementById('footer-grand-lembur')) document.getElementById('footer-grand-lembur').textContent = totalLembur.toLocaleString('id-ID');
        if(document.getElementById('footer-grand-makan')) document.getElementById('footer-grand-makan').textContent = totalMakan.toLocaleString('id-ID');
        if(document.getElementById('footer-grand-kasbon')) document.getElementById('footer-grand-kasbon').textContent = totalKasbon > 0 ? totalKasbon.toLocaleString('id-ID') : '-';
        if(document.getElementById('footer-grand-jumlah')) document.getElementById('footer-grand-jumlah').textContent = totalJumlah.toLocaleString('id-ID');

        // Update header
        if(document.getElementById('header-total-gaji')) document.getElementById('header-total-gaji').textContent = 'Rp ' + totalJumlah.toLocaleString('id-ID');
        if(document.getElementById('header-total-kasbon')) document.getElementById('header-total-kasbon').textContent = totalKasbon > 0 ? 'Rp ' + totalKasbon.toLocaleString('id-ID') : '-';
    }

    inputs.forEach(function (input) {
        input.addEventListener('input', function () {
            const row = this.closest('tr');
            const cols = row.querySelectorAll('.input-rupiah');
            const basic  = parseFloat(cols[0]?.value) || 0;
            const lembur = parseFloat(cols[1]?.value) || 0;
            const makan  = parseFloat(cols[2]?.value) || 0;
            const kasbon = parseFloat(cols[3]?.value) || 0;
            const jumlah = basic + lembur + makan - kasbon;

            const jumlahCol = row.querySelector('.jumlah-col');
            if (jumlahCol) {
                jumlahCol.textContent = jumlah.toLocaleString('id-ID');
            }

            calculateGrandTotals();
        });
    });

    document.querySelectorAll('.input-jam').forEach(function(input) {
        input.addEventListener('input', function() {
            const row = this.closest('tr');
            const rate = parseFloat(this.getAttribute('data-rate')) || 0;
            const hours = parseFloat(this.value) || 0;
            const totalLemburInput = row.querySelectorAll('.input-rupiah')[1];
            
            if (totalLemburInput) {
                totalLemburInput.value = hours * rate;
                // Trigger input event to update grand totals
                totalLemburInput.dispatchEvent(new Event('input'));
            }
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const hiddenUserId = document.getElementById('hiddenUserId');
    const form = searchInput.closest('form');

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

    searchInput.addEventListener('change', function() {
        if (!this.value) {
            hiddenUserId.value = '';
            return;
        }
        const foundId = findMatch();
        if (foundId) {
            hiddenUserId.value = foundId;
            form.submit();
        }
    });

    form.addEventListener('submit', function(e) {
        if (searchInput.value) {
            const foundId = findMatch();
            if (foundId) {
                hiddenUserId.value = foundId;
            } else {
                e.preventDefault();
                if (typeof showToast !== 'undefined') {
                    showToast("Karyawan tidak ditemukan. Silakan ketik nama atau NIK dengan benar.", "danger");
                } else {
                    alert("Karyawan tidak ditemukan. Silakan ketik nama atau NIK dengan benar.");
                }
            }
        } else {
            e.preventDefault();
            if (typeof showToast !== 'undefined') {
                showToast("Silakan pilih karyawan terlebih dahulu.", "warning");
            } else {
                alert("Silakan pilih karyawan terlebih dahulu.");
            }
        }
    });
});
</script>
@endpush
