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
      <div class="text-[11.5px] text-ink-4">Data perairan umum · Tahun {{ $tahunAktif }} · Sumber: database produklaut &amp; nelayan</div>
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
      <span class="text-[10px] font-bold tracking-[.8px] uppercase px-3 py-1 rounded-full bg-teal-lt text-teal flex-shrink-0">RINGKASAN {{ $tahunAktif }}</span>
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Empat indikator utama kondisi perikanan perairan umum Jawa Timur</span>
    </div>

    <div class="grid grid-cols-4 gap-4 max-[1100px]:grid-cols-2 max-[720px]:grid-cols-1">

      {{-- KPI Nelayan --}}
      <div class="bg-white border border-line rounded-2xl p-6 relative overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all kpi-stripe-teal">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-teal-lt">🎣</div>
          <span class="text-[9.5px] font-bold tracking-[.5px] px-2.5 py-1 rounded-full bg-teal-lt text-teal">PERAIRAN UMUM</span>
        </div>
        <div class="text-[11px] font-semibold tracking-[.4px] text-ink-4 mb-2">TOTAL NELAYAN</div>
        <div class="text-[38px] font-extrabold text-ink leading-none mb-1 tracking-[-1.5px]">{{ number_format($totalNelayan,0,',','.') }}</div>
        <div class="text-xs text-ink-3">orang · perairan umum saja<br><span class="text-[10.5px] text-ink-4 italic">tidak termasuk nelayan laut</span></div>
        @if($gNelayan!==null)
          <div class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2.5 py-1 rounded-full mt-3.5 {{ $gNelayan>0?'bg-[#E3FAF3] text-teal':($gNelayan<0?'bg-coral-lt text-coral':'bg-bg text-ink-4') }}">
            {{ $gNelayan>0?'↑':($gNelayan<0?'↓':'→') }} {{ abs($gNelayan) }}% dari {{ $tahunAktif-1 }}
          </div>
        @endif
      </div>

      {{-- KPI Produksi --}}
      <div class="bg-white border border-line rounded-2xl p-6 relative overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all kpi-stripe-amber">
        <div class="flex justify-between items-start mb-4">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg bg-amber-lt">🐟</div>
          <span class="text-[9.5px] font-bold tracking-[.5px] px-2.5 py-1 rounded-full bg-amber-lt text-amber">{{ strtoupper($satuan) }}</span>
        </div>
        <div class="text-[11px] font-semibold tracking-[.4px] text-ink-4 mb-2">TOTAL PRODUKSI</div>
        <div class="text-[38px] font-extrabold text-ink leading-none mb-1 tracking-[-1.5px]">{{ number_format($grandTotal,0,',','.') }}</div>
        <div class="text-xs text-ink-3">{{ $satuan }} · ikan + lunak + keras + lainnya</div>
        @if($gProduksi!==null)
          <div class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2.5 py-1 rounded-full mt-3.5 {{ $gProduksi>0?'bg-[#E3FAF3] text-teal':($gProduksi<0?'bg-coral-lt text-coral':'bg-bg text-ink-4') }}">
            {{ $gProduksi>0?'↑':($gProduksi<0?'↓':'→') }} {{ abs($gProduksi) }}% dari {{ $tahunAktif-1 }}
          </div>
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
        <div class="text-xs text-ink-3">{{ $satuanPrdktv }} · produksi ÷ nelayan perairan umum</div>
        @if($gPrdktv!==null)
          <div class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2.5 py-1 rounded-full mt-3.5 {{ $gPrdktv>0?'bg-[#E3FAF3] text-teal':($gPrdktv<0?'bg-coral-lt text-coral':'bg-bg text-ink-4') }}">
            {{ $gPrdktv>0?'↑':($gPrdktv<0?'↓':'→') }} {{ abs($gPrdktv) }}% dari {{ $tahunAktif-1 }}
          </div>
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
        <div class="text-xs text-ink-3">{{ $jenisDominanPersen }}% dari total produksi {{ $tahunAktif }}</div>
        <div class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2.5 py-1 rounded-full mt-3.5 bg-bg text-ink-4">Komoditas terbesar</div>
      </div>
    </div>

    {{-- ── Efisiensi & Komposisi ── --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
      <span class="text-[10px] font-bold tracking-[.8px] uppercase px-3 py-1 rounded-full bg-amber-lt text-amber flex-shrink-0">EFISIENSI &amp; KOMPOSISI</span>
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Produktivitas per nelayan perairan umum dan proporsi jenis hasil tangkapan</span>
      <span class="text-[11.5px] text-ink-4 flex-shrink-0 italic">↳ Lanjutan KPI Produktivitas &amp; Jenis Dominan</span>
    </div>

    <div class="grid grid-cols-2 gap-4 max-[720px]:grid-cols-1">

      <div class="bg-white border border-line rounded-2xl p-6 shadow-sm">
        <div class="mb-4">
          <div class="text-sm font-bold text-ink mb-1">Tren Produktivitas per Nelayan Perairan Umum</div>
          <div class="text-xs text-ink-3 leading-relaxed">Rata-rata hasil tangkapan per nelayan perairan umum dari tahun ke tahun.
            Meningkat berarti efisiensi membaik; menurun berarti perlu evaluasi alat tangkap atau daya dukung perairan.</div>
          <span class="inline-block mt-2 font-mono text-[9.5px] font-semibold tracking-[.4px] px-2.5 py-0.5 rounded bg-amber-lt text-amber">{{ strtoupper($satuanPrdktv) }}</span>
        </div>
        <div class="h-chart"><canvas id="cPrdktv"></canvas></div>
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
      <span class="text-[10px] font-bold tracking-[.8px] uppercase px-3 py-1 rounded-full bg-teal-lt text-teal flex-shrink-0">TREN HISTORIS</span>
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Dinamika nelayan dan produksi lintas tahun — apakah keduanya bergerak searah?</span>
      <span class="text-[11.5px] text-ink-4 flex-shrink-0 italic">↳ Divergensi = sinyal tekanan ekosistem</span>
    </div>

    <div class="bg-white border border-line rounded-2xl p-6 shadow-sm mb-4">
      <div class="mb-4">
        <div class="text-sm font-bold text-ink mb-1">Tren Jumlah Nelayan Perairan Umum — Lintas Tahun</div>
        <div class="text-xs text-ink-3 leading-relaxed">Perubahan populasi nelayan perairan umum per tahun (<em>tidak termasuk nelayan laut</em>).
          Baca grafik ini bersama grafik Tren Produksi di bawah — jika nelayan turun namun produksi naik, artinya efisiensi meningkat.</div>
        <span class="inline-block mt-2 font-mono text-[9.5px] font-semibold tracking-[.4px] px-2.5 py-0.5 rounded bg-teal-lt text-teal">ORANG / TAHUN</span>
      </div>
      <div class="h-chart-md mt-3.5"><canvas id="cNelayan"></canvas></div>
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
            <div class="text-xs text-ink-3 leading-relaxed">Dua sumbu Y independen — perhatikan <strong class="text-ink-2 font-bold">arah tren bersama</strong>.</div>
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
      <span class="text-[10px] font-bold tracking-[.8px] uppercase px-3 py-1 rounded-full bg-cobalt-lt text-cobalt flex-shrink-0">ANALISIS WILAYAH</span>
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Volume produksi, efisiensi per nelayan, dan konsentrasi SDM per kabupaten/kota</span>
      <span class="text-[11.5px] text-ink-4 flex-shrink-0 italic">↳ Volume ≠ efisiensi — butuh keduanya untuk kebijakan tepat sasaran</span>
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
      <span class="text-[10px] font-bold tracking-[.8px] uppercase px-3 py-1 rounded-full bg-teal-lt text-teal flex-shrink-0">SINTESIS ANALITIK</span>
      <div class="flex-1 h-px bg-line"></div>
      <span class="text-[14.5px] font-bold text-ink">Tiga temuan yang saling menguatkan — dari data menuju rekomendasi kebijakan</span>
    </div>

    <div class="flex items-center justify-center gap-3 my-3 mb-4 text-[11.5px] text-ink-4 font-medium italic">
      <div class="flex-1 h-px bg-gradient-to-r from-transparent via-line-2 to-transparent"></div>
      Temuan 1 membangun konteks input–output &nbsp;→&nbsp; Temuan 2 mendalami efisiensi dan komoditas &nbsp;→&nbsp; Temuan 3 menerjemahkan ke rekomendasi wilayah
      <div class="flex-1 h-px bg-gradient-to-r from-transparent via-line-2 to-transparent"></div>
    </div>

    <div class="grid grid-cols-3 gap-4 max-[1100px]:grid-cols-1">

      {{-- Insight 1 --}}
      <div class="bg-white border border-line rounded-2xl px-7 pt-7 pb-6 shadow-sm flex flex-col ins-top-teal">
        <div class="text-[10.5px] font-bold tracking-[.7px] text-teal mb-3">TEMUAN 01 · KESEIMBANGAN INPUT–OUTPUT</div>
        <div class="text-[15.5px] font-extrabold text-ink mb-3 leading-snug">Pertumbuhan nelayan dan produksi tidak selalu seiring — dan di situlah analisis dimulai</div>
        <div class="text-[13px] text-ink-3 leading-[1.9] flex-1">
          Tahun {{ $tahunAktif }}, tercatat <strong class="text-ink-2 font-bold">{{ number_format($totalNelayan,0,',','.') }} nelayan perairan umum</strong>
          yang secara kolektif menghasilkan <strong class="text-ink-2 font-bold">{{ number_format($grandTotal,0,',','.') }} {{ $satuan }}</strong> produksi.
          @if($gNelayan!==null && $gProduksi!==null)
            Dibanding {{ $tahunAktif-1 }}, jumlah nelayan <strong class="text-ink-2 font-bold">{{ $gNelayan>=0?'bertambah':'berkurang' }} {{ abs($gNelayan) }}%</strong>,
            sementara produksi <strong class="text-ink-2 font-bold">{{ $gProduksi>=0?'naik':'turun' }} {{ abs($gProduksi) }}%</strong>.
            @if($gNelayan>0 && $gProduksi>0 && $gProduksi>$gNelayan)
              Produksi tumbuh lebih cepat dari penambahan nelayan — <strong class="text-ink-2 font-bold">sinyal positif</strong>: setiap nelayan baru berkontribusi lebih besar.
            @elseif($gNelayan>0 && $gProduksi>0 && $gProduksi<=$gNelayan)
              Nelayan bertambah lebih cepat dari pertumbuhan produksi — <strong class="text-ink-2 font-bold">waspadai kejenuhan kapasitas</strong>.
            @elseif($gNelayan>0 && $gProduksi<0)
              Nelayan bertambah namun produksi justru turun — ini <strong class="text-ink-2 font-bold">sinyal divergensi serius</strong>: perlu evaluasi kondisi ekosistem perairan.
            @elseif($gNelayan<0 && $gProduksi>0)
              Nelayan berkurang namun produksi tetap naik — <strong class="text-ink-2 font-bold">efisiensi meningkat signifikan</strong>.
            @else
              Keduanya bergerak ke arah yang sama — keseimbangan sistem perlu dijaga.
            @endif
          @endif
          <em class="text-ink-4 text-xs">Lihat grafik Tren Nelayan &amp; Perbandingan Indeks di bagian Tren Historis untuk konteks multi-tahun.</em>
        </div>
        <div class="mt-5 rounded-xl p-4 bg-teal-lt text-teal text-xs leading-relaxed">
          <span class="block text-[10px] font-bold tracking-[.5px] opacity-70 mb-1">RASIO PERTUMBUHAN TAHUN INI</span>
          <span class="block text-lg font-extrabold text-ink font-mono mb-1">
            @if($gNelayan!==null && $gProduksi!==null)
              Nelayan {{ $gNelayan>=0?'+':'' }}{{ $gNelayan }}% · Produksi {{ $gProduksi>=0?'+':'' }}{{ $gProduksi }}%
            @else
              {{ number_format($totalNelayan,0,',','.') }} nelayan → {{ number_format($grandTotal,0,',','.') }} {{ $satuan }}
            @endif
          </span>
        </div>
      </div>

      {{-- Insight 2 --}}
      <div class="bg-white border border-line rounded-2xl px-7 pt-7 pb-6 shadow-sm flex flex-col ins-top-amber">
        <div class="text-[10.5px] font-bold tracking-[.7px] text-amber mb-3">TEMUAN 02 · EFISIENSI &amp; KOMODITAS UNGGULAN</div>
        <div class="text-[15.5px] font-extrabold text-ink mb-3 leading-snug">Produktivitas mengungkap efisiensi sejati — komposisi menentukan prioritas intervensi</div>
        <div class="text-[13px] text-ink-3 leading-[1.9] flex-1">
          Dari rasio produksi terhadap nelayan perairan umum, diperoleh produktivitas
          <strong class="text-ink-2 font-bold">{{ number_format($produktivitas,2,',','.') }} {{ $satuanPrdktv }}</strong> pada {{ $tahunAktif }}.
          @if($gPrdktv!==null)
            Angka ini <strong class="text-ink-2 font-bold">{{ $gPrdktv>=0?'meningkat':'menurun' }} {{ abs($gPrdktv) }}%</strong> dibanding {{ $tahunAktif-1 }}.
            @if($gPrdktv>=0)
              Peningkatan ini mengindikasikan kapasitas produksi per individu nelayan membaik.
            @else
              Penurunan ini perlu diwaspadai — evaluasi alat tangkap dan pemetaan zona tangkap direkomendasikan.
            @endif
          @endif
          Dari sisi komposisi, <strong class="text-ink-2 font-bold">{{ $jenisDominan }}</strong> mendominasi
          <strong class="text-ink-2 font-bold">{{ $jenisDominanPersen }}%</strong> total produksi.
          <em class="text-ink-4 text-xs">Kebijakan pengelolaan stok dan penetapan harga sebaiknya menjadikan komoditas ini sebagai titik tumpu utama.</em>
        </div>
        <div class="mt-5 rounded-xl p-4 bg-amber-lt text-amber text-xs leading-relaxed">
          <span class="block text-[10px] font-bold tracking-[.5px] opacity-70 mb-1">PRODUKTIVITAS {{ $tahunAktif }}</span>
          <span class="block text-lg font-extrabold text-ink font-mono mb-1">{{ number_format($produktivitas,2,',','.') }} {{ $satuanPrdktv }}</span>
          @if($gPrdktv!==null)
            <span class="text-xs font-bold">{{ $gPrdktv>=0?'↑':'↓' }} {{ abs($gPrdktv) }}% vs {{ $tahunAktif-1 }} · Dominan: {{ $jenisDominan }} {{ $jenisDominanPersen }}%</span>
          @endif
        </div>
      </div>

      {{-- Insight 3 --}}
      <div class="bg-white border border-line rounded-2xl px-7 pt-7 pb-6 shadow-sm flex flex-col ins-top-cobalt">
        <div class="text-[10.5px] font-bold tracking-[.7px] text-cobalt mb-3">TEMUAN 03 · DISPARITAS &amp; REKOMENDASI WILAYAH</div>
        <div class="text-[15.5px] font-extrabold text-ink mb-3 leading-snug">Volume tinggi dan efisiensi tinggi jarang berada di wilayah yang sama — dan itu bermakna</div>
        <div class="text-[13px] text-ink-3 leading-[1.9] flex-1">
          Analisis wilayah mengungkap dua profil yang membutuhkan pendekatan kebijakan berbeda.
          <strong class="text-ink-2 font-bold">{{ preg_replace('/^(Kabupaten|Kota)\s+/i','', $top5Prod->first()?->nama_kabupaten_kota??'-') }}</strong>
          memimpin dari sisi volume produksi absolut.
          Di sisi lain, <strong class="text-ink-2 font-bold">{{ preg_replace('/^(Kabupaten|Kota)\s+/i','', $top5Prdktv->first()?->nama_kabupaten_kota??'-') }}</strong>
          unggul dalam produktivitas per nelayan perairan umum.
          @if(strtolower(preg_replace('/^(Kabupaten|Kota)\s+/i','',$top5Prod->first()?->nama_kabupaten_kota??'')) ===
              strtolower(preg_replace('/^(Kabupaten|Kota)\s+/i','',$top5Prdktv->first()?->nama_kabupaten_kota??'')))
            Keduanya berada di wilayah yang sama — keunggulan kompetitif solid yang layak dijadikan model replikasi.
          @else
            Perbedaan ini adalah <strong class="text-ink-2 font-bold">realitas struktural</strong>: wilayah bervolume besar butuh investasi infrastruktur,
            sementara wilayah paling efisien perlu penguatan kapasitas dan replikasi model ke kabupaten sekitarnya.
          @endif
          <em class="text-ink-4 text-xs">Temuan ini adalah simpulan dari seluruh rantai analisis: KPI → tren → komposisi → peta wilayah.</em>
        </div>
        <div class="mt-5 rounded-xl p-4 bg-cobalt-lt text-cobalt text-xs leading-relaxed">
          <span class="block text-[10px] font-bold tracking-[.5px] opacity-70 mb-1">PERBANDINGAN PUNCAK WILAYAH</span>
          <span class="block text-lg font-extrabold text-ink font-mono mb-1">{{ preg_replace('/^(Kabupaten|Kota)\s+/i','', $top5Prod->first()?->nama_kabupaten_kota??'-') }}</span>
          <span class="text-xs font-semibold block mt-0.5">Efisiensi: {{ preg_replace('/^(Kabupaten|Kota)\s+/i','', $top5Prdktv->first()?->nama_kabupaten_kota??'-') }}</span>
        </div>
      </div>
    </div>

    {{-- ── Tabel Data Lengkap ── --}}
    <div class="flex items-center gap-3 mt-8 mb-4">
      <span class="text-[10px] font-bold tracking-[.8px] uppercase px-3 py-1 rounded-full bg-cobalt-lt text-cobalt flex-shrink-0">DATA LENGKAP</span>
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

      <div class="px-6 py-3 text-[11px] text-ink-4 border-t border-line leading-relaxed">
        ⚠️ Kolom <strong>Nelayan</strong> dan <strong>Produktivitas</strong> menggunakan
        <code class="font-mono bg-bg px-1.5 py-0.5 rounded text-[10.5px]">perairan_umum</code> dari tabel
        <code class="font-mono bg-bg px-1.5 py-0.5 rounded text-[10.5px]">nelayan</code> —
        <em>tidak termasuk</em> kolom <code class="font-mono bg-bg px-1.5 py-0.5 rounded text-[10.5px]">laut</code>.
        Satuan produksi otomatis: <strong>{{ $satuan }}</strong>.
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