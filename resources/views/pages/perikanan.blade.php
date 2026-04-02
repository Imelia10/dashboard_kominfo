@extends('layouts.app')

@section('content')
{{-- Wrapper: full height, scrollable, background abu-abu --}}
<div class="h-full overflow-y-auto bg-[#F4F6FA]">
  <div class="w-full px-8 py-7 pb-16">

    {{-- ═══════════════════════════════════════════
         PAGE HEADER
    ═══════════════════════════════════════════ --}}
    <div class="mb-6">
      {{-- Breadcrumb --}}
      <div class="flex items-center gap-1.5 mb-2">
        <a href="{{ route('home') }}" class="text-[11px] text-[#1565C0] hover:underline">Dashboard</a>
        <svg class="w-3 h-3 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-[11px] text-gray-400">Produksi Perikanan</span>
      </div>

      <div class="flex items-end justify-between flex-wrap gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <div class="w-6 h-[3px] rounded-full bg-gradient-to-r from-[#1565C0] to-[#42A5F5]"></div>
            <span class="text-[10px] font-bold text-[#1565C0] uppercase tracking-[1.4px]">DASHBOARD</span>
          </div>
          <h1 class="text-[28px] font-extrabold text-[#0d2a5e] leading-tight" style="font-family:'Plus Jakarta Sans',sans-serif; letter-spacing:-0.5px">
            Analisis Data Perikanan <span class="text-[#1565C0]">Jawa Timur</span> {{ $tahunAktif }}
          </h1>
          <p class="text-[13px] text-gray-400 mt-1">Dashboard Analisis Produksi dan Aktivitas Nelayan Regional</p>
        </div>

        <form method="GET" action="{{ route('perikanan') }}" class="flex items-center gap-2">
          <div class="relative">
            <select name="tahun" onchange="this.form.submit()"
              class="appearance-none pl-4 pr-9 py-[9px] rounded-lg text-[12.5px] font-semibold text-white cursor-pointer outline-none border-0"
              style="background:#1565C0">
              @foreach($daftarTahun as $t)
                <option value="{{ $t }}" {{ $t==$tahunAktif?'selected':'' }}>Januari – Desember {{ $t }}</option>
              @endforeach
            </select>
            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </div>
          <button type="submit" name="mode" value="perbandingan"
            class="px-4 py-[9px] rounded-lg border border-[#e0e5ef] bg-white text-[#374151] text-[12.5px] font-semibold hover:bg-gray-50 transition-colors whitespace-nowrap">
            Perbandingan Tahun
          </button>
        </form>
      </div>
    </div>

    {{-- ═══════════════════════════════════════════
         KPI CARDS — 3 kolom penuh lebar
    ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-3 gap-4 mb-5">

      {{-- KPI 1: Total Nilai Produksi --}}
      <div class="relative bg-white rounded-2xl p-5 border border-[#e9edf4] overflow-hidden"
        style="box-shadow:0 2px 8px rgba(21,101,192,0.07)">
        <div class="absolute top-0 inset-x-0 h-[3px] rounded-t-2xl bg-gradient-to-r from-[#1565C0] to-[#42A5F5]"></div>
        <div class="flex items-start justify-between mb-3 pt-1">
          <div>
            <div class="text-[10.5px] font-semibold text-gray-400 uppercase tracking-[0.8px] mb-1">Total Nilai Produksi</div>
            @if($growthNilai !== null)
            <span class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2 py-0.5 rounded-full {{ $growthNilai>=0?'bg-[#e8f5e9] text-[#2E7D32]':'bg-[#fdecea] text-[#c62828]' }}">
              {{ $growthNilai>=0?'↑':'↓' }} {{ abs(round($growthNilai,1)) }}%
            </span>
            @endif
          </div>
          <div class="w-10 h-10 rounded-xl bg-[#EFF6FF] flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-[#1565C0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>
            </svg>
          </div>
        </div>
        <div class="text-[11px] text-gray-400 mb-1">Total Nilai Produksi</div>
        <div class="text-[26px] font-extrabold text-[#0d2a5e] leading-tight" style="font-family:'Plus Jakarta Sans',sans-serif">
          Rp {{ number_format($totalNilaiProduksi/1e12,1) }} Triliun
        </div>
      </div>

      {{-- KPI 2: Total Jumlah Nelayan --}}
      <div class="relative bg-white rounded-2xl p-5 border border-[#e9edf4] overflow-hidden"
        style="box-shadow:0 2px 8px rgba(21,101,192,0.07)">
        <div class="absolute top-0 inset-x-0 h-[3px] rounded-t-2xl bg-gradient-to-r from-[#6A1B9A] to-[#AB47BC]"></div>
        <div class="flex items-start justify-between mb-3 pt-1">
          <div>
            <div class="text-[10.5px] font-semibold text-gray-400 uppercase tracking-[0.8px] mb-1">Total Jumlah Nelayan</div>
            @if($growthNelayan !== null)
            <span class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2 py-0.5 rounded-full {{ $growthNelayan>=0?'bg-[#e8f5e9] text-[#2E7D32]':'bg-[#fdecea] text-[#c62828]' }}">
              {{ $growthNelayan>=0?'↑':'↓' }} {{ abs(round($growthNelayan,1)) }}%
            </span>
            @endif
          </div>
          <div class="w-10 h-10 rounded-xl bg-[#F5F3FF] flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-[#6A1B9A]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
          </div>
        </div>
        <div class="text-[11px] text-gray-400 mb-1">Total Jumlah Nelayan</div>
        <div class="text-[26px] font-extrabold text-[#0d2a5e] leading-tight" style="font-family:'Plus Jakarta Sans',sans-serif">
          {{ number_format($totalNelayan) }} <span class="text-[15px] text-gray-400 font-medium">jiwa</span>
        </div>
      </div>

      {{-- KPI 3: Rata-rata Produksi / Nelayan --}}
      <div class="relative bg-white rounded-2xl p-5 border border-[#e9edf4] overflow-hidden"
        style="box-shadow:0 2px 8px rgba(21,101,192,0.07)">
        <div class="absolute top-0 inset-x-0 h-[3px] rounded-t-2xl bg-gradient-to-r from-[#E65100] to-[#FFA726]"></div>
        <div class="flex items-start justify-between mb-3 pt-1">
          <div>
            <div class="text-[10.5px] font-semibold text-gray-400 uppercase tracking-[0.8px] mb-1">Rata-rata Produksi / Nelayan</div>
            @if($growthRata !== null)
            <span class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2 py-0.5 rounded-full {{ $growthRata>=0?'bg-[#e8f5e9] text-[#2E7D32]':'bg-[#fdecea] text-[#c62828]' }}">
              {{ $growthRata>=0?'↑':'↓' }} {{ abs(round($growthRata,1)) }}%
            </span>
            @endif
          </div>
          <div class="w-10 h-10 rounded-xl bg-[#FFF3E0] flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-[#E65100]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 10h16l-2 3H6l-2-3z"/><path d="M12 4v6"/>
              <path d="M2 16c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/>
            </svg>
          </div>
        </div>
        <div class="text-[11px] text-gray-400 mb-1">Rata-rata Produksi / Nelayan</div>
        <div class="text-[26px] font-extrabold text-[#0d2a5e] leading-tight" style="font-family:'Plus Jakarta Sans',sans-serif">
          {{ $totalNelayan>0 ? number_format($rataRataProduksiPerNelayan,1) : '0' }}
          <span class="text-[15px] text-gray-400 font-medium">kg/hari</span>
        </div>
      </div>

    </div>

    {{-- ═══════════════════════════════════════════
         ROW 2: Nelayan Chart (2/3) + Tangkapan Chart (1/3)
    ═══════════════════════════════════════════ --}}
    <div class="grid gap-4 mb-5" style="grid-template-columns:2fr 1fr">

      {{-- Kabupaten Nelayan Terbanyak --}}
      <div class="bg-white rounded-2xl p-6 border border-[#e9edf4]" style="box-shadow:0 2px 8px rgba(21,101,192,0.07)">
        <h3 class="text-[14.5px] font-bold text-[#0d2a5e]" style="font-family:'Plus Jakarta Sans',sans-serif">
          Kabupaten dengan Jumlah Nelayan Terbanyak
        </h3>
        <p class="text-[11.5px] text-gray-400 mb-5">Data nelayan laut dan perairan umum per kabupaten/kota</p>

        @php
          $maxNelayan = $topNelayanKabupaten->max('total_nelayan') ?: 1;
          $barCols    = ['#F97316','#1565C0','#F97316','#1565C0','#F97316','#1565C0'];
        @endphp

        <div class="flex flex-col gap-4">
          @foreach($topNelayanKabupaten as $i => $row)
            @php
              $pct  = ($row->total_nelayan / $maxNelayan) * 100;
              $name = strtoupper(preg_replace('/^(Kabupaten|Kota)\s+/i','', $row->nama_kabupaten_kota));
              $col  = $barCols[$i % count($barCols)];
            @endphp
            <div>
              <div class="flex justify-between items-center mb-1.5">
                <span class="text-[11.5px] font-semibold text-[#374151] tracking-[0.3px]">{{ $name }}</span>
                <span class="text-[12px] font-bold text-[#0d2a5e]">{{ number_format($row->total_nelayan) }}</span>
              </div>
              <div class="h-2 w-full bg-[#EEF0F5] rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width:{{ $pct }}%;background:{{ $col }}"></div>
              </div>
            </div>
          @endforeach
        </div>

        <p class="text-[10.5px] text-gray-400 mt-5 leading-relaxed border-t border-[#f3f4f8] pt-4">
          Grafik ini menunjukkan sebaran jumlah nelayan di wilayah pesisir Jawa Timur, mencerminkan konsentrasi aktivitas perikanan regional.
        </p>
      </div>

      {{-- Top 5 Jenis Tangkapan --}}
      <div class="bg-white rounded-2xl p-6 border border-[#e9edf4]" style="box-shadow:0 2px 8px rgba(21,101,192,0.07)">
        <div class="flex justify-between items-start mb-0.5">
          <h3 class="text-[14px] font-bold text-[#0d2a5e]" style="font-family:'Plus Jakarta Sans',sans-serif">
            Top 5 Jenis Hasil Tangkapan Terbanyak
          </h3>
          <span class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.5px] whitespace-nowrap">PERSENTASE (%)</span>
        </div>
        <p class="text-[11.5px] text-gray-400 mb-5">Berdasarkan volume produksi tahunan</p>

        @php
          $jenisData = [
            ['label'=>'IKAN',                   'value'=>$totalIkan,            'color'=>'#1565C0'],
            ['label'=>'BINATANG LUNAK',          'value'=>$totalBinatangLunak,   'color'=>'#1976D2'],
            ['label'=>'BINATANG BERKULIT KERAS', 'value'=>$totalBerkulitKeras,   'color'=>'#FFA726'],
            ['label'=>'BINATANG AIR LAINNYA',    'value'=>$totalBinatangLainnya, 'color'=>'#42A5F5'],
            ['label'=>'LAINNYA',                 'value'=>$totalLainnya,         'color'=>'#BDBDBD'],
          ];
          $grandTotalJenis = array_sum(array_column($jenisData,'value')) ?: 1;
        @endphp

        <div class="flex flex-col gap-3.5">
          @foreach($jenisData as $j)
            @php $pct2 = round(($j['value']/$grandTotalJenis)*100,1); @endphp
            <div class="flex items-center gap-2">
              <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $j['color'] }}"></div>
              <span class="text-[10.5px] font-semibold text-[#374151] flex-1">{{ $j['label'] }}</span>
              <div class="w-[90px] h-[5px] bg-[#f3f4f8] rounded-full overflow-hidden flex-shrink-0">
                <div class="h-full rounded-full" style="width:{{ $pct2 }}%;background:{{ $j['color'] }}"></div>
              </div>
              <span class="text-[11.5px] font-bold w-10 text-right flex-shrink-0" style="color:{{ $j['color'] }}">{{ $pct2 }}%</span>
            </div>
          @endforeach
        </div>

        <p class="text-[10.5px] text-gray-400 mt-5 leading-relaxed border-t border-[#f3f4f8] pt-4">
          Grafik ini menunjukkan peringkat lima besar kategori hasil tangkapan berdasarkan volume produksi tahunan.
        </p>
      </div>

    </div>

    {{-- ═══════════════════════════════════════════
         ROW 3: Top Kabupaten Produksi — FULL WIDTH
    ═══════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl p-6 border border-[#e9edf4] mb-5" style="box-shadow:0 2px 8px rgba(21,101,192,0.07)">
      <h3 class="text-[14.5px] font-bold text-[#0d2a5e] mb-0.5" style="font-family:'Plus Jakarta Sans',sans-serif">
        Top Kabupaten dengan Nilai Produksi Tertinggi
      </h3>
      <p class="text-[11.5px] text-gray-400 mb-5">Berdasarkan total volume produksi laut (ikan + binatang lunak + berkulit keras + lainnya)</p>

      @php
        $maxProd     = $topProduksiKabupaten->max('total_produksi') ?: 1;
        $rankPalette = ['#F97316','#1565C0','#F97316','#1565C0','#F97316','#1565C0','#F97316','#1565C0','#F97316','#1565C0'];
      @endphp

      <div class="grid grid-cols-1 gap-3.5">
        @foreach($topProduksiKabupaten->take(10) as $ri => $row)
          @php
            $pct3   = ($row->total_produksi / $maxProd) * 100;
            $shortN = preg_replace('/^(Kabupaten|Kota)\s+/i','', $row->nama_kabupaten_kota);
            $rc     = $rankPalette[min($ri,9)];
            // format nilai
            if($row->total_produksi >= 1e12)       $valFmt = 'Rp '.number_format($row->total_produksi/1e12,2).' T';
            elseif($row->total_produksi >= 1e9)    $valFmt = 'Rp '.number_format($row->total_produksi/1e9,1).'M';
            elseif($row->total_produksi >= 1e6)    $valFmt = number_format($row->total_produksi/1e6,1).' Jt ton';
            else                                   $valFmt = number_format($row->total_produksi,0).' ton';
          @endphp
          <div class="flex items-center gap-4">
            {{-- Rank badge --}}
            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-[11px] font-extrabold flex-shrink-0"
              style="font-family:'Plus Jakarta Sans',sans-serif;background:{{ $rc }}">
              {{ str_pad($ri+1,2,'0',STR_PAD_LEFT) }}
            </div>
            {{-- Bar + label --}}
            <div class="flex-1 min-w-0">
              <div class="flex justify-between items-center mb-1.5">
                <span class="text-[13px] font-semibold text-[#0d2a5e] truncate">{{ $shortN }}</span>
                <span class="text-[12.5px] font-bold text-[#1565C0] ml-4 flex-shrink-0">{{ $valFmt }}</span>
              </div>
              <div class="h-[7px] w-full bg-[#EEF0F5] rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width:{{ $pct3 }}%;background:{{ $rc }}"></div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <p class="text-[10.5px] text-gray-400 mt-5 leading-relaxed border-t border-[#f3f4f8] pt-4">
        Peringkat ini menunjukkan kontribusi ekonomi sektor perikanan dari kabupaten-kabupaten utama di Jawa Timur.
      </p>
    </div>

    {{-- ═══════════════════════════════════════════
         ROW 4: INSIGHT CARDS — 3 kolom
    ═══════════════════════════════════════════ --}}
    <div class="grid grid-cols-3 gap-4 mb-5">

      <div class="bg-white rounded-2xl p-5 border border-[#e9edf4]" style="box-shadow:0 2px 6px rgba(21,101,192,0.05)">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-xl bg-[#FFF3E0] flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-[#E65100]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
              <circle cx="12" cy="9" r="2.5"/>
            </svg>
          </div>
          <div>
            <div class="text-[12px] font-bold text-[#0d2a5e] mb-1.5">Wilayah Produksi Tertinggi</div>
            <p class="text-[12px] text-gray-500 leading-relaxed">
              Kabupaten <strong class="text-[#1565C0]">{{ $topProduksiKabupaten->first()?->nama_kabupaten_kota ?? '-' }}</strong>
              mendominasi total produksi perikanan laut di Jawa Timur tahun {{ $tahunAktif }}.
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-5 border border-[#e9edf4]" style="box-shadow:0 2px 6px rgba(21,101,192,0.05)">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-xl bg-[#E3F2FD] flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-[#1565C0]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 16.5c0 2.485-4.03 4.5-9 4.5S3 18.985 3 16.5c0-1.56 1.48-2.937 3.75-3.788"/>
              <path d="M12 2C9.24 2 7 4.24 7 7c0 2.76 2.24 5 5 5s5-2.24 5-5c0-2.76-2.24-5-5-5z"/>
            </svg>
          </div>
          <div>
            <div class="text-[12px] font-bold text-[#0d2a5e] mb-1.5">Komoditas Tangkapan Dominan</div>
            <p class="text-[12px] text-gray-500 leading-relaxed">
              <strong class="text-[#1565C0]">Ikan</strong> masih menjadi komoditas utama dengan kontribusi
              <strong>{{ round(($totalIkan/($grandTotalJenis?:1))*100,1) }}%</strong> dari total produksi.
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-5 border border-[#e9edf4]" style="box-shadow:0 2px 6px rgba(21,101,192,0.05)">
        <div class="flex items-start gap-3">
          <div class="w-10 h-10 rounded-xl bg-[#E8F5E9] flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-[#2E7D32]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
            </svg>
          </div>
          <div>
            <div class="text-[12px] font-bold text-[#0d2a5e] mb-1.5">Efisiensi Nelayan</div>
            <p class="text-[12px] text-gray-500 leading-relaxed">
              Rata-rata produksi per nelayan mencapai
              <strong class="text-[#2E7D32]">{{ number_format($rataRataProduksiPerNelayan,1) }} kg/hari</strong>,
              mencerminkan tingkat produktivitas perikanan regional.
            </p>
          </div>
        </div>
      </div>

    </div>

    {{-- ═══════════════════════════════════════════
         ROW 5: TABEL DETAIL — FULL WIDTH
    ═══════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl p-6 border border-[#e9edf4] mb-6" style="box-shadow:0 2px 8px rgba(21,101,192,0.07)">
      <div class="flex justify-between items-center mb-5 flex-wrap gap-3">
        <div>
          <h3 class="text-[14.5px] font-bold text-[#0d2a5e] mb-0.5" style="font-family:'Plus Jakarta Sans',sans-serif">
            Detail Data Per Kabupaten/Kota
          </h3>
          <p class="text-[11.5px] text-gray-400">Tahun {{ $tahunAktif }} – Semua Jenis Produksi</p>
        </div>
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400 pointer-events-none"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          <input type="text" id="searchTable" placeholder="Cari kabupaten..." oninput="filterTable()"
            class="pl-9 pr-4 py-2 rounded-lg border border-[#e9edf4] text-[12px] text-[#374151] outline-none focus:border-[#1565C0] w-52 transition-colors">
        </div>
      </div>

      <div class="overflow-x-auto">
        <table id="detailTable" class="w-full text-[12px]" style="border-collapse:collapse">
          <thead>
            <tr style="background:#F4F6FA">
              <th class="px-4 py-3 text-left text-[10px] font-bold text-[#6b7280] uppercase tracking-[0.6px] whitespace-nowrap rounded-l-lg">Kabupaten/Kota</th>
              <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.6px]" style="color:#1565C0">Ikan (ton)</th>
              <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.6px]" style="color:#6A1B9A">B. Lunak (ton)</th>
              <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.6px]" style="color:#E65100">B. Keras (ton)</th>
              <th class="px-4 py-3 text-right text-[10px] font-bold uppercase tracking-[0.6px]" style="color:#2E7D32">B. Air Lainnya</th>
              <th class="px-4 py-3 text-right text-[10px] font-bold text-[#374151] uppercase tracking-[0.6px]">Total Produksi</th>
              <th class="px-4 py-3 text-right text-[10px] font-bold text-[#374151] uppercase tracking-[0.6px] rounded-r-lg">Total Nelayan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($detailPerKabupaten as $row)
            <tr class="hover:bg-[#F8FAFF] transition-colors" style="border-bottom:1px solid #f3f4f8">
              <td class="px-4 py-3 font-semibold" style="color:#0d2a5e">{{ $row->nama_kabupaten_kota }}</td>
              <td class="px-4 py-3 text-right font-medium" style="color:#1565C0">{{ number_format($row->ikan??0) }}</td>
              <td class="px-4 py-3 text-right font-medium" style="color:#6A1B9A">{{ number_format($row->binatang_lunak??0) }}</td>
              <td class="px-4 py-3 text-right font-medium" style="color:#E65100">{{ number_format($row->binatang_berkulit_keras??0) }}</td>
              <td class="px-4 py-3 text-right font-medium" style="color:#2E7D32">{{ number_format($row->binatang_air_lainnya??0) }}</td>
              <td class="px-4 py-3 text-right font-bold" style="color:#0d2a5e">{{ number_format($row->total_produksi??0) }}</td>
              <td class="px-4 py-3 text-right font-semibold" style="color:#374151">{{ number_format($row->total_nelayan??0) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>{{-- end px-8 py-7 --}}
</div>{{-- end h-full overflow-y-auto --}}

@push('scripts')
<script>
function filterTable(){
  const q = document.getElementById('searchTable').value.toLowerCase();
  document.querySelectorAll('#detailTable tbody tr').forEach(r=>{
    r.style.display = r.cells[0].textContent.toLowerCase().includes(q)?'':'none';
  });
}
</script>
@endpush
@endsection