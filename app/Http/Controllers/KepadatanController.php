<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KepadatanController extends Controller
{
   public function index()
{
    $tahunList = DB::table('jml_penduduk')
        ->select('tahun')->distinct()->orderBy('tahun')->pluck('tahun');
    $tahunDefault = $tahunList->last();
    return view('pages.kepadatan', compact('tahunList', 'tahunDefault'));
}

    public function apiData(Request $request)
    {
        $tahun = $request->input('tahun', 2024);
        $sql = "SELECT jp.kode_kabupaten_kota, jp.nama_kabupaten_kota, jp.tahun,
            (jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) as total_penduduk,
            jp.`Jumlah Penduduk Laki  Laki` as laki_laki,
            jp.`Jumlah Penduduk Perempuan` as perempuan,
            lw.`Luas Wilayah (Km2)` as luas_km2,
            lw.`Jumlah Pulau` as jumlah_pulau,
            ROUND((jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) / NULLIF(lw.`Luas Wilayah (Km2)`, 0), 2) as kepadatan,
            CASE
                WHEN ROUND((jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) / NULLIF(lw.`Luas Wilayah (Km2)`, 0), 2) <= 50 THEN 'Tidak Padat'
                WHEN ROUND((jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) / NULLIF(lw.`Luas Wilayah (Km2)`, 0), 2) <= 250 THEN 'Kurang Padat'
                WHEN ROUND((jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) / NULLIF(lw.`Luas Wilayah (Km2)`, 0), 2) <= 400 THEN 'Cukup Padat'
                ELSE 'Sangat Padat'
            END as kategori_kepadatan
            FROM jml_penduduk jp
            LEFT JOIN luas_wlyh lw ON TRIM(lw.nama_kabupaten_kota) = TRIM(jp.nama_kabupaten_kota) AND lw.tahun = jp.tahun
            WHERE jp.tahun = ? AND LOWER(jp.nama_kabupaten_kota) NOT LIKE '%jawa timur%'
            ORDER BY kepadatan DESC";
        $data = collect(DB::select($sql, [$tahun]));
        $summary = [
            'total_penduduk' => $data->sum('total_penduduk'),
            'rata_kepadatan' => round($data->avg('kepadatan'), 2),
            'tidak_padat'  => $data->where('kategori_kepadatan','Tidak Padat')->count(),
            'kurang_padat' => $data->where('kategori_kepadatan','Kurang Padat')->count(),
            'cukup_padat'  => $data->where('kategori_kepadatan','Cukup Padat')->count(),
            'sangat_padat' => $data->where('kategori_kepadatan','Sangat Padat')->count(),
        ];
        return response()->json(['tahun'=>$tahun,'data'=>$data,'summary'=>$summary]);
    }

    public function apiTrend(Request $request)
    {
        $kode = $request->input('kode');
        if (!$kode) return response()->json(['error'=>'kode diperlukan'],400);
        $data = DB::select("SELECT jp.tahun, jp.nama_kabupaten_kota,
            (jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) as total_penduduk,
            jp.`Jumlah Penduduk Laki  Laki` as laki_laki,
            jp.`Jumlah Penduduk Perempuan` as perempuan,
            lw.`Luas Wilayah (Km2)` as luas_km2,
            ROUND((jp.`Jumlah Penduduk Laki  Laki` + jp.`Jumlah Penduduk Perempuan`) / NULLIF(lw.`Luas Wilayah (Km2)`, 0), 2) as kepadatan
            FROM jml_penduduk jp
            LEFT JOIN luas_wlyh lw ON TRIM(lw.nama_kabupaten_kota) = TRIM(jp.nama_kabupaten_kota) AND lw.tahun = jp.tahun
            WHERE jp.kode_kabupaten_kota = ? ORDER BY jp.tahun", [$kode]);
        return response()->json(['kode'=>$kode,'trend'=>$data]);
    }

    public function apiUmur(Request $request)
    {
        $tahun = $request->input('tahun', 2023);
        $data = DB::table('penduduk_umur')->where('tahun',$tahun)->orderBy('Kelompok_Umur')->get();
        return response()->json(['tahun'=>$tahun,'data'=>$data]);
    }
}