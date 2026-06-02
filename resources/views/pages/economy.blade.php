@extends('layouts.app')

@section('content')
<div class="page-inner">
<div class="page-wrapper">

<style>
/* ─── BASE ─────────────────────────────────────────────────── */
.page-inner  { width:100%; height:100%; overflow-y:auto; overflow-x:hidden; background:#F4F6FA; }
.page-wrapper{ max-width:1380px; margin:0 auto; padding:28px 28px 56px; }

/* ─── HEADER ────────────────────────────────────────────────── */
.pg-header { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:28px; }
.pg-breadcrumb { display:flex; align-items:center; gap:8px; margin-bottom:6px; }
.pg-breadcrumb-bar { width:28px; height:3px; background:#1565C0; border-radius:2px; }
.pg-breadcrumb-label { font-size:10.5px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; color:#1565C0; }
.pg-title { font-family:'Plus Jakarta Sans',sans-serif; font-size:28px; font-weight:900; color:#0f1a2e; line-height:1.15; }
.pg-subtitle { font-size:14px; color:#8a97b0; font-weight:400; margin-top:3px; }

/* Year selector */
.year-sel { display:flex; align-items:center; gap:6px; background:#fff; border:1.5px solid #e2e8f0; border-radius:10px; padding:8px 14px; box-shadow:0 1px 4px rgba(0,0,0,.06); }
.year-sel svg { flex-shrink:0; }
.year-sel-label { font-size:10.5px; font-weight:800; letter-spacing:.8px; text-transform:uppercase; color:#94a3b8; }
.year-sel select { border:none; outline:none; background:transparent; font-size:13px; font-weight:700; color:#334155; cursor:pointer; font-family:inherit; }

/* ─── RINGKASAN SECTION LABEL ───────────────────────────────── */
.ringkasan-label {
  display:inline-flex; align-items:center; gap:8px;
  background:#f1f5f9; border-radius:10px; padding:6px 14px;
  font-size:13px; font-weight:700; color:#475569;
  margin-bottom:14px;
}
.ringkasan-label-kpm  { background:rgba(14,116,144,.08); color:#0e7490; }
.ringkasan-label-ang  { background:rgba(146,64,14,.08); color:#92400e; }
.ringkasan-divider { flex:1; height:1px; background:#e2e8f0; margin-left:8px; }

/* icon wrapper supaya SVG inherit warna dan tidak ter-clip */
.rl-icon {
  display:inline-flex; align-items:center; justify-content:center;
  width:20px; height:20px; flex-shrink:0; overflow:visible;
}
.rl-icon svg {
  display:block; overflow:visible;
  width:16px; height:16px;
}

/* ─── KPI ROW ────────────────────────────────────────────────── */
.kpi-row { display:flex; gap:14px; margin-bottom:24px; flex-wrap:wrap; }
.kpi-group {
  flex:1; min-width:320px;
  background:#fff; border:1px solid rgba(203,213,225,.7); border-radius:16px;
  padding:14px; display:flex; flex-direction:column; gap:12px;
}
.kpi-group-cards { display:flex; gap:12px; }

.kpi-card2 {
  flex:1; background:#fff; border-radius:12px; padding:18px 18px 14px;
  border:1px solid rgba(203,213,225,.6);
  box-shadow:0 1px 3px rgba(0,0,0,.06);
  display:flex; flex-direction:column; gap:8px;
  min-width:140px;
}
.kpi2-icon-row { display:flex; align-items:center; gap:8px; }
.kpi2-icon-row svg { flex-shrink:0; }
.kpi2-label { font-size:9.5px; font-weight:800; letter-spacing:.7px; text-transform:uppercase; color:#64748b; line-height:1.3; }
.kpi2-value { font-family:'Plus Jakarta Sans',sans-serif; font-size:24px; font-weight:900; color:#0f172a; line-height:1.1; }
.kpi2-sub   { font-size:11px; font-weight:700; }
.kpi2-sub.blue   { color:#0284c7; }
.kpi2-sub.brown  { color:#92400e; }
.kpi2-sub.red    { color:#b91c1c; }
.kpi2-progress   { height:5px; border-radius:3px; overflow:hidden; margin-top:2px; }
.kpi2-progress-track { height:100%; border-radius:3px; transition:width .7s ease; }

/* KPM card borders */
.kpi-card2.kpm { border-color:rgba(14,116,144,.25); }
/* Anggaran card borders */
.kpi-card2.ang { border-color:rgba(146,64,14,.25); }

/* ─── SECTION HEADER ROW ─────────────────────────────────────── */
.sec-title2 { font-family:'Plus Jakarta Sans',sans-serif; font-size:15px; font-weight:700; color:#0f172a; }

/* ─── PERBANDINGAN CHART ─────────────────────────────────────── */
.perbandingan-row { display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:24px; }
@media(max-width:860px){ .perbandingan-row { grid-template-columns:1fr; } }

.perb-card {
  background:#fff; border:1px solid rgba(203,213,225,.7); border-radius:16px;
  padding:24px 28px 28px; box-shadow:0 1px 3px rgba(0,0,0,.05);
}
.perb-title { font-family:'Plus Jakarta Sans',sans-serif; font-size:14.5px; font-weight:700; color:#0f172a; margin-bottom:20px; }
.perb-subtitle { font-size:11.5px; color:#94a3b8; font-weight:500; margin-bottom:20px; }

.perb-item { margin-bottom:18px; }
.perb-item:last-child { margin-bottom:0; }
.perb-item-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:5px; }
.perb-name   { font-size:13px; font-weight:600; color:#0f172a; }
.perb-values { font-size:11.5px; color:#64748b; font-weight:400; }
.perb-pct-badge { font-size:12px; font-weight:800; }
.perb-pct-badge.blue  { color:#0284c7; }
.perb-pct-badge.brown { color:#92400e; }

/* Double bar: rencana (bg) + realisasi (fill) */
.perb-bar-outer { position:relative; height:10px; background:#e0f2fe; border-radius:6px; overflow:hidden; }
.perb-bar-outer.ang { background:#fef3c7; }
.perb-bar-fill  { height:100%; border-radius:6px; transition:width .7s ease; position:relative; }
.perb-bar-fill.blue  { background:#0284c7; }
.perb-bar-fill.brown { background:#b45309; }
.perb-bar-meta { display:flex; justify-content:space-between; margin-top:3px; }
.perb-bar-meta span { font-size:10px; color:#94a3b8; font-weight:500; }

/* ─── DATA DETAIL TABLE ──────────────────────────────────────── */
.tbl-card {
  background:#fff; border:1px solid rgba(203,213,225,.7); border-radius:16px;
  box-shadow:0 1px 3px rgba(0,0,0,.05); overflow:hidden;
}
.tbl-header {
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;
  gap:10px; padding:18px 22px; border-bottom:1px solid #e2e8f0;
}
.tbl-toolbar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

.tbl-search {
  display:flex; align-items:center; gap:7px;
  background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:9px;
  padding:6px 12px; transition:border-color .2s;
}
.tbl-search:focus-within { border-color:#0284c7; background:#fff; }
.tbl-search input { border:none; outline:none; background:transparent; font-size:12.5px; color:#334155; font-family:inherit; width:180px; }
.tbl-search input::placeholder { color:#94a3b8; }

.rows-sel { display:flex; align-items:center; gap:5px; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:9px; padding:6px 10px; font-size:12px; font-weight:600; color:#64748b; }
.rows-sel select { border:none; outline:none; background:transparent; font-size:12px; font-weight:700; color:#0284c7; cursor:pointer; font-family:inherit; }

/* Table */
.tbl-wrap { overflow-x:auto; }
.dtl-table { width:100%; border-collapse:collapse; font-size:12px; }
.dtl-table thead { background:#f8fafc; border-bottom:1px solid #e2e8f0; }
.dtl-table th {
  font-size:10px; font-weight:800; letter-spacing:.7px; text-transform:uppercase;
  color:#64748b; padding:12px 14px; text-align:left; white-space:nowrap;
  cursor:pointer; user-select:none; transition:color .15s;
}
.dtl-table th:hover { color:#0284c7; }
.dtl-table th .si { display:inline-block; margin-left:3px; opacity:.3; font-size:9px; transition:opacity .15s, transform .15s; }
.dtl-table th.sorted .si { opacity:1; color:#0284c7; }
.dtl-table th.sorted-desc .si { transform:rotate(180deg); }

.dtl-table td { padding:11px 14px; border-bottom:1px solid #f1f5f9; color:#334155; font-weight:500; white-space:nowrap; }
.dtl-table tbody tr:last-child td { border-bottom:none; }
.dtl-table tbody tr:hover td { background:#f8fafc; }

.td-nama-val { font-size:11.5px; font-weight:700; color:#0f172a; }
.td-rank-val { font-size:11px; font-weight:700; color:#94a3b8; text-align:center; }

/* % realisasi bar in cell */
.td-pct-wrap { display:flex; align-items:center; gap:8px; min-width:120px; }
.td-pct-track { flex:1; height:6px; background:#e0f2fe; border-radius:3px; overflow:hidden; min-width:50px; }
.td-pct-fill  { height:100%; border-radius:3px; background:#0284c7; transition:width .6s ease; }
.td-pct-val   { font-size:11px; font-weight:800; color:#0284c7; min-width:36px; text-align:right; }

/* % serapan badge */
.badge-serapan { display:inline-flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; border:1px solid transparent; white-space:nowrap; }
.bs-high   { background:rgba(2,132,199,.1);  border-color:rgba(2,132,199,.2);  color:#0284c7; }
.bs-good   { background:rgba(146,64,14,.1);  border-color:rgba(146,64,14,.2);  color:#92400e; }
.bs-warn   { background:rgba(220,38,38,.1);  border-color:rgba(220,38,38,.2);  color:#dc2626; }
.bs-low    { background:rgba(220,38,38,.15); border-color:rgba(220,38,38,.3);  color:#b91c1c; }

.td-red   { color:#b91c1c !important; font-weight:700 !important; }
.td-green { color:#166534 !important; font-weight:700 !important; }

/* Footer pagination */
.tbl-footer {
  display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;
  gap:8px; padding:14px 22px; background:#f8fafc; border-top:1px solid #e2e8f0;
}
.tbl-footer-info { font-size:12px; color:#64748b; font-weight:500; }
.tbl-footer-info b { color:#334155; }
.pg-btns { display:flex; gap:4px; }
.pg-btn {
  min-width:28px; height:28px; padding:0 7px; border-radius:6px;
  border:1px solid #e2e8f0; background:#fff; font-size:12px; font-weight:700;
  color:#475569; cursor:pointer; display:flex; align-items:center; justify-content:center;
  font-family:inherit; transition:all .15s;
}
.pg-btn:hover:not(:disabled) { border-color:#0284c7; color:#0284c7; background:#e0f2fe; }
.pg-btn.active { background:#0284c7; border-color:#0284c7; color:#fff; }
.pg-btn:disabled { opacity:.35; cursor:not-allowed; }

.tbl-empty { text-align:center; padding:36px 20px; color:#94a3b8; font-size:13px; }
</style>

{{-- ── HEADER ──────────────────────────────────────────────────── --}}
<div class="pg-header">
  <div>
    <div class="pg-breadcrumb">
      <div class="pg-breadcrumb-bar"></div>
      <span class="pg-breadcrumb-label">Dashboard</span>
    </div>
    <h1 class="pg-title">Analisis Penyaluran <span style="color:#0284c7">BANSOS</span> Jawa Timur</h1>
    <p class="pg-subtitle">Analisis Rencana vs Realisasi Kabupaten/Kota</p>
  </div>
  <form method="GET" action="{{ route('economy') }}">
    <div class="year-sel">
      {{-- Icon Kalender --}}
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
        <line x1="16" y1="2" x2="16" y2="6"/>
        <line x1="8" y1="2" x2="8" y2="6"/>
        <line x1="3" y1="10" x2="21" y2="10"/>
      </svg>
      <span class="year-sel-label">Tahun</span>
      <select name="tahun" onchange="this.form.submit()">
        @foreach($validYears as $yr)
          <option value="{{ $yr }}" {{ $yr==$selectedYear?'selected':'' }}>{{ $yr }}</option>
        @endforeach
      </select>
      {{-- Icon Chevron --}}
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </div>
  </form>
</div>

{{-- ── KPI ROW ──────────────────────────────────────────────────── --}}
<div class="kpi-row">

  {{-- KPM Group --}}
  <div class="kpi-group">
    <div style="display:flex;align-items:center;gap:8px">
      <div class="ringkasan-label ringkasan-label-kpm">
        <span class="rl-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#0e7490" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </span>
        Ringkasan KPM
      </div>
      <div class="ringkasan-divider"></div>
    </div>
    <div class="kpi-group-cards">

      {{-- Card: Total Rencana KPM --}}
      <div class="kpi-card2 kpm">
        <div class="kpi2-icon-row">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          <div class="kpi2-label">TOTAL RENCANA<br>KPM</div>
        </div>
        <div class="kpi2-value">{{ number_format($totalRencanaKpm) }}</div>
        <div class="kpi2-sub blue">Jiwa Terdata</div>
      </div>

      {{-- Card: Total Realisasi KPM --}}
      <div class="kpi-card2 kpm">
        <div class="kpi2-icon-row">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <div class="kpi2-label">TOTAL REALISASI<br>KPM</div>
        </div>
        <div class="kpi2-value">{{ number_format($totalRealisasiKpm) }}</div>
        @php $gapKpmProv = $totalRencanaKpm - $totalRealisasiKpm; @endphp
        <div class="kpi2-sub {{ $gapKpmProv>0?'red':'blue' }}">
          Gap : {{ number_format(abs($gapKpmProv)) }}
        </div>
      </div>

      {{-- Card: % Realisasi KPM --}}
      <div class="kpi-card2 kpm">
        <div class="kpi2-icon-row">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <line x1="19" y1="5" x2="5" y2="19"/>
            <circle cx="6.5" cy="6.5" r="2.5"/>
            <circle cx="17.5" cy="17.5" r="2.5"/>
          </svg>
          <div class="kpi2-label">% REALISASI KPM</div>
        </div>
        <div class="kpi2-value">{{ $pctKpm }}%</div>
        <div class="kpi2-progress" style="background:rgba(2,132,199,.1)">
          <div class="kpi2-progress-track" style="width:{{ min($pctKpm,100) }}%;background:#0284c7"></div>
        </div>
      </div>

    </div>
  </div>

  {{-- Anggaran Group --}}
  <div class="kpi-group">
    <div style="display:flex;align-items:center;gap:8px">
      <div class="ringkasan-label ringkasan-label-ang">
        <span class="rl-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
            <line x1="2" y1="10" x2="22" y2="10"/>
          </svg>
        </span>
        Ringkasan Anggaran
      </div>
      <div class="ringkasan-divider"></div>
    </div>
    <div class="kpi-group-cards">

      {{-- Card: Total Rencana Anggaran --}}
      <div class="kpi-card2 ang">
        <div class="kpi2-icon-row">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
            <line x1="2" y1="10" x2="22" y2="10"/>
          </svg>
          <div class="kpi2-label">TOTAL RENCANA<br>ANGGARAN</div>
        </div>
        <div class="kpi2-value">{{ number_format($totalRencanaAng/1e12,2) }}T</div>
        <div class="kpi2-sub brown">Rupiah</div>
      </div>

      {{-- Card: Total Realisasi Anggaran --}}
      <div class="kpi-card2 ang">
        <div class="kpi2-icon-row">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <line x1="12" y1="1" x2="12" y2="23"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
          <div class="kpi2-label">TOTAL REALISASI<br>ANGGARAN</div>
        </div>
        <div class="kpi2-value">{{ number_format($totalRealisasiAng/1e12,2) }}T</div>
        @php $gapAngProv = $totalRencanaAng - $totalRealisasiAng; @endphp
        <div class="kpi2-sub {{ $gapAngProv>0?'red':'brown' }}">
          Gap : {{ number_format(abs($gapAngProv)/1e12,2) }}T
        </div>
      </div>

      {{-- Card: % Serapan Anggaran --}}
      <div class="kpi-card2 ang">
        <div class="kpi2-icon-row">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
          <div class="kpi2-label">% SERAPAN<br>ANGGARAN</div>
        </div>
        <div class="kpi2-value">{{ $pctAng }}%</div>
        <div class="kpi2-progress" style="background:rgba(146,64,14,.1)">
          <div class="kpi2-progress-track" style="width:{{ min($pctAng,100) }}%;background:#92400e"></div>
        </div>
      </div>

    </div>
  </div>

</div>{{-- /kpi-row --}}

{{-- ── PERBANDINGAN CHARTS (Top 5 by % Realisasi) ─────────────── --}}
@php
  // Top 5 KPM by pct_kpm
  $top5Kpm = collect($kabAnalytics)
    ->sortByDesc('pct_kpm')
    ->take(5)
    ->values()
    ->all();

  // Top 5 Anggaran by pct_ang
  $top5Ang2 = collect($kabAnalytics)
    ->sortByDesc('pct_ang')
    ->take(5)
    ->values()
    ->all();
@endphp

<div class="perbandingan-row">
  {{-- KPM Chart --}}
  <div class="perb-card">
    <div class="perb-title">Top 5 Tertinggi berdasarkan % Realisasi KPM</div>
    @foreach($top5Kpm as $rank => $item)
      @php
        $pctReal = $item['pct_kpm'];
        $barWidth = min($pctReal, 100);
      @endphp
      <div class="perb-item">
        <div class="perb-item-header">
          <span class="perb-name">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#e0f2fe;border-radius:50%;font-size:10px;font-weight:800;color:#0284c7;margin-right:6px;">{{ $rank+1 }}</span>
            {{ $item['nama'] }}
          </span>
          <span class="perb-pct-badge blue">{{ $pctReal }}%</span>
        </div>
        <div class="perb-bar-outer">
          <div class="perb-bar-fill blue" style="width:{{ $barWidth }}%"></div>
        </div>
        <div class="perb-bar-meta">
          <span>Realisasi: {{ number_format($item['realisasi_kpm']) }} KPM</span>
          <span>Rencana: {{ number_format($item['rencana_kpm']) }} KPM</span>
        </div>
      </div>
    @endforeach
  </div>

  {{-- Anggaran Chart --}}
  <div class="perb-card">
    <div class="perb-title">Top 5 Tertinggi berdasarkan % Serapan Anggaran</div>
    @foreach($top5Ang2 as $rank => $item)
      @php
        $pctRealA = $item['pct_ang'];
        $barWidthA = min($pctRealA, 100);
      @endphp
      <div class="perb-item">
        <div class="perb-item-header">
          <span class="perb-name">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:#fef3c7;border-radius:50%;font-size:10px;font-weight:800;color:#92400e;margin-right:6px;">{{ $rank+1 }}</span>
            {{ $item['nama'] }}
          </span>
          <span class="perb-pct-badge brown">{{ $pctRealA }}%</span>
        </div>
        <div class="perb-bar-outer ang">
          <div class="perb-bar-fill brown" style="width:{{ $barWidthA }}%"></div>
        </div>
        <div class="perb-bar-meta">
          <span>Realisasi: Rp {{ number_format($item['realisasi_ang']/1e9,1) }}M</span>
          <span>Rencana: Rp {{ number_format($item['rencana_ang']/1e9,1) }}M</span>
        </div>
      </div>
    @endforeach
  </div>
</div>

{{-- ── DATA DETAIL PER WILAYAH TABLE ──────────────────────────── --}}
<div class="tbl-card">

  {{-- Header --}}
  <div class="tbl-header">
    <div style="display:flex;align-items:center;gap:8px">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <line x1="3" y1="9" x2="21" y2="9"/>
        <line x1="3" y1="15" x2="21" y2="15"/>
        <line x1="9" y1="3" x2="9" y2="21"/>
      </svg>
      <span class="sec-title2">Data Detail Per Wilayah</span>
    </div>
    <div class="tbl-toolbar">
      <div class="tbl-search">
        {{-- Icon Search --}}
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" id="tblSearch" placeholder="Cari kabupaten/kota...">
      </div>
      <div class="rows-sel">
        Tampilkan
        <select id="tblRows">
          <option value="6" selected>6</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="999">Semua</option>
        </select>
        baris
      </div>
    </div>
  </div>

  {{-- Table --}}
  <div class="tbl-wrap">
    <table class="dtl-table" id="dtlTable">
      <thead>
        <tr>
          <th style="width:36px;text-align:center">#</th>
          <th data-col="nama" class="sorted">KABUPATEN/KOTA <span class="si">▲</span></th>
          <th data-col="rencana_kpm">RENCANA<br>KPM <span class="si">▲</span></th>
          <th data-col="realisasi_kpm">REALISASI<br>KPM <span class="si">▲</span></th>
          <th data-col="gap_kpm">GAP<br>KPM <span class="si">▲</span></th>
          <th data-col="pct_kpm">% REALISASI <span class="si">▲</span></th>
          <th data-col="rencana_ang">RENCANA<br>ANGGARAN (RP) <span class="si">▲</span></th>
          <th data-col="gap_ang">GAP<br>ANGGARAN (RP) <span class="si">▲</span></th>
          <th data-col="realisasi_ang">REALISASI<br>ANGGARAN (RP) <span class="si">▲</span></th>
          <th data-col="pct_ang" style="text-align:center">% SERAPAN <span class="si">▲</span></th>
        </tr>
      </thead>
      <tbody id="dtlBody">
        @foreach($kabAnalytics as $item)
          @php
            $gapKpm2 = $item['rencana_kpm'] - $item['realisasi_kpm'];
            $gapAng2 = $item['rencana_ang'] - $item['realisasi_ang'];
            $pctBar  = min($item['pct_kpm'], 100);

            if ($item['pct_ang'] >= 95)     { $bsCls = 'bs-high'; }
            elseif ($item['pct_ang'] >= 88) { $bsCls = 'bs-good'; }
            elseif ($item['pct_ang'] >= 80) { $bsCls = 'bs-warn'; }
            else                            { $bsCls = 'bs-low';  }
          @endphp
          <tr class="dtl-row"
              data-nama="{{ strtolower($item['nama']) }}"
              data-rencana_kpm="{{ $item['rencana_kpm'] }}"
              data-realisasi_kpm="{{ $item['realisasi_kpm'] }}"
              data-gap_kpm="{{ $gapKpm2 }}"
              data-pct_kpm="{{ $item['pct_kpm'] }}"
              data-rencana_ang="{{ $item['rencana_ang'] }}"
              data-gap_ang="{{ $gapAng2 }}"
              data-realisasi_ang="{{ $item['realisasi_ang'] }}"
              data-pct_ang="{{ $item['pct_ang'] }}">
            <td class="td-rank-val">–</td>
            <td class="td-nama-val">{{ $item['nama'] }}</td>
            <td>{{ number_format($item['rencana_kpm']) }}</td>
            <td>{{ number_format($item['realisasi_kpm']) }}</td>
            <td class="{{ $gapKpm2 > 0 ? 'td-red' : 'td-green' }}">
              {{ number_format(abs($gapKpm2)) }}
            </td>
            <td>
              <div class="td-pct-wrap">
                <div class="td-pct-track">
                  <div class="td-pct-fill" style="width:{{ $pctBar }}%"></div>
                </div>
                <span class="td-pct-val">{{ $item['pct_kpm'] }}%</span>
              </div>
            </td>
            <td>{{ number_format($item['rencana_ang']) }}</td>
            <td class="{{ $gapAng2 > 0 ? 'td-red' : 'td-green' }}">
              {{ number_format(abs($gapAng2)) }}
            </td>
            <td>{{ number_format($item['realisasi_ang']) }}</td>
            <td style="text-align:center">
              <span class="badge-serapan {{ $bsCls }}">{{ $item['pct_ang'] }}%</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="tbl-empty" id="tblEmpty" style="display:none">Tidak ada wilayah yang cocok.</div>
  </div>

  {{-- Footer --}}
  <div class="tbl-footer">
    <div class="tbl-footer-info" id="tblInfo"></div>
    <div class="pg-btns" id="tblPg"></div>
  </div>

</div>{{-- /tbl-card --}}

</div>{{-- /page-wrapper --}}
</div>{{-- /page-inner --}}

<script>
(function(){
  const allRows = Array.from(document.querySelectorAll('#dtlBody .dtl-row'));
  const tbody   = document.getElementById('dtlBody');
  const search  = document.getElementById('tblSearch');
  const rowsSel = document.getElementById('tblRows');
  const emptyEl = document.getElementById('tblEmpty');
  const infoEl  = document.getElementById('tblInfo');
  const pgEl    = document.getElementById('tblPg');

  // Default: urut abjad A-Z
  let page = 1, sortCol = 'nama', sortAsc = true, q = '', perPage = 6;

  const getVal = (r, c) => c === 'nama' ? (r.dataset[c]||'') : (parseFloat(r.dataset[c])||0);

  const getFiltered = () => q ? allRows.filter(r => r.dataset.nama.includes(q)) : allRows;

  const getSorted = rows => [...rows].sort((a,b)=>{
    const va = getVal(a,sortCol), vb = getVal(b,sortCol);
    return typeof va==='string'
      ? (sortAsc ? va.localeCompare(vb) : vb.localeCompare(va))
      : (sortAsc ? va-vb : vb-va);
  });

  function render(){
    const rows  = getSorted(getFiltered());
    const total = rows.length;
    const pp    = perPage >= 999 ? total : perPage;
    const pages = Math.max(1, Math.ceil(total/pp));
    if(page > pages) page = pages;
    const s = (page-1)*pp, e = Math.min(s+pp, total);

    allRows.forEach(r => { r.style.display='none'; r.cells[0].textContent='–'; });
    emptyEl.style.display = total===0 ? 'block' : 'none';

    rows.slice(s,e).forEach((r,i)=>{
      r.style.display=''; r.cells[0].textContent = s+i+1; tbody.appendChild(r);
    });

    infoEl.innerHTML = total===0 ? 'Tidak ada hasil'
      : `Menampilkan <b>${s+1}&ndash;${e}</b> dari <b>${total}</b> Kabupaten/Kota di Jawa Timur`;

    pgEl.innerHTML = '';
    const mk = (lbl, p, dis, act) => {
      const b = document.createElement('button');
      b.className = 'pg-btn'+(act?' active':'');
      b.disabled = dis; b.innerHTML = lbl;
      b.onclick = ()=>{ page=p; render(); };
      return b;
    };
    pgEl.appendChild(mk('&#8249;', page-1, page<=1, false));
    const rng = [];
    for(let p=1;p<=pages;p++){
      if(p===1||p===pages||Math.abs(p-page)<=1) rng.push(p);
      else if(rng[rng.length-1]!=='…') rng.push('…');
    }
    rng.forEach(p => {
      if(p==='…'){const b=document.createElement('button');b.className='pg-btn';b.disabled=true;b.textContent='…';pgEl.appendChild(b);}
      else pgEl.appendChild(mk(p,p,false,p===page));
    });
    pgEl.appendChild(mk('&#8250;', page+1, page>=pages, false));
  }

  // Klik header untuk sort — set active class
  document.querySelectorAll('#dtlTable thead th[data-col]').forEach(th=>{
    th.onclick = ()=>{
      const c = th.dataset.col;
      if(sortCol===c) sortAsc=!sortAsc; else { sortCol=c; sortAsc= c==='nama'; }
      page=1;
      document.querySelectorAll('#dtlTable thead th').forEach(t=>t.classList.remove('sorted','sorted-desc'));
      th.classList.add('sorted');
      if(!sortAsc) th.classList.add('sorted-desc');
      render();
    };
  });

  search.oninput = ()=>{ q=search.value.toLowerCase().trim(); page=1; render(); };
  rowsSel.onchange = ()=>{ perPage=parseInt(rowsSel.value); page=1; render(); };

  // Animate progress bars on load
  document.querySelectorAll('.td-pct-fill,.kpi2-progress-track,.perb-bar-fill').forEach(el=>{
    const w=el.style.width; el.style.width='0';
    setTimeout(()=>{ el.style.width=w; }, 80);
  });

  // Set initial sorted indicator on "KABUPATEN/KOTA" column
  const namaHeader = document.querySelector('#dtlTable thead th[data-col="nama"]');
  if(namaHeader){ namaHeader.classList.add('sorted'); }

  render();
})();
</script>
@endsection