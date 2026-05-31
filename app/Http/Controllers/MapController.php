<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    // ── Jenis bencana (sama dengan BencanaController) ─────────────
    private const JENIS = [
        'gempa', 'tsunami', 'gempa_tsunami', 'gunung', 'longsor',
        'banjir', 'kering', 'karhutla', 'cuaca_eks', 'abrasi',
    ];

    public function geojson()
    {
        $path = resource_path('Kabupaten-Kota (Provinsi Jawa Timur).geojson');

        if (!file_exists($path)) {
            return response()->json(['message' => 'File GeoJSON tidak ditemukan'], 404);
        }

        $geojson = file_get_contents($path);
        if ($geojson === false) {
            return response()->json(['message' => 'Gagal membaca file GeoJSON'], 500);
        }

        return response($geojson, 200)->header('Content-Type', 'application/json');
    }

    /**
     * GeoJSON bencana: merge data intensity & severity per kabupaten/kota
     * ke dalam properties setiap feature GeoJSON.
     *
     * Query param (optional): ?tahun=2024
     */
    public function bencanaGeojson(Request $request)
    {
        $path = resource_path('Kabupaten-Kota (Provinsi Jawa Timur).geojson');
        if (!file_exists($path)) {
            return response()->json(['message' => 'File GeoJSON tidak ditemukan'], 404);
        }

        $geojson = json_decode(file_get_contents($path), true);
        if (!$geojson) {
            return response()->json(['message' => 'GeoJSON tidak valid'], 500);
        }

        $tahun = $request->input('tahun', null);

        // ── Ambil data dari DB ────────────────────────────────────
        $qJml    = DB::table('jml_bencana');
        $qKorban = DB::table('korban');
        $qRumah  = DB::table('rumahrusak');

        if ($tahun) {
            $qJml->where('tahun', $tahun);
            $qKorban->where('tahun', $tahun);
            $qRumah->where('tahun', $tahun);
        }

        $rawJml    = $qJml->get()->groupBy('nama_kabupaten_kota');
        $rawKorban = $qKorban->get()->groupBy('nama_kabupaten_kota');
        $rawRumah  = $qRumah->get()->groupBy('nama_kabupaten_kota');

        // Gabungkan semua nama kabupaten
        $allKab = $rawJml->keys()
            ->merge($rawKorban->keys())
            ->merge($rawRumah->keys())
            ->unique();

        // ── Hitung per kabupaten ──────────────────────────────────
        $stats = [];
        foreach ($allKab as $nama) {
            $jmlRows    = $rawJml[$nama]    ?? collect();
            $korbanRows = $rawKorban[$nama] ?? collect();
            $rumahRows  = $rawRumah[$nama]  ?? collect();

            $totalKejadian = 0;
            $kejadianPerJenis = [];
            foreach (self::JENIS as $kode) {
                $v = $jmlRows->sum("jml_bencana_{$kode}");
                $kejadianPerJenis[$kode] = (int)$v;
                $totalKejadian += (int)$v;
            }

            $totalKorban = 0;
            foreach (self::JENIS as $kode) {
                $totalKorban += (int)$korbanRows->sum("korban_{$kode}_md_hlg");
                $totalKorban += (int)$korbanRows->sum("korban_{$kode}_luka");
            }

            $totalRumah = 0;
            foreach (self::JENIS as $kode) {
                $totalRumah += (int)$rumahRows->sum("rmh_rb_{$kode}");
                $totalRumah += (int)$rumahRows->sum("rmh_rs_{$kode}");
                $totalRumah += (int)$rumahRows->sum("rmh_rr_{$kode}");
            }

            $stats[$nama] = [
                'total_kejadian'     => $totalKejadian,
                'total_korban'       => $totalKorban,
                'total_rmh_rusak'    => $totalRumah,
                'severity_raw'       => $totalKejadian > 0
                    ? ($totalKorban + $totalRumah) / $totalKejadian
                    : 0,
                'kejadian_per_jenis' => $kejadianPerJenis,
            ];
        }

        // ── Min-Max normalisasi severity (0–10) ───────────────────
        $sevVals = array_column($stats, 'severity_raw');
        $maxSev  = count($sevVals) ? max($sevVals) : 1;
        $minSev  = count($sevVals) ? min($sevVals) : 0;
        foreach ($stats as $nama => &$s) {
            $s['severity'] = ($maxSev - $minSev) > 0
                ? round(($s['severity_raw'] - $minSev) / ($maxSev - $minSev) * 10, 2)
                : 0;
        }
        unset($s);

        // ── Helper: normalise nama untuk matching ─────────────────
        $normalize = function (string $s): string {
            $s = mb_strtolower(trim($s));
            // Perbaiki anomali "kota kota xxx" di geojson
            $s = preg_replace('/^kota kota /i', 'kota ', $s);
            return $s;
        };

        // Bangun lookup normalisasi dari stats
        $statsNorm = [];
        foreach ($stats as $nama => $val) {
            $statsNorm[$normalize($nama)] = $val;
        }

        // ── Merge ke GeoJSON features ─────────────────────────────
        foreach ($geojson['features'] as &$feature) {
            $p     = $feature['properties'];
            $type  = $p['TYPE_2']  ?? '';
            $name  = $p['NAME_2']  ?? '';
            $full  = trim($type . ' ' . $name);
            $key   = $normalize($full);

            $d = $statsNorm[$key] ?? null;

            $feature['properties']['nama']           = $full;
            $feature['properties']['total_kejadian'] = $d ? $d['total_kejadian']  : 0;
            $feature['properties']['total_korban']   = $d ? $d['total_korban']    : 0;
            $feature['properties']['total_rmh_rusak']= $d ? $d['total_rmh_rusak'] : 0;
            $feature['properties']['severity']       = $d ? $d['severity']        : 0;
            $feature['properties']['severity_raw']   = $d ? round($d['severity_raw'], 1) : 0;
        }
        unset($feature);

        return response()->json($geojson)
            ->header('Content-Type', 'application/json');
    }

    public function statistik(Request $request)
    {
        $tahun = (int) $request->input('tahun', date('Y'));

        $data = DB::table('statusdesa')
            ->where('tahun', $tahun)
            ->select(
                'kode_kabupaten_kota',
                'nama_kabupaten_kota',
                'sangat_tertinggal',
                'tertinggal',
                'berkembang',
                'maju',
                'mandiri'
            )
            ->get()
            ->map(function ($row) {
                return [
                    'kode_kabupaten_kota' => str_pad((string) $row->kode_kabupaten_kota, 4, '0', STR_PAD_LEFT),
                    'nama_kabupaten_kota' => $row->nama_kabupaten_kota,
                    'sangat_tertinggal'   => (int) $row->sangat_tertinggal,
                    'tertinggal'          => (int) $row->tertinggal,
                    'berkembang'          => (int) $row->berkembang,
                    'maju'                => (int) $row->maju,
                    'mandiri'             => (int) $row->mandiri,
                ];
            })
            ->values();

        return response()->json($data);
    }
}