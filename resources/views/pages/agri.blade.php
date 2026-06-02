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

/* ══════════════════════════════════════════
   SEMESTER + URBAN ROW (2-col)
══════════════════════════════════════════ */
.smt-urban-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 24px;
}

/* ── Distribusi Semesteran Card ── */
.semester-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 22px 20px 20px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
}
.reg-section-title {
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1px;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 16px;
}
.semester-donut-area {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  margin-bottom: 16px;
}
/* Donut container with center label overlay */
.semester-donut-container {
  position: relative;
  width: 160px;
  height: 160px;
}
.semester-donut-container canvas {
  position: absolute;
  top: 0; left: 0;
}
.semester-donut-center {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  pointer-events: none;
}
.semester-donut-pct {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 28px;
  font-weight: 800;
  color: #1b5e20;
  line-height: 1;
}
.semester-donut-sub {
  font-size: 9.5px;
  font-weight: 700;
  letter-spacing: .8px;
  text-transform: uppercase;
  color: #9ca3af;
  margin-top: 3px;
}
.semester-legend {
  display: flex;
  gap: 20px;
  justify-content: center;
  margin-top: 10px;
}
.semester-legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #374151;
}
.semester-legend-dot {
  width: 10px; height: 10px;
  border-radius: 50%;
}
.semester-stats {
  border-top: 1px solid #f3f4f6;
  padding-top: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-top: auto;
}
.semester-stat-row {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
}
.semester-stat-label { color: #6b7280; }
.semester-stat-val   { font-weight: 700; color: #374151; font-variant-numeric: tabular-nums; }

/* ── Urban vs Rural Card ── */
.urban-rural-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 22px 20px 20px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
  display: flex;
  flex-direction: column;
}
.urban-rural-body {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 22px;
  margin-top: 6px;
}
.ur-item-header {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 10px;
}
.ur-item-label {
  font-size: 13px;
  font-weight: 800;
  letter-spacing: .8px;
  text-transform: uppercase;
  color: #1a2e1a;
  font-family: 'Plus Jakarta Sans', sans-serif;
}
.ur-item-val {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 20px;
  font-weight: 800;
}
.ur-bar-track {
  height: 12px;
  background: #f3f4f6;
  border-radius: 6px;
  overflow: hidden;
}
.ur-bar-fill {
  height: 100%;
  border-radius: 6px;
  transition: width .7s cubic-bezier(.34,1.56,.64,1);
}
.ur-note {
  margin-top: auto;
  font-size: 11px;
  color: #9ca3af;
  font-style: italic;
  padding-top: 14px;
  border-top: 1px solid #f3f4f6;
  line-height: 1.6;
  margin-top: 20px;
}

/* ══════════════════════════════════════════
   TOP PRODUKSI ROW (2-col)
══════════════════════════════════════════ */
.top-prod-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 24px;
}
.top-prod-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 22px 20px 18px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.top-prod-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 18px;
}
.top-prod-icon { font-size: 18px; }
.top-prod-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  font-weight: 800;
  color: #1a2e1a;
}
.top-prod-subtitle {
  font-size: 11px;
  color: #9ca3af;
  margin-left: 2px;
}

/* Top bars */
.top-kab-item { margin-bottom: 14px; }
.top-kab-item:last-child { margin-bottom: 0; }
.top-kab-name {
  display: flex;
  justify-content: space-between;
  font-size: 12.5px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}
.top-kab-val          { font-weight: 700; }
.top-kab-val.green    { color: #2e7d32; }
.top-kab-val.red      { color: #dc2626; }
.bar-track {
  height: 8px;
  background: #f3f4f6;
  border-radius: 4px;
  overflow: hidden;
}
.bar-fill {
  height: 100%;
  border-radius: 4px;
  transition: width .6s cubic-bezier(.34,1.56,.64,1);
}
.bar-fill.gold { background: linear-gradient(90deg, #f59e0b, #fde68a); }
.bar-fill.red  { background: linear-gradient(90deg, #ef4444, #fca5a5); }

/* ── Detail Table ── */
.detail-card {
  background: #fff;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
  padding: 22px 20px 10px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.05);
}
.detail-table-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.section-heading {
  display: flex;
  align-items: center;
  gap: 10px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  font-weight: 800;
  color: #1a2e1a;
}
.section-heading svg {
  width: 18px; height: 18px;
  stroke: #43a047; fill: none; stroke-width: 2;
  stroke-linecap: round; stroke-linejoin: round;
}
.detail-table-link {
  font-size: 12px;
  font-weight: 600;
  color: #2e7d32;
  text-decoration: none;
}
.detail-table-link:hover { text-decoration: underline; }
.detail-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
  margin-top: 14px;
}
.detail-table thead tr { background: #f9fafb; }
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
.detail-table tr:last-child td { border-bottom: none; }
.detail-table tr.total-row td {
  background: #1b5e20;
  color: #fff;
  font-weight: 700;
  border-bottom: none;
}
.detail-table .num { text-align: right; font-variant-numeric: tabular-nums; }
.prod-val { color: #2e7d32; font-weight: 700; }

/* ── Responsive ── */
@media (max-width: 900px) {
  .kpi-grid      { grid-template-columns: 1fr; }
  .charts-row    { grid-template-columns: 1fr; }
  .smt-urban-row { grid-template-columns: 1fr; }
  .top-prod-row  { grid-template-columns: 1fr; }
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
        {{ $luasPct > 0 ? '↑' : ($luasPct < 0 ? '↓' : '—') }} {{ abs($luasPct) }}%
      </div>
      <div class="kpi-compare">
        <div class="kpi-compare-row"><span>{{ $tahun - 1 }}</span><span>{{ number_format($luasPrev, 2) }} rb Ha</span></div>
        <div class="kpi-compare-row"><span>{{ $tahun }}</span><span>{{ number_format($luas, 2) }} rb Ha</span></div>
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
        {{ $prodPct > 0 ? '↑' : ($prodPct < 0 ? '↓' : '—') }} {{ abs($prodPct) }}%
      </div>
      <div class="kpi-compare">
        <div class="kpi-compare-row"><span>{{ $tahun - 1 }}</span><span>{{ number_format($prodPrev, 2) }} jt Ton</span></div>
        <div class="kpi-compare-row"><span>{{ $tahun }}</span><span>{{ number_format($prod, 2) }} jt Ton</span></div>
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
        {{ $prdvPct > 0 ? '↑' : ($prdvPct < 0 ? '↓' : '—') }} {{ abs($prdvPct) }}%
      </div>
      <div class="kpi-compare">
        <div class="kpi-compare-row"><span>{{ $tahun - 1 }}</span><span>{{ number_format($prdvPrev, 2) }} Ku/Ha</span></div>
        <div class="kpi-compare-row"><span>{{ $tahun }}</span><span>{{ number_format($prdv, 2) }} Ku/Ha</span></div>
      </div>
    </div>
  </div>

  {{-- ════ CHARTS ════ --}}
  <div class="charts-row">

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

  {{-- ════ SEMESTER + URBAN VS RURAL (2-col) ════ --}}
  @php
    $smt1Total = $semesterData->smt1 ?? 0;
    $smt2Total = $semesterData->smt2 ?? 0;
    $smtGrand  = $smt1Total + $smt2Total;
    $smt1Pct   = $smtGrand > 0 ? round($smt1Total / $smtGrand * 100) : 0;
    $smt2Pct   = 100 - $smt1Pct;

    $kotaVal = round($urbanRuralData->kota ?? 0, 1);
    $kabVal  = round($urbanRuralData->kabupaten ?? 0, 1);
    $urMax   = max($kotaVal, $kabVal, 1);
  @endphp

  <div class="smt-urban-row">

    {{-- Distribusi Semesteran --}}
    <div class="semester-card">
      <div class="reg-section-title">Distribusi Semesteran</div>

      <div class="semester-donut-area">
        <div class="semester-donut-container">
          <canvas id="chartSemester" width="160" height="160"></canvas>
          <div class="semester-donut-center">
            <div class="semester-donut-pct">{{ $smt1Pct }}%</div>
            <div class="semester-donut-sub">SMT 1 Focus</div>
          </div>
        </div>
        <div class="semester-legend">
          <div class="semester-legend-item">
            <span class="semester-legend-dot" style="background:#1b5e20"></span> SMT 1
          </div>
          <div class="semester-legend-item">
            <span class="semester-legend-dot" style="background:#f9a825"></span> SMT 2
          </div>
        </div>
      </div>

      <div class="semester-stats">
        <div class="semester-stat-row">
          <span class="semester-stat-label">SMT 1 (Jan–Jun)</span>
          <span class="semester-stat-val">{{ number_format($smt1Total, 0, ',', '.') }} Ha</span>
        </div>
        <div class="semester-stat-row">
          <span class="semester-stat-label">SMT 2 (Jul–Des)</span>
          <span class="semester-stat-val">{{ number_format($smt2Total, 0, ',', '.') }} Ha</span>
        </div>
        <div class="semester-stat-row" style="border-top:1px solid #f3f4f6;padding-top:6px;margin-top:2px">
          <span class="semester-stat-label" style="font-weight:700;color:#374151">Total {{ $tahun }}</span>
          <span class="semester-stat-val" style="color:#1b5e20">{{ number_format($smtGrand, 0, ',', '.') }} Ha</span>
        </div>
      </div>
    </div>

    {{-- Urban vs Rural --}}
    <div class="urban-rural-card">
      <div class="reg-section-title">Urban vs Rural Farming</div>

      <div class="urban-rural-body">
        <div>
          <div class="ur-item-header">
            <div class="ur-item-label">Kota</div>
            <div class="ur-item-val" style="color:#f9a825">{{ number_format($kotaVal, 1) }} Ku/Ha</div>
          </div>
          <div class="ur-bar-track">
            <div class="ur-bar-fill" style="width:{{ round($kotaVal/$urMax*100) }}%;background:#f9a825"></div>
          </div>
        </div>
        <div>
          <div class="ur-item-header">
            <div class="ur-item-label">Kabupaten</div>
            <div class="ur-item-val" style="color:#1565c0">{{ number_format($kabVal, 1) }} Ku/Ha</div>
          </div>
          <div class="ur-bar-track">
            <div class="ur-bar-fill" style="width:{{ round($kabVal/$urMax*100) }}%;background:#1565c0"></div>
          </div>
        </div>
      </div>

      <div class="ur-note">
        Note: High efficiency observed in urban buffer zones due to intensive tech adoption.
      </div>
    </div>
  </div>

  {{-- ════ TOP 5 PRODUKSI TERBANYAK & TERDIKIT ════ --}}
  @php
    // Filter keluar nama yang mengandung "Jawa Timur" dari top/bottom
    $topKabFiltered    = ($topKabData ?? collect())->filter(fn($k) => stripos($k->nama_kabupaten_kota, 'Jawa Timur') === false)->values();
    $bottomKabFiltered = ($bottomKabData ?? collect())->filter(fn($k) => stripos($k->nama_kabupaten_kota, 'Jawa Timur') === false)->values();
    $topKabMax         = $topKabFiltered->max('produksi') ?: 1;
    $bottomKabMax      = $bottomKabFiltered->max('produksi') ?: 1;
  @endphp

  <div class="top-prod-row">

    {{-- Top 5 Terbanyak --}}
    <div class="top-prod-card">
      <div class="top-prod-header">
        <span class="top-prod-icon">🏆</span>
        <div>
          <span class="top-prod-title">Top 5 Produksi Terbanyak</span>
          <span class="top-prod-subtitle">(Lumbung Padi)</span>
        </div>
      </div>
      @foreach($topKabFiltered->take(5) as $kab)
        @php $barW = round($kab->produksi / $topKabMax * 100); @endphp
        <div class="top-kab-item">
          <div class="top-kab-name">
            <span>{{ $kab->nama_kabupaten_kota }}</span>
            <span class="top-kab-val green">{{ number_format($kab->produksi, 0, ',', '.') }} Ton</span>
          </div>
          <div class="bar-track">
            <div class="bar-fill gold" style="width:{{ $barW }}%"></div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Top 5 Terdikit --}}
    <div class="top-prod-card">
      <div class="top-prod-header">
        <span class="top-prod-icon">⚠️</span>
        <div>
          <span class="top-prod-title">Top 5 Produksi Terdikit</span>
          <span class="top-prod-subtitle">(Area Defisit)</span>
        </div>
      </div>
      @foreach($bottomKabFiltered->take(5) as $kab)
        @php $barW = round($kab->produksi / $bottomKabMax * 100); @endphp
        <div class="top-kab-item">
          <div class="top-kab-name">
            <span>{{ $kab->nama_kabupaten_kota }}</span>
            <span class="top-kab-val red">{{ number_format($kab->produksi, 0, ',', '.') }} Ton</span>
          </div>
          <div class="bar-track">
            <div class="bar-fill red" style="width:{{ $barW }}%"></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- ════ DETAIL TABLE ════ --}}
  <div class="detail-card">
    <div class="detail-table-header">
      <div class="section-heading">
        <svg viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>
        Tabel Rincian Konsolidasi Bulanan {{ $tahun }}
      </div>
      <a href="#" class="detail-table-link">Lihat Semua ›</a>
    </div>

    <table class="detail-table">
      <thead>
        <tr>
          <th>Bulan</th>
          <th class="num">Luas Panen (Ha)</th>
          <th class="num">Produksi GKG (Ton)</th>
          <th class="num">Produktivitas (Ku/Ha)</th>
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
const dataKorelasi = @json(
    $korelasiData
        ->where('nama_kabupaten_kota', '!=', 'Jawa Timur')
        ->values()
);

const korelasiLabels = dataKorelasi.map(x => x.nama_kabupaten_kota);
const korelasiLuas   = dataKorelasi.map(x => x.luas_panen);
const korelasiProd   = dataKorelasi.map(x => x.produksi);

const bulanLabels    = @json($bulanCols);
const musimanNow     = @json(collect($bulanCols)->map(fn($b)=>$musiman?->$b ?? 0));
const musimanPrev    = @json(collect($bulanCols)->map(fn($b)=>$musimanPrev?->$b ?? 0));

const smt1Pct = {{ $smt1Pct }};
const smt2Pct = {{ $smt2Pct }};

// ── Chart 1: Bar Chart Korelasi ──
new Chart(document.getElementById('chartKorelasi'), {
  type: 'bar',
  data: {
    labels: korelasiLabels,
    datasets: [
      { label:'Luas Panen (Ha)', data:korelasiLuas, backgroundColor:'rgba(67,160,71,0.75)', borderRadius:5, yAxisID:'yLuas' },
      { label:'Produksi (Ton)',  data:korelasiProd, backgroundColor:'rgba(239,154,154,0.80)', borderRadius:5, yAxisID:'yProd' }
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:false,
    interaction:{ mode:'index', intersect:false },
    plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:ctx=>`${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')}` } } },
    scales:{
      x:{ ticks:{font:{size:10},maxRotation:35}, grid:{display:false} },
      yLuas:{ type:'linear', position:'left',  ticks:{font:{size:10}, callback:v=>(v/1000).toFixed(0)+'rb'}, grid:{color:'#f3f4f6'} },
      yProd:{ type:'linear', position:'right', ticks:{font:{size:10}, callback:v=>(v/1000000).toFixed(1)+'jt'}, grid:{display:false} }
    }
  }
});

// ── Chart 2: Bar Chart Musiman ──
new Chart(document.getElementById('chartMusiman'), {
  type: 'bar',
  data: {
    labels: bulanLabels.map(b=>b.substring(0,3)),
    datasets: [
      { label:'{{ $tahun }}',     data:musimanNow,  backgroundColor:'rgba(21,101,192,0.78)', borderRadius:4 },
      { label:'{{ $tahun - 1 }}', data:musimanPrev, backgroundColor:'rgba(249,168,37,0.72)', borderRadius:4 }
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:false,
    interaction:{ mode:'index', intersect:false },
    plugins:{ legend:{display:false}, tooltip:{ callbacks:{ label:ctx=>`${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')} Ha` } } },
    scales:{
      x:{ ticks:{font:{size:11}}, grid:{display:false} },
      y:{ ticks:{font:{size:10}, callback:v=>(v/1000).toFixed(0)+'rb'}, grid:{color:'#f3f4f6'} }
    }
  }
});

// ── Donut Chart: Distribusi Semesteran ──
new Chart(document.getElementById('chartSemester'), {
  type: 'doughnut',
  data: {
    labels: ['SMT 1 (Jan–Jun)', 'SMT 2 (Jul–Des)'],
    datasets: [{
      data: [smt1Pct, smt2Pct],
      backgroundColor: ['#1b5e20', '#f9a825'],
      borderWidth: 0,
      hoverOffset: 4,
    }]
  },
  options: {
    responsive: false,
    cutout: '70%',
    plugins: {
      legend: { display: false },
      tooltip: { callbacks:{ label:ctx=>`${ctx.label}: ${ctx.parsed}%` } }
    }
  }
});
</script>
@endpush