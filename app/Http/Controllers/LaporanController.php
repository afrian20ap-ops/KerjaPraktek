<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LaporanLapangan;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LaporanController extends Controller
{
    protected CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    // ==========================================
    // ADMIN
    // ==========================================

    public function indexAdmin(Request $request)
    {
        $tanggalDari   = $request->get('tanggal_dari', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSampai = $request->get('tanggal_sampai', now()->format('Y-m-d'));
        $karyawanId    = $request->get('karyawan_id');
        $status        = $request->get('status');

        $query = LaporanLapangan::with('user')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($karyawanId) $query->where('user_id', $karyawanId);
        if ($status)     $query->where('status', $status);

        $laporan  = $query->orderByDesc('tanggal')->get();
        $karyawan = User::where('role', 'karyawan')->orderBy('name')->get();

        return view('admin.laporan.index', compact('laporan', 'karyawan', 'tanggalDari', 'tanggalSampai'));
    }

    public function approveAdmin(Request $request, $id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        $laporan->update(['status' => 'Disetujui', 'catatan' => null]);
        return redirect()->route('admin.laporan')->with('success', 'Status laporan berhasil diperbarui.');
    }

    // ==========================================
    // SUPERVISI
    // ==========================================

    public function indexSupervisi(Request $request)
    {
        $tanggalDari   = $request->get('tanggal_dari', now()->subDays(30)->format('Y-m-d'));
        $tanggalSampai = $request->get('tanggal_sampai', now()->format('Y-m-d'));
        $karyawanId    = $request->get('karyawan_id');
        $status        = $request->get('status');

        $query = LaporanLapangan::with('user')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($karyawanId) $query->where('user_id', $karyawanId);
        if ($status)     $query->where('status', $status);

        $laporan  = $query->orderByDesc('tanggal')->get();
        $karyawan = User::where('role', 'karyawan')->orderBy('name')->get();

        return view('supervisi.laporan.index', compact('laporan', 'karyawan', 'tanggalDari', 'tanggalSampai'));
    }

    public function approveSupervisi(Request $request, $id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        $laporan->update(['status' => 'Disetujui', 'catatan' => null]);
        return redirect()->route('supervisi.laporan')->with('success', 'Status laporan berhasil diperbarui.');
    }

    // ==========================================
    // DOWNLOAD XLSX  (Admin & Supervisi)
    // ==========================================

    /**
     * Generate & download laporan sebagai XLSX.
     * Pastikan file  scripts/generate_laporan_xlsx.py  ada di root project.
     * Install Python deps: pip install openpyxl requests Pillow
     */
    public function downloadXlsx($id)
    {
        $laporan = LaporanLapangan::with('user')->findOrFail($id);
        $xlsxBytes = $this->generateExcelBytes($laporan);

        $filename = 'Laporan_'
            . Str::slug($laporan->user->name ?? 'karyawan')
            . '_' . Carbon::parse($laporan->tanggal)->format('Y-m-d')
            . '.xlsx';

        return response($xlsxBytes, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($xlsxBytes),
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function downloadBulkXlsx(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada laporan yang dipilih.');
        }

        $laporans = LaporanLapangan::with('user')->whereIn('id', $ids)->get();
        if ($laporans->isEmpty()) {
            return redirect()->back()->with('error', 'Laporan tidak ditemukan.');
        }

        if ($laporans->count() === 1) {
            return $this->downloadXlsx($laporans->first()->id);
        }

        $xlsxBytes = $this->generateBulkExcelBytes($laporans);
        $filename = 'Laporan_Bulk_' . now()->format('Ymd_His') . '.xlsx';

        return response($xlsxBytes, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => strlen($xlsxBytes),
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
        ]);
    }

    private function generateBulkExcelBytes($laporans)
    {
        // Gather all photos
        $combinedFotoPaths = [];
        $combinedFotoDescs = [];
        $names = [];
        $first = $laporans->first();
        
        foreach ($laporans as $laporan) {
            $uName = $laporan->user->name ?? 'Karyawan';
            if (!in_array($uName, $names)) {
                $names[] = $uName;
            }
            if (is_array($laporan->foto_paths)) {
                foreach ($laporan->foto_paths as $idx => $path) {
                    $combinedFotoPaths[] = $path;
                    $combinedFotoDescs[] = ($laporan->foto_deskripsis[$idx] ?? '') . ' (' . $uName . ')';
                }
            }
        }
        
        $totalPhotos = count($combinedFotoPaths);
        $totalPages = max(1, ceil($totalPhotos / 8));

        // Create spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Gabungan');

        // Page settings
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(0.5);
        foreach (range('B', 'N') as $col) {
            $sheet->getColumnDimension($col)->setWidth(9.71);
        }

        $mediumBorder = [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            'color' => ['rgb' => '000000'],
        ];
        $noBorder = [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
        ];

        $setOuterBorder = function($sheet, $minRow, $maxRow, $minCol, $maxCol) use ($mediumBorder, $noBorder) {
            for ($r = $minRow; $r <= $maxRow; $r++) {
                for ($c = $minCol; $c <= $maxCol; $c++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                    $cell = $sheet->getCell($colLetter . $r);
                    
                    $borders = [];
                    $borders['left']   = ($c == $minCol)  ? $mediumBorder : $noBorder;
                    $borders['right']  = ($c == $maxCol)  ? $mediumBorder : $noBorder;
                    $borders['top']    = ($r == $minRow)  ? $mediumBorder : $noBorder;
                    $borders['bottom'] = ($r == $maxRow)  ? $mediumBorder : $noBorder;
                    
                    $cell->getStyle()->getBorders()->applyFromArray($borders);
                }
            }
        };

        $tempFiles = [];
        $ROWS_PER_REPORT = 70; 

        for ($page = 0; $page < $totalPages; $page++) {
            $offset = $page * $ROWS_PER_REPORT;
            
            $pageFotoPaths = array_slice($combinedFotoPaths, $page * 8, 8);
            $pageFotoDescs = array_slice($combinedFotoDescs, $page * 8, 8);

            $data = [
                'nama'             => implode(', ', $names),
                'lokasi'           => $first->lokasi ?? '-',
                'tanggal'          => Carbon::parse($first->tanggal)->format('d-m-Y'),
                'judul'            => 'LAPORAN INSPEKSI PEKERJAAN',
                'diajukan_oleh'    => 'CV. Garuda Jaya',
                'diperiksa_oleh_1' => 'CV. Titian Mahakarya',
                'diperiksa_oleh_2' => 'PT.',
                'foto_paths'       => $pageFotoPaths,
                'foto_deskripsis'  => $pageFotoDescs,
            ];

            // Print area
            if ($page === $totalPages - 1) {
                $maxRow = $offset + 65;
                $sheet->getPageSetup()->setPrintArea('A1:N' . $maxRow);
            }

            // Row heights
            $labelRows = [5, 6, 18, 19, 31, 32, 44, 45];
            $photoRows = [];
            foreach (range(7, 17) as $r) $photoRows[] = $r;
            foreach (range(20, 30) as $r) $photoRows[] = $r;
            foreach (range(33, 43) as $r) $photoRows[] = $r;
            foreach (range(46, 56) as $r) $photoRows[] = $r;

            $photoRowsSet = array_flip($photoRows);
            $labelRowsSet = array_flip($labelRows);

            for ($r = 1; $r <= 70; $r++) {
                $actualRow = $r + $offset;
                if (isset($labelRowsSet[$r])) {
                    $sheet->getRowDimension($actualRow)->setRowHeight(16);
                } elseif (isset($photoRowsSet[$r])) {
                    $sheet->getRowDimension($actualRow)->setRowHeight(20);
                } else {
                    $sheet->getRowDimension($actualRow)->setRowHeight(15);
                }
            }

            // Header - B:C Logo
            $sheet->mergeCells('B'.(1+$offset).':C'.(4+$offset));
            $sheet->setCellValue('B'.(1+$offset), 'Logo');
            $sheet->getStyle('B'.(1+$offset))->getFont()->setName('Calibri')->setSize(11);
            $sheet->getStyle('B'.(1+$offset))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B'.(1+$offset))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $setOuterBorder($sheet, 1+$offset, 4+$offset, 2, 3);

            // Header - D:K Title
            $sheet->mergeCells('D'.(1+$offset).':K'.(4+$offset));
            // Show up to 3 names, if more append "dkk"
            $displayName = count($names) > 3 ? implode(', ', array_slice($names, 0, 3)) . ' dkk' : $data['nama'];
            $sheet->setCellValue('D'.(1+$offset), $data['judul'] . "\nTim: " . $displayName . "\nLokasi: " . $data['lokasi']);
            $sheet->getStyle('D'.(1+$offset))->getFont()->setName('Calibri')->setBold(true)->setSize(14);
            $sheet->getStyle('D'.(1+$offset))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D'.(1+$offset))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('D'.(1+$offset))->getAlignment()->setWrapText(true);
            $setOuterBorder($sheet, 1+$offset, 4+$offset, 4, 11);

            // Header - L:M
            $sheet->mergeCells('L'.(1+$offset).':M'.(4+$offset));
            $sheet->getStyle('L'.(1+$offset))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L'.(1+$offset))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $setOuterBorder($sheet, 1+$offset, 4+$offset, 12, 13);

            // Photo Sections
            $sections = [
                ['dr1' => 5,  'dr2' => 6,  'pr1' => 7,  'pr2' => 17],
                ['dr1' => 18, 'dr2' => 19, 'pr1' => 20, 'pr2' => 30],
                ['dr1' => 31, 'dr2' => 32, 'pr1' => 33, 'pr2' => 43],
                ['dr1' => 44, 'dr2' => 45, 'pr1' => 46, 'pr2' => 56],
            ];

            $sides = [
                ['outer_min' => 2, 'outer_max' => 7, 'photo_min' => 3, 'photo_max' => 6, 'anchor' => 'C'],
                ['outer_min' => 8, 'outer_max' => 13, 'photo_min' => 9, 'photo_max' => 12, 'anchor' => 'I'],
            ];

            $fotoPaths = $data['foto_paths'];
            $fotoDescs = $data['foto_deskripsis'];
            $fotoIdx = 0;
            $BOX_W = 272;
            $BOX_H = 264;

            foreach ($sections as $sec) {
                $dr1 = $sec['dr1'] + $offset;
                $dr2 = $sec['dr2'] + $offset;
                $pr1 = $sec['pr1'] + $offset;
                $pr2 = $sec['pr2'] + $offset;

                foreach ($sides as $sd) {
                    $outerMin = $sd['outer_min'];
                    $outerMax = $sd['outer_max'];
                    $photoMin = $sd['photo_min'];
                    $photoMax = $sd['photo_max'];
                    $anchor   = $sd['anchor'];

                    $descText = $fotoDescs[$fotoIdx] ?? '';

                    $setOuterBorder($sheet, $dr1, $pr2, $outerMin, $outerMax);

                    $outerAnchorLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($outerMin);
                    $outerEndLetter    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($outerMax);
                    $descRange = "{$outerAnchorLetter}{$dr1}:{$outerEndLetter}{$dr2}";
                    $sheet->mergeCells($descRange);
                    
                    $dc = $sheet->getCell("{$outerAnchorLetter}{$dr1}");
                    $dc->setValue("Deskripsi :  " . $descText);
                    $dc->getStyle()->getFont()->setName('Calibri')->setSize(11);
                    $dc->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $dc->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $dc->getStyle()->getAlignment()->setWrapText(true);

                    $photoEndLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($photoMax);
                    $photoRange = "{$anchor}{$pr1}:{$photoEndLetter}{$pr2}";
                    $sheet->mergeCells($photoRange);
                    
                    $pc = $sheet->getCell("{$anchor}{$pr1}");
                    $pc->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $pc->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    if ($fotoIdx < count($fotoPaths) && $fotoPaths[$fotoIdx]) {
                        $imgInfo = $this->resizeAndSaveTempImage($fotoPaths[$fotoIdx], $BOX_W, $BOX_H);
                        if ($imgInfo) {
                            $tempFiles[] = $imgInfo['path'];
                            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                            $drawing->setPath($imgInfo['path']);
                            $drawing->setWidth($imgInfo['width']);
                            $drawing->setHeight($imgInfo['height']);
                            $drawing->setCoordinates("{$anchor}{$pr1}");
                            
                            $cellW_px = 272;
                            $cellH_px = 264;
                            $offsetX = (int)(($cellW_px - $imgInfo['width']) / 2);
                            $offsetY = (int)(($cellH_px - $imgInfo['height']) / 2);
                            if ($offsetX > 0) $drawing->setOffsetX($offsetX);
                            if ($offsetY > 0) $drawing->setOffsetY($offsetY);
                            $drawing->setWorksheet($sheet);
                        } else {
                            $pc->setValue('[Foto tidak tersedia]');
                        }
                    }
                    $fotoIdx++;
                }
            }

            // Footer
            $tanggalStr = $data['tanggal'];
            $sheet->mergeCells('K'.(57+$offset).':K'.(58+$offset));
            $sheet->setCellValue('K'.(57+$offset), 'Jakarta,');
            $sheet->getStyle('K'.(57+$offset))->getFont()->setName('Calibri')->setBold(true)->setSize(11);
            $sheet->getStyle('K'.(57+$offset))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('K'.(57+$offset))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('K'.(57+$offset))->getBorders()->getTop()->applyFromArray($mediumBorder);

            $sheet->mergeCells('L'.(57+$offset).':M'.(58+$offset));
            $sheet->setCellValue('L'.(57+$offset), $tanggalStr);
            $sheet->getStyle('L'.(57+$offset))->getFont()->setName('Calibri')->setBold(true)->setSize(11);
            $sheet->getStyle('L'.(57+$offset))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('L'.(57+$offset))->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $sheet->getStyle('L'.(57+$offset))->getBorders()->getTop()->applyFromArray($mediumBorder);

            $footers = [
                ['ref' => 'B'.(59+$offset), 'txt' => 'Diajukan Oleh,'],
                ['ref' => 'F'.(59+$offset), 'txt' => 'Diperiksa Oleh,'],
                ['ref' => 'K'.(59+$offset), 'txt' => 'Diperiksa Oleh,'],
            ];
            foreach ($footers as $f) {
                $sheet->setCellValue($f['ref'], $f['txt']);
                $sheet->getStyle($f['ref'])->getFont()->setName('Calibri')->setSize(11);
            }

            $lineRanges = ['B'.(63+$offset).':D'.(63+$offset), 'F'.(63+$offset).':H'.(63+$offset), 'K'.(63+$offset).':M'.(63+$offset)];
            foreach ($lineRanges as $rng) {
                $sheet->mergeCells($rng);
                $parts = explode(':', $rng);
                $sheet->getStyle($parts[0])->getBorders()->getBottom()->applyFromArray($mediumBorder);
            }

            $signNames = [
                ['ref' => 'B'.(65+$offset), 'key' => 'diajukan_oleh', 'def' => 'CV. Garuda Jaya'],
                ['ref' => 'F'.(65+$offset), 'key' => 'diperiksa_oleh_1', 'def' => 'CV. Titian Mahakarya'],
                ['ref' => 'K'.(65+$offset), 'key' => 'diperiksa_oleh_2', 'def' => 'PT.'],
            ];
            foreach ($signNames as $sn) {
                $sheet->setCellValue($sn['ref'], $data[$sn['key']] ?? $sn['def']);
                $sheet->getStyle($sn['ref'])->getFont()->setName('Calibri')->setBold(true)->setSize(11);
            }

            // Add page break at the end of each page (between tables)
            if ($page < $totalPages - 1) {
                $sheet->setBreak('A' . (67+$offset), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
            }
        }

        // Save to stream
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        ob_start();
        $writer->save('php://output');
        $xlsxBytes = ob_get_clean();

        // Cleanup temp files
        foreach ($tempFiles as $f) {
            if (file_exists($f)) @unlink($f);
        }

        return $xlsxBytes;
    }

    private function generateExcelBytes($laporan)
    {
        $data = [
            'nama'             => $laporan->user->name ?? '-',
            'lokasi'           => $laporan->lokasi ?? '-',
            'tanggal'          => Carbon::parse($laporan->tanggal)->format('d-m-Y'),
            'judul'            => 'LAPORAN INSPEKSI PEKERJAAN',
            'diajukan_oleh'    => 'CV. Garuda Jaya',
            'diperiksa_oleh_1' => 'CV. Titian Mahakarya',
            'diperiksa_oleh_2' => 'PT.',
            'foto_paths'       => $laporan->foto_paths      ?? [],
            'foto_deskripsis'  => $laporan->foto_deskripsis ?? [],
        ];

        // Create spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan');

        // Page settings
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setPrintArea('A1:N65');

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(0.5);
        foreach (range('B', 'N') as $col) {
            $sheet->getColumnDimension($col)->setWidth(9.71);
        }

        // Row heights
        $labelRows = [5, 6, 18, 19, 31, 32, 44, 45];
        $photoRows = [];
        foreach (range(7, 17) as $r) $photoRows[] = $r;
        foreach (range(20, 30) as $r) $photoRows[] = $r;
        foreach (range(33, 43) as $r) $photoRows[] = $r;
        foreach (range(46, 56) as $r) $photoRows[] = $r;

        $photoRowsSet = array_flip($photoRows);
        $labelRowsSet = array_flip($labelRows);

        for ($r = 1; $r <= 70; $r++) {
            if (isset($labelRowsSet[$r])) {
                $sheet->getRowDimension($r)->setRowHeight(16);
            } elseif (isset($photoRowsSet[$r])) {
                $sheet->getRowDimension($r)->setRowHeight(20);
            } else {
                $sheet->getRowDimension($r)->setRowHeight(15);
            }
        }

        // Medium Border Style
        $mediumBorder = [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            'color' => ['rgb' => '000000'],
        ];
        $noBorder = [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
        ];

        $setOuterBorder = function($sheet, $minRow, $maxRow, $minCol, $maxCol) use ($mediumBorder, $noBorder) {
            for ($r = $minRow; $r <= $maxRow; $r++) {
                for ($c = $minCol; $c <= $maxCol; $c++) {
                    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                    $cell = $sheet->getCell($colLetter . $r);
                    
                    $borders = [];
                    $borders['left']   = ($c == $minCol)  ? $mediumBorder : $noBorder;
                    $borders['right']  = ($c == $maxCol)  ? $mediumBorder : $noBorder;
                    $borders['top']    = ($r == $minRow)  ? $mediumBorder : $noBorder;
                    $borders['bottom'] = ($r == $maxRow)  ? $mediumBorder : $noBorder;
                    
                    $cell->getStyle()->getBorders()->applyFromArray($borders);
                }
            }
        };

        // Header - B1:C4 (Logo placeholder)
        $sheet->mergeCells('B1:C4');
        $sheet->setCellValue('B1', 'Logo');
        $sheet->getStyle('B1')->getFont()->setName('Calibri')->setSize(11);
        $sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $setOuterBorder($sheet, 1, 4, 2, 3);

        // Header - D1:K4 (Title)
        $sheet->mergeCells('D1:K4');
        $sheet->setCellValue('D1', $data['judul']);
        $sheet->getStyle('D1')->getFont()->setName('Calibri')->setBold(true)->setSize(16);
        $sheet->getStyle('D1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $setOuterBorder($sheet, 1, 4, 4, 11);

        // Header - L1:M4
        $sheet->mergeCells('L1:M4');
        $sheet->getStyle('L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $setOuterBorder($sheet, 1, 4, 12, 13);

        // Logo image
        $tempFiles = [];
        $logoUrl = $data['logo_url'] ?? '';
        if ($logoUrl) {
            $logoInfo = $this->resizeAndSaveTempImage($logoUrl, 173, 173);
            if ($logoInfo) {
                $tempFiles[] = $logoInfo['path'];
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath($logoInfo['path']);
                $drawing->setWidth($logoInfo['width']);
                $drawing->setHeight($logoInfo['height']);
                $drawing->setCoordinates('L1');
                $drawing->setWorksheet($sheet);
            }
        }

        // Photo Sections
        $sections = [
            ['dr1' => 5,  'dr2' => 6,  'pr1' => 7,  'pr2' => 17],
            ['dr1' => 18, 'dr2' => 19, 'pr1' => 20, 'pr2' => 30],
            ['dr1' => 31, 'dr2' => 32, 'pr1' => 33, 'pr2' => 43],
            ['dr1' => 44, 'dr2' => 45, 'pr1' => 46, 'pr2' => 56],
        ];

        $sides = [
            ['outer_min' => 2, 'outer_max' => 7, 'photo_min' => 3, 'photo_max' => 6, 'anchor' => 'C'],
            ['outer_min' => 8, 'outer_max' => 13, 'photo_min' => 9, 'photo_max' => 12, 'anchor' => 'I'],
        ];

        $fotoPaths = $data['foto_paths'];
        $fotoDescs = $data['foto_deskripsis'];
        $fotoIdx = 0;

        // Box size in px
        $BOX_W = 272;
        $BOX_H = 264;

        foreach ($sections as $sec) {
            $dr1 = $sec['dr1'];
            $dr2 = $sec['dr2'];
            $pr1 = $sec['pr1'];
            $pr2 = $sec['pr2'];

            foreach ($sides as $sd) {
                $outerMin = $sd['outer_min'];
                $outerMax = $sd['outer_max'];
                $photoMin = $sd['photo_min'];
                $photoMax = $sd['photo_max'];
                $anchor   = $sd['anchor'];

                $descText = $fotoDescs[$fotoIdx] ?? '';

                // Outer border (B:G or H:M)
                $setOuterBorder($sheet, $dr1, $pr2, $outerMin, $outerMax);

                // Merge description row
                $outerAnchorLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($outerMin);
                $outerEndLetter    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($outerMax);
                $descRange = "{$outerAnchorLetter}{$dr1}:{$outerEndLetter}{$dr2}";
                $sheet->mergeCells($descRange);
                
                $dc = $sheet->getCell("{$outerAnchorLetter}{$dr1}");
                $dc->setValue("Deskripsi :  " . $descText);
                $dc->getStyle()->getFont()->setName('Calibri')->setSize(12);
                $dc->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $dc->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // Merge photo area (C:F or I:L)
                $photoEndLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($photoMax);
                $photoRange = "{$anchor}{$pr1}:{$photoEndLetter}{$pr2}";
                $sheet->mergeCells($photoRange);
                
                $pc = $sheet->getCell("{$anchor}{$pr1}");
                $pc->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $pc->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // Insert image
                if ($fotoIdx < count($fotoPaths) && $fotoPaths[$fotoIdx]) {
                    $imgInfo = $this->resizeAndSaveTempImage($fotoPaths[$fotoIdx], $BOX_W, $BOX_H);
                    if ($imgInfo) {
                        $tempFiles[] = $imgInfo['path'];
                        
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setPath($imgInfo['path']);
                        $drawing->setWidth($imgInfo['width']);
                        $drawing->setHeight($imgInfo['height']);
                        $drawing->setCoordinates("{$anchor}{$pr1}");
                        
                        // Center drawing inside merged cell
                        $cellW_px = 272; // total width of the box C:F or I:L
                        $cellH_px = 264; // total height of the box 11 rows
                        $offsetX = (int)(($cellW_px - $imgInfo['width']) / 2);
                        $offsetY = (int)(($cellH_px - $imgInfo['height']) / 2);
                        if ($offsetX > 0) $drawing->setOffsetX($offsetX);
                        if ($offsetY > 0) $drawing->setOffsetY($offsetY);

                        $drawing->setWorksheet($sheet);
                    } else {
                        $pc->setValue('[Foto tidak tersedia]');
                    }
                }

                $fotoIdx++;
            }
        }

        // Footer
        $tanggalStr = $data['tanggal'];
        $sheet->mergeCells('K57:K58');
        $sheet->setCellValue('K57', 'Jakarta,');
        $sheet->getStyle('K57')->getFont()->setName('Calibri')->setBold(true)->setSize(11);
        $sheet->getStyle('K57')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('K57')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('K57')->getBorders()->getTop()->applyFromArray($mediumBorder);

        $sheet->mergeCells('L57:M58');
        $sheet->setCellValue('L57', $tanggalStr);
        $sheet->getStyle('L57')->getFont()->setName('Calibri')->setBold(true)->setSize(11);
        $sheet->getStyle('L57')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('L57')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('L57')->getBorders()->getTop()->applyFromArray($mediumBorder);

        $footers = [
            ['ref' => 'B59', 'txt' => 'Diajukan Oleh,'],
            ['ref' => 'F59', 'txt' => 'Diperiksa Oleh,'],
            ['ref' => 'K59', 'txt' => 'Diperiksa Oleh,'],
        ];
        foreach ($footers as $f) {
            $sheet->setCellValue($f['ref'], $f['txt']);
            $sheet->getStyle($f['ref'])->getFont()->setName('Calibri')->setSize(11);
        }

        $lineRanges = ['B63:D63', 'F63:H63', 'K63:M63'];
        foreach ($lineRanges as $rng) {
            $sheet->mergeCells($rng);
            $parts = explode(':', $rng);
            $sheet->getStyle($parts[0])->getBorders()->getBottom()->applyFromArray($mediumBorder);
        }

        $signNames = [
            ['ref' => 'B65', 'key' => 'diajukan_oleh', 'def' => 'CV. Garuda Jaya'],
            ['ref' => 'F65', 'key' => 'diperiksa_oleh_1', 'def' => 'CV. Titian Mahakarya'],
            ['ref' => 'K65', 'key' => 'diperiksa_oleh_2', 'def' => 'PT.'],
        ];
        foreach ($signNames as $sn) {
            $sheet->setCellValue($sn['ref'], $data[$sn['key']] ?? $sn['def']);
            $sheet->getStyle($sn['ref'])->getFont()->setName('Calibri')->setBold(true)->setSize(11);
        }

        // Save to stream
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        ob_start();
        $writer->save('php://output');
        $xlsxBytes = ob_get_clean();

        // Cleanup temp files
        foreach ($tempFiles as $f) {
            if (file_exists($f)) @unlink($f);
        }

        return $xlsxBytes;
    }

    private function resizeAndSaveTempImage($url, $boxW, $boxH)
    {
        try {
            $imgData = null;

            // Avoid single-threaded HTTP request deadlock on local servers (like php artisan serve)
            $baseUrl = asset('storage') . '/';
            if (str_starts_with($url, $baseUrl)) {
                $relativePath = str_replace($baseUrl, '', $url);
                $localPath = storage_path('app/public/' . $relativePath);
                if (file_exists($localPath)) {
                    $imgData = @file_get_contents($localPath);
                }
            }

            // Fallback for external URLs (like Cloudinary)
            if (!$imgData) {
                if (function_exists('curl_init')) {
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $imgData = curl_exec($ch);
                    curl_close($ch);
                }
                if (!$imgData) {
                    $imgData = @file_get_contents($url);
                }
            }

            if (!$imgData) return null;

            $srcImg = @imagecreatefromstring($imgData);
            if (!$srcImg) return null;

            $ow = imagesx($srcImg);
            $oh = imagesy($srcImg);

            $ratio = min($boxW / $ow, $boxH / $oh);
            $nw = (int)($ow * $ratio);
            $nh = (int)($oh * $ratio);

            $dstImg = imagecreatetruecolor($nw, $nh);
            
            imagealphablending($dstImg, false);
            imagesavealpha($dstImg, true);
            $transparent = imagecolorallocatealpha($dstImg, 255, 255, 255, 127);
            imagefilledrectangle($dstImg, 0, 0, $nw, $nh, $transparent);

            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $nw, $nh, $ow, $oh);

            $tmpFile = tempnam(sys_get_temp_dir(), 'img_') . '.jpg';
            imagejpeg($dstImg, $tmpFile, 85);

            imagedestroy($srcImg);
            imagedestroy($dstImg);

            return [
                'path' => $tmpFile,
                'width' => $nw,
                'height' => $nh,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    // ==========================================
    // KARYAWAN
    // ==========================================

    public function indexKaryawan(Request $request)
    {
        $userId     = session('user_id');
        $bulanTahun = $request->get('bulan_tahun', now()->format('Y-m'));

        $tanggalObj    = Carbon::createFromFormat('Y-m', $bulanTahun);
        $tanggalDari   = $tanggalObj->copy()->startOfMonth()->format('Y-m-d');
        $tanggalSampai = $tanggalObj->copy()->endOfMonth()->format('Y-m-d');

        $laporan = LaporanLapangan::where('user_id', $userId)
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->orderByDesc('tanggal')
            ->get();

        return view('karyawan.laporan.index', compact('laporan', 'tanggalDari', 'tanggalSampai', 'bulanTahun'));
    }

    public function storeKaryawan(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'lokasi'           => 'required|string|max:255',
            'foto'             => 'required|array|min:1|max:8',
            'foto.*'           => 'image|max:5120',
            'foto_deskripsi'   => 'nullable|array|max:8',
            'foto_deskripsi.*' => 'nullable|string|max:255',
        ]);

        $fotoUrls = $fotoDeskripsis = [];

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $idx => $file) {
                if (!$file || !$file->isValid()) continue;
                $fotoUrls[]       = $this->cloudinary->upload($file);
                $deskText         = $request->input('foto_deskripsi.' . $idx, '');
                $fotoDeskripsis[] = is_string($deskText) ? trim($deskText) : '';
            }
        }

        if (empty($fotoUrls)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Minimal harus ada 1 foto yang valid.'], 422);
            }
            return back()->withErrors(['foto' => 'Minimal harus ada 1 foto yang valid.'])->withInput();
        }

        LaporanLapangan::create([
            'user_id'             => session('user_id'),
            'tanggal'             => $request->tanggal,
            'lokasi'              => $request->lokasi,
            'deskripsi_pekerjaan' => '-',
            'foto_paths'          => $fotoUrls,
            'foto_deskripsis'     => $fotoDeskripsis,
            'status'              => 'Terkirim',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Laporan berhasil dikirim.',
                'redirect' => route('karyawan.laporan'),
            ]);
        }

        return redirect()->route('karyawan.laporan')->with('success', 'Laporan berhasil dikirim.');
    }

    public function editKaryawan($id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        abort_if($laporan->user_id !== session('user_id'), 403);
        abort_if($laporan->status === 'Disetujui', 403, 'Laporan sudah disetujui, tidak dapat diedit.');
        return view('karyawan.laporan.edit', compact('laporan'));
    }

    public function updateKaryawan(Request $request, $id)
    {
        $laporan = LaporanLapangan::findOrFail($id);
        abort_if($laporan->user_id !== session('user_id'), 403);
        abort_if($laporan->status === 'Disetujui', 403, 'Laporan sudah disetujui, tidak dapat diedit.');

        $request->validate([
            'tanggal'          => 'required|date',
            'lokasi'           => 'required|string|max:255',
            'foto'             => 'nullable|array|size:2',
            'foto.*'           => 'image|max:5120',
            'foto_deskripsi'   => 'nullable|array|size:2',
            'foto_deskripsi.*' => 'nullable|string|max:255',
            'removed_fotos'    => 'nullable|array',
            'removed_fotos.*'  => 'string',
        ]);

        $currentFotos      = $laporan->foto_paths      ?? [];
        $currentDeskripsis = $laporan->foto_deskripsis ?? [];

        if ($request->filled('removed_fotos')) {
            $removedIndexes = $request->removed_fotos;
            $newFotos = $newDeskripsis = [];
            foreach ($currentFotos as $i => $url) {
                if (!in_array((string)$i, $removedIndexes)) {
                    if (in_array($url, $removedIndexes)) { $this->cloudinary->delete($url); continue; }
                    $newFotos[]      = $url;
                    $newDeskripsis[] = $currentDeskripsis[$i] ?? '';
                } else {
                    $this->cloudinary->delete($url);
                }
            }
            $currentFotos      = $newFotos;
            $currentDeskripsis = $newDeskripsis;
        }

        $remainingSlots = 8 - count($currentFotos);
        if ($request->hasFile('foto')) {
            $uploadedCount = 0;
            foreach ($request->file('foto') as $idx => $file) {
                if ($uploadedCount >= $remainingSlots) break;
                $currentFotos[]      = $this->cloudinary->upload($file);
                $currentDeskripsis[] = $request->input('foto_deskripsi.' . $idx, '');
                $uploadedCount++;
            }
        }

        if ($request->has('existing_deskripsi')) {
            foreach ($request->input('existing_deskripsi', []) as $i => $desk) {
                if (isset($currentDeskripsis[$i])) $currentDeskripsis[$i] = $desk;
            }
        }

        if (empty($currentFotos)) {
            return back()->withErrors(['foto' => 'Minimal harus ada 1 foto.'])->withInput();
        }

        $laporan->update([
            'tanggal'             => $request->tanggal,
            'lokasi'              => $request->lokasi,
            'deskripsi_pekerjaan' => '-',
            'foto_paths'          => $currentFotos,
            'foto_deskripsis'     => $currentDeskripsis,
        ]);

        return redirect()->route('karyawan.laporan')->with('success', 'Laporan berhasil diperbarui.');
    }
}