<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerikananController extends Controller
{
    public function index(Request $request)
    {
        /* ── Daftar tahun tersedia ── */
        $daftarTahun = DB::table('produklaut')->select('tahun')
            ->union(DB::table('nelayan')->select('tahun'))
            ->orderBy('tahun', 'desc')->pluck('tahun')->unique()->values();

        $tahunAktif   = $request->input('tahun', $daftarTahun->first() ?? date('Y'));
        $tahunSebelum = $tahunAktif - 1;

        /* ══════════════════════════════════════════════
         |  PRODUKSI — aktif & sebelumnya
         ══════════════════════════════════════════════ */
        $pAktif = DB::table('produklaut')->where('tahun', $tahunAktif)
            ->selectRaw('
                SUM(ikan)                        AS total_ikan,
                SUM(binatang_lunak)              AS total_lunak,
                SUM(binatang_berkulit_keras)     AS total_keras,
                SUM(binatang_air_lainnya)        AS total_lainnya,
                SUM(ikan+binatang_lunak+binatang_berkulit_keras+binatang_air_lainnya) AS grand_total
            ')->first();

        $pSebelum = DB::table('produklaut')->where('tahun', $tahunSebelum)
            ->selectRaw('SUM(ikan+binatang_lunak+binatang_berkulit_keras+binatang_air_lainnya) AS grand_total')
            ->first();

        /* ══════════════════════════════════════════════
         |  NELAYAN — PERAIRAN UMUM SAJA (bukan laut)
         ══════════════════════════════════════════════ */
        $nAktif   = (float) DB::table('nelayan')->where('tahun', $tahunAktif)->sum('perairan_umum');
        $nSebelum = (float) DB::table('nelayan')->where('tahun', $tahunSebelum)->sum('perairan_umum');

        /* ── KPI raw values ── */
        $grandTotal   = (float) ($pAktif->grand_total ?? 0);
        $totalIkan    = (float) ($pAktif->total_ikan   ?? 0);
        $totalLunak   = (float) ($pAktif->total_lunak  ?? 0);
        $totalKeras   = (float) ($pAktif->total_keras  ?? 0);
        $totalLainnya = (float) ($pAktif->total_lainnya ?? 0);
        $totalNelayan = $nAktif;

        /* ── Satuan otomatis (deteksi skala) ── */
        $countRec = DB::table('produklaut')->where('tahun', $tahunAktif)->count() ?: 1;
        $avg      = $grandTotal / $countRec;
        $satuan   = $avg >= 10000 ? 'kg' : 'ton';
        $satuanPrdktv = $satuan . '/nelayan';

        /* ── Produktivitas ── */
        $produktivitas = $totalNelayan > 0 ? round($grandTotal / $totalNelayan, 2) : 0;

        /* ── Growth % ── */
        $prevTotal  = (float) ($pSebelum->grand_total ?? 0);
        $gProduksi  = $prevTotal  > 0 ? round(($grandTotal   - $prevTotal)  / $prevTotal  * 100, 1) : null;
        $gNelayan   = $nSebelum   > 0 ? round(($totalNelayan - $nSebelum)   / $nSebelum   * 100, 1) : null;
        $prevPrdktv = $nSebelum   > 0 ? round($prevTotal / $nSebelum, 2) : 0;
        $gPrdktv    = $prevPrdktv > 0 ? round(($produktivitas - $prevPrdktv) / $prevPrdktv * 100, 1) : null;

        /* ── KPI-4: Jenis Dominan ── */
        $jenisList = [
            'Ikan'           => $totalIkan,
            'Binatang Lunak' => $totalLunak,
            'Berkulit Keras' => $totalKeras,
            'Air Lainnya'    => $totalLainnya,
        ];
        $jenisDominan       = array_key_first(array_filter($jenisList, fn($v) => $v === max($jenisList)));
        $jenisDominanPersen = $grandTotal > 0 ? round(max($jenisList) / $grandTotal * 100, 1) : 0;

        /* ══════════════════════════════════════════════
         |  TREN — nelayan perairan umum & produksi
         ══════════════════════════════════════════════ */
        $trenNelayan = DB::table('nelayan')
            ->selectRaw('tahun, SUM(perairan_umum) AS total_nelayan')   // PERAIRAN UMUM SAJA
            ->groupBy('tahun')->orderBy('tahun')->get();

        $trenProduksi = DB::table('produklaut')
            ->selectRaw('tahun, SUM(ikan+binatang_lunak+binatang_berkulit_keras+binatang_air_lainnya) AS total_produksi')
            ->groupBy('tahun')->orderBy('tahun')->get();

        /* ── Tren gabungan untuk dual-axis & produktivitas ── */
        $trenGabungan = DB::table('produklaut as p')
            ->join('nelayan as n', fn($j) =>
                $j->on('p.kode_kabupaten_kota', '=', 'n.kode_kabupaten_kota')
                  ->on('p.tahun', '=', 'n.tahun'))
            ->selectRaw('
                p.tahun,
                SUM(p.ikan+p.binatang_lunak+p.binatang_berkulit_keras+p.binatang_air_lainnya) AS total_produksi,
                SUM(n.perairan_umum) AS total_nelayan')              // PERAIRAN UMUM SAJA
            ->groupBy('p.tahun')->orderBy('p.tahun')->get()
            ->map(fn($r) => tap($r, fn($r) =>
                $r->produktivitas = $r->total_nelayan > 0
                    ? round($r->total_produksi / $r->total_nelayan, 2) : 0));

        /* ══════════════════════════════════════════════
         |  TOP 5 — produksi & produktivitas
         ══════════════════════════════════════════════ */
        $top5Prod = DB::table('produklaut')
            ->selectRaw('nama_kabupaten_kota,
                SUM(ikan+binatang_lunak+binatang_berkulit_keras+binatang_air_lainnya) AS total_produksi')
            ->groupBy('kode_kabupaten_kota', 'nama_kabupaten_kota')
            ->orderByDesc('total_produksi')->limit(5)->get();

        $top5Prdktv = DB::table('produklaut as p')
            ->join('nelayan as n', fn($j) =>
                $j->on('p.kode_kabupaten_kota', '=', 'n.kode_kabupaten_kota')
                  ->on('p.tahun', '=', 'n.tahun'))
            ->selectRaw('
                p.nama_kabupaten_kota,
                ROUND(
                    SUM(p.ikan+p.binatang_lunak+p.binatang_berkulit_keras+p.binatang_air_lainnya)
                    / NULLIF(SUM(n.perairan_umum), 0),              -- PERAIRAN UMUM SAJA
                2) AS produktivitas')
            ->groupBy('p.kode_kabupaten_kota', 'p.nama_kabupaten_kota')
            ->havingRaw('produktivitas IS NOT NULL AND produktivitas > 0')
            ->orderByDesc('produktivitas')->limit(5)->get();

        /* ── Top 5 Nelayan Perairan Umum per Kabupaten ── */
        $top5Nelayan = DB::table('nelayan')
            ->where('tahun', $tahunAktif)
            ->selectRaw('nama_kabupaten_kota, SUM(perairan_umum) AS total_nelayan')  // PERAIRAN UMUM SAJA
            ->groupBy('kode_kabupaten_kota', 'nama_kabupaten_kota')
            ->orderByDesc('total_nelayan')->limit(5)->get();

        /* ══════════════════════════════════════════════
         |  KOMPOSISI DONUT
         ══════════════════════════════════════════════ */
        $kTotal    = $totalIkan + $totalLunak + $totalKeras + $totalLainnya;
        $komposisi = $kTotal > 0 ? [
            ['label' => 'Ikan',           'persen' => round($totalIkan    / $kTotal * 100, 1), 'nilai' => $totalIkan],
            ['label' => 'Binatang Lunak', 'persen' => round($totalLunak   / $kTotal * 100, 1), 'nilai' => $totalLunak],
            ['label' => 'Berkulit Keras', 'persen' => round($totalKeras   / $kTotal * 100, 1), 'nilai' => $totalKeras],
            ['label' => 'Air Lainnya',    'persen' => round($totalLainnya / $kTotal * 100, 1), 'nilai' => $totalLainnya],
        ] : [];

        /* ══════════════════════════════════════════════
         |  TABEL DETAIL PER KAB/KOTA
         ══════════════════════════════════════════════ */
        $detailKab = DB::table('produklaut as p')
            ->where('p.tahun', $tahunAktif)
            ->leftJoin(
                DB::raw('(
                    SELECT kode_kabupaten_kota,
                           SUM(perairan_umum) AS total_nelayan   -- PERAIRAN UMUM SAJA
                    FROM nelayan
                    WHERE tahun = ' . (int) $tahunAktif . '
                    GROUP BY kode_kabupaten_kota
                ) AS n'),
                'p.kode_kabupaten_kota', '=', 'n.kode_kabupaten_kota'
            )
            ->selectRaw('
                p.nama_kabupaten_kota,
                SUM(p.ikan)                        AS ikan,
                SUM(p.binatang_lunak)              AS binatang_lunak,
                SUM(p.binatang_berkulit_keras)     AS binatang_berkulit_keras,
                SUM(p.binatang_air_lainnya)        AS binatang_air_lainnya,
                SUM(p.ikan+p.binatang_lunak+p.binatang_berkulit_keras+p.binatang_air_lainnya) AS total_produksi,
                MAX(n.total_nelayan)               AS total_nelayan,
                ROUND(
                    SUM(p.ikan+p.binatang_lunak+p.binatang_berkulit_keras+p.binatang_air_lainnya)
                    / NULLIF(MAX(n.total_nelayan), 0),
                2) AS produktivitas')
            ->groupBy('p.kode_kabupaten_kota', 'p.nama_kabupaten_kota')
            ->orderByDesc('total_produksi')->get();

        return view('pages.perikanan', compact(
            'daftarTahun', 'tahunAktif',
            'totalNelayan', 'gNelayan',
            'grandTotal',   'gProduksi',
            'produktivitas','gPrdktv',
            'jenisDominan', 'jenisDominanPersen',
            'satuan',       'satuanPrdktv',
            'totalIkan',    'totalLunak', 'totalKeras', 'totalLainnya',
            'komposisi',    'kTotal',
            'trenNelayan',  'trenProduksi', 'trenGabungan',
            'top5Prod',     'top5Prdktv',  'top5Nelayan',
            'detailKab'
        ));
    }
}