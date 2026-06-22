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

        // ── Grafik 1: Korelasi Luas Panen vs Produksi per Kabupaten/Kota (agri3) ──
        // Tidak dibatasi limit agar Kota juga tetap muncul (Kota umumnya berproduksi
        // lebih kecil dibanding Kabupaten sehingga akan tersingkir jika dibatasi top-N)
        $korelasiData = DB::table('agri3')
            ->selectRaw('
                nama_kabupaten_kota,
                SUM(`Luas Panen Tanaman Padi (ha) (Ha)`) AS luas_panen,
                SUM(`Rekap Produksi Padi (ton) (Ton)`)   AS produksi
            ')
            ->where('tahun', $tahun)
            ->groupBy('nama_kabupaten_kota')
            ->orderByDesc('produksi')
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
        // CATATAN: card "Urban vs Rural Farming" di halaman sudah diganti
        // menjadi Peta Produktivitas Padi (lihat method mapData() di bawah
        // & view pages.agri). Variabel ini tetap dihitung & dikirim ke view
        // agar tidak mematahkan kompatibilitas bila masih dipakai di tempat lain.
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

    /**
     * Normalisasi nama wilayah agar pencocokan antara nama di database
     * ("Kabupaten Pacitan", "Kota Surabaya", dst.) dengan properti nama
     * pada file GeoJSON (yang formatnya bisa berbeda-beda — kadang tanpa
     * prefix "Kabupaten"/"Kota", kadang huruf besar semua, kadang ada
     * spasi ganda) tetap berhasil walau penulisannya tidak 100% identik.
     *
     * Contoh hasil normalisasi:
     *  "Kabupaten Pacitan"  -> "pacitan"
     *  "KAB. PACITAN"       -> "pacitan"
     *  "Kota Surabaya"      -> "surabaya"
     *  "KOTA SURABAYA"      -> "surabaya"
     */
    private function normalizeNamaWilayah(?string $nama): string
    {
        if (!$nama) {
            return '';
        }
        $n = mb_strtolower(trim($nama));
        // Hilangkan prefix administratif: "kabupaten", "kab.", "kab", "kota"
        $n = preg_replace('/^(kabupaten|kab\.?|kota)\s+/u', '', $n);
        // Rapikan tanda baca & spasi ganda sisa
        $n = preg_replace('/[.,]/u', '', $n);
        $n = preg_replace('/\s+/u', ' ', $n);
        return trim($n);
    }

    /**
     * API: GeoJSON peta produktivitas per Kabupaten/Kota (untuk peta di halaman Agri).
     * GET /api/agri/map?tahun=2025
     *
     * Menggabungkan geometri batas wilayah Jatim (file GeoJSON yang sama dipakai
     * oleh peta Bencana) dengan data produktivitas, luas panen, produksi, dan
     * distribusi semesteran per kabupaten/kota dari tabel agri3 & agri1.
     *
     * Pencocokan nama wilayah dilakukan dua tahap:
     *  1. Exact match terhadap nama_kabupaten_kota dari database.
     *  2. Jika gagal, fallback ke normalized match (lihat normalizeNamaWilayah())
     *     supaya tetap cocok walau format penulisan di GeoJSON sedikit berbeda
     *     (tanpa prefix, huruf besar semua, dll).
     *
     * Properti yang dikembalikan per feature (dipakai oleh popup
     * "Peta Produktivitas Padi" di pages.agri — pengganti card
     * "Urban vs Rural Farming"):
     *  - nama           : nama kabupaten/kota (dari database, bukan dari GeoJSON,
     *                     supaya penulisan konsisten di seluruh popup)
     *  - luas_panen     : total luas panen (Ha)              → "Luas Panen Aktif"
     *  - produksi       : total produksi GKG (Ton)            → "Total Produksi GKG"
     *  - produktivitas  : rata-rata produktivitas (Ku/Ha)     → dasar warna & badge popup
     *                       (>57 Tinggi / 55-57 Normal / <55 Rendah)
     *  - smt1, smt2     : total produksi semester 1 & 2 (Ton) → "Fokus Semester 1/2"
     *  - smt1_pct, smt2_pct : persentase distribusi semesteran
     *  - _matched       : flag debug (true/false) — apakah feature ini berhasil
     *                     dicocokkan dengan data agri3/agri1. Bisa dicek lewat
     *                     console di browser bila peta masih tampak kosong.
     */
    public function mapData(Request $request)
    {
        $tahun = $request->input('tahun', 2025);

        // ── Agregat Luas Panen, Produksi, Produktivitas per kab/kota (agri3) ──
        $agri3 = DB::table('agri3')
            ->selectRaw('
                nama_kabupaten_kota,
                SUM(`Luas Panen Tanaman Padi (ha) (Ha)`) AS luas_panen,
                SUM(`Rekap Produksi Padi (ton) (Ton)`)   AS produksi,
                AVG(`Produktivitas Tanaman Padi (ku/ha) (Ku/ha)`) AS produktivitas
            ')
            ->where('tahun', $tahun)
            ->where('nama_kabupaten_kota', '!=', 'Jawa Timur')
            ->groupBy('nama_kabupaten_kota')
            ->get()
            ->keyBy('nama_kabupaten_kota');

        // ── Distribusi Semesteran per kab/kota (agri1) ──
        $smt1Cols = ['Januari','Februari','Maret','April','Mei','Juni'];
        $smt2Cols = ['Juli','Agustus','September','Oktober','November','Desember'];

        $agri1 = DB::table('agri1')
            ->selectRaw(
                'nama_kabupaten_kota, ' .
                implode('+', array_map(fn($b) => "COALESCE(SUM(`$b`),0)", $smt1Cols)) . ' AS smt1, ' .
                implode('+', array_map(fn($b) => "COALESCE(SUM(`$b`),0)", $smt2Cols)) . ' AS smt2'
            )
            ->where('tahun', $tahun)
            ->where('nama_kabupaten_kota', '!=', 'Jawa Timur')
            ->groupBy('nama_kabupaten_kota')
            ->get()
            ->keyBy('nama_kabupaten_kota');

        // ── Lookup tambahan berdasarkan nama yang dinormalisasi (fallback) ──
        $agri3ByNorm = $agri3->keyBy(fn($r) => $this->normalizeNamaWilayah($r->nama_kabupaten_kota));
        $agri1ByNorm = $agri1->keyBy(fn($r) => $this->normalizeNamaWilayah($r->nama_kabupaten_kota));

        // ── Geometri batas wilayah Jatim ──
        // NOTE: gunakan public/data/jatim_kabkota.geojson bila tersedia, namun
        // fallback ke sumber resmi di resources apabila belum disebarkan ke public.
        $publicGeoPath   = public_path('data/jatim_kabkota.geojson');
        $resourceGeoPath = resource_path('Kabupaten-Kota (Provinsi Jawa Timur).geojson');

        if (file_exists($publicGeoPath)) {
            $geoPath = $publicGeoPath;
        } elseif (file_exists($resourceGeoPath)) {
            $geoPath = $resourceGeoPath;
        } else {
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => [],
                '_debug_error' => "GeoJSON file not found at: {$publicGeoPath} or {$resourceGeoPath}",
            ]);
        }

        $geoJson = file_get_contents($geoPath);
        $geo = json_decode($geoJson, true);
        if (!$geo || !isset($geo['features']) || !is_array($geo['features'])) {
            return response()->json([
                'type' => 'FeatureCollection',
                'features' => [],
                '_debug_error' => "GeoJSON file invalid or unreadable: {$geoPath}",
            ]);
        }

        $matchedCount   = 0;
        $unmatchedNames = [];

        foreach ($geo['features'] as &$feature) {
            // NOTE: sesuaikan/lengkapi daftar key ini jika field nama wilayah
            // di GeoJSON kamu memakai nama property lain (mis. 'NAMOBJ',
            // 'WADMKK', 'KABKOT', dst — umum dipakai pada GeoJSON batas
            // administrasi BIG/Kemendagri Indonesia).
            $namaGeo = $feature['properties']['nama']
                ?? $feature['properties']['NAME_2']
                ?? $feature['properties']['name']
                ?? $feature['properties']['NAMOBJ']
                ?? $feature['properties']['WADMKK']
                ?? $feature['properties']['KABKOT']
                ?? null;

            if (!$namaGeo) {
                continue;
            }

            // 1) Coba exact match dulu (paling cepat, paling akurat)
            $a3 = $agri3[$namaGeo] ?? null;
            $a1 = $agri1[$namaGeo] ?? null;

            // 2) Fallback ke normalized match jika exact match gagal
            $namaResolved = $namaGeo;
            if (!$a3 && !$a1) {
                $normGeo = $this->normalizeNamaWilayah($namaGeo);
                $a3 = $agri3ByNorm[$normGeo] ?? null;
                $a1 = $agri1ByNorm[$normGeo] ?? null;
                if ($a3) {
                    $namaResolved = $a3->nama_kabupaten_kota;
                } elseif ($a1) {
                    $namaResolved = $a1->nama_kabupaten_kota;
                }
            }

            $isMatched = (bool) ($a3 || $a1);
            if ($isMatched) {
                $matchedCount++;
            } else {
                $unmatchedNames[] = $namaGeo;
            }

            $smt1     = $a1->smt1 ?? 0;
            $smt2     = $a1->smt2 ?? 0;
            $smtTotal = $smt1 + $smt2;

            // Gunakan nama dari database (jika match) agar penulisan di popup
            // konsisten dengan tabel/chart lain di halaman. Jika tidak match,
            // tetap tampilkan nama asli dari GeoJSON.
            $feature['properties']['nama']         = $isMatched ? $namaResolved : $namaGeo;
            $feature['properties']['luas_panen']   = round($a3->luas_panen ?? 0);
            $feature['properties']['produksi']     = round($a3->produksi ?? 0);
            $feature['properties']['produktivitas']= round($a3->produktivitas ?? 0, 1);
            $feature['properties']['smt1']         = round($smt1);
            $feature['properties']['smt2']         = round($smt2);
            $feature['properties']['smt1_pct']     = $smtTotal > 0 ? round($smt1 / $smtTotal * 100) : 0;
            $feature['properties']['smt2_pct']     = $smtTotal > 0 ? round($smt2 / $smtTotal * 100) : 0;
            $feature['properties']['_matched']     = $isMatched;
        }
        unset($feature);

        // Info debug ringkas (tidak mempengaruhi rendering peta, hanya
        // membantu menelusuri masalah dari Network tab / console browser).
        $geo['_debug'] = [
            'tahun'            => $tahun,
            'total_features'   => count($geo['features']),
            'matched'          => $matchedCount,
            'unmatched_count'  => count($unmatchedNames),
            'unmatched_names'  => array_values(array_unique($unmatchedNames)),
        ];

        return response()->json($geo);
    }
}