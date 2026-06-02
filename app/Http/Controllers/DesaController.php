<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DesaController extends Controller
{
    private function db()
    {
        return DB::table('statusdesa');
    }

    public function demographics(Request $request)
    {
        $selectedYear = $request->get('year', 2024);

        // ── 1. Total Desa Mandiri ─────────────────────────────────
        $totalMandiri = $this->db()
            ->where('tahun', $selectedYear)
            ->sum('mandiri');

        // ── 2. Growth Rate vs tahun sebelumnya ───────────────────
        $totalMandiriPrev = $this->db()
            ->where('tahun', $selectedYear - 1)
            ->sum('mandiri');

        $growthRate = $totalMandiriPrev > 0
            ? round((($totalMandiri - $totalMandiriPrev) / $totalMandiriPrev) * 100, 1)
            : 0;

        $growthLabel = ($growthRate >= 0 ? '+' : '') . $growthRate . '%';

        // ── 3. Zero Tertinggal ────────────────────────────────────
        $zeroTertinggal = $this->db()
            ->where('tahun', $selectedYear)
            ->where(function ($q) {
                $q->where('sangat_tertinggal', '>', 0)
                  ->orWhere('tertinggal', '>', 0);
            })
            ->count();

        // ── 4. Semua kabupaten tahun terpilih ─────────────────────
        $allKab = $this->db()
            ->where('tahun', $selectedYear)
            ->where('nama_kabupaten_kota', '!=', '')
            ->where('kode_kabupaten_kota', '!=', '0000')
            ->select(
                'kode_kabupaten_kota',
                'nama_kabupaten_kota',
                'mandiri',
                'maju',
                'berkembang',
                'sangat_tertinggal',
                'tertinggal'
            )
            ->get()
            ->filter(fn($r) => ($r->mandiri + $r->maju + $r->berkembang) > 0);

        // ── 5. Growth + naik status per kabupaten ────────────────
        // Ambil semua kolom tahun sebelumnya (bukan hanya mandiri)
        $prevYearData = $this->db()
            ->where('tahun', $selectedYear - 1)
            ->where('kode_kabupaten_kota', '!=', '0000')
            ->select(
                'nama_kabupaten_kota',
                'mandiri',
                'maju',
                'berkembang',
                'tertinggal',
                'sangat_tertinggal'
            )
            ->get()
            ->keyBy('nama_kabupaten_kota');

        $kabWithGrowth = $allKab->map(function ($row) use ($prevYearData) {
            $prev        = $prevYearData->get($row->nama_kabupaten_kota);
            $prevMandiri = $prev ? $prev->mandiri : 0;

            $growth = $prevMandiri > 0
                ? round((($row->mandiri - $prevMandiri) / $prevMandiri) * 100, 1)
                : ($row->mandiri > 0 ? 100 : 0);

            // ── Hitung desa naik status ───────────────────────────
            // Menggunakan selisih positif antar kolom sebagai estimasi
            // perpindahan status (karena data adalah aggregate per kab)
        $naikDetail = [];

            if ($prev) {
                $deltas = [
                    'sangat_tertinggal' => ($row->sangat_tertinggal ?? 0) - ($prev->sangat_tertinggal ?? 0),
                    'tertinggal'        => ($row->tertinggal        ?? 0) - ($prev->tertinggal        ?? 0),
                    'berkembang'        => ($row->berkembang        ?? 0) - ($prev->berkembang        ?? 0),
                    'maju'              => ($row->maju              ?? 0) - ($prev->maju              ?? 0),
                    'mandiri'           => ($row->mandiri           ?? 0) - ($prev->mandiri           ?? 0),
                ];

                $levels = ['sangat_tertinggal' => 'Sangat Tertinggal', 'tertinggal' => 'Tertinggal',
                        'berkembang' => 'Berkembang', 'maju' => 'Maju', 'mandiri' => 'Mandiri'];

                foreach ($levels as $col => $label) {
                    if ($deltas[$col] > 0) {
                        $naikDetail[] = ['jumlah' => $deltas[$col], 'label' => $label, 'arah' => 'naik'];
                    } elseif ($deltas[$col] < 0) {
                        $naikDetail[] = ['jumlah' => abs($deltas[$col]), 'label' => $label, 'arah' => 'turun'];
                    }
                }
            }

            return [
                'name'         => $row->nama_kabupaten_kota,
                'kode'         => $row->kode_kabupaten_kota,
                'mandiri'      => $row->mandiri,
                'maju'         => $row->maju,
                'berkembang'   => $row->berkembang,
                'growth'       => $growth,
                'growth_label' => ($growth >= 0 ? '+' : '') . $growth . '%',
                'naik_detail'  => $naikDetail,
            ];
        })->values();

        $topKabupaten    = $kabWithGrowth->sortByDesc('growth')->take(5)->values();
        $bottomKabupaten = $kabWithGrowth->sortBy('growth')->take(5)->values();

        // ── 6. IDM Proportions ────────────────────────────────────
        $totMandiri      = $allKab->sum('mandiri');
        $totMaju         = $allKab->sum('maju');
        $totBerkembang   = $allKab->sum('berkembang');
        $totTertinggal   = $allKab->sum('tertinggal');
        $totSgTertinggal = $allKab->sum('sangat_tertinggal');
        $grandTotal      = $totMandiri + $totMaju + $totBerkembang
                         + $totTertinggal + $totSgTertinggal;

        $idmProportions = [];
        if ($grandTotal > 0) {
            $palette = [
                ['label' => 'Mandiri',           'val' => $totMandiri,      'color' => '#1D9E75'],
                ['label' => 'Maju',              'val' => $totMaju,         'color' => '#378ADD'],
                ['label' => 'Berkembang',        'val' => $totBerkembang,   'color' => '#EF9F27'],
                ['label' => 'Tertinggal',        'val' => $totTertinggal,   'color' => '#E24B4A'],
                ['label' => 'Sangat Tertinggal', 'val' => $totSgTertinggal, 'color' => '#A32D2D'],
            ];
            foreach ($palette as $p) {
                if ($p['val'] > 0) {
                    $idmProportions[] = [
                        'label'   => $p['label'],
                        'percent' => round($p['val'] / $grandTotal * 100),
                        'color'   => $p['color'],
                        'count'   => $p['val'],
                    ];
                }
            }
        }

        // ── 7. Trend 2022–2025 ────────────────────────────────────
        $trendRaw = $this->db()
            ->whereIn('tahun', [2022, 2023, 2024, 2025])
            ->where('nama_kabupaten_kota', '!=', '')
            ->where('kode_kabupaten_kota', '!=', '0000')
            ->select(
                'tahun',
                DB::raw('SUM(mandiri) as mandiri'),
                DB::raw('SUM(maju) as maju'),
                DB::raw('SUM(berkembang) as berkembang'),
                DB::raw('SUM(tertinggal) as tertinggal'),
                DB::raw('SUM(sangat_tertinggal) as sangat_tertinggal')
            )
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        $trendData = [
            'years'             => $trendRaw->pluck('tahun')->toArray(),
            'mandiri'           => $trendRaw->pluck('mandiri')->map(fn($v) => (int)$v)->toArray(),
            'maju'              => $trendRaw->pluck('maju')->map(fn($v) => (int)$v)->toArray(),
            'berkembang'        => $trendRaw->pluck('berkembang')->map(fn($v) => (int)$v)->toArray(),
            'tertinggal'        => $trendRaw->pluck('tertinggal')->map(fn($v) => (int)$v)->toArray(),
            'sangat_tertinggal' => $trendRaw->pluck('sangat_tertinggal')->map(fn($v) => (int)$v)->toArray(),
        ];

        // ── 8. Bakorwil ───────────────────────────────────────────
        $bakorwilMapping = [
            'Bakorwil I Madiun'      => ['3519','3520','3521','3502','3503','3501'],
            'Bakorwil II Bojonegoro' => ['3522','3523','3524','3525','3518','3516','3517'],
            'Bakorwil III Malang'    => ['3507','3505','3504','3506','3508','3514','3513'],
            'Bakorwil IV Pamekasan'  => ['3526','3527','3528','3529'],
            'Bakorwil V Jember'      => ['3509','3510','3511','3512','3515'],
        ];

        $allKabByCode = $this->db()
            ->where('tahun', $selectedYear)
            ->where('kode_kabupaten_kota', '!=', '0000')
            ->select('kode_kabupaten_kota', 'mandiri', 'maju', 'berkembang')
            ->get()
            ->keyBy(fn($r) => (string)$r->kode_kabupaten_kota);

        $bakorwil = [];
        foreach ($bakorwilMapping as $bName => $codes) {
            $mandiri = 0; $maju = 0; $berkembang = 0;
            foreach ($codes as $code) {
                $row = $allKabByCode->get($code);
                if ($row) {
                    $mandiri    += $row->mandiri;
                    $maju       += $row->maju;
                    $berkembang += $row->berkembang;
                }
            }
            $bakorwil[] = [
                'name'         => $bName,
                'mandiri'      => $mandiri,
                'maju'         => $maju,
                'berkembang'   => $berkembang,
                'topPerformer' => false,
            ];
        }

        // Tandai top performer
        $maxMandiri = max(array_column($bakorwil, 'mandiri'));
        foreach ($bakorwil as &$b) {
            $b['topPerformer'] = ($b['mandiri'] === $maxMandiri);
        }
        unset($b);

        // ── 9. Tahun tersedia untuk dropdown ──────────────────────
        $availableYears = $this->db()
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun')
            ->pluck('tahun')
            ->toArray();

        // ── Kemas data ────────────────────────────────────────────
        $data = [
            'year'            => $selectedYear,
            'availableYears'  => $availableYears,
            'totalDesaMandiri'=> number_format($totalMandiri),
            'growthRate'      => $growthLabel,
            'growthRaw'       => $growthRate,
            'zeroTertinggal'  => $zeroTertinggal,
            'topKabupaten'    => $topKabupaten,
            'bottomKabupaten' => $bottomKabupaten,
            'idmProportions'  => $idmProportions,
            'trendData'       => $trendData,
            'bakorwil'        => $bakorwil,
            'grandTotal'      => number_format($grandTotal),
            'mandiriPercent'  => $grandTotal > 0
                ? round($totMandiri / $grandTotal * 100)
                : 0,
        ];

        return view('pages/demographics', compact('data'));
    }
}