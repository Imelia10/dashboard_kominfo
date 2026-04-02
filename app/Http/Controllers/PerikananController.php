<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerikananController extends Controller
{
    public function index(Request $request)
    {
        // ── 1. Daftar tahun dari kedua tabel ──────────────────────────────────
        $daftarTahun = DB::table('produklaut')
            ->select('tahun')
            ->union(DB::table('nelayan')->select('tahun'))
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->unique()
            ->values();

        $tahunAktif = $request->input('tahun', $daftarTahun->first() ?? date('Y'));

        // ── 2. Tahun sebelumnya (untuk growth) ───────────────────────────────
        $tahunSebelum = $tahunAktif - 1;

        // ── 3. Agregat produksi per tahun aktif ───────────────────────────────
        $produksiAktif = DB::table('produklaut')
            ->where('tahun', $tahunAktif)
            ->selectRaw('
                SUM(ikan)                  AS total_ikan,
                SUM(binatang_lunak)        AS total_binatang_lunak,
                SUM(binatang_berkulit_keras) AS total_berkulit_keras,
                SUM(binatang_air_lainnya)  AS total_binatang_lainnya,
                SUM(ikan + binatang_lunak + binatang_berkulit_keras + binatang_air_lainnya) AS grand_total
            ')
            ->first();

        // ── 4. Agregat produksi tahun sebelumnya ─────────────────────────────
        $produksiSebelum = DB::table('produklaut')
            ->where('tahun', $tahunSebelum)
            ->selectRaw('SUM(ikan + binatang_lunak + binatang_berkulit_keras + binatang_air_lainnya) AS grand_total')
            ->first();

        // ── 5. Agregat nelayan ────────────────────────────────────────────────
        $nelayanAktif   = DB::table('nelayan')->where('tahun', $tahunAktif)->sum(DB::raw('laut + perairan_umum'));
        $nelayanSebelum = DB::table('nelayan')->where('tahun', $tahunSebelum)->sum(DB::raw('laut + perairan_umum'));

        // ── 6. KPI values ─────────────────────────────────────────────────────
        $totalIkan            = (float)($produksiAktif->total_ikan            ?? 0);
        $totalBinatangLunak   = (float)($produksiAktif->total_binatang_lunak  ?? 0);
        $totalBerkulitKeras   = (float)($produksiAktif->total_berkulit_keras  ?? 0);
        $totalBinatangLainnya = (float)($produksiAktif->total_binatang_lainnya ?? 0);
        $grandTotal           = (float)($produksiAktif->grand_total           ?? 0);
        $totalLainnya         = 0; // placeholder — bisa diisi bila ada kolom lain
        $totalNelayan         = (float)$nelayanAktif;

        // Nilai produksi estimasi (grand_total ton × harga rata-rata Rp 20.000/kg)
        $hargaPerKg           = 20000;
        $totalNilaiProduksi   = $grandTotal * 1000 * $hargaPerKg; // ton → kg → rupiah

        // Rata-rata produksi per nelayan per hari (asumsi 300 hari tangkap/tahun)
        $hariTangkap          = 300;
        $rataRataProduksiPerNelayan = $totalNelayan > 0
            ? ($grandTotal * 1000) / ($totalNelayan * $hariTangkap)
            : 0;

        // ── 7. Growth calculations ────────────────────────────────────────────
        $prevGrandTotal = (float)($produksiSebelum->grand_total ?? 0);
        $growthNilai    = $prevGrandTotal > 0 ? (($grandTotal - $prevGrandTotal) / $prevGrandTotal) * 100 : null;

        $prevNelayan    = (float)$nelayanSebelum;
        $growthNelayan  = $prevNelayan > 0 ? (($totalNelayan - $prevNelayan) / $prevNelayan) * 100 : null;

        // Rata produksi per nelayan tahun sebelumnya
        $prevRata       = $prevNelayan > 0 ? ($prevGrandTotal * 1000) / ($prevNelayan * $hariTangkap) : 0;
        $growthRata     = $prevRata > 0 ? (($rataRataProduksiPerNelayan - $prevRata) / $prevRata) * 100 : null;

        // ── 8. Top kabupaten dengan nelayan terbanyak ─────────────────────────
        $topNelayanKabupaten = DB::table('nelayan')
            ->where('tahun', $tahunAktif)
            ->selectRaw('
                kode_kabupaten_kota,
                nama_kabupaten_kota,
                SUM(laut + perairan_umum) AS total_nelayan
            ')
            ->groupBy('kode_kabupaten_kota', 'nama_kabupaten_kota')
            ->orderByDesc('total_nelayan')
            ->limit(6)
            ->get();

        // ── 9. Top kabupaten dengan produksi tertinggi ────────────────────────
        $topProduksiKabupaten = DB::table('produklaut')
            ->where('tahun', $tahunAktif)
            ->selectRaw('
                kode_kabupaten_kota,
                nama_kabupaten_kota,
                SUM(ikan + binatang_lunak + binatang_berkulit_keras + binatang_air_lainnya) AS total_produksi
            ')
            ->groupBy('kode_kabupaten_kota', 'nama_kabupaten_kota')
            ->orderByDesc('total_produksi')
            ->limit(10)
            ->get();

        // ── 10. Detail per kabupaten (join produksi + nelayan) ────────────────
        $detailPerKabupaten = DB::table('produklaut as p')
            ->where('p.tahun', $tahunAktif)
            ->leftJoin(
                DB::raw('(SELECT kode_kabupaten_kota, SUM(laut + perairan_umum) AS total_nelayan FROM nelayan WHERE tahun = '.(int)$tahunAktif.' GROUP BY kode_kabupaten_kota) AS n'),
                'p.kode_kabupaten_kota', '=', 'n.kode_kabupaten_kota'
            )
            ->selectRaw('
                p.nama_kabupaten_kota,
                SUM(p.ikan)                    AS ikan,
                SUM(p.binatang_lunak)          AS binatang_lunak,
                SUM(p.binatang_berkulit_keras) AS binatang_berkulit_keras,
                SUM(p.binatang_air_lainnya)    AS binatang_air_lainnya,
                SUM(p.ikan + p.binatang_lunak + p.binatang_berkulit_keras + p.binatang_air_lainnya) AS total_produksi,
                MAX(n.total_nelayan)           AS total_nelayan
            ')
            ->groupBy('p.kode_kabupaten_kota', 'p.nama_kabupaten_kota')
            ->orderByDesc('total_produksi')
            ->get();

        return view('pages.perikanan', compact(
            'tahunAktif',
            'daftarTahun',
            'totalNilaiProduksi',
            'totalNelayan',
            'rataRataProduksiPerNelayan',
            'growthNilai',
            'growthNelayan',
            'growthRata',
            'totalIkan',
            'totalBinatangLunak',
            'totalBerkulitKeras',
            'totalBinatangLainnya',
            'totalLainnya',
            'grandTotal',
            'topNelayanKabupaten',
            'topProduksiKabupaten',
            'detailPerKabupaten'
        ));
    }
}