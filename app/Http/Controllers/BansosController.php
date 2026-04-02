<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BansosController extends Controller
{
    /**
     * Deteksi tahun berdasarkan urutan data di tabel bansos_pangan.
     * Data dibagi per blok: setelah row 'Jawa Timur' = tahun berikutnya.
     * Tahun dimulai dari 2022 → 2023 → 2024.
     */
    private function getDataByYear(int $year): array
    {
        // Ambil semua data mentah sesuai urutan insert
        $allRows = DB::table('bansospangan')
            ->select('nama_kabupaten_kota', 'rencana_kpm', 'realisasi_kpm',
                     'rencana_anggaran', 'realisasi_anggaran', 'kode_kabupaten_kota')
            ->get()
            ->toArray();

        // Pisahkan menjadi blok per tahun
        $blocks = [];
        $current = [];
        foreach ($allRows as $row) {
            $current[] = $row;
            if (strtolower(trim($row->nama_kabupaten_kota)) === 'jawa timur') {
                $blocks[] = $current;
                $current  = [];
            }
        }
        // Sisa data yang belum ada 'Jawa Timur' (seharusnya tidak ada)
        if (!empty($current)) {
            $blocks[] = $current;
        }

        // Mapping: blok ke-0 → 2022, ke-1 → 2023, ke-2 → 2024
        $yearMap = [2022 => 0, 2023 => 1, 2024 => 2];
        $idx     = $yearMap[$year] ?? 2;

        return isset($blocks[$idx]) ? $blocks[$idx] : [];
    }

    public function economy(Request $request)
    {
        $selectedYear = (int) $request->get('tahun', 2024);
        $validYears   = [2022, 2023, 2024];
        if (!in_array($selectedYear, $validYears)) {
            $selectedYear = 2024;
        }

        $rawData = $this->getDataByYear($selectedYear);

        // Pisahkan data Jawa Timur (summary provinsi) & kabupaten/kota
        $provinsiRow = null;
        $kabData     = [];

        foreach ($rawData as $row) {
            $nama = trim($row->nama_kabupaten_kota);
            if (strtolower($nama) === 'jawa timur') {
                $provinsiRow = $row;
            } else {
                $kabData[] = $row;
            }
        }

        // ── KPI PROVINSI ─────────────────────────────────────────────
        $totalRencanaKpm    = $provinsiRow ? $provinsiRow->rencana_kpm      : 0;
        $totalRealisasiKpm  = $provinsiRow ? $provinsiRow->realisasi_kpm    : 0;
        $totalRencanaAng    = $provinsiRow ? $provinsiRow->rencana_anggaran  : 0;
        $totalRealisasiAng  = $provinsiRow ? $provinsiRow->realisasi_anggaran: 0;

        $pctKpm = $totalRencanaKpm  > 0
            ? round(($totalRealisasiKpm  / $totalRencanaKpm)  * 100, 1) : 0;
        $pctAng = $totalRencanaAng  > 0
            ? round(($totalRealisasiAng  / $totalRencanaAng)  * 100, 1) : 0;

        // ── PER-KABUPATEN ANALYTICS ───────────────────────────────────
        $kabAnalytics = [];
        foreach ($kabData as $row) {
            $rKpm  = (int) $row->rencana_kpm;
            $reKpm = (int) $row->realisasi_kpm;
            $rAng  = (float) $row->rencana_anggaran;
            $reAng = (float) $row->realisasi_anggaran;

            $pctKpmKab = $rKpm  > 0 ? round(($reKpm  / $rKpm)  * 100, 1) : 0;
            $pctAngKab = $rAng  > 0 ? round(($reAng  / $rAng)  * 100, 1) : 0;
            $nilaiPerKpm = $reKpm > 0 ? round($reAng / $reKpm) : 0;
            $gapKpm   = $rKpm  - $reKpm;
            $gapAng   = $rAng  - $reAng;

            $kabAnalytics[] = [
                'nama'        => trim($row->nama_kabupaten_kota),
                'kode'        => $row->kode_kabupaten_kota,
                'rencana_kpm'    => $rKpm,
                'realisasi_kpm'  => $reKpm,
                'rencana_ang'    => $rAng,
                'realisasi_ang'  => $reAng,
                'pct_kpm'     => $pctKpmKab,
                'pct_ang'     => $pctAngKab,
                'nilai_per_kpm'  => $nilaiPerKpm,
                'gap_kpm'     => $gapKpm,
                'gap_ang'     => $gapAng,
            ];
        }

        // ── KLASIFIKASI PERFORMA (berdasarkan % realisasi KPM) ────────
        $tinggi   = array_filter($kabAnalytics, fn($k) => $k['pct_kpm'] >= 95);
        $menengah = array_filter($kabAnalytics, fn($k) => $k['pct_kpm'] >= 70 && $k['pct_kpm'] < 95);
        $rendah   = array_filter($kabAnalytics, fn($k) => $k['pct_kpm'] < 70);

        // Sort masing-masing
        usort($tinggi,   fn($a, $b) => $b['pct_kpm'] <=> $a['pct_kpm']);
        usort($menengah, fn($a, $b) => $b['pct_kpm'] <=> $a['pct_kpm']);
        usort($rendah,   fn($a, $b) => $a['pct_kpm'] <=> $b['pct_kpm']);

        // ── TOP & BOTTOM SERAPAN ANGGARAN ─────────────────────────────
        $sortedByAng = $kabAnalytics;
        usort($sortedByAng, fn($a, $b) => $b['pct_ang'] <=> $a['pct_ang']);
        $top5Ang    = array_slice($sortedByAng, 0, 5);
        $bottom5Ang = array_slice(array_reverse($sortedByAng), 0, 5);

        // ── NILAI PER KPM untuk chart ──────────────────────────────────
        // Ambil sample 10 kabupaten terbesar (by realisasi kpm)
        $sortedBySize = $kabAnalytics;
        usort($sortedBySize, fn($a, $b) => $b['realisasi_kpm'] <=> $a['realisasi_kpm']);
        $sampleNilai = array_slice($sortedBySize, 0, 10);
        $avgNilaiPerKpm = count($kabAnalytics) > 0
            ? round(array_sum(array_column($kabAnalytics, 'nilai_per_kpm')) / count($kabAnalytics))
            : 600000;

        // ── GAP TABLE (sorted by |gap_kpm| desc) ─────────────────────
        $gapTable = $kabAnalytics;
        usort($gapTable, fn($a, $b) => abs($b['gap_kpm']) <=> abs($a['gap_kpm']));
        $gapTable = array_slice($gapTable, 0, 10);

        // ── CHART BAR DATA (untuk chart rencana vs realisasi KPM) ─────
        // Gunakan semua kab, diurutkan by rencana_kpm desc, ambil 38
        $chartData = $kabAnalytics;
        usort($chartData, fn($a, $b) => $b['rencana_kpm'] <=> $a['rencana_kpm']);

        // ── WAWASAN KINERJA UTAMA ──────────────────────────────────────
        $perfect100 = array_filter($kabAnalytics, fn($k) => $k['pct_kpm'] >= 100);
        $over100    = array_filter($kabAnalytics, fn($k) => $k['pct_kpm'] > 100);
        $terendah2  = array_slice($rendah, 0, 2);

        // Selisih anggaran terbesar
        $sortedByGapAng = $kabAnalytics;
        usort($sortedByGapAng, fn($a, $b) => abs($b['gap_ang']) <=> abs($a['gap_ang']));
        $terbesarGap = $sortedByGapAng[0] ?? null;

        return view('pages.economy', compact(
            'selectedYear', 'validYears',
            'totalRencanaKpm', 'totalRealisasiKpm', 'pctKpm',
            'totalRencanaAng', 'totalRealisasiAng', 'pctAng',
            'kabAnalytics',
            'tinggi', 'menengah', 'rendah',
            'top5Ang', 'bottom5Ang',
            'sampleNilai', 'avgNilaiPerKpm',
            'gapTable',
            'chartData',
            'perfect100', 'over100', 'terendah2', 'terbesarGap'
        ));
    }
}