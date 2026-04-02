@extends('layouts.app')

@push('styles')
<style>
/* ─── PAGE INNER ──────────────────────────────────────────── */
.page-inner {
  width: 100%;
  height: 100%;
  overflow-y: auto;
  overflow-x: hidden;
  background: #F0F4FA;
}
.page-wrapper {
  max-width: 1280px;
  margin: 0 auto;
  padding: 28px 24px 48px;
}

/* ─── HEADER ─────────────────────────────────────────────── */
.page-header {
  margin-bottom: 24px;
}
.breadcrumb {
  display: flex; align-items: center; gap: 6px;
  font-size: 11.5px; color: #6b7280; margin-bottom: 6px;
  font-weight: 500; letter-spacing: .3px;
}
.breadcrumb a { color: #1565C0; text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }
.breadcrumb-bar {
  width: 32px; height: 3px; background: #1565C0; border-radius: 2px;
  display: inline-block; margin-right: 6px; vertical-align: middle;
}
.page-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 26px; font-weight: 800; color: #0f1f3c; line-height: 1.2;
}
.page-title span { color: #1565C0; }
.page-subtitle {
  font-size: 12.5px; color: #8a97b0; margin-top: 4px; font-weight: 400;
}

/* ─── YEAR SELECTOR ──────────────────────────────────────── */
.year-selector {
  display: flex; align-items: center; gap: 6px;
  background: #fff; border: 1.5px solid #e4e8f0;
  border-radius: 10px; padding: 7px 14px;
  font-size: 13px; font-weight: 600; color: #1565C0;
  cursor: pointer; box-shadow: 0 1px 4px rgba(21,101,192,.07);
}
.year-selector select {
  border: none; outline: none; background: transparent;
  font-size: 13px; font-weight: 700; color: #1565C0;
  cursor: pointer; font-family: inherit;
}

/* ─── KPI GRID ───────────────────────────────────────────── */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 14px;
  margin-bottom: 22px;
}
@media (max-width: 1100px) { .kpi-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width: 680px)  { .kpi-grid { grid-template-columns: repeat(2,1fr); } }

.kpi-card {
  background: #fff;
  border-radius: 14px;
  padding: 16px 18px 14px;
  box-shadow: 0 1px 6px rgba(21,101,192,.07);
  border: 1px solid #e4eaf5;
  position: relative; overflow: hidden;
}
.kpi-card::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
}
.kpi-card.blue::before   { background: linear-gradient(90deg,#1565C0,#42a5f5); }
.kpi-card.green::before  { background: linear-gradient(90deg,#2e7d32,#66bb6a); }
.kpi-card.teal::before   { background: linear-gradient(90deg,#00796b,#26c6da); }
.kpi-card.orange::before { background: linear-gradient(90deg,#e65100,#ffa726); }
.kpi-card.amber::before  { background: linear-gradient(90deg,#f57f17,#ffee58); }
.kpi-card.purple::before { background: linear-gradient(90deg,#6a1b9a,#ab47bc); }

.kpi-label {
  font-size: 10px; font-weight: 700; color: #9aacca;
  letter-spacing: .8px; text-transform: uppercase; margin-bottom: 6px;
}
.kpi-value {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 22px; font-weight: 800; color: #0f1f3c; line-height: 1.1;
}
.kpi-sub { font-size: 10.5px; color: #8a97b0; margin-top: 2px; }
.kpi-change {
  display: inline-flex; align-items: center; gap: 3px;
  font-size: 11px; font-weight: 700; border-radius: 6px; padding: 2px 7px; margin-top: 6px;
}
.kpi-change.up   { background: #e8f5e9; color: #2e7d32; }
.kpi-change.down { background: #fff3e0; color: #e65100; }
.kpi-change.neutral { background: #e3f2fd; color: #1565C0; }

/* progress bar in kpi */
.kpi-progress { height: 4px; background: #f0f4fa; border-radius: 2px; margin-top: 8px; overflow: hidden; }
.kpi-progress-fill { height: 100%; border-radius: 2px; transition: width .6s ease; }

/* ─── CHART CARD ─────────────────────────────────────────── */
.section-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 15.5px; font-weight: 700; color: #0f1f3c; margin-bottom: 2px;
}
.section-subtitle {
  font-size: 11.5px; color: #8a97b0; margin-bottom: 14px;
}

.chart-card {
  background: #fff;
  border-radius: 16px;
  padding: 20px 22px 16px;
  box-shadow: 0 1px 8px rgba(21,101,192,.07);
  border: 1px solid #e4eaf5;
  margin-bottom: 20px;
}

/* ─── BAR CHART (rencana vs realisasi) ───────────────────── */
.bar-chart-wrap {
  overflow-x: auto;
  padding-bottom: 4px;
}
.bar-chart-inner {
  min-width: 900px;
  display: flex; align-items: flex-end; gap: 7px;
  height: 160px; padding: 0 4px;
}
.bar-group {
  display: flex; flex-direction: column; align-items: center; gap: 2px; flex: 1;
}
.bar-pair {
  display: flex; gap: 3px; align-items: flex-end; width: 100%;
  max-width: 36px; min-width: 22px;
}
.bar {
  flex: 1; border-radius: 4px 4px 0 0; min-height: 4px;
  position: relative; cursor: pointer; transition: opacity .15s;
}
.bar:hover { opacity: .75; }
.bar-rencana  { background: #BBDEFB; }
.bar-realisasi{ background: #1565C0; }
.bar-label {
  font-size: 8.5px; color: #9aacca; text-align: center;
  max-width: 36px; overflow: hidden; white-space: nowrap;
  margin-top: 4px;
}

/* ─── TWO-COL GRID ───────────────────────────────────────── */
.two-col-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 20px;
}
@media (max-width: 860px) { .two-col-grid { grid-template-columns: 1fr; } }

.three-col-grid {
  display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; margin-bottom: 20px;
}
@media (max-width: 900px) { .three-col-grid { grid-template-columns: 1fr; } }

/* ─── RANKING BARS ───────────────────────────────────────── */
.rank-item { margin-bottom: 10px; }
.rank-label-row {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 4px;
}
.rank-name  { font-size: 12.5px; font-weight: 600; color: #374151; }
.rank-pct   { font-size: 12px; font-weight: 700; color: #1565C0; }
.rank-bar-wrap { height: 7px; background: #f0f4fa; border-radius: 4px; overflow: hidden; }
.rank-bar-fill { height: 100%; border-radius: 4px; transition: width .7s ease; }

.section-divider {
  font-size: 10px; font-weight: 800; letter-spacing: 1px;
  text-transform: uppercase; padding: 6px 0 8px; margin-top: 4px;
}
.divider-green  { color: #2e7d32; }
.divider-red    { color: #c62828; }

/* ─── PIE-STYLE LIST ─────────────────────────────────────── */
.pie-item {
  display: flex; align-items: center; gap: 9px; padding: 7px 0;
  border-bottom: 1px solid #f0f4fa;
}
.pie-item:last-child { border-bottom: none; }
.pie-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
.pie-bar-wrap { flex: 1; height: 6px; background: #f0f4fa; border-radius: 3px; overflow: hidden; }
.pie-bar-fill { height: 100%; border-radius: 3px; transition: width .7s ease; }
.pie-pct { font-size: 12px; font-weight: 700; color: #374151; min-width: 38px; text-align: right; }

/* ─── NILAI PER KPM CHART ────────────────────────────────── */
.nilai-chart { display: flex; flex-direction: column; gap: 8px; }
.nilai-item {
  display: flex; align-items: center; gap: 10px;
}
.nilai-nama { font-size: 11px; font-weight: 600; color: #374151; min-width: 110px; }
.nilai-bar-wrap { flex: 1; height: 14px; background: #f0f4fa; border-radius: 4px; overflow: hidden; position: relative; }
.nilai-bar-fill {
  height: 100%; border-radius: 4px;
  background: linear-gradient(90deg, #42a5f5, #1565C0);
  transition: width .7s ease;
}
.nilai-avg-line {
  position: absolute; top: 0; bottom: 0; width: 2px; background: #ef5350;
  z-index: 2;
}
.nilai-val { font-size: 11px; font-weight: 700; color: #1565C0; min-width: 70px; text-align: right; }

/* ─── WAWASAN CARD ───────────────────────────────────────── */
.wawasan-card {
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 1px 8px rgba(21,101,192,.07);
  border: 1px solid #e4eaf5;
}
.wawasan-title {
  font-size: 10px; font-weight: 800; letter-spacing: 1px;
  text-transform: uppercase; color: #1565C0; margin-bottom: 14px;
}
.wawasan-item {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 10px 0; border-bottom: 1px solid #f0f4fa;
}
.wawasan-item:last-child { border-bottom: none; }
.wawasan-icon {
  width: 32px; height: 32px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
  font-size: 14px;
}
.wawasan-icon.green  { background: #e8f5e9; }
.wawasan-icon.blue   { background: #e3f2fd; }
.wawasan-icon.orange { background: #fff3e0; }
.wawasan-icon.amber  { background: #fffde7; }
.wawasan-item-label {
  font-size: 9.5px; font-weight: 800; letter-spacing: .6px;
  text-transform: uppercase; color: #9aacca; margin-bottom: 2px;
}
.wawasan-item-val {
  font-size: 12.5px; font-weight: 700; color: #0f1f3c; line-height: 1.3;
}
.wawasan-footer {
  font-size: 10px; color: #9aacca; margin-top: 12px; display: flex; align-items: center; gap: 5px;
}

/* ─── KLASIFIKASI ────────────────────────────────────────── */
.klasifikasi-grid {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px;
}
@media (max-width: 860px) { .klasifikasi-grid { grid-template-columns: 1fr; } }

.klasifikasi-col { }
.klasifikasi-header {
  display: flex; align-items: center; gap: 8px; margin-bottom: 12px;
}
.klasifikasi-dot { width: 10px; height: 10px; border-radius: 50%; }
.klasifikasi-cat {
  font-size: 11px; font-weight: 800; letter-spacing: .6px; text-transform: uppercase;
}
.klasifikasi-cat.green  { color: #2e7d32; }
.klasifikasi-cat.orange { color: #e65100; }
.klasifikasi-cat.red    { color: #c62828; }

.klas-item {
  display: flex; align-items: center; justify-content: space-between;
  padding: 5px 10px; border-radius: 8px; margin-bottom: 4px;
}
.klas-item.green  { background: #f1faf2; border-left: 3px solid #43a047; }
.klas-item.orange { background: #fff8f1; border-left: 3px solid #fb8c00; }
.klas-item.red    { background: #fff1f1; border-left: 3px solid #ef5350; }
.klas-name  { font-size: 11.5px; font-weight: 600; color: #374151; }
.klas-pct   { font-size: 12px; font-weight: 700; }
.klas-pct.green  { color: #2e7d32; }
.klas-pct.orange { color: #e65100; }
.klas-pct.red    { color: #c62828; }

/* ─── GAP TABLE ──────────────────────────────────────────── */
.gap-table-wrap { overflow-x: auto; }
.gap-table {
  width: 100%; border-collapse: collapse;
  font-size: 12.5px;
}
.gap-table th {
  font-size: 10px; font-weight: 800; letter-spacing: .6px;
  text-transform: uppercase; color: #9aacca;
  padding: 8px 14px; text-align: left;
  border-bottom: 2px solid #e4eaf5; white-space: nowrap;
}
.gap-table td {
  padding: 9px 14px; color: #374151; border-bottom: 1px solid #f0f4fa;
  font-weight: 500;
}
.gap-table tr:hover td { background: #f7f9fd; }
.gap-table tr.kritis td { background: #fff5f5; }
.gap-table tr.waspada td { background: #fffbf0; }
.gap-table tr.stabil td { background: #f1faf2; }

.badge {
  display: inline-flex; align-items: center;
  font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px;
}
.badge.kritis  { background: #ffebee; color: #c62828; }
.badge.waspada { background: #fff3e0; color: #e65100; }
.badge.stabil  { background: #e8f5e9; color: #2e7d32; }

.legend-row {
  display: flex; gap: 14px; justify-content: flex-end; align-items: center;
  margin-bottom: 10px;
}
.legend-item { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: #6b7280; }
.legend-dot  { width: 8px; height: 8px; border-radius: 50%; }
</style>
@endpush

@section('content')
<div class="page-inner">
<div class="page-wrapper">

  {{-- ── HEADER ─────────────────────────────────────────────── --}}
  <div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div class="page-header" style="margin-bottom:0">
      <div class="breadcrumb">
        <span class="breadcrumb-bar"></span>
        DASHBOARD
      </div>
      <div style="display:flex;align-items:center;gap:10px">
        <div>
          <h1 class="page-title">Analisis Penyaluran <span>BANSOS</span> Jawa Timur</h1>
          <p class="page-subtitle">Analisis Rencana vs Realisasi Kabupaten/Kota</p>
        </div>
      </div>
    </div>
    {{-- Year Selector --}}
    <form method="GET" action="{{ route('economy') }}" style="display:flex;align-items:center;gap:8px">
      <div class="year-selector">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1565C0" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        TAHUN
        <select name="tahun" onchange="this.form.submit()">
          @foreach($validYears as $yr)
            <option value="{{ $yr }}" {{ $yr == $selectedYear ? 'selected' : '' }}>{{ $yr }}</option>
          @endforeach
        </select>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1565C0" stroke-width="2">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </div>
    </form>
  </div>

  {{-- ── KPI GRID ─────────────────────────────────────────────── --}}
  <div class="kpi-grid">
    {{-- 1. Total Rencana KPM --}}
    <div class="kpi-card blue">
      <div class="kpi-label">Total Rencana KPM</div>
      <div class="kpi-value">{{ number_format($totalRencanaKpm) }}</div>
      <div class="kpi-sub">Keluarga Penerima Manfaat</div>
      <div class="kpi-progress"><div class="kpi-progress-fill" style="background:#1565C0;width:100%"></div></div>
    </div>
    {{-- 2. Total Realisasi KPM --}}
    <div class="kpi-card green">
      <div class="kpi-label">Total Realisasi KPM</div>
      <div class="kpi-value">{{ number_format($totalRealisasiKpm) }}</div>
      <div class="kpi-sub">KPM Tersalurkan</div>
      <div class="kpi-progress">
        <div class="kpi-progress-fill" style="background:#43a047;width:{{ min($pctKpm,100) }}%"></div>
      </div>
    </div>
    {{-- 3. % Realisasi KPM --}}
    <div class="kpi-card teal">
      <div class="kpi-label">% Realisasi KPM</div>
      <div class="kpi-value">{{ $pctKpm }}%</div>
      @if($pctKpm >= 95)
        <span class="kpi-change up">✓ Tercapai</span>
      @elseif($pctKpm >= 80)
        <span class="kpi-change neutral">~ Menengah</span>
      @else
        <span class="kpi-change down">⚠ Rendah</span>
      @endif
    </div>
    {{-- 4. Rencana Anggaran --}}
    <div class="kpi-card orange">
      <div class="kpi-label">Rencana Anggaran</div>
      <div class="kpi-value">Rp {{ number_format($totalRencanaAng/1e9, 1) }} M</div>
      <div class="kpi-sub">Miliar Rupiah</div>
      <div class="kpi-progress"><div class="kpi-progress-fill" style="background:#fb8c00;width:100%"></div></div>
    </div>
    {{-- 5. Realisasi Anggaran --}}
    <div class="kpi-card amber">
      <div class="kpi-label">Realisasi Anggaran</div>
      <div class="kpi-value">Rp {{ number_format($totalRealisasiAng/1e9, 1) }} M</div>
      <div class="kpi-sub">Miliar Rupiah</div>
      <div class="kpi-progress">
        <div class="kpi-progress-fill" style="background:#f9a825;width:{{ min($pctAng,100) }}%"></div>
      </div>
    </div>
    {{-- 6. % Serapan Anggaran --}}
    <div class="kpi-card purple">
      <div class="kpi-label">% Serapan Anggaran</div>
      <div class="kpi-value">{{ $pctAng }}%</div>
      @if($pctAng >= 95)
        <span class="kpi-change up">✓ Optimal</span>
      @elseif($pctAng >= 80)
        <span class="kpi-change neutral">~ Menengah</span>
      @else
        <span class="kpi-change down">⚠ Rendah</span>
      @endif
    </div>
  </div>

  {{-- ── CHART: Rencana vs Realisasi KPM ───────────────────── --}}
  <div class="chart-card">
    <div class="flex items-start justify-between flex-wrap gap-2 mb-3">
      <div>
        <div class="section-title">Analisis Penerima KPM: Rencana vs Realisasi</div>
        <div class="section-subtitle">Perbandingan per Kabupaten/Kota di Jawa Timur ({{ count($chartData) }} Wilayah)</div>
      </div>
      <div style="display:flex;gap:14px;align-items:center">
        <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#9aacca;font-weight:600">
          <div style="width:14px;height:10px;background:#BBDEFB;border-radius:2px"></div> Rencana
        </div>
        <div style="display:flex;align-items:center;gap:5px;font-size:11px;color:#1565C0;font-weight:600">
          <div style="width:14px;height:10px;background:#1565C0;border-radius:2px"></div> Realisasi
        </div>
      </div>
    </div>
    <div class="bar-chart-wrap">
      <div class="bar-chart-inner">
        @php
          $maxKpm = collect($chartData)->max('rencana_kpm');
          $maxKpm = max($maxKpm, 1);
        @endphp
        @foreach($chartData as $kab)
          @php
            $hR  = round(($kab['rencana_kpm']   / $maxKpm) * 150);
            $hRe = round(($kab['realisasi_kpm']  / $maxKpm) * 150);
            $shortName = strlen($kab['nama']) > 8 ? substr($kab['nama'], 0, 8).'.' : $kab['nama'];
          @endphp
          <div class="bar-group">
            <div class="bar-pair">
              <div class="bar bar-rencana"   style="height:{{ $hR }}px"
                   title="{{ $kab['nama'] }}: Rencana {{ number_format($kab['rencana_kpm']) }}"></div>
              <div class="bar bar-realisasi" style="height:{{ $hRe }}px"
                   title="{{ $kab['nama'] }}: Realisasi {{ number_format($kab['realisasi_kpm']) }}"></div>
            </div>
            <div class="bar-label">{{ $shortName }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- ── PERINGKAT & NILAI PER KPM & WAWASAN ────────────────── --}}
  <div class="three-col-grid">

    {{-- Peringkat Serapan Anggaran --}}
    <div class="chart-card" style="margin-bottom:0">
      <div class="section-title">Peringkat Penyerapan Anggaran</div>
      <div class="section-subtitle">% Serapan Anggaran per Wilayah</div>

      <div class="section-divider divider-green">▲ TOP 5 PENYERAPAN TERTINGGI</div>
      @foreach($top5Ang as $item)
        <div class="rank-item">
          <div class="rank-label-row">
            <span class="rank-name">{{ $item['nama'] }}</span>
            <span class="rank-pct" style="color:#2e7d32">{{ $item['pct_ang'] }}%</span>
          </div>
          <div class="rank-bar-wrap">
            <div class="rank-bar-fill" style="width:{{ min($item['pct_ang'],100) }}%;background:linear-gradient(90deg,#43a047,#66bb6a)"></div>
          </div>
        </div>
      @endforeach

      <div class="section-divider divider-red" style="margin-top:14px">▼ BOTTOM 5 PENYERAPAN TERENDAH</div>
      @foreach($bottom5Ang as $item)
        <div class="rank-item">
          <div class="rank-label-row">
            <span class="rank-name">{{ $item['nama'] }}</span>
            <span class="rank-pct" style="color:#c62828">{{ $item['pct_ang'] }}%</span>
          </div>
          <div class="rank-bar-wrap">
            <div class="rank-bar-fill" style="width:{{ min($item['pct_ang'],100) }}%;background:linear-gradient(90deg,#ef5350,#ef9a9a)"></div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Nilai Per KPM --}}
    <div class="chart-card" style="margin-bottom:0">
      <div class="flex items-start justify-between flex-wrap gap-2 mb-1">
        <div>
          <div class="section-title">Nilai Per KPM (Rata-rata Bantuan)</div>
          <div class="section-subtitle">Realisasi Anggaran ÷ Realisasi KPM</div>
        </div>
        <div style="background:#fff3e0;color:#e65100;font-size:10px;font-weight:700;
            border-radius:8px;padding:4px 10px;white-space:nowrap">
          Rata-rata Jatim: Rp {{ number_format($avgNilaiPerKpm) }}
        </div>
      </div>
      @php
        $maxNilai = collect($sampleNilai)->max('nilai_per_kpm');
        $maxNilai = max($maxNilai, 1);
      @endphp
      <div class="nilai-chart">
        @foreach($sampleNilai as $item)
          @php
            $pctBar = round(($item['nilai_per_kpm'] / $maxNilai) * 100);
            $avgPct = round(($avgNilaiPerKpm / $maxNilai) * 100);
            $shortN = strlen($item['nama']) > 14 ? substr($item['nama'],0,14).'.' : $item['nama'];
          @endphp
          <div class="nilai-item">
            <div class="nilai-nama">{{ $shortN }}</div>
            <div class="nilai-bar-wrap">
              <div class="nilai-bar-fill" style="width:{{ $pctBar }}%"></div>
              <div class="nilai-avg-line" style="left:{{ $avgPct }}%"
                   title="Rata-rata: Rp {{ number_format($avgNilaiPerKpm) }}"></div>
            </div>
            <div class="nilai-val">Rp {{ number_format($item['nilai_per_kpm']) }}</div>
          </div>
        @endforeach
      </div>
      <div style="font-size:10px;color:#9aacca;margin-top:10px;display:flex;align-items:center;gap:6px">
        <div style="width:2px;height:12px;background:#ef5350;border-radius:1px"></div>
        Garis merah = rata-rata provinsi
      </div>
    </div>

    {{-- Wawasan Kinerja Utama --}}
    <div class="wawasan-card" style="margin-bottom:0">
      <div class="wawasan-title">Wawasan Kinerja Utama</div>

      {{-- 100% realisasi --}}
      @php
        $perfect100Names = collect($perfect100)->pluck('nama')->take(3)->implode(', ');
      @endphp
      <div class="wawasan-item">
        <div class="wawasan-icon green">✅</div>
        <div>
          <div class="wawasan-item-label">100% Realisasi</div>
          <div class="wawasan-item-val">{{ $perfect100Names ?: '–' }}</div>
        </div>
      </div>

      {{-- Over 100% --}}
      @php
        $over100Names = collect($over100)->map(fn($k) => $k['nama'].' ('.$k['pct_kpm'].'%)')->take(2)->implode(', ');
      @endphp
      <div class="wawasan-item">
        <div class="wawasan-icon blue">📈</div>
        <div>
          <div class="wawasan-item-label">+100% Realisasi</div>
          <div class="wawasan-item-val">{{ $over100Names ?: 'Tidak ada' }}</div>
        </div>
      </div>

      {{-- Realisasi terendah --}}
      @php
        $terendahNames = collect($terendah2)->map(fn($k) => $k['nama'].' ('.$k['pct_kpm'].'%)')->implode(', ');
      @endphp
      <div class="wawasan-item">
        <div class="wawasan-icon orange">⚠️</div>
        <div>
          <div class="wawasan-item-label">Realisasi Terendah</div>
          <div class="wawasan-item-val">{{ $terendahNames ?: '–' }}</div>
        </div>
      </div>

      {{-- Selisih terbesar --}}
      <div class="wawasan-item">
        <div class="wawasan-icon amber">ℹ️</div>
        <div>
          <div class="wawasan-item-label">Selisih Terbesar</div>
          <div class="wawasan-item-val">
            @if($terbesarGap)
              {{ $terbesarGap['nama'] }} (–Rp {{ number_format($terbesarGap['gap_ang']/1e6,1) }}M)
            @else –
            @endif
          </div>
        </div>
      </div>

      <div class="wawasan-footer">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9aacca" stroke-width="2">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        Data diperbarui otomatis dari sistem pusat
      </div>
    </div>

  </div>{{-- /three-col-grid --}}

  {{-- ── KLASIFIKASI PERFORMA ─────────────────────────────────── --}}
  <div class="chart-card">
    <div class="section-title" style="margin-bottom:2px">Klasifikasi Performa Penyaluran (% Realisasi KPM)</div>
    <div class="section-subtitle" style="margin-bottom:16px">Berdasarkan rumus: (Realisasi KPM / Rencana KPM) × 100%</div>

    <div class="klasifikasi-grid">
      {{-- TINGGI --}}
      <div>
        <div class="klasifikasi-header">
          <div class="klasifikasi-dot" style="background:#43a047"></div>
          <span class="klasifikasi-cat green">TINGGI (&gt;95%)</span>
        </div>
        @forelse($tinggi as $item)
          <div class="klas-item green">
            <span class="klas-name">{{ $item['nama'] }}</span>
            <span class="klas-pct green">{{ $item['pct_kpm'] }}%</span>
          </div>
        @empty
          <div style="font-size:12px;color:#9aacca;padding:6px 10px">Tidak ada</div>
        @endforelse
      </div>

      {{-- MENENGAH --}}
      <div>
        <div class="klasifikasi-header">
          <div class="klasifikasi-dot" style="background:#fb8c00"></div>
          <span class="klasifikasi-cat orange">MENENGAH (70–95%)</span>
        </div>
        @forelse($menengah as $item)
          <div class="klas-item orange">
            <span class="klas-name">{{ $item['nama'] }}</span>
            <span class="klas-pct orange">{{ $item['pct_kpm'] }}%</span>
          </div>
        @empty
          <div style="font-size:12px;color:#9aacca;padding:6px 10px">Tidak ada</div>
        @endforelse
      </div>

      {{-- RENDAH --}}
      <div>
        <div class="klasifikasi-header">
          <div class="klasifikasi-dot" style="background:#ef5350"></div>
          <span class="klasifikasi-cat red">RENDAH (&lt;70%)</span>
        </div>
        @forelse($rendah as $item)
          <div class="klas-item red">
            <span class="klas-name">{{ $item['nama'] }}</span>
            <span class="klas-pct red">{{ $item['pct_kpm'] }}%</span>
          </div>
        @empty
          <div style="font-size:12px;color:#9aacca;padding:6px 10px">Tidak ada</div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- ── GAP TABLE ────────────────────────────────────────────── --}}
  <div class="chart-card">
    <div class="flex items-start justify-between flex-wrap gap-3 mb-3">
      <div>
        <div class="section-title">Tabel Interaktif Selisih (Gap) Wilayah</div>
        <div class="section-subtitle">MONITORING DEVIASI ANTARA TARGET RENCANA DAN REALISASI LAPANGAN</div>
      </div>
      <div class="legend-row">
        <div class="legend-item"><div class="legend-dot" style="background:#ef5350"></div> Kritis</div>
        <div class="legend-item"><div class="legend-dot" style="background:#fb8c00"></div> Waspada</div>
        <div class="legend-item"><div class="legend-dot" style="background:#43a047"></div> Stabil</div>
      </div>
    </div>
    <div class="gap-table-wrap">
      <table class="gap-table">
        <thead>
          <tr>
            <th>Kabupaten/Kota</th>
            <th>Gap KPM (R–RE)</th>
            <th>Gap Anggaran (Rp)</th>
            <th>% Realisasi KPM</th>
            <th>% Serapan Anggaran</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach($gapTable as $item)
            @php
              if ($item['pct_kpm'] < 70)       $status = 'kritis';
              elseif ($item['pct_kpm'] < 90)    $status = 'waspada';
              else                               $status = 'stabil';
              $gapAngStr = $item['gap_ang'] > 0
                ? '–Rp '.number_format($item['gap_ang'])
                : 'Rp '.number_format(abs($item['gap_ang']));
            @endphp
            <tr class="{{ $status }}">
              <td style="font-weight:600;color:#1a2236">{{ $item['nama'] }}</td>
              <td style="color:{{ $item['gap_kpm'] > 0 ? '#c62828' : '#2e7d32' }};font-weight:700">
                {{ $item['gap_kpm'] > 0 ? '-'.number_format($item['gap_kpm']) : number_format(abs($item['gap_kpm'])) }}
              </td>
              <td style="color:{{ $item['gap_ang'] > 0 ? '#c62828' : '#2e7d32' }};font-weight:700">
                {{ $item['gap_ang'] > 0 ? '–Rp '.number_format($item['gap_ang']) : 'Rp '.number_format(abs($item['gap_ang'])) }}
              </td>
              <td style="font-weight:700;color:{{ $item['pct_kpm']>=95?'#2e7d32':($item['pct_kpm']>=70?'#e65100':'#c62828') }}">
                {{ $item['pct_kpm'] }}%
              </td>
              <td style="font-weight:700;color:{{ $item['pct_ang']>=95?'#2e7d32':($item['pct_ang']>=70?'#e65100':'#c62828') }}">
                {{ $item['pct_ang'] }}%
              </td>
              <td><span class="badge {{ $status }}">{{ ucfirst($status) }}</span></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
</div>
@endsection

@push('scripts')
<script>
// Animate progress bars & bars on load
document.addEventListener('DOMContentLoaded', () => {
  // Nilai chart fills already set via inline style (CSS handles transition)
  // Trigger reflow for CSS transitions
  document.querySelectorAll('.rank-bar-fill, .kpi-progress-fill, .nilai-bar-fill, .pie-bar-fill').forEach(el => {
    const w = el.style.width;
    el.style.width = '0';
    setTimeout(() => { el.style.width = w; }, 60);
  });
});
</script>
@endpush