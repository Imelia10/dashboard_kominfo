@extends('layouts.app')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          sora: ['Sora', 'sans-serif'],
          mono: ['"JetBrains Mono"', 'monospace'],
        },
        colors: {
          teal:   { DEFAULT: '#0B6E61', 2: '#138A7A', lt: '#E4F5F2', md: '#A8DAD4' },
          amber:  { DEFAULT: '#B85C0A', 2: '#D46B0C', lt: '#FDF0E6', md: '#F5C49A' },
          cobalt: { DEFAULT: '#1C3660', 2: '#254880', lt: '#E8EEF8', md: '#A4BBDB' },
          coral:  { DEFAULT: '#B53226', lt: '#FDECEA' },
          ink:    { DEFAULT: '#0C1117', 2: '#1E2D3D', 3: '#516170', 4: '#8FA3B1' },
          line:   { DEFAULT: '#DDE4EA', 2: '#EEF2F5' },
          bg:     '#F2F5F8',
        },
      }
    }
  }
</script>
<style>
  body, html { font-family: 'Sora', sans-serif; }
  .kpi-stripe-teal   { border-left: 4px solid #138A7A; }
  .kpi-stripe-amber  { border-left: 4px solid #D46B0C; }
  .kpi-stripe-cobalt { border-left: 4px solid #254880; }
  .kpi-stripe-coral  { border-left: 4px solid #B53226; }
  .ins-top-teal      { border-top: 4px solid #0B6E61; }
  .ins-top-amber     { border-top: 4px solid #B85C0A; }
  .ins-top-cobalt    { border-top: 4px solid #1C3660; }
  .h-chart    { position: relative; height: 210px; }
  .h-chart-sm { position: relative; height: 180px; }
  .h-chart-md { position: relative; height: 200px; }
  select { appearance: none; }
</style>
@endpush

@section('content')
<div class="w-full bg-bg font-sora text-ink-2 pb-20">

  {{-- ══ TOPBAR ══ --}}
  <div class="flex items-center justify-between flex-wrap gap-3 px-10 py-4 bg-white border-b border-line sticky top-0 z-10">
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-1.5 text-[11.5px] text-ink-4">
        <a href="{{ route('home') }}" class="text-teal-2 font-semibold hover:underline">Dashboard</a>
        <span class="text-line text-sm">/</span>
        <span class="text-ink-3 font-semibold">Produksi Perikanan</span>
      </div>
      <div class="text-base font-extrabold text-ink tracking-tight">Analisis Produksi Perikanan — Jawa Timur</div>
      <div class="text-[11.5px] text-ink-4">Data perairan umum · Tahun {{ $tahunAktif }} </div>
    </div>
    <form method="GET" action="{{ route('perikanan') }}">
      <div class="relative">
        <select name="tahun" onchange="this.form.submit()"
          class="bg-teal-lt border-[1.5px] border-teal-md text-teal rounded-[9px] py-2 pl-4 pr-9 text-[13.5px] font-bold cursor-pointer outline-none font-sora focus:border-teal-2 focus:ring-2 focus:ring-teal/20 transition-all">
          @foreach($daftarTahun as $t)
            <option value="{{ $t }}" {{ $t==$tahunAktif?'selected':'' }}>{{ $t }}</option>
          @endforeach
        </select>
        <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#0B6E61" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
    </form>
  </div>

  {{-- ══ CONTENT ══ --}}
  <div class="px-10 pt-7">

    {{-- ── KPI ── --}}
    <div class="flex items-center gap-3 mt-1 mb-4">
      <span class="text-[10px] font-bold tracking-[.8px] uppercase px-3 py-1 rounded-full bg-teal-lt text-teal flex-shrink-0"></span>
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Empat indikator utama kondisi perikanan perairan umum Jawa Timur</span>
    </div>

    <div class="grid grid-cols-4 gap-4 max-[1100px]:grid-cols-2 max-[720px]:grid-cols-1">

      {{-- KPI Nelayan --}}
      <div class="bg-white border border-line rounded-2xl p-6 relative overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all kpi-stripe-teal">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-teal-lt">👨‍👨</div>
          <span class="text-[9.5px] font-bold tracking-[.5px] px-2.5 py-1 rounded-full bg-teal-lt text-teal">PERAIRAN UMUM</span>
        </div>
        <div class="text-[11px] font-semibold tracking-[.4px] text-ink-4 mb-2">TOTAL NELAYAN</div>
        <div class="text-[38px] font-extrabold text-ink leading-none mb-1 tracking-[-1.5px]">{{ number_format($totalNelayan,0,',','.') }}</div>
        @if($gNelayan!==null) 
        @endif
      </div>

      {{-- KPI Produksi --}}
      <div class="bg-white border border-line rounded-2xl p-6 relative overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all kpi-stripe-amber">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-amber-lt">🎣</div>
          <span class="text-[9.5px] font-bold tracking-[.5px] px-2.5 py-1 rounded-full bg-amber-lt text-amber">{{ strtoupper($satuan) }}</span>
        </div>
        <div class="text-[11px] font-semibold tracking-[.4px] text-ink-4 mb-2">TOTAL PRODUKSI</div>
        <div class="text-[38px] font-extrabold text-ink leading-none mb-1 tracking-[-1.5px]">{{ number_format($grandTotal,0,',','.') }}</div>        @if($gProduksi!==null)
  
        @endif
      </div>

      {{-- KPI Produktivitas --}}
      <div class="bg-white border border-line rounded-2xl p-6 relative overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all kpi-stripe-cobalt">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-cobalt-lt">⚡</div>
          <span class="text-[9.5px] font-bold tracking-[.5px] px-2.5 py-1 rounded-full bg-cobalt-lt text-cobalt">{{ strtoupper($satuanPrdktv) }}</span>
        </div>
        <div class="text-[11px] font-semibold tracking-[.4px] text-ink-4 mb-2">PRODUKTIVITAS</div>
        <div class="text-[38px] font-extrabold text-ink leading-none mb-1 tracking-[-1.5px]">{{ number_format($produktivitas,2,',','.') }}</div>
        @if($gPrdktv!==null)
        @endif
      </div>

      {{-- KPI Jenis Dominan --}}
      <div class="bg-white border border-line rounded-2xl p-6 relative overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all kpi-stripe-coral">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-coral-lt">🏆</div>
          <span class="text-[9.5px] font-bold tracking-[.5px] px-2.5 py-1 rounded-full bg-coral-lt text-coral">KOMODITAS</span>
        </div>
        <div class="text-[11px] font-semibold tracking-[.4px] text-ink-4 mb-2">JENIS DOMINAN</div>
        <div class="text-[28px] font-extrabold text-ink leading-none mb-1 pt-0.5">{{ $jenisDominan }}</div>
      </div>
    </div>

    {{-- ── Efisiensi & Komposisi ── --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Produktivitas per nelayan perairan umum dan proporsi jenis hasil tangkapan</span>
    </div>

    <div class="grid grid-cols-2 gap-4 max-[720px]:grid-cols-1">

      <div class="bg-white border border-line rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-300">

    <!-- Header -->
    <div class="flex items-start justify-between gap-3 mb-4">

        <div class="space-y-2">
            <h3 class="text-sm font-semibold text-ink leading-snug">
                Tren Produktivitas Nelayan Perairan Umum
            </h3>

            <p class="text-[11px] text-ink-3 leading-relaxed">
                Rata-rata hasil tangkapan per nelayan dari tahun ke tahun untuk melihat efisiensi produksi perairan umum.
            </p>

            <!-- Info Indicator -->
            <div class="flex flex-wrap gap-2 pt-1">

                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full 
                    bg-emerald-50 text-emerald-600 text-[10px] font-medium">
                    ↑ Meningkat = Efisiensi membaik
                </span>

                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full 
                    bg-rose-50 text-rose-600 text-[10px] font-medium">
                    ↓ Menurun = Perlu evaluasi
                </span>

            </div>
        </div>

        <!-- Badge -->
        <div class="shrink-0">
            <span class="inline-flex items-center rounded-full 
                bg-amber-50 text-amber-600 text-[10px] font-semibold 
                px-3 py-1 tracking-wide shadow-sm">
                {{ strtoupper($satuanPrdktv) }}
            </span>
        </div>

    </div>

    <!-- Divider -->
    <div class="border-t border-line/70 mb-4"></div>

    <!-- Chart -->
    <div class="h-chart">
        <canvas id="cPrdktv"></canvas>
    </div>

</div>

      <div class="bg-white border border-line rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
          <div class="text-sm font-bold text-ink mb-1">Komposisi Jenis Produksi — {{ $tahunAktif }}</div>
          <div class="text-xs text-ink-3 leading-relaxed">Distribusi empat kategori komoditas dari total <strong class="text-ink-2 font-bold">{{ number_format($kTotal,0,',','.') }} {{ $satuan }}</strong>.
            <strong class="text-ink-2 font-bold">{{ $jenisDominan }}</strong> mendominasi {{ $jenisDominanPersen }}% produksi.</div>
          <span class="inline-block mt-2 font-mono text-[9.5px] font-semibold tracking-[.4px] px-2.5 py-0.5 rounded bg-teal-lt text-teal">PERSENTASE (%)</span>
        </div>
        <div class="flex items-center gap-6 mt-3">
          <div class="relative w-[155px] h-[155px] flex-shrink-0">
            <canvas id="cDonut"></canvas>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-center pointer-events-none">
              <div class="text-base font-extrabold text-ink font-mono leading-tight">{{ number_format($kTotal,0,',','.') }}</div>
              <div class="text-[9px] font-semibold text-ink-4 mt-0.5 tracking-[.3px]">{{ strtoupper($satuan) }}</div>
            </div>
          </div>
          <div class="flex-1 flex flex-col gap-3">
            @php $dC = ['#0B6E61','#B85C0A','#1C3660','#8FA3B1']; @endphp
            @foreach($komposisi as $i=>$item)
            <div class="flex items-center gap-2.5">
              <div class="w-2.5 h-2.5 rounded-[3px] flex-shrink-0" style="background:{{ $dC[$i%4] }}"></div>
              <span class="text-[12.5px] text-ink-2 flex-1">{{ $item['label'] }}</span>
              <div class="w-[52px] h-[5px] bg-bg rounded-full overflow-hidden flex-shrink-0">
                <div class="h-full rounded-full" style="width:{{ $item['persen'] }}%;background:{{ $dC[$i%4] }}"></div>
              </div>
              <span class="text-xs font-bold text-ink min-w-[36px] text-right font-mono">{{ $item['persen'] }}%</span>
            </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    {{-- ── Tren Historis ── --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Dinamika nelayan dan produksi lintas tahun</span>
    </div>

    <div class="bg-white border border-line rounded-2xl p-6 shadow-sm mb-4">
      <div class="mb-4">
        <div class="text-sm font-bold text-ink mb-1">Tren Jumlah Nelayan Perairan Umum.</div>
        <span class="inline-block mt-2 font-mono text-[9.5px] font-semibold tracking-[.4px] px-2.5 py-0.5 rounded bg-teal-lt text-teal">ORANG / TAHUN</span>
      </div>
      <div class="h-chart-md mt-3.5"><canvas id="cNelayan"></canvas></div>
     <div class="text-[9px] text-ink-1 leading-relaxed mt-2">
   jika nelayan turun namun produksi naik, artinya efisiensi meningkat.
</div>
    </div>

    <div class="grid grid-cols-2 gap-4 max-[720px]:grid-cols-1">

      <div class="bg-white border border-line rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
          <div class="text-sm font-bold text-ink mb-1">Tren Total Produksi Laut per Tahun</div>
          <div class="text-xs text-ink-3 leading-relaxed">Volume gabungan semua komoditas dari tahun ke tahun.</div>
          <span class="inline-block mt-2 font-mono text-[9.5px] font-semibold tracking-[.4px] px-2.5 py-0.5 rounded bg-amber-lt text-amber">{{ strtoupper($satuan) }} / TAHUN</span>
        </div>
        <div class="h-chart"><canvas id="cProduksi"></canvas></div>
      </div>

      <div class="bg-white border border-line rounded-2xl p-6 shadow-sm">
        <div class="flex justify-between items-start flex-wrap gap-2 mb-4">
          <div>
            <div class="text-sm font-bold text-ink mb-1">Perbandingan Indeks: Produksi vs Nelayan</div>
          </div>
          <div class="flex gap-4 text-xs font-semibold flex-shrink-0">
            <span class="text-amber flex items-center gap-1.5"><span class="inline-block w-5 h-[3px] rounded bg-amber"></span>Produksi</span>
            <span class="text-teal flex items-center gap-1.5"><span class="inline-block w-5 h-[3px] rounded bg-teal"></span>Nelayan</span>
          </div>
        </div>
        <div class="h-chart"><canvas id="cVs"></canvas></div>
      </div>
    </div>

    {{-- ── Analisis Wilayah ── --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Volume produksi, efisiensi per nelayan, dan konsentrasi SDM per kabupaten/kota</span>
    </div>

    <div class="grid grid-cols-3 gap-4 max-[1100px]:grid-cols-2 max-[720px]:grid-cols-1">
      <div class="bg-white border border-line rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
          <div class="text-sm font-bold text-ink mb-1">Top 5 — Volume Produksi</div>
          <div class="text-xs text-ink-3 leading-relaxed">Kabupaten dengan total tangkapan tertinggi.</div>
          <span class="inline-block mt-2 font-mono text-[9.5px] font-semibold tracking-[.4px] px-2.5 py-0.5 rounded bg-amber-lt text-amber">{{ strtoupper($satuan) }}</span>
        </div>
        <div class="h-chart-sm"><canvas id="cTop5P"></canvas></div>
      </div>
      <div class="bg-white border border-line rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
          <div class="text-sm font-bold text-ink mb-1">Top 5 — Produktivitas per Nelayan</div>
          <div class="text-xs text-ink-3 leading-relaxed">Hasil ÷ nelayan perairan umum per kabupaten. Ukuran efisiensi riil.</div>
          <span class="inline-block mt-2 font-mono text-[9.5px] font-semibold tracking-[.4px] px-2.5 py-0.5 rounded bg-teal-lt text-teal">{{ strtoupper($satuanPrdktv) }}</span>
        </div>
        <div class="h-chart-sm"><canvas id="cTop5Pv"></canvas></div>
      </div>
      <div class="bg-white border border-line rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
          <div class="text-sm font-bold text-ink mb-1">Top 5 — Konsentrasi Nelayan</div>
          <div class="text-xs text-ink-3 leading-relaxed">Kabupaten dengan nelayan perairan umum terbanyak.</div>
          <span class="inline-block mt-2 font-mono text-[9.5px] font-semibold tracking-[.4px] px-2.5 py-0.5 rounded bg-cobalt-lt text-cobalt">ORANG</span>
        </div>
        <div class="h-chart-sm"><canvas id="cTop5N"></canvas></div>
      </div>
    </div>

  {{-- ── Sintesis Analitik ── --}}
<div class="flex items-center gap-3 mt-8 mb-4">
  <div class="flex-1 h-px bg-line"></div>
  <span class="text-[14.5px] font-bold text-ink">Tiga insight yang saling menguatkan</span>
</div>

<div class="grid grid-cols-3 gap-4 max-[1100px]:grid-cols-1">

  {{-- Insight 1 --}}
  <div class="bg-white border border-line rounded-2xl px-7 pt-7 pb-6 shadow-sm flex flex-col ins-top-teal">
    <div class="text-[10.5px] font-bold tracking-[.7px] text-teal mb-3">INSIGHT 01</div>
    <div class="text-[15.5px] font-extrabold text-ink mb-3 leading-snug">
      Volume nelayan dan produksi bergerak berbeda — selisihnya menentukan tekanan pada sumber daya
    </div>
    <div class="text-[13px] text-ink-3 leading-[1.9] flex-1">
      Tahun {{ $tahunAktif }}, tercatat
      <strong class="text-ink-2 font-bold">{{ number_format($totalNelayan,0,',','.') }} nelayan perairan umum</strong>
      yang menghasilkan total
      <strong class="text-ink-2 font-bold">{{ number_format($grandTotal,0,',','.') }} {{ $satuan }}</strong> produksi.

      @if($nSebelum > 0 && $prevTotal > 0)
        Dibanding {{ $tahunAktif - 1 }}, nelayan
        <strong class="text-ink-2 font-bold">
          {{ $selisihNelayan >= 0 ? 'bertambah' : 'berkurang' }}
          {{ number_format(abs($selisihNelayan),0,',','.') }} orang
        </strong>
        (dari {{ number_format($nSebelum,0,',','.') }}),
        sementara produksi
        <strong class="text-ink-2 font-bold">
          {{ $selisihProduksi >= 0 ? 'naik' : 'turun' }}
          {{ number_format(abs($selisihProduksi),0,',','.') }} {{ $satuan }}
        </strong>
        (dari {{ number_format($prevTotal,0,',','.') }}).

        @if($selisihNelayan < 0 && $selisihProduksi > 0)
          Nelayan menyusut namun produksi tetap naik —
          <strong class="text-ink-2 font-bold">kapasitas tangkap per orang meningkat nyata</strong>.
        @elseif($selisihNelayan > 0 && $selisihProduksi < 0)
          Nelayan bertambah namun produksi justru turun —
          <strong class="text-ink-2 font-bold">sinyal divergensi serius</strong>: perlu evaluasi kondisi ekosistem perairan.
        @elseif($selisihNelayan > 0 && $selisihProduksi > 0)
          Keduanya tumbuh — perlu dipastikan pertumbuhan produksi
          <strong class="text-ink-2 font-bold">sebanding</strong> dengan penambahan nelayan.
        @else
          Keduanya menyusut — tekanan produksi dan SDM bergerak searah.
        @endif
      @endif
    </div>
    <div class="mt-5 rounded-xl p-4 bg-teal-lt text-teal text-xs leading-relaxed">
      <span class="block text-[10px] font-bold tracking-[.5px] opacity-70 mb-1">RINGKASAN VOLUME {{ $tahunAktif }}</span>
      <span class="block text-lg font-extrabold text-ink font-mono mb-1">
        {{ number_format($totalNelayan,0,',','.') }} orang · {{ number_format($grandTotal,0,',','.') }} {{ $satuan }}
      </span>
      @if($nSebelum > 0)
        <span class="text-xs font-semibold">
          vs {{ $tahunAktif - 1 }}: {{ number_format($nSebelum,0,',','.') }} orang · {{ number_format($prevTotal,0,',','.') }} {{ $satuan }}
        </span>
      @endif
    </div>
  </div>

  {{-- Temuan 2 --}}
  <div class="bg-white border border-line rounded-2xl px-7 pt-7 pb-6 shadow-sm flex flex-col ins-top-amber">
    <div class="text-[10.5px] font-bold tracking-[.7px] text-amber mb-3">INSIGHT 02</div>
    <div class="text-[15.5px] font-extrabold text-ink mb-3 leading-snug">
      Produktivitas mencerminkan beban nyata tiap nelayan — komoditas dominan menentukan fokus intervensi
    </div>
    <div class="text-[13px] text-ink-3 leading-[1.9] flex-1">
      Rata-rata setiap nelayan perairan umum menanggung
      <strong class="text-ink-2 font-bold">{{ number_format($produktivitas,2,',','.') }} {{ $satuanPrdktv }}</strong>
      hasil tangkapan pada <strong class="text-ink-2 font-bold">{{ $tahunAktif }}</strong>.

      @if($prevPrdktvVal > 0)
        Angka ini
        <strong class="text-ink-2 font-bold">
          {{ $selisihPrdktv >= 0 ? 'naik' : 'turun' }}
          {{ number_format(abs($selisihPrdktv),2,',','.') }} {{ $satuan }}
        </strong>
        dibanding tahun {{ $tahunAktif - 1 }}
        (dari <strong class="text-ink-2 font-bold">{{ number_format($prevPrdktvVal,2,',','.') }} {{ $satuanPrdktv }}</strong>).
      @endif

      Komoditas terbesar adalah <strong class="text-ink-2 font-bold">{{ $jenisDominan }}</strong>
      dengan volume absolut
      <strong class="text-ink-2 font-bold">{{ number_format($nilaiDominan,0,',','.') }} {{ $satuan }}</strong>.
    </div>
    <div class="mt-5 rounded-xl p-4 bg-amber-lt text-amber text-xs leading-relaxed">
      <span class="block text-[10px] font-bold tracking-[.5px] opacity-70 mb-1">PRODUKTIVITAS {{ $tahunAktif }}</span>
      <span class="block text-lg font-extrabold text-ink font-mono mb-1">
        {{ number_format($produktivitas,2,',','.') }} {{ $satuanPrdktv }}
      </span>
      @if($prevPrdktvVal > 0)
        <span class="text-xs font-semibold">
          vs {{ $tahunAktif - 1 }}: {{ number_format($prevPrdktvVal,2,',','.') }} {{ $satuanPrdktv }}
          · {{ $selisihPrdktv >= 0 ? '↑' : '↓' }} {{ number_format(abs($selisihPrdktv),2,',','.') }} {{ $satuan }}
        </span>
      @endif
    </div>
  </div>

  {{-- Temuan 3 --}}
  @php
    $nmProd   = preg_replace('/^(Kabupaten|Kota)\s+/i','', $top5Prod->first()?->nama_kabupaten_kota   ?? '-');
    $nmPrdktv = preg_replace('/^(Kabupaten|Kota)\s+/i','', $top5Prdktv->first()?->nama_kabupaten_kota ?? '-');
    $nmNel    = preg_replace('/^(Kabupaten|Kota)\s+/i','', $top5Nelayan->first()?->nama_kabupaten_kota ?? '-');
    $valProd   = $top5Prod->first()?->total_produksi   ?? 0;
    $valPrdktv = $top5Prdktv->first()?->produktivitas  ?? 0;
    $valNel    = $top5Nelayan->first()?->total_nelayan  ?? 0;
  @endphp
  <div class="bg-white border border-line rounded-2xl px-7 pt-7 pb-6 shadow-sm flex flex-col ins-top-cobalt">
    <div class="text-[10.5px] font-bold tracking-[.7px] text-cobalt mb-3">INSIGHT 03</div>
    <div class="text-[15.5px] font-extrabold text-ink mb-3 leading-snug">
      Wilayah volume tertinggi dan wilayah paling efisien jarang sama — keduanya butuh pendekatan kebijakan berbeda
    </div>
    <div class="text-[13px] text-ink-3 leading-[1.9] flex-1">
      <strong class="text-ink-2 font-bold">{{ $nmProd }}</strong> memimpin volume absolut dengan
      <strong class="text-ink-2 font-bold">{{ number_format($valProd,0,',','.') }} {{ $satuan }}</strong>.
      Sementara <strong class="text-ink-2 font-bold">{{ $nmPrdktv }}</strong> unggul dalam efisiensi
      dengan rata-rata
      <strong class="text-ink-2 font-bold">{{ number_format($valPrdktv,2,',','.') }} {{ $satuanPrdktv }}</strong>
      per nelayan.
      Konsentrasi SDM terbesar berada di
      <strong class="text-ink-2 font-bold">{{ $nmNel }}</strong>
      dengan <strong class="text-ink-2 font-bold">{{ number_format($valNel,0,',','.') }} orang</strong> nelayan perairan umum.

      @if(strtolower($nmProd) !== strtolower($nmPrdktv))
        Fakta bahwa pemimpin volume dan pemimpin efisiensi adalah wilayah yang berbeda menunjukkan
        <strong class="text-ink-2 font-bold">distribusi kapasitas yang tidak merata</strong> —
        intervensi perlu disesuaikan per profil wilayah.
      @else
        <strong class="text-ink-2 font-bold">{{ $nmProd }}</strong> memimpin sekaligus di volume maupun efisiensi —
        wilayah ini layak dijadikan <strong class="text-ink-2 font-bold">model replikasi</strong> kebijakan.
      @endif
    </div>
    <div class="mt-5 rounded-xl p-4 bg-cobalt-lt text-cobalt text-xs leading-relaxed">
      <span class="block text-[10px] font-bold tracking-[.5px] opacity-70 mb-1">PUNCAK WILAYAH {{ $tahunAktif }}</span>
      <span class="block text-lg font-extrabold text-ink font-mono mb-1">{{ $nmProd }}</span>
      <span class="text-xs font-semibold block mt-0.5">
        Volume: {{ number_format($valProd,0,',','.') }} {{ $satuan }}
        · Efisiensi terbaik: {{ $nmPrdktv }} ({{ number_format($valPrdktv,2,',','.') }} {{ $satuanPrdktv }})
      </span>
    </div>
  </div>

</div>

    {{-- ── Tabel Data Lengkap ── --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Rincian semua kabupaten/kota — Tahun {{ $tahunAktif }}</span>
    </div>

    <div class="bg-white border border-line rounded-2xl overflow-hidden shadow-sm">
      <div class="flex justify-between items-center flex-wrap gap-3 px-6 py-5 border-b border-line">
        <div>
          <h3 class="text-sm font-extrabold text-ink">Tabel Rincian Per Kabupaten / Kota</h3>
          <p class="text-xs text-ink-3 mt-0.5">Produksi: {{ $satuan }} · Nelayan: orang (perairan umum) · Produktivitas: {{ $satuanPrdktv }}</p>
        </div>
        <div class="relative">
          <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#8FA3B1" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="srch" placeholder="Cari kabupaten / kota…"
            class="pl-8 pr-3 py-2 border border-line rounded-xl text-[13px] text-ink-2 outline-none w-56 font-sora focus:border-teal-2 focus:ring-2 focus:ring-teal/10 transition-all">
        </div>
      </div>

      @php $mxP = $detailKab->max('total_produksi') ?: 1; @endphp
      <div class="overflow-x-auto">
        <table class="w-full border-collapse text-[12.5px]" id="dtbl">
          <thead>
            <tr class="bg-bg">
              <th class="px-4 py-3 text-left text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">#</th>
              <th class="px-4 py-3 text-left text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">Kabupaten / Kota</th>
              <th class="px-4 py-3 text-right text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">Ikan ({{ $satuan }})</th>
              <th class="px-4 py-3 text-right text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">B. Lunak ({{ $satuan }})</th>
              <th class="px-4 py-3 text-right text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">B. Keras ({{ $satuan }})</th>
              <th class="px-4 py-3 text-right text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">Air Lainnya ({{ $satuan }})</th>
              <th class="px-4 py-3 text-right text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">Total Produksi ({{ $satuan }})</th>
              <th class="px-4 py-3 text-right text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">Nelayan (Orang)</th>
              <th class="px-4 py-3 text-right text-[10.5px] font-bold tracking-[.4px] text-ink-4 whitespace-nowrap">Produktivitas ({{ $satuanPrdktv }})</th>
            </tr>
          </thead>
          <tbody>
            @foreach($detailKab as $i=>$r)
            @php $bw = ($r->total_produksi / $mxP) * 100; @endphp
            <tr class="border-b border-line-2 last:border-b-0 hover:bg-[#F6FAFD] transition-colors">
              <td class="px-4 py-3 text-left text-ink-4 font-mono text-[11px]">{{ $i+1 }}</td>
              <td class="px-4 py-3 text-left font-bold text-ink">{{ $r->nama_kabupaten_kota }}</td>
              <td class="px-4 py-3 text-right font-semibold text-teal">{{ number_format($r->ikan??0,0,',','.') }}</td>
              <td class="px-4 py-3 text-right font-semibold text-amber">{{ number_format($r->binatang_lunak??0,0,',','.') }}</td>
              <td class="px-4 py-3 text-right font-semibold text-cobalt">{{ number_format($r->binatang_berkulit_keras??0,0,',','.') }}</td>
              <td class="px-4 py-3 text-right text-ink-3">{{ number_format($r->binatang_air_lainnya??0,0,',','.') }}</td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                  <span class="font-bold text-ink">{{ number_format($r->total_produksi??0,0,',','.') }}</span>
                  <div class="w-12 h-[5px] bg-line rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-teal" style="width:{{ $bw }}%"></div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-right text-ink-3">{{ number_format($r->total_nelayan??0,0,',','.') }}</td>
              <td class="px-4 py-3 text-right font-bold text-cobalt-2">{{ $r->produktivitas ? number_format($r->produktivitas,2,',','.') : '–' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

  </div>{{-- /content --}}
</div>{{-- /inner --}}
</div>{{-- /wrap --}}
@endsection

@push('scripts')
<script>
const dNel  = @json($trenNelayan);
const dProd = @json($trenProduksi);
const dGab  = @json($trenGabungan);
const dT5P  = @json($top5Prod);
const dT5Pv = @json($top5Prdktv);
const dT5N  = @json($top5Nelayan);
const dKomp = @json($komposisi);
const SAT   = @json($satuan);
const SATPV = @json($satuanPrdktv);

const TEAL='#0B6E61', AMBER='#B85C0A', COBALT='#1C3660', SLATE='#8FA3B1', GRID='#EEF2F5';
const fmt  = v => Number(v).toLocaleString('id-ID');
const fmtD = v => Number(v).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});
const sn   = s => (s||'').replace(/^(Kabupaten|Kota)\s+/i,'');
const axX  = () => ({grid:{display:false},ticks:{font:{size:11},color:'#718096'}});
const axY  = (lbl,cb=fmt,col='#9CA3AF') => ({grid:{color:GRID},title:{display:!!lbl,text:lbl,font:{size:10,weight:'600'},color:col},ticks:{font:{size:10},color:'#9CA3AF',callback:v=>cb(v)}});

new Chart(document.getElementById('cPrdktv'),{type:'bar',data:{labels:dGab.map(r=>r.tahun),datasets:[{data:dGab.map(r=>r.produktivitas),backgroundColor:AMBER,borderRadius:8,barPercentage:.55}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>fmtD(ctx.raw)+' '+SATPV}}},scales:{x:axX(),y:axY(SATPV.toUpperCase(),fmtD,AMBER)}}});

new Chart(document.getElementById('cDonut'),{type:'doughnut',data:{labels:dKomp.map(r=>r.label),datasets:[{data:dKomp.map(r=>r.persen),backgroundColor:[TEAL,AMBER,COBALT,SLATE],borderWidth:3,borderColor:'#fff'}]},options:{responsive:true,maintainAspectRatio:true,cutout:'68%',plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>ctx.label+': '+ctx.raw+'%'}}}}});

new Chart(document.getElementById('cNelayan'),{type:'line',data:{labels:dNel.map(r=>r.tahun),datasets:[{data:dNel.map(r=>r.total_nelayan),borderColor:TEAL,backgroundColor:'rgba(11,110,97,.09)',fill:true,tension:.38,borderWidth:2.5,pointBackgroundColor:TEAL,pointRadius:5,pointHoverRadius:7}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>fmt(ctx.raw)+' orang'}}},scales:{x:axX(),y:axY('ORANG')}}});

new Chart(document.getElementById('cProduksi'),{type:'bar',data:{labels:dProd.map(r=>r.tahun),datasets:[{data:dProd.map(r=>r.total_produksi),backgroundColor:COBALT,borderRadius:8,barPercentage:.55}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>fmt(ctx.raw)+' '+SAT}}},scales:{x:axX(),y:axY(SAT.toUpperCase())}}});

new Chart(document.getElementById('cVs'),{type:'bar',data:{labels:dGab.map(r=>r.tahun),datasets:[{label:'Produksi',data:dGab.map(r=>r.total_produksi),backgroundColor:AMBER,borderRadius:6,barPercentage:.38,yAxisID:'yP'},{label:'Nelayan',data:dGab.map(r=>r.total_nelayan),backgroundColor:TEAL+'BB',borderRadius:6,barPercentage:.38,yAxisID:'yN'}]},options:{responsive:true,maintainAspectRatio:false,interaction:{mode:'index',intersect:false},plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>{const r=dGab[ctx.dataIndex];return ctx.datasetIndex===0?'Produksi: '+fmt(r.total_produksi)+' '+SAT:'Nelayan: '+fmt(r.total_nelayan)+' orang';}}}},scales:{x:axX(),yP:{position:'left',grid:{color:GRID},ticks:{font:{size:10},color:AMBER,callback:v=>fmt(v)},title:{display:true,text:SAT.toUpperCase(),font:{size:9.5},color:AMBER}},yN:{position:'right',grid:{display:false},ticks:{font:{size:10},color:TEAL,callback:v=>fmt(v)},title:{display:true,text:'ORANG',font:{size:9.5},color:TEAL}}}}});

const hBarOpts = (cb,tip) => ({indexAxis:'y',responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false},tooltip:{callbacks:{label:ctx=>tip(ctx.raw)}}},scales:{x:{grid:{color:GRID},ticks:{font:{size:10.5},color:'#9CA3AF',callback:v=>cb(v)}},y:{grid:{display:false},ticks:{font:{size:11.5},color:'#1E2D3D'}}}});
new Chart(document.getElementById('cTop5P'), {type:'bar',data:{labels:dT5P.map(r=>sn(r.nama_kabupaten_kota)),datasets:[{data:dT5P.map(r=>r.total_produksi),backgroundColor:AMBER,borderRadius:6,barPercentage:.65}]},options:hBarOpts(fmt,v=>fmt(v)+' '+SAT)});
new Chart(document.getElementById('cTop5Pv'),{type:'bar',data:{labels:dT5Pv.map(r=>sn(r.nama_kabupaten_kota)),datasets:[{data:dT5Pv.map(r=>r.produktivitas),backgroundColor:TEAL,borderRadius:6,barPercentage:.65}]},options:hBarOpts(fmtD,v=>fmtD(v)+' '+SATPV)});
new Chart(document.getElementById('cTop5N'), {type:'bar',data:{labels:dT5N.map(r=>sn(r.nama_kabupaten_kota)),datasets:[{data:dT5N.map(r=>r.total_nelayan),backgroundColor:COBALT,borderRadius:6,barPercentage:.65}]},options:hBarOpts(fmt,v=>fmt(v)+' orang')});

document.getElementById('srch').addEventListener('input',function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#dtbl tbody tr').forEach(r=>{r.style.display=r.cells[1].textContent.toLowerCase().includes(q)?'':'none';});
});
</script>
@endpush