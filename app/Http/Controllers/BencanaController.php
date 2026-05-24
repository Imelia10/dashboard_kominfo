<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BencanaController extends Controller
{
    private const T_JML    = 'jml_bencana';
    private const T_KORBAN = 'korban';
    private const T_RUMAH  = 'rumahrusak';

    private const JENIS = [
        'gempa'         => ['label' => 'Gempa Bumi',              'warna' => '#ef4444'],
        'tsunami'       => ['label' => 'Tsunami',                 'warna' => '#3b82f6'],
        'gempa_tsunami' => ['label' => 'Gempa + Tsunami',         'warna' => '#8b5cf6'],
        'gunung'        => ['label' => 'Erupsi Gunung Api',       'warna' => '#f97316'],
        'longsor'       => ['label' => 'Tanah Longsor',           'warna' => '#a16207'],
        'banjir'        => ['label' => 'Banjir',                  'warna' => '#0ea5e9'],
        'kering'        => ['label' => 'Kekeringan',              'warna' => '#eab308'],
        'karhutla'      => ['label' => 'Kebakaran Hutan & Lahan', 'warna' => '#dc2626'],
        'cuaca_eks'     => ['label' => 'Cuaca Ekstrem',           'warna' => '#6366f1'],
        'abrasi'        => ['label' => 'Abrasi',                  'warna' => '#14b8a6'],
    ];

    private const W_KORBAN = 0.6;
    private const W_RUMAH  = 0.4;

    public function index(Request $request)
    {
        $tahunDipilih = $request->input('tahun', null);
        $kabDipilih   = $request->input('kabupaten', null);
        $jenisDipilih = $request->input('jenis', null);

        // Daftar tahun & kabupaten
        $tahunList = DB::table(self::T_JML)->distinct()->orderByDesc('tahun')->pluck('tahun');
        if (!$tahunDipilih) $tahunDipilih = $tahunList->first();
        $kabList = DB::table(self::T_JML)->distinct()->orderBy('nama_kabupaten_kota')->pluck('nama_kabupaten_kota');

        // ══════════════════════════════════════════════════════════
        // DATA DENGAN FILTER (untuk KPI, Map, Trend, Gauge, Scatter)
        // ══════════════════════════════════════════════════════════
        $rawJml = DB::table(self::T_JML)
            ->when($tahunDipilih, fn($q) => $q->where('tahun', $tahunDipilih))
            ->when($kabDipilih,   fn($q) => $q->where('nama_kabupaten_kota', $kabDipilih))
            ->get()->keyBy('nama_kabupaten_kota');

        $rawKorban = DB::table(self::T_KORBAN)
            ->when($tahunDipilih, fn($q) => $q->where('tahun', $tahunDipilih))
            ->when($kabDipilih,   fn($q) => $q->where('nama_kabupaten_kota', $kabDipilih))
            ->get()->keyBy('nama_kabupaten_kota');

        $rawRumah = DB::table(self::T_RUMAH)
            ->when($tahunDipilih, fn($q) => $q->where('tahun', $tahunDipilih))
            ->when($kabDipilih,   fn($q) => $q->where('nama_kabupaten_kota', $kabDipilih))
            ->get()->keyBy('nama_kabupaten_kota');

        // Agregat per kabupaten (filtered) → KPI, Scatter, Gauge
        $perKabFiltered = $this->buildPerKab($rawJml, $rawKorban, $rawRumah);
        $kpi            = $this->buildKpi($perKabFiltered);
        $perKabFiltered = $this->calcSeverity($perKabFiltered);
        $severityIndex  = round($perKabFiltered->avg('severity') ?? 0, 1);
        $scatter        = $perKabFiltered
            ->filter(fn($r) => $r['total_kejadian'] > 0)
            ->map(fn($r) => [
                'nama'      => $r['nama'],
                'frekuensi' => $r['total_kejadian'],
                'dampak'    => $r['total_korban'] + $r['total_rmh_rusak'],
            ])->values();

        // Komposisi jenis (filtered — untuk donut di filter panel)
        $komposisi = $this->buildKomposisi($rawJml, $jenisDipilih);

        // Trend (filtered kabupaten, semua tahun)
        $trend = $this->buildTrend($kabDipilih);

        // ══════════════════════════════════════════════════════════
        // DATA TANPA FILTER (untuk Top5 Stacked Bar & Tabel Detail)
        // ══════════════════════════════════════════════════════════
        $rawJmlAll    = DB::table(self::T_JML)->get()->keyBy('nama_kabupaten_kota');
        $rawKorbanAll = DB::table(self::T_KORBAN)->get()->keyBy('nama_kabupaten_kota');
        $rawRumahAll  = DB::table(self::T_RUMAH)->get()->keyBy('nama_kabupaten_kota');

        $perKabAll = $this->buildPerKab($rawJmlAll, $rawKorbanAll, $rawRumahAll);
        $perKabAll = $this->calcSeverity($perKabAll);

        // Top 5 (unfiltered)
        $top5 = $perKabAll->sortByDesc('total_kejadian')->take(5)->values();

        // Komposisi untuk legend stacked bar (unfiltered)
        $komposisiAll = $this->buildKomposisi($rawJmlAll, null);

        // Tabel detail (unfiltered) + status
        $detail = $perKabAll->sortByDesc('total_kejadian')->map(function ($r) {
            $r['status'] = match (true) {
                $r['severity'] >= 7 || $r['korban_md_hlg'] >= 5 => 'KRITIS',
                $r['severity'] >= 4 || $r['korban_md_hlg'] >= 1 => 'WASPADA',
                $r['total_kejadian'] > 0                         => 'PANTAU',
                default                                          => 'AMAN',
            };
            return $r;
        })->values();

        return view('pages.Bencana', compact(
            'tahunDipilih', 'tahunList', 'kabList', 'kabDipilih', 'jenisDipilih',
            'kpi', 'severityIndex',
            'komposisi',        // donut (filtered)
            'komposisiAll',     // legend stacked bar (unfiltered)
            'trend', 'scatter',
            'top5', 'detail',   // TIDAK kena filter
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPERS (tidak berubah)
    // ─────────────────────────────────────────────────────────────

    private function buildPerKab($rawJml, $rawKorban, $rawRumah): \Illuminate\Support\Collection
    {
        $allKab = $rawJml->keys()
            ->merge($rawKorban->keys())
            ->merge($rawRumah->keys())
            ->unique()->sort()->values();

        return $allKab->map(function ($nama) use ($rawJml, $rawKorban, $rawRumah) {
            $j = $rawJml[$nama]    ?? null;
            $k = $rawKorban[$nama] ?? null;
            $r = $rawRumah[$nama]  ?? null;

            $kejadian = [];
            foreach (array_keys(self::JENIS) as $kode) {
                $col = "jml_bencana_{$kode}";
                $kejadian[$kode] = $j ? (int)($j->$col ?? 0) : 0;
            }
            $totalKejadian = array_sum($kejadian);

            $totalKorbanMdHlg = 0;
            $totalKorbanLuka  = 0;
            foreach (array_keys(self::JENIS) as $kode) {
                $totalKorbanMdHlg += $k ? (int)($k->{"korban_{$kode}_md_hlg"} ?? 0) : 0;
                $totalKorbanLuka  += $k ? (int)($k->{"korban_{$kode}_luka"}   ?? 0) : 0;
            }
            $totalKorban = $totalKorbanMdHlg + $totalKorbanLuka;

            $totalRmhRusak    = 0;
            $totalRmhTerendam = 0;
            foreach (array_keys(self::JENIS) as $kode) {
                $totalRmhRusak    += $r ? (
                    (int)($r->{"rmh_rb_{$kode}"} ?? 0) +
                    (int)($r->{"rmh_rs_{$kode}"} ?? 0) +
                    (int)($r->{"rmh_rr_{$kode}"} ?? 0)
                ) : 0;
                $totalRmhTerendam += $r ? (int)($r->{"rmh_trdm_{$kode}"} ?? 0) : 0;
            }

            return [
                'nama'              => $nama,
                'kejadian'          => $kejadian,
                'total_kejadian'    => $totalKejadian,
                'korban_md_hlg'     => $totalKorbanMdHlg,
                'korban_luka'       => $totalKorbanLuka,
                'total_korban'      => $totalKorban,
                'total_rmh_rusak'   => $totalRmhRusak,
                'total_rmh_terendam'=> $totalRmhTerendam,
                'severity'          => 0.0,
            ];
        });
    }

    private function buildKpi(\Illuminate\Support\Collection $perKab): object
    {
        return (object)[
            'total_kejadian'    => $perKab->sum('total_kejadian'),
            'total_korban'      => $perKab->sum('total_korban'),
            'korban_md_hlg'     => $perKab->sum('korban_md_hlg'),
            'total_rmh_rusak'   => $perKab->sum('total_rmh_rusak'),
            'total_rmh_terendam'=> $perKab->sum('total_rmh_terendam'),
        ];
    }

    private function calcSeverity(\Illuminate\Support\Collection $perKab): \Illuminate\Support\Collection
    {
        $maxKorban = max(1, $perKab->max('total_korban'));
        $minKorban = $perKab->min('total_korban');
        $maxRumah  = max(1, $perKab->max('total_rmh_rusak'));
        $minRumah  = $perKab->min('total_rmh_rusak');

        return $perKab->map(function ($r) use ($maxKorban, $minKorban, $maxRumah, $minRumah) {
            if ($r['total_kejadian'] === 0) { $r['severity'] = 0.0; return $r; }

            $normKorban = ($maxKorban - $minKorban) > 0
                ? ($r['total_korban']    - $minKorban) / ($maxKorban - $minKorban) : 0;
            $normRumah  = ($maxRumah - $minRumah) > 0
                ? ($r['total_rmh_rusak'] - $minRumah)  / ($maxRumah  - $minRumah)  : 0;

            $dampak = (self::W_RUMAH * $normRumah) + (self::W_KORBAN * $normKorban);
            $r['severity'] = round(min(10, ($dampak / $r['total_kejadian']) * 10), 2);
            return $r;
        });
    }

    private function buildTrend(?string $kabDipilih): \Illuminate\Support\Collection
    {
        return DB::table(self::T_JML)
            ->when($kabDipilih, fn($q) => $q->where('nama_kabupaten_kota', $kabDipilih))
            ->orderBy('tahun')->get()
            ->groupBy('tahun')->map(function ($rows, $tahun) {
                $totals = ['tahun' => $tahun];
                foreach (array_keys(self::JENIS) as $kode) {
                    $totals[$kode] = $rows->sum("jml_bencana_{$kode}");
                }
                $totals['total'] = array_sum(array_map(fn($k) => $totals[$k], array_keys(self::JENIS)));
                return $totals;
            })->values();
    }

    private function buildKomposisi($rawJml, ?string $jenisDipilih): array
    {
        $result = [];
        foreach (self::JENIS as $kode => $meta) {
            $col   = "jml_bencana_{$kode}";
            $total = $rawJml->sum(fn($r) => (int)($r->$col ?? 0));
            if ($total > 0) {
                $result[] = ['kode' => $kode, 'label' => $meta['label'], 'warna' => $meta['warna'], 'total' => $total];
            }
        }
        usort($result, fn($a, $b) => $b['total'] - $a['total']);
        return $result;
    }

    public static function getJenis(): array { return self::JENIS; }
}