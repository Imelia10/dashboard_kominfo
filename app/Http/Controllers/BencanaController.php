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

        $tahunList = DB::table(self::T_JML)->distinct()->orderByDesc('tahun')->pluck('tahun');
        if (!$tahunDipilih) $tahunDipilih = $tahunList->first();
        $kabList = DB::table(self::T_JML)->distinct()->orderBy('nama_kabupaten_kota')->pluck('nama_kabupaten_kota');

        // ══════════════════════════════════════════════════════════
        // DATA SEMUA TAHUN — untuk KPI global & Map (tidak difilter tahun)
        // ══════════════════════════════════════════════════════════
        $rawJmlAll    = DB::table(self::T_JML)->get()->keyBy('nama_kabupaten_kota');
        $rawKorbanAll = DB::table(self::T_KORBAN)->get()->keyBy('nama_kabupaten_kota');
        $rawRumahAll  = DB::table(self::T_RUMAH)->get()->keyBy('nama_kabupaten_kota');

        $perKabAll = $this->buildPerKab($rawJmlAll, $rawKorbanAll, $rawRumahAll);
        $perKabAll = $this->calcSeverity($perKabAll);

        // KPI global (semua tahun)
        $kpi = $this->buildKpi($perKabAll);

        // Daerah korban terbanyak (semua tahun) — untuk KPI card 4
        $korbanTerbanyak = $perKabAll->sortByDesc('total_korban')->first();

        // ══════════════════════════════════════════════════════════
        // DATA FILTERED (tahun + kab) — untuk Tren & Analisis Keparahan
        // ══════════════════════════════════════════════════════════
        $rawJmlF    = DB::table(self::T_JML)
            ->when($tahunDipilih, fn($q) => $q->where('tahun', $tahunDipilih))
            ->when($kabDipilih,   fn($q) => $q->where('nama_kabupaten_kota', $kabDipilih))
            ->get()->keyBy('nama_kabupaten_kota');

        $rawKorbanF = DB::table(self::T_KORBAN)
            ->when($tahunDipilih, fn($q) => $q->where('tahun', $tahunDipilih))
            ->when($kabDipilih,   fn($q) => $q->where('nama_kabupaten_kota', $kabDipilih))
            ->get()->keyBy('nama_kabupaten_kota');

        $rawRumahF  = DB::table(self::T_RUMAH)
            ->when($tahunDipilih, fn($q) => $q->where('tahun', $tahunDipilih))
            ->when($kabDipilih,   fn($q) => $q->where('nama_kabupaten_kota', $kabDipilih))
            ->get()->keyBy('nama_kabupaten_kota');

        $perKabF = $this->buildPerKab($rawJmlF, $rawKorbanF, $rawRumahF);
        $perKabF = $this->calcSeverity($perKabF);

        // ── Scatter: frekuensi vs dampak (filtered) ───────────────
        $scatter = $perKabF
            ->filter(fn($r) => $r['total_kejadian'] > 0)
            ->map(fn($r) => [
                'nama'      => $r['nama'],
                'frekuensi' => $r['total_kejadian'],
                'dampak'    => $r['total_korban'] + $r['total_rmh_rusak'],
                'severity'  => $r['severity'],
            ])->values();

        // ── Top 3 terparah (severity) — filtered ─────────────────
        $top3Parah = $perKabF
            ->filter(fn($r) => $r['total_kejadian'] > 0)
            ->sortByDesc('severity')
            ->take(3)
            ->values();

        // ── Tren per tahun (filtered kabupaten) ───────────────────
        $trendKorban = $this->buildTrendKorban($kabDipilih)->keyBy('tahun');
        $trend = $this->buildTrend($kabDipilih)->map(function ($row) use ($trendKorban) {
            $row['total_korban'] = $trendKorban[$row['tahun']]['total_korban'] ?? 0;
            return $row;
        });

        // ── Komposisi jenis di tahun terpilih (untuk pie chart) ───
        // Jumlah kejadian per jenis pada tahun & kab filter
        $komposisiTahun = $this->buildKomposisi($rawJmlF);

        // ══════════════════════════════════════════════════════════
        // DATA UNFILTERED — Top 5 stacked bar & Tabel detail
        // ══════════════════════════════════════════════════════════
        $komposisiAll = $this->buildKomposisi($rawJmlAll);

        $top5 = $perKabAll->sortByDesc('total_kejadian')->take(5)->values();

        $detail = $perKabAll->sortByDesc('total_kejadian')->map(function ($r) {
            // Severity sebagai persen (0-100)
            $r['severity_pct'] = min(100, round($r['severity'] * 10));
            return $r;
        })->values();

        return view('pages.Bencana', compact(
            'tahunDipilih', 'tahunList', 'kabList', 'kabDipilih',
            'kpi', 'korbanTerbanyak',
            'trend', 'komposisiTahun',
            'scatter', 'top3Parah',
            'komposisiAll', 'top5', 'detail',
        ));
    }

    // ─────────────────────────────────────────────────────────────
    private function buildPerKab($rawJml, $rawKorban, $rawRumah): \Illuminate\Support\Collection
    {
        $allKab = $rawJml->keys()->merge($rawKorban->keys())->merge($rawRumah->keys())
            ->unique()->sort()->values();

        return $allKab->map(function ($nama) use ($rawJml, $rawKorban, $rawRumah) {
            $j = $rawJml[$nama]    ?? null;
            $k = $rawKorban[$nama] ?? null;
            $r = $rawRumah[$nama]  ?? null;

            $kejadian = [];
            foreach (array_keys(self::JENIS) as $kode) {
                $kejadian[$kode] = $j ? (int)($j->{"jml_bencana_{$kode}"} ?? 0) : 0;
            }
            $totalKejadian = array_sum($kejadian);

            $totalMdHlg = 0; $totalLuka = 0;
            foreach (array_keys(self::JENIS) as $kode) {
                $totalMdHlg += $k ? (int)($k->{"korban_{$kode}_md_hlg"} ?? 0) : 0;
                $totalLuka  += $k ? (int)($k->{"korban_{$kode}_luka"}   ?? 0) : 0;
            }
            $totalKorban = $totalMdHlg + $totalLuka;

            $totalRusak = 0; $totalTerendam = 0;
            foreach (array_keys(self::JENIS) as $kode) {
                $totalRusak    += $r ? ((int)($r->{"rmh_rb_{$kode}"}??0)+(int)($r->{"rmh_rs_{$kode}"}??0)+(int)($r->{"rmh_rr_{$kode}"}??0)) : 0;
                $totalTerendam += $r ? (int)($r->{"rmh_trdm_{$kode}"} ?? 0) : 0;
            }

            return [
                'nama'               => $nama,
                'kejadian'           => $kejadian,
                'total_kejadian'     => $totalKejadian,
                'korban_md_hlg'      => $totalMdHlg,
                'korban_luka'        => $totalLuka,
                'total_korban'       => $totalKorban,
                'total_rmh_rusak'    => $totalRusak,
                'total_rmh_terendam' => $totalTerendam,
                'severity'           => 0.0,
            ];
        });
    }

    private function buildKpi(\Illuminate\Support\Collection $perKab): object
    {
        return (object)[
            'total_kejadian'     => $perKab->sum('total_kejadian'),
            'total_korban'       => $perKab->sum('total_korban'),
            'korban_md_hlg'      => $perKab->sum('korban_md_hlg'),
            'total_rmh_rusak'    => $perKab->sum('total_rmh_rusak'),
            'total_rmh_terendam' => $perKab->sum('total_rmh_terendam'),
        ];
    }

    /**
     * Severity = (Rumah Rusak + Korban) / Banyak Kejadian  →  skala 0-10 via Min-Max
     */
    private function calcSeverity(\Illuminate\Support\Collection $perKab): \Illuminate\Support\Collection
    {
        // Hitung raw severity dulu
        $withRaw = $perKab->map(function ($r) {
            $r['sev_raw'] = $r['total_kejadian'] > 0
                ? ($r['total_rmh_rusak'] + $r['total_korban']) / $r['total_kejadian']
                : 0;
            return $r;
        });

        $maxRaw = max(1, $withRaw->max('sev_raw'));
        $minRaw = $withRaw->min('sev_raw');

        return $withRaw->map(function ($r) use ($maxRaw, $minRaw) {

            // Normalisasi untuk ranking/internal logic
            $norm = ($maxRaw - $minRaw) > 0
                ? ($r['sev_raw'] - $minRaw) / ($maxRaw - $minRaw)
                : 0;

            // Tetap dipakai untuk sorting Top 3
            $r['severity'] = round($norm * 10, 2);

            // Tambahan nilai asli dampak per kejadian
            $r['severity_raw'] = round($r['sev_raw'], 1);

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

    /** Juga hitung total_korban per tahun untuk line chart kedua */
    private function buildTrendKorban(?string $kabDipilih): \Illuminate\Support\Collection
    {
        return DB::table(self::T_KORBAN)
            ->when($kabDipilih, fn($q) => $q->where('nama_kabupaten_kota', $kabDipilih))
            ->orderBy('tahun')->get()
            ->groupBy('tahun')->map(function ($rows, $tahun) {
                $total = 0;
                foreach (array_keys(self::JENIS) as $kode) {
                    $total += $rows->sum("korban_{$kode}_md_hlg") + $rows->sum("korban_{$kode}_luka");
                }
                return ['tahun' => $tahun, 'total_korban' => $total];
            })->values();
    }

    private function buildKomposisi($rawJml): array
    {
        $result = [];
        $grandTotal = 0;
        foreach (self::JENIS as $kode => $meta) {
            $total = $rawJml->sum(fn($r) => (int)($r->{"jml_bencana_{$kode}"} ?? 0));
            if ($total > 0) {
                $result[] = ['kode' => $kode, 'label' => $meta['label'], 'warna' => $meta['warna'], 'total' => $total];
                $grandTotal += $total;
            }
        }
        // Tambah persen
        foreach ($result as &$item) {
            $item['persen'] = $grandTotal > 0 ? round($item['total'] / $grandTotal * 100, 1) : 0;
        }
        usort($result, fn($a, $b) => $b['total'] - $a['total']);
        return $result;
    }

    public static function getJenis(): array { return self::JENIS; }
}