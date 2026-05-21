<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KemiskinanController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('tahun', 'semua');

// DATA KEMISKINAN
        if ($selectedYear == '2025') {

    $dataMiskin = DB::table('pnddkmiskin')
        ->where('tahun', 2025)
        ->orderBy('kabupatenKota')
        ->get();

} elseif ($selectedYear == '2023') {

    $dataMiskin = DB::table('pnddkmiskin')
        ->where('tahun', 2023)
        ->orderBy('kabupatenKota')
        ->get();

} else {

    // semua data 2023 + 2025
    $dataMiskin = DB::table('pnddkmiskin')
        ->whereIn('tahun', [2023, 2025])
        ->orderBy('tahun')
        ->orderBy('kabupatenKota')
        ->get();
}

// tetap dipakai untuk perbandingan ranking
$dataMiskin2025 = DB::table('pnddkmiskin')
    ->where('tahun', 2025)
    ->orderBy('kabupatenKota')
    ->get();

$dataMiskin2023 = DB::table('pnddkmiskin')
    ->where('tahun', 2023)
    ->orderBy('kabupatenKota')
    ->get();

        // KPI: rata-rata kemiskinan
        $rataRata2025 = $dataMiskin->avg('jumlahPnddk');
        $rataRata2023 = $dataMiskin2023->avg('jumlahPnddk');

        // KPI: di atas dan di bawah rata-rata
        $diAtasRataRata  = $dataMiskin2025->filter(fn($r) => $r->jumlahPnddk > $rataRata2025)->count();
        $diBawahRataRata = $dataMiskin2025->filter(fn($r) => $r->jumlahPnddk <= $rataRata2025)->count();
        $totalKab        = $dataMiskin2025->count();

        // KPI: tertinggi & terendah
        $tertinggi = $dataMiskin2025->sortByDesc('jumlahPnddk')->first();
        $terendah  = $dataMiskin2025->sortBy('jumlahPnddk')->first();

        // Top 5 tertinggi & terendah
        $top5Tertinggi = $dataMiskin2025->sortByDesc('jumlahPnddk')->take(5)->values();
        $top5Terendah  = $dataMiskin2025->sortBy('jumlahPnddk')->take(5)->values();

        // Deviasi dari rata-rata per kab/kota
        $deviasi = $dataMiskin2025->map(fn($r) => (object)[
            'kabupatenKota' => $r->kabupatenKota,
            'jumlahPnddk'   => $r->jumlahPnddk,
            'deviasi'       => round($r->jumlahPnddk - $rataRata2025, 2),
            'statusRata'    => $r->jumlahPnddk > $rataRata2025 ? 'atas' : 'bawah',
        ])->sortByDesc('deviasi')->values();

        // Semua 38 kab/kota untuk chart bar + peta
        $semuaKab = $dataMiskin2025->map(fn($r) => (object)[
            'kabupatenKota' => $r->kabupatenKota,
            'jumlahPnddk'   => $r->jumlahPnddk,
            'statusRata'    => $r->jumlahPnddk > $rataRata2025 ? 'atas' : 'bawah',
        ])->sortByDesc('jumlahPnddk')->values();

        // Perubahan ranking 2023 vs 2025
        $ranking2025 = $dataMiskin2025->sortByDesc('jumlahPnddk')
            ->values()
            ->map(fn($r, $i) => ['kab' => $r->kabupatenKota, 'rank' => $i + 1, 'nilai' => $r->jumlahPnddk]);

        $ranking2023 = $dataMiskin2023->sortByDesc('jumlahPnddk')
            ->values()
            ->map(fn($r, $i) => ['kab' => $r->kabupatenKota, 'rank' => $i + 1, 'nilai' => $r->jumlahPnddk]);

        // Gabung ranking untuk top 5
        $rankingGabung = $ranking2025->take(8)->map(function ($r) use ($ranking2023) {
            $old = $ranking2023->firstWhere('kab', $r['kab']);
            $oldRank = $old ? $old['rank'] : null;
            $perubahan = $oldRank ? $oldRank - $r['rank'] : null; // positif = naik
            return (object)[
                'kab'        => $r['kab'],
                'rank2025'   => $r['rank'],
                'rank2023'   => $oldRank,
                'nilai2025'  => $r['nilai'],
                'nilai2023'  => $old ? $old['nilai'] : null,
                'perubahan'  => $perubahan,
            ];
        })->values();

        // ── Data Ketenagakerjaan per Usia ───────────────────────────────────
        $dataKerja2025 = DB::table('datakerja')->where('tahun', 2025)->get();
        $dataKerja2023 = DB::table('datakerja')->where('tahun', 2023)->get();

        // Chart 9: Tingkat pengangguran per usia 2025
        // Rumus: Pengangguran Jumlah / Jumlah AK * 100
        $tingkatPengangguranPerUsia = $dataKerja2025->map(fn($r) => (object)[
            'usia'      => $r->Kelompok_Umur,
            'pct'       => $r->{'Angkatan Kerja - Jumlah Angkatan Kerja'} > 0
                            ? round(($r->{'Angkatan Kerja Pengangguran - Jumlah'} / $r->{'Angkatan Kerja - Jumlah Angkatan Kerja'}) * 100, 2)
                            : 0,
            'pengangguranJml' => $r->{'Angkatan Kerja Pengangguran - Jumlah'},
            'akJml'           => $r->{'Angkatan Kerja - Jumlah Angkatan Kerja'},
        ])->values();

        // Chart 10: % bekerja per usia 2023 vs 2025
        $pctBekerjaPerUsia2025 = $dataKerja2025->map(fn($r) => (object)[
            'usia' => $r->Kelompok_Umur,
            'pct'  => $r->{'Angkatan Kerja - Persentase Bekerja terhadap Angkatan Kerja'},
        ])->values();

        $pctBekerjaPerUsia2023 = $dataKerja2023->map(fn($r) => (object)[
            'usia' => $r->Kelompok_Umur,
            'pct'  => $r->{'Angkatan Kerja - Persentase Bekerja terhadap Angkatan Kerja'},
        ])->values();

        // Struktur penduduk per usia (bekerja / pengangguran / non-AK)
        $strukturUsia = $dataKerja2025->map(fn($r) => (object)[
            'usia'        => $r->Kelompok_Umur,
            'bekerja'     => $r->{'Angkatan Kerja - Bekerja'},
            'pengangguran'=> $r->{'Angkatan Kerja Pengangguran - Jumlah'},
            'nonAK'       => $r->{'Bukan Angkatan Kerja - Jumlah Bukan Angkatan Kerja'},
            'total'       => $r->{'Angkatan Kerja + Bukan Angkatan Kerja (Jumlah )'},
        ])->values();

        return view('pages.kemiskinan', compact(
                'selectedYear',
            'dataMiskin2025',
            'dataMiskin2023',
            'rataRata2025',
            'rataRata2023',
            'diAtasRataRata',
            'diBawahRataRata',
            'totalKab',
            'tertinggi',
            'terendah',
            'top5Tertinggi',
            'top5Terendah',
            'deviasi',
            'semuaKab',
            'rankingGabung',
            'tingkatPengangguranPerUsia',
            'pctBekerjaPerUsia2025',
            'pctBekerjaPerUsia2023',
            'strukturUsia',
        ));
    }
}