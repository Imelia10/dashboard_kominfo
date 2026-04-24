<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnvironmentController extends Controller
{
    public function index()
    {
        // ── Semua tahun valid (untuk dropdown) ──
        $tahunList = DB::table('kebakaranhutan')
            ->select('tahun')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', 0)
            ->where('tahun', '!=', '')
            ->distinct()
            ->orderBy('tahun')
            ->pluck('tahun');

        // ── Semua lokasi valid (untuk dropdown) ──
        $lokasiList = DB::table('kebakaranhutan')
            ->select('lokasi')
            ->whereNotNull('lokasi')
            ->where('lokasi', '!=', '')
            ->where('lokasi', '!=', '0')
            ->distinct()
            ->orderBy('lokasi')
            ->pluck('lokasi');

        // ── Agregasi luas per tahun ──
        $perTahun = DB::table('kebakaranhutan')
            ->select('tahun', DB::raw('SUM(luas_areal_kebakaran) as total'))
            ->whereNotNull('tahun')
            ->where('tahun', '!=', 0)
            ->where('tahun', '!=', '')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        // ── Agregasi luas per lokasi (kumulatif) ──
        $perLokasi = DB::table('kebakaranhutan')
            ->select('lokasi', DB::raw('SUM(luas_areal_kebakaran) as total'))
            ->whereNotNull('lokasi')
            ->where('lokasi', '!=', '')
            ->where('lokasi', '!=', '0')
            ->groupBy('lokasi')
            ->orderByDesc('total')
            ->get();

        // ── KPI ──
        $totalLuas      = $perTahun->sum('total');
        $peakRow        = $perTahun->sortByDesc('total')->first();
        $tahunTertinggi = $peakRow->tahun ?? '-';
        $luasTertinggi  = $peakRow->total ?? 0;
        $rataRata       = $perTahun->count() > 0
            ? $totalLuas / $perTahun->count()
            : 0;

        // ── Top lokasi ──
        $topLokasi = $perLokasi->first();

        // ── Raw data tabel ──
        $rawData = DB::table('kebakaranhutan')
            ->orderBy('tahun')
            ->orderByDesc('luas_areal_kebakaran')
            ->get();

        return view('pages.environment', compact(
            'tahunList',
            'lokasiList',
            'perTahun',
            'perLokasi',
            'totalLuas',
            'tahunTertinggi',
            'luasTertinggi',
            'rataRata',
            'topLokasi',
            'rawData'
        ));
    }
}