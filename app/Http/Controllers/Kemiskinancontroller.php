<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KemiskinanController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->get('tahun', 'semua');

        // ══════════════════════════════════════════════════════════════════════
        // SELF JOIN: ambil data 2025 sekaligus data 2023 per kab/kota
        // Menggantikan dua query terpisah + penggabungan manual di PHP
        // ══════════════════════════════════════════════════════════════════════
        $joinData = DB::table('pnddkmiskin as t1')
            ->join('pnddkmiskin as t2', function ($join) {
                $join->on('t1.kabupatenKota', '=', 't2.kabupatenKota')
                     ->where('t2.tahun', 2023);
            })
            ->where('t1.tahun', 2025)
            ->select(
                't1.kabupatenKota',
                't1.jumlahPnddk as jumlahPnddk2025',
                't2.jumlahPnddk as jumlahPnddk2023'
            )
            ->orderBy('t1.kabupatenKota')
            ->get();

        // Bentuk collection per tahun dari hasil JOIN — tidak perlu query ulang
        $dataMiskin2025 = $joinData->map(fn($r) => (object)[
            'kabupatenKota' => $r->kabupatenKota,
            'jumlahPnddk'   => $r->jumlahPnddk2025,
        ]);

        $dataMiskin2023 = $joinData->map(fn($r) => (object)[
            'kabupatenKota' => $r->kabupatenKota,
            'jumlahPnddk'   => $r->jumlahPnddk2023,
        ]);

        // ══════════════════════════════════════════════════════════════════════
        // Tentukan dataset AKTIF berdasarkan tahun yang dipilih user
        // KPI, chart, peta ikut $dataAktif
        // ══════════════════════════════════════════════════════════════════════
        if ($selectedYear == '2023') {
            $dataAktif = $dataMiskin2023;
        } elseif ($selectedYear == '2025') {
            $dataAktif = $dataMiskin2025;
        } else {
            // "semua" → gabungan 2023 + 2025 untuk KPI & chart
            $dataAktif = collect()
                ->merge($dataMiskin2025->map(fn($r) => (object)[
                    'kabupatenKota' => $r->kabupatenKota,
                    'jumlahPnddk'   => $r->jumlahPnddk,
                    'tahun'         => 2025,
                ]))
                ->merge($dataMiskin2023->map(fn($r) => (object)[
                    'kabupatenKota' => $r->kabupatenKota,
                    'jumlahPnddk'   => $r->jumlahPnddk,
                    'tahun'         => 2023,
                ]))
                ->values();
        }

        // DATA TAMPIL untuk tabel — sama persis dengan $dataAktif

        $dataMiskin = $dataAktif;

        // ══════════════════════════════════════════════════════════════════════
        // KPI — semua pakai $dataAktif agar ikut filter tahun
        // Nama $rataRata2025 dipertahankan agar view tidak perlu diubah
        // ══════════════════════════════════════════════════════════════════════
        $rataRata2025 = $dataAktif->avg('jumlahPnddk');
        $rataRata2023 = $dataMiskin2023->avg('jumlahPnddk');

        $diAtasRataRata  = $dataAktif->filter(fn($r) => $r->jumlahPnddk > $rataRata2025)->count();
        $diBawahRataRata = $dataAktif->filter(fn($r) => $r->jumlahPnddk <= $rataRata2025)->count();
        $totalKab        = $dataAktif->count();

        $tertinggi = $dataAktif->sortByDesc('jumlahPnddk')->first();
        $terendah  = $dataAktif->sortBy('jumlahPnddk')->first();

        // Top 5 tertinggi & terendah
        $top5Tertinggi = $dataAktif->sortByDesc('jumlahPnddk')->take(5)->values();
        $top5Terendah  = $dataAktif->sortBy('jumlahPnddk')->take(5)->values();

        // Deviasi dari rata-rata per kab/kota
        $deviasi = $dataAktif->map(fn($r) => (object)[
    'kabupatenKota' => $r->kabupatenKota,
    'jumlahPnddk'   => $r->jumlahPnddk,
    'deviasi'       => round($r->jumlahPnddk - $rataRata2025, 2),
    'statusRata'    => $r->jumlahPnddk > $rataRata2025 ? 'atas' : 'bawah',
    'tahun'         => $r->tahun ?? null,   // ← tambah ini
])->sortByDesc('deviasi')->values();

        // Semua kab/kota untuk chart bar + peta
        $semuaKab = $dataAktif->map(fn($r) => (object)[
            'kabupatenKota' => $r->kabupatenKota,
            'jumlahPnddk'   => $r->jumlahPnddk,
            'statusRata'    => $r->jumlahPnddk > $rataRata2025 ? 'atas' : 'bawah',
        ])->sortByDesc('jumlahPnddk')->values();

        // ══════════════════════════════════════════════════════════════════════
        // Perubahan ranking — selalu 2023 vs 2025, tidak ikut filter
        // ══════════════════════════════════════════════════════════════════════
        $ranking2025 = $dataMiskin2025->sortByDesc('jumlahPnddk')
            ->values()
            ->map(fn($r, $i) => [
                'kab'   => $r->kabupatenKota,
                'rank'  => $i + 1,
                'nilai' => $r->jumlahPnddk,
            ]);

        $ranking2023 = $dataMiskin2023->sortByDesc('jumlahPnddk')
            ->values()
            ->map(fn($r, $i) => [
                'kab'   => $r->kabupatenKota,
                'rank'  => $i + 1,
                'nilai' => $r->jumlahPnddk,
            ]);

        $rankingGabung = $ranking2025->take(8)->map(function ($r) use ($ranking2023) {
            $old       = $ranking2023->firstWhere('kab', $r['kab']);
            $oldRank   = $old ? $old['rank'] : null;
            $perubahan = $oldRank ? $oldRank - $r['rank'] : null;
            return (object)[
                'kab'       => $r['kab'],
                'rank2025'  => $r['rank'],
                'rank2023'  => $oldRank,
                'nilai2025' => $r['nilai'],
                'nilai2023' => $old ? $old['nilai'] : null,
                'perubahan' => $perubahan,
            ];
        })->values();

        // ── Data Ketenagakerjaan per Usia ───────────────────────────────────
        $dataKerja2025 = DB::table('datakerja')->where('tahun', 2025)->get();
        $dataKerja2023 = DB::table('datakerja')->where('tahun', 2023)->get();

        $tingkatPengangguranPerUsia = $dataKerja2025->map(fn($r) => (object)[
            'usia'            => $r->Kelompok_Umur,
            'pct'             => $r->{'Angkatan Kerja - Jumlah Angkatan Kerja'} > 0
                                    ? round(($r->{'Angkatan Kerja Pengangguran - Jumlah'} / $r->{'Angkatan Kerja - Jumlah Angkatan Kerja'}) * 100, 2)
                                    : 0,
            'pengangguranJml' => $r->{'Angkatan Kerja Pengangguran - Jumlah'},
            'akJml'           => $r->{'Angkatan Kerja - Jumlah Angkatan Kerja'},
        ])->values();

        $pctBekerjaPerUsia2025 = $dataKerja2025->map(fn($r) => (object)[
            'usia' => $r->Kelompok_Umur,
            'pct'  => $r->{'Angkatan Kerja - Persentase Bekerja terhadap Angkatan Kerja'},
        ])->values();

        $pctBekerjaPerUsia2023 = $dataKerja2023->map(fn($r) => (object)[
            'usia' => $r->Kelompok_Umur,
            'pct'  => $r->{'Angkatan Kerja - Persentase Bekerja terhadap Angkatan Kerja'},
        ])->values();

        $strukturUsia = $dataKerja2025->map(fn($r) => (object)[
            'usia'         => $r->Kelompok_Umur,
            'bekerja'      => $r->{'Angkatan Kerja - Bekerja'},
            'pengangguran' => $r->{'Angkatan Kerja Pengangguran - Jumlah'},
            'nonAK'        => $r->{'Bukan Angkatan Kerja - Jumlah Bukan Angkatan Kerja'},
            'total'        => $r->{'Angkatan Kerja + Bukan Angkatan Kerja (Jumlah )'},
        ])->values();

        return view('pages.kemiskinan', compact(
            'selectedYear',
            'dataMiskin',
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