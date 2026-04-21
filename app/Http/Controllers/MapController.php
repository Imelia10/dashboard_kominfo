<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function geojson()
    {
        $path = resource_path('Kabupaten-Kota (Provinsi Jawa Timur).geojson');

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File GeoJSON tidak ditemukan',
            ], 404);
        }

        $geojson = file_get_contents($path);

        if ($geojson === false) {
            return response()->json([
                'message' => 'Gagal membaca file GeoJSON',
            ], 500);
        }

        return response($geojson, 200)->header('Content-Type', 'application/json');
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