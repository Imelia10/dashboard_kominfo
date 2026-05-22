@extends('layouts.app')

@push('styles')
<style>
/* ══════════════════════════════════════════
   AGRI PAGE — Jatim Agri-Data Explorer
══════════════════════════════════════════ */
.agri-wrap {
  padding: 28px 28px 48px;
  background: #F0F4F8;
  min-height: 100%;
}

/* ── Page Header ── */
.agri-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  margin-bottom: 28px;
  flex-wrap: wrap;
  gap: 16px;
}
.agri-title-block .tag {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: #2e7d32;
  background: #e8f5e9;
  border-radius: 6px;
  padding: 3px 10px;
  display: inline-block;
  margin-bottom: 6px;
}
.agri-title-block h1 {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 28px;
  font-weight: 800;
  color: #1a2e1a;
  letter-spacing: -0.5px;
  line-height: 1.15;
}
.agri-title-block p {
  font-size: 13px;
  color: #6b7280;
  margin-top: 4px;
}

/* ── Year Switcher ── */
.year-switcher {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 6px;
}
.year-switcher label {
  font-size: 9.5px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #9ca3af;
}
.year-tabs {
  display: flex;
  gap: 4px;
  background: #e5e7eb;
  border-radius: 10px;
  padding: 4px;
}
.year-tab {
  padding: 6px 16px;
  font-size: 13px;
  font-weight: 700;
  border-radius: 7px;
  border: none;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  transition: all .2s;
  text-decoration: none;
  font-family: 'DM Sans', sans-serif;
}
.year-tab.active,
.year-tab:hover {
  background: #fff;
  color: #2e7d32;
  box-shadow: 0 1px 6px rgba(0,0,0,0.10);
}
.year-tab.active { color: #1b5e20; }

/* ── KPI Cards ── */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}
.kpi-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 22px 20px 18px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
  position: relative;
  overflow: hidden;
}
.kpi-card::before {
  content:'';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  background: var(--kpi-color, #4caf50);
}
.kpi-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 10px;
}
.kpi-value {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 36px;
  font-weight: 800;
  color: var(--kpi-color, #2e7d32);
  line-height: 1;
  margin-bottom: 4px;
}
.kpi-unit {
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  margin-left: 4px;
}
.kpi-change {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 20px;
  margin-bottom: 14px;
}
.kpi-change.up   { color: #16a34a; background: #dcfce7; }
.kpi-change.down { color: #dc2626; background: #fee2e2; }
.kpi-change.flat { color: #6b7280; background: #f3f4f6; }
.kpi-compare {
  border-top: 1px solid #f3f4f6;
  padding-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.kpi-compare-row {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  color: #6b7280;
}
.kpi-compare-row span:last-child {
  font-weight: 600;
  color: #374151;
}

/* ── Charts Row ── */
.charts-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 24px;
}
.chart-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 22px 20px 18px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.chart-card-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 800;
  letter-spacing: .5px;
  text-transform: uppercase;
  color: #1a2e1a;
  margin-bottom: 4px;
}
.chart-card-sub {
  font-size: 11.5px;
  color: #9ca3af;
  margin-bottom: 18px;
}
.chart-legend {
  display: flex;
  gap: 14px;
  margin-bottom: 14px;
  flex-wrap: wrap;
}
.legend-dot {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  color: #6b7280;
}
.legend-dot span {
  width: 10px; height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}
.chart-container {
  position: relative;
  height: 260px;
  width: 100%;
}

/* ── Regional Row ── */
.regional-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 22px 20px 18px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
  margin-bottom: 24px;
}
.regional-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 24px;
  margin-top: 18px;
}
.reg-section-title {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 12px;
}

/* Top kabupaten bars */
.top-kab-item {
  margin-bottom: 10px;
}
.top-kab-name {
  display: flex;
  justify-content: space-between;
  font-size: 12.5px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 5px;
}
.top-kab-pct {
  color: #2e7d32;
  font-weight: 700;
}
.bar-track {
  height: 6px;
  background: #f3f4f6;
  border-radius: 3px;
  overflow: hidden;
}
.bar-fill {
  height: 100%;
  border-radius: 3px;
  background: linear-gradient(90deg, #43a047, #a5d6a7);
  transition: width .6s cubic-bezier(.34,1.56,.64,1);
}

/* Donut wrapper */
.donut-wrap {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}
.donut-wrap canvas { max-width: 150px; }

/* YoY zones */
.yoy-zone { margin-bottom: 16px; }
.yoy-zone-label {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 6px;
}
.yoy-zone-label.up   { color: #16a34a; }
.yoy-zone-label.down { color: #dc2626; }
.yoy-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12.5px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 4px;
}
.yoy-arrow { font-size: 16px; }
.yoy-arrow.up   { color: #16a34a; }
.yoy-arrow.down { color: #dc2626; }

/* ── Detail Table ── */
.detail-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 22px 20px 10px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.detail-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  margin-top: 14px;
}
.detail-table thead tr {
  background: #f9fafb;
}
.detail-table th {
  text-align: left;
  padding: 10px 12px;
  font-size: 10.5px;
  font-weight: 700;
  letter-spacing: .6px;
  text-transform: uppercase;
  color: #6b7280;
  border-bottom: 2px solid #e5e7eb;
}
.detail-table td {
  padding: 11px 12px;
  border-bottom: 1px solid #f3f4f6;
  color: #374151;
}
.detail-table tr:last-child td {
  border-bottom: none;
}
.detail-table tr.total-row td {
  background: #1b5e20;
  color: #fff;
  font-weight: 700;
  border-bottom: none;
}
.detail-table .num { text-align: right; font-variant-numeric: tabular-nums; }
.prod-val { color: #2e7d32; font-weight: 700; }

/* ── Section heading ── */
.section-heading {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 0;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .5px;
  color: #1a2e1a;
}
.section-heading svg {
  width: 18px; height: 18px;
  stroke: #43a047; fill: none; stroke-width: 2;
  stroke-linecap: round; stroke-linejoin: round;
}

/* ── Responsive ── */
@media (max-width: 900px) {
  .kpi-grid      { grid-template-columns: 1fr; }
  .charts-row    { grid-template-columns: 1fr; }
  .regional-grid { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
  .agri-wrap { padding: 16px 12px 40px; }
  .kpi-value { font-size: 28px; }
}
</style>
@endpush

@section('content')
<div class="agri-wrap">

  {{-- ════ HEADER ════ --}}
  <div class="agri-header">
    <div class="agri-title-block">
      <div class="tag">Pertanian</div>
      <h1>Jatim Agri-Data Explorer</h1>
      <p>Sistem Pemantauan Ketahanan Pangan — Data Padi Provinsi Jawa Timur</p>
    </div>
    <div class="year-switcher">
      <label>Tahun</label>
      <div class="year-tabs">
        @foreach($availableYears as $y)
          <a href="{{ route('agri', ['tahun' => $y]) }}"
             class="year-tab {{ $tahun == $y ? 'active' : '' }}">
            {{ $y }}
          </a>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ════ KPI CARDS ════ --}}
  <div class="kpi-grid">

    {{-- Luas Panen --}}
    @php
      $luas     = round(($kpi->luas_panen ?? 0) / 1000, 2);
      $luasPrev = round(($kpiPrev->luas_panen ?? 0) / 1000, 2);
      $luasPct  = $luasPrev > 0 ? round(($luas - $luasPrev) / $luasPrev * 100, 2) : 0;
    @endphp
    <div class="kpi-card" style="--kpi-color:#43a047">
      <div class="kpi-label">Luas Panen Aktif</div>
      <div class="kpi-value">{{ number_format($luas, 2) }}<span class="kpi-unit">ribu Ha</span></div>
      <div class="kpi-change {{ $luasPct > 0 ? 'up' : ($luasPct < 0 ? 'down' : 'flat') }}">
        {{ $luasPct > 0 ? '↑' : ($luasPct < 0 ? '↓' : '—') }}
        {{ abs($luasPct) }}%
      </div>
      <div class="kpi-compare">
        <div class="kpi-compare-row">
          <span>{{ $tahun - 1 }}</span>
          <span>{{ number_format($luasPrev, 2) }} rb Ha</span>
        </div>
        <div class="kpi-compare-row">
          <span>{{ $tahun }}</span>
          <span>{{ number_format($luas, 2) }} rb Ha</span>
        </div>
      </div>
    </div>

    {{-- Produksi Total --}}
    @php
      $prod     = round(($kpi->produksi_total ?? 0) / 1000000, 2);
      $prodPrev = round(($kpiPrev->produksi_total ?? 0) / 1000000, 2);
      $prodPct  = $prodPrev > 0 ? round(($prod - $prodPrev) / $prodPrev * 100, 2) : 0;
    @endphp
    <div class="kpi-card" style="--kpi-color:#e53935">
      <div class="kpi-label">Produksi Total</div>
      <div class="kpi-value">{{ number_format($prod, 2) }}<span class="kpi-unit">juta Ton</span></div>
      <div class="kpi-change {{ $prodPct > 0 ? 'up' : ($prodPct < 0 ? 'down' : 'flat') }}">
        {{ $prodPct > 0 ? '↑' : ($prodPct < 0 ? '↓' : '—') }}
        {{ abs($prodPct) }}%
      </div>
      <div class="kpi-compare">
        <div class="kpi-compare-row">
          <span>{{ $tahun - 1 }}</span>
          <span>{{ number_format($prodPrev, 2) }} jt Ton</span>
        </div>
        <div class="kpi-compare-row">
          <span>{{ $tahun }}</span>
          <span>{{ number_format($prod, 2) }} jt Ton</span>
        </div>
      </div>
    </div>

    {{-- Produktivitas --}}
    @php
      $prdv     = round($kpi->produktivitas ?? 0, 2);
      $prdvPrev = round($kpiPrev->produktivitas ?? 0, 2);
      $prdvPct  = $prdvPrev > 0 ? round(($prdv - $prdvPrev) / $prdvPrev * 100, 2) : 0;
    @endphp
    <div class="kpi-card" style="--kpi-color:#0288d1">
      <div class="kpi-label">Produktivitas Lahan</div>
      <div class="kpi-value">{{ number_format($prdv, 2) }}<span class="kpi-unit">Ku/Ha</span></div>
      <div class="kpi-change {{ $prdvPct > 0 ? 'up' : ($prdvPct < 0 ? 'down' : 'flat') }}">
        {{ $prdvPct > 0 ? '↑' : ($prdvPct < 0 ? '↓' : '—') }}
        {{ abs($prdvPct) }}%
      </div>
      <div class="kpi-compare">
        <div class="kpi-compare-row">
          <span>{{ $tahun - 1 }}</span>
          <span>{{ number_format($prdvPrev, 2) }} Ku/Ha</span>
        </div>
        <div class="kpi-compare-row">
          <span>{{ $tahun }}</span>
          <span>{{ number_format($prdv, 2) }} Ku/Ha</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ════ CHARTS ════ --}}
  <div class="charts-row">

    {{-- Grafik 1: Korelasi Luas Panen vs Produksi (Bar horizontal) --}}
    <div class="chart-card">
      <div class="chart-card-title">Grafik 1: Top Produksi per Kabupaten</div>
      <div class="chart-card-sub">Perbandingan Luas Panen & Rekap Produksi Padi — {{ $tahun }}</div>
      <div class="chart-legend">
        <div class="legend-dot"><span style="background:#43a047"></span> Luas Panen (Ha)</div>
        <div class="legend-dot"><span style="background:#ef9a9a"></span> Produksi (Ton)</div>
      </div>
      <div class="chart-container">
        <canvas id="chartKorelasi"></canvas>
      </div>
      <p style="font-size:11px;color:#9ca3af;margin-top:10px">
        Menunjukkan korelasi luas panen dengan total produksi per kabupaten/kota.
      </p>
    </div>

    {{-- Grafik 2: Volatilitas Musiman (Bar bulanan) --}}
    <div class="chart-card">
      <div class="chart-card-title">Grafik 2: Volatilitas Musiman (Bulanan)</div>
      <div class="chart-card-sub">Luas Panen Bulanan — {{ $tahun }} vs {{ $tahun - 1 }}</div>
      <div class="chart-legend">
        <div class="legend-dot"><span style="background:#1565C0"></span> {{ $tahun }}</div>
        <div class="legend-dot"><span style="background:#f9a825"></span> {{ $tahun - 1 }}</div>
      </div>
      <div class="chart-container">
        <canvas id="chartMusiman"></canvas>
      </div>
      <p style="font-size:11px;color:#9ca3af;margin-top:10px">
        Highlight: Pergeseran puncak panen antar bulan dalam dua tahun terakhir.
      </p>
    </div>
  </div>

  {{-- ════ REGIONAL ════ --}}
  <div class="regional-card">
    <div class="section-heading">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
      Regional Performance &amp; Risk Concentration
    </div>

    <div class="regional-grid">
      {{-- Top Lumbung Padi --}}
      <div>
        <div class="reg-section-title">Top Lumbung Padi</div>
        @foreach($topKab as $i => $kab)
          @php $pct = $totalProduksi > 0 ? round($kab->produksi / $totalProduksi * 100) : 0; @endphp
          <div class="top-kab-item">
            <div class="top-kab-name">
              <span>{{ Str::limit($kab->nama_kabupaten_kota, 22) }}</span>
              <span class="top-kab-pct">{{ $pct }}%</span>
            </div>
            <div class="bar-track">
              <div class="bar-fill" style="width:{{ $pct }}%"></div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Konsentrasi Risiko (Donut) --}}
      <div class="donut-wrap">
        <div class="reg-section-title" style="text-align:center">Konsentrasi Risiko</div>
        <canvas id="chartDonut" width="150" height="150"></canvas>
        @php
          $top3Share = $topKab->take(3)->sum('produksi');
          $top3Pct   = $totalProduksi > 0 ? round($top3Share / $totalProduksi * 100) : 0;
        @endphp
        <div style="text-align:center">
          <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:24px;font-weight:800;color:#c62828">{{ $top3Pct }}%</div>
          <div style="font-size:11px;color:#9ca3af;margin-top:2px">"1/3 ketahanan rentan pada 3 daerah."</div>
          <div style="display:flex;align-items:center;gap:8px;justify-content:center;margin-top:8px;font-size:11px">
            <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:2px;background:#c62828;display:inline-block"></span> TOP 3</span>
            <span style="display:inline-flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:2px;background:#e5e7eb;display:inline-block"></span> Lainnya</span>
          </div>
        </div>
      </div>

      {{-- Zona Kinerja YoY --}}
      <div>
        <div class="reg-section-title">Zona Kinerja Lahan (YoY)</div>
        <div class="yoy-zone">
          <div class="yoy-zone-label up">↑ Top Gainers</div>
          @foreach($gainers as $g)
            <div class="yoy-item">
              <span class="yoy-arrow up">▲</span>
              {{ Str::limit($g->nama_kabupaten_kota, 24) }}
              <span style="font-size:11px;color:#16a34a;margin-left:auto">+{{ round($g->pct_change, 1) }}%</span>
            </div>
          @endforeach
        </div>
        <div class="yoy-zone">
          <div class="yoy-zone-label down">↓ Top Losers</div>
          @foreach($losers as $l)
            <div class="yoy-item">
              <span class="yoy-arrow down">▼</span>
              {{ Str::limit($l->nama_kabupaten_kota, 24) }}
              <span style="font-size:11px;color:#dc2626;margin-left:auto">{{ round($l->pct_change, 1) }}%</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- ════ DETAIL TABLE ════ --}}
  <div class="detail-card">
    <div class="section-heading">
      <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>
      Detail Data Bulanan {{ $tahun }}
    </div>

    <table class="detail-table">
      <thead>
        <tr>
          <th>Bulan</th>
          <th class="num">Luas Panen (Ha)</th>
          <th class="num">Produksi GKG (Ton)</th>
          <th class="num">Produktivitas (Ton/Ha)</th>
        </tr>
      </thead>
      <tbody>
        @php $totalLuas=0; $totalProd=0; $totalPrdv=0; $cnt=0; @endphp
        @foreach($detailBulanan as $row)
          @php
            $totalLuas += $row['luas_panen'];
            $totalProd += $row['produksi'];
            if($row['produktivitas']>0){ $totalPrdv += $row['produktivitas']; $cnt++; }
          @endphp
          <tr>
            <td>{{ $row['bulan'] }}</td>
            <td class="num">{{ number_format($row['luas_panen'], 0, ',', '.') }}</td>
            <td class="num">{{ number_format($row['produksi'], 0, ',', '.') }}</td>
            <td class="num prod-val">{{ number_format($row['produktivitas'], 2) }}</td>
          </tr>
        @endforeach
        <tr class="total-row">
          <td>TOTAL / AVG</td>
          <td class="num">{{ number_format($totalLuas, 0, ',', '.') }}</td>
          <td class="num">{{ number_format($totalProd, 0, ',', '.') }}</td>
          <td class="num">{{ $cnt > 0 ? number_format($totalPrdv/$cnt, 2) : '-' }} Avg</td>
        </tr>
      </tbody>
    </table>
  </div>

</div>{{-- /agri-wrap --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'DM Sans', sans-serif";

// ── Data from PHP ──
const korelasiLabels  = @json($korelasiData->pluck('nama_kabupaten_kota')->map(fn($n)=>Str::limit($n,18)));
const korelasiLuas    = @json($korelasiData->pluck('luas_panen'));
const korelasiProd    = @json($korelasiData->pluck('produksi'));

const bulanLabels     = @json($bulanCols);
const musimanNow      = @json(collect($bulanCols)->map(fn($b)=>$musiman?->$b ?? 0));
const musimanPrev     = @json(collect($bulanCols)->map(fn($b)=>$musimanPrev?->$b ?? 0));

const donutTop3Pct    = {{ $top3Pct }};

// ── Chart 1: Bar Chart Korelasi ──
new Chart(document.getElementById('chartKorelasi'), {
  type: 'bar',
  data: {
    labels: korelasiLabels,
    datasets: [
      {
        label: 'Luas Panen (Ha)',
        data: korelasiLuas,
        backgroundColor: 'rgba(67,160,71,0.75)',
        borderRadius: 5,
        yAxisID: 'yLuas',
      },
      {
        label: 'Produksi (Ton)',
        data: korelasiProd,
        backgroundColor: 'rgba(239,154,154,0.80)',
        borderRadius: 5,
        yAxisID: 'yProd',
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')}`
        }
      }
    },
    scales: {
      x: {
        ticks: { font: { size: 10 }, maxRotation: 35 },
        grid: { display: false }
      },
      yLuas: {
        type: 'linear', position: 'left',
        ticks: { font: { size: 10 }, callback: v => (v/1000).toFixed(0)+'rb' },
        grid: { color: '#f3f4f6' }
      },
      yProd: {
        type: 'linear', position: 'right',
        ticks: { font: { size: 10 }, callback: v => (v/1000000).toFixed(1)+'jt' },
        grid: { display: false }
      }
    }
  }
});

// ── Chart 2: Bar Chart Musiman ──
new Chart(document.getElementById('chartMusiman'), {
  type: 'bar',
  data: {
    labels: bulanLabels.map(b => b.substring(0,3)),
    datasets: [
      {
        label: '{{ $tahun }}',
        data: musimanNow,
        backgroundColor: 'rgba(21,101,192,0.78)',
        borderRadius: 4,
      },
      {
        label: '{{ $tahun - 1 }}',
        data: musimanPrev,
        backgroundColor: 'rgba(249,168,37,0.72)',
        borderRadius: 4,
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')} Ha`
        }
      }
    },
    scales: {
      x: {
        ticks: { font: { size: 11 } },
        grid: { display: false }
      },
      y: {
        ticks: { font: { size: 10 }, callback: v => (v/1000).toFixed(0)+'rb' },
        grid: { color: '#f3f4f6' }
      }
    }
  }
});

// ── Donut Chart ──
new Chart(document.getElementById('chartDonut'), {
  type: 'doughnut',
  data: {
    labels: ['TOP 3', 'Lainnya'],
    datasets: [{
      data: [donutTop3Pct, 100 - donutTop3Pct],
      backgroundColor: ['#c62828', '#e5e7eb'],
      borderWidth: 0,
      hoverOffset: 4,
    }]
  },
  options: {
    responsive: false,
    cutout: '68%',
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: { label: ctx => `${ctx.label}: ${ctx.parsed}%` }
      }
    }
  }
});
</script>
@endpush