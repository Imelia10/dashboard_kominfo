<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KepadatanController extends Controller
{
    public function index(Request $request)
{
    $tahunList = DB::table('jml_penduduk')
        ->select('tahun')
        ->distinct()
        ->orderBy('tahun')
        ->where('tahun', '!=', 2022)  // ← tambah ini
        ->pluck('tahun');

    // Pakai tahun dari request GET, fallback ke tahun terakhir
    $tahunDefault = $request->input('tahun', $tahunList->last());

    // Pastikan tahun valid
    if (!$tahunList->contains($tahunDefault)) {
        $tahunDefault = $tahunList->last();
    }

    return view('pages.kepadatan', compact('tahunList', 'tahunDefault'));
}

    public function apiData(Request $request)
    {
        $tahun = $request->input('tahun', 2024);

        /**
         * Subquery luas: ambil 1 baris per wilayah (GROUP BY nama),
         * pakai MIN() supaya tidak duplikat walau luas_wlyh punya banyak tahun.
         * Luas wilayah secara geografis tidak berubah antar tahun jadi ini aman.
         *
         * Kategori kepadatan mengacu PUSLITBANG Permukiman 2011 (jiwa/ha):
         *   <150 jiwa/ha  = <15.000 jiwa/km²  → Tidak Padat
         *   151-200 jiwa/ha = 15.001-20.000    → Sedang
         *   201-400 jiwa/ha = 20.001-40.000    → Tinggi
         *   >400 jiwa/ha  = >40.000 jiwa/km²  → Sangat Padat
         */
        $sql = "
            SELECT
                jp.kode_kabupaten_kota,
                jp.nama_kabupaten_kota,
                jp.tahun,
                (jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) AS total_penduduk,
                jp.`Jumlah Penduduk Laki  Laki`  AS laki_laki,
                jp.`Jumlah Penduduk Perempuan`   AS perempuan,
                lw.luas_km2,
                lw.jumlah_pulau,
                ROUND(
                    (jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`)
                    / NULLIF(lw.luas_km2, 0)
                , 2) AS kepadatan,
                CASE
                    WHEN ROUND((jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) / NULLIF(lw.luas_km2, 0), 2) < 15000
                        THEN 'Tidak Padat'
                    WHEN ROUND((jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) / NULLIF(lw.luas_km2, 0), 2) <= 20000
                        THEN 'Sedang'
                    WHEN ROUND((jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) / NULLIF(lw.luas_km2, 0), 2) <= 40000
                        THEN 'Tinggi'
                    ELSE 'Sangat Padat'
                END AS kategori_kepadatan
            FROM jml_penduduk jp
            LEFT JOIN (
                SELECT
                    LOWER(TRIM(nama_kabupaten_kota))  AS nama_key,
                    MIN(`Luas Wilayah (Km2)`)          AS luas_km2,
                    MIN(`Jumlah Pulau`)                AS jumlah_pulau
                FROM luas_wlyh
                GROUP BY LOWER(TRIM(nama_kabupaten_kota))
            ) lw ON lw.nama_key = LOWER(TRIM(jp.nama_kabupaten_kota))
            WHERE jp.tahun = ?
              AND LOWER(jp.nama_kabupaten_kota) NOT LIKE '%jawa timur%'
            ORDER BY kepadatan DESC
        ";

        $data = collect(DB::select($sql, [$tahun]));

        $summary = [
            'total_penduduk' => $data->sum('total_penduduk'),
            'rata_kepadatan' => round($data->avg('kepadatan'), 2),
            'tidak_padat'  => $data->where('kategori_kepadatan', 'Tidak Padat')->count(),
            'sedang'       => $data->where('kategori_kepadatan', 'Sedang')->count(),
            'tinggi'       => $data->where('kategori_kepadatan', 'Tinggi')->count(),
            'sangat_padat' => $data->where('kategori_kepadatan', 'Sangat Padat')->count(),
        ];

        return response()->json(['tahun' => $tahun, 'data' => $data, 'summary' => $summary]);
    }

    public function apiTrend(Request $request)
    {
        $kode = $request->input('kode');
        if (!$kode) return response()->json(['error' => 'kode diperlukan'], 400);

        // Untuk trend: pakai luas per tahun agar grafik kepadatan antar tahun akurat
        // Kalau luas_wlyh tidak punya tahun tertentu, fallback ke MIN
        $data = DB::select("
            SELECT
                jp.tahun,
                jp.nama_kabupaten_kota,
                (jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) AS total_penduduk,
                jp.`Jumlah Penduduk Laki  Laki` AS laki_laki,
                jp.`Jumlah Penduduk Perempuan`  AS perempuan,
                lw.luas_km2,
                ROUND(
                    (jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`)
                    / NULLIF(lw.luas_km2, 0)
                , 2) AS kepadatan
            FROM jml_penduduk jp
            LEFT JOIN (
                SELECT
                    LOWER(TRIM(nama_kabupaten_kota)) AS nama_key,
                    MIN(`Luas Wilayah (Km2)`)         AS luas_km2
                FROM luas_wlyh
                GROUP BY LOWER(TRIM(nama_kabupaten_kota))
            ) lw ON lw.nama_key = LOWER(TRIM(jp.nama_kabupaten_kota))
            WHERE jp.kode_kabupaten_kota = ?
            ORDER BY jp.tahun
        ", [$kode]);

        return response()->json(['kode' => $kode, 'trend' => $data]);
    }

    public function apiUmur(Request $request)
    {
        $tahun = $request->input('tahun', 2023);
        $data  = DB::table('penduduk_umur')
            ->where('tahun', $tahun)
            ->orderBy('Kelompok_Umur')
            ->get();
        return response()->json(['tahun' => $tahun, 'data' => $data]);
    }
}