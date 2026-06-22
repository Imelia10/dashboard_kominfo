<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgriController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', 2025);

        // ── KPI: Luas Panen, Produksi, Produktivitas dari agri3 ──
        $kpi = DB::table('agri3')
            ->selectRaw('
                SUM(`Luas Panen Tanaman Padi (ha) (Ha)`)  AS luas_panen,
                AVG(`Produktivitas Tanaman Padi (ku/ha) (Ku/ha)`) AS produktivitas,
                SUM(`Rekap Produksi Padi (ton) (Ton)`)    AS produksi_total
            ')
            ->where('tahun', $tahun)
            ->first();

        // KPI tahun sebelumnya untuk perbandingan
        $kpiPrev = DB::table('agri3')
            ->selectRaw('
                SUM(`Luas Panen Tanaman Padi (ha) (Ha)`)  AS luas_panen,
                AVG(`Produktivitas Tanaman Padi (ku/ha) (Ku/ha)`) AS produktivitas,
                SUM(`Rekap Produksi Padi (ton) (Ton)`)    AS produksi_total
            ')
            ->where('tahun', $tahun - 1)
            ->first();

        // Semua tahun yang tersedia
        $availableYears = DB::table('agri3')
            ->select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        // ── Grafik 1: Korelasi Luas Panen vs Produksi per Kabupaten (agri3) ──
        $korelasiData = DB::table('agri3')
            ->selectRaw('
                nama_kabupaten_kota,
                SUM(`Luas Panen Tanaman Padi (ha) (Ha)`) AS luas_panen,
                SUM(`Rekap Produksi Padi (ton) (Ton)`)   AS produksi
            ')
            ->where('tahun', $tahun)
            ->groupBy('nama_kabupaten_kota')
            ->orderByDesc('produksi')
            ->limit(15)
            ->get();

        // ── Grafik 2: Volatilitas Musiman – Luas Panen Bulanan (agri2) ──
        $bulanCols = ['Januari','Februari','Maret','April','Mei','Juni',
                      'Juli','Agustus','September','Oktober','November','Desember'];

        $musiman = DB::table('agri2')
            ->selectRaw(implode(',', array_map(fn($b) => "SUM(`$b`) AS `$b`", $bulanCols)))
            ->where('tahun', $tahun)
            ->first();

        $musimanPrev = DB::table('agri2')
            ->selectRaw(implode(',', array_map(fn($b) => "SUM(`$b`) AS `$b`", $bulanCols)))
            ->where('tahun', $tahun - 1)
            ->first();

        // ── Distribusi Semesteran dari agri1 (tahun yang dipilih) ──
        // SMT 1 = Januari s/d Juni, SMT 2 = Juli s/d Desember
        $smt1Cols = ['Januari','Februari','Maret','April','Mei','Juni'];
        $smt2Cols = ['Juli','Agustus','September','Oktober','November','Desember'];

        $semesterRaw = DB::table('agri1')
            ->selectRaw(
                implode('+', array_map(fn($b) => "COALESCE(SUM(`$b`),0)", $smt1Cols)) . ' AS smt1, ' .
                implode('+', array_map(fn($b) => "COALESCE(SUM(`$b`),0)", $smt2Cols)) . ' AS smt2'
            )
            ->where('tahun', $tahun)
            ->first();

        $semesterData = $semesterRaw;

        // ── Urban vs Rural: rata-rata produktivitas Kota vs Kabupaten ──
        // Menggunakan agri3, bedakan berdasarkan prefix nama_kabupaten_kota
        $urbanRuralData = DB::table('agri3')
            ->selectRaw('
                AVG(CASE WHEN nama_kabupaten_kota LIKE "Kota %" THEN `Produktivitas Tanaman Padi (ku/ha) (Ku/ha)` END) AS kota,
                AVG(CASE WHEN nama_kabupaten_kota LIKE "Kabupaten %" THEN `Produktivitas Tanaman Padi (ku/ha) (Ku/ha)` END) AS kabupaten
            ')
            ->where('tahun', $tahun)
            ->first();

        // ── Ketangguhan Musim Kemarau: YoY change pada bulan kemarau (Jul-Sep) ──
        // Ambil 5 kabupaten dengan penurunan terbesar di bulan kemarau (agri2)
        $kemaraData = DB::table('agri2 as a')
            ->join('agri2 as b', function($j) use ($tahun) {
                $j->on('a.nama_kabupaten_kota', '=', 'b.nama_kabupaten_kota')
                  ->where('b.tahun', $tahun - 1);
            })
            ->selectRaw('
                a.nama_kabupaten_kota,
                (
                  (COALESCE(a.`Juli`,0) + COALESCE(a.`Agustus`,0) + COALESCE(a.`September`,0))
                  - (COALESCE(b.`Juli`,0) + COALESCE(b.`Agustus`,0) + COALESCE(b.`September`,0))
                ) / NULLIF(
                  (COALESCE(b.`Juli`,0) + COALESCE(b.`Agustus`,0) + COALESCE(b.`September`,0))
                , 0) * 100 AS pct_change
            ')
            ->where('a.tahun', $tahun)
            ->orderBy('pct_change', 'asc')
            ->limit(3)
            ->get();

        // ── Top 5 Produksi Terbanyak (Lumbung Padi) ──
        $topKabData = DB::table('agri3')
            ->selectRaw('nama_kabupaten_kota, SUM(`Rekap Produksi Padi (ton) (Ton)`) AS produksi')
            ->where('tahun', $tahun)
            ->groupBy('nama_kabupaten_kota')
            ->orderByDesc('produksi')
            ->limit(5)
            ->get();

        // ── Top 5 Produksi Terdikit (Area Defisit) ──
        $bottomKabData = DB::table('agri3')
            ->selectRaw('nama_kabupaten_kota, SUM(`Rekap Produksi Padi (ton) (Ton)`) AS produksi')
            ->where('tahun', $tahun)
            ->groupBy('nama_kabupaten_kota')
            ->orderBy('produksi', 'asc')
            ->limit(5)
            ->get();

        // ── Regional: Top Lumbung Padi (agri3)
        $topKab = $topKabData;
        $totalProduksi = $topKab->sum('produksi');

        // ── YoY per Kabupaten ──
        $yoy = DB::table('agri3 as a')
            ->join('agri3 as b', function($j) use ($tahun) {
                $j->on('a.nama_kabupaten_kota', '=', 'b.nama_kabupaten_kota')
                  ->where('b.tahun', $tahun - 1);
            })
            ->selectRaw('
                a.nama_kabupaten_kota,
                a.`Rekap Produksi Padi (ton) (Ton)` AS prod_now,
                b.`Rekap Produksi Padi (ton) (Ton)` AS prod_prev,
                (a.`Rekap Produksi Padi (ton) (Ton)` - b.`Rekap Produksi Padi (ton) (Ton)`)
                  / NULLIF(b.`Rekap Produksi Padi (ton) (Ton)`, 0) * 100 AS pct_change
            ')
            ->where('a.tahun', $tahun)
            ->orderByDesc('pct_change')
            ->get();

        $gainers = $yoy->take(2);
        $losers  = $yoy->sortBy('pct_change')->take(2);

        // ── Detail Data Bulanan (agri1 + agri2) untuk tabel ──
        $detailBulanan = [];
        foreach ($bulanCols as $bulan) {
            $row1 = DB::table('agri1')->selectRaw("SUM(`$bulan`) AS val")->where('tahun', $tahun)->first();
            $row2 = DB::table('agri2')->selectRaw("SUM(`$bulan`) AS val")->where('tahun', $tahun)->first();
            $luas      = $row2->val ?? 0;
            $produksi  = $row1->val ?? 0;
            $produktiv = $luas > 0 ? round($produksi / $luas / 10, 2) : 0;
            $detailBulanan[] = [
                'bulan'        => $bulan,
                'luas_panen'   => $luas,
                'produksi'     => $produksi,
                'produktivitas'=> $produktiv,
            ];
        }

        return view('pages.agri', compact(
            'tahun', 'availableYears',
            'kpi', 'kpiPrev',
            'korelasiData',
            'bulanCols', 'musiman', 'musimanPrev',
            'semesterData',
            'urbanRuralData',
            'kemaraData',
            'topKab', 'totalProduksi',
            'topKabData', 'bottomKabData',
            'gainers', 'losers',
            'detailBulanan'
        ));
    }
}