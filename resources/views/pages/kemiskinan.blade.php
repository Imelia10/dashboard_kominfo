@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ═══ BASE ═══════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }
body, html { font-family: 'Plus Jakarta Sans', sans-serif; background: #F0F2F5; color: #1A202C; }
.mono { font-family: 'JetBrains Mono', monospace; }

/* ═══ TOPBAR ══════════════════════════════════════════════════════ */
.topbar {
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
  padding: 14px 40px; background: #fff; border-bottom: 1px solid #E2E8F0;
  position: sticky; top: 0; z-index: 100;
  box-shadow: 0 1px 8px rgba(0,0,0,.07);
}
.breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 11.5px; color: #9CA3AF; margin-bottom: 3px; }
.breadcrumb a { color: #DC2626; font-weight: 700; text-decoration: none; }
.breadcrumb a:hover { text-decoration: underline; }
.topbar-title { font-size: 15px; font-weight: 800; color: #111827; }
.topbar-sub   { font-size: 11px; color: #9CA3AF; margin-top: 1px; }
.topbar-tag   { font-size: 11px; font-weight: 700; padding: 5px 14px; border-radius: 99px; }
.topbar-tag.red  { background: #FEE2E2; color: #991B1B; }
.topbar-tag.blue { background: #DBEAFE; color: #1E40AF; }

/* ═══ SECTION HEADERS ════════════════════════════════════════════ */
.sec-hdr { display: flex; align-items: center; gap: 14px; margin: 40px 0 16px; }
.sec-pill {
  font-size: 10px; font-weight: 800; letter-spacing: .9px; text-transform: uppercase;
  padding: 5px 14px; border-radius: 99px; white-space: nowrap; flex-shrink: 0;
}
.sec-pill.red   { background: #FEE2E2; color: #991B1B; }
.sec-pill.blue  { background: #DBEAFE; color: #1E40AF; }
.sec-pill.amber { background: #FEF3C7; color: #92400E; }
.sec-pill.green { background: #DCFCE7; color: #166534; }
.sec-line { flex: 1; height: 1px; background: #E2E8F0; }
.sec-q    { font-size: 13px; font-weight: 700; color: #374151; white-space: nowrap; flex-shrink: 0; }

/* ═══ ALUR BAR ═══════════════════════════════════════════════════ */
.alur {
  display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
  background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;
  padding: 9px 16px; margin-bottom: 18px; font-size: 11px; color: #6B7280;
}
.alur-lbl { font-size: 9.5px; font-weight: 800; text-transform: uppercase; letter-spacing: .6px; color: #9CA3AF; margin-right: 4px; }
.alur-step { font-weight: 700; color: #374151; }
.alur-arr  { color: #D1D5DB; font-size: 14px; }

/* ═══ KPI CARDS ══════════════════════════════════════════════════ */
.kpi-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
@media (max-width:1100px) { .kpi-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width:640px)  { .kpi-grid { grid-template-columns: 1fr; } }

.kpi {
  background: #fff; border-radius: 18px; padding: 22px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 4px rgba(0,0,0,.05), 0 4px 20px rgba(0,0,0,.04);
  position: relative; overflow: hidden;
  transition: box-shadow .2s, transform .18s;
}
.kpi:hover { box-shadow: 0 8px 36px rgba(0,0,0,.11); transform: translateY(-2px); }
.kpi::after {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 99px 99px 0 0;
}
.kpi.red::after   { background: linear-gradient(90deg,#DC2626,#F87171); }
.kpi.green::after { background: linear-gradient(90deg,#16A34A,#4ADE80); }
.kpi.amber::after { background: linear-gradient(90deg,#D97706,#FCD34D); }
.kpi.blue::after  { background: linear-gradient(90deg,#2563EB,#60A5FA); }

.kpi-top    { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
.kpi-ico    { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.kpi-ico.red   { background: #FEE2E2; }
.kpi-ico.green { background: #DCFCE7; }
.kpi-ico.amber { background: #FEF3C7; }
.kpi-ico.blue  { background: #DBEAFE; }
.kpi-bdg    { font-size: 9px; font-weight: 800; letter-spacing: .5px; text-transform: uppercase; padding: 3px 8px; border-radius: 99px; }
.kpi-bdg.red   { background:#FEE2E2; color:#991B1B; }
.kpi-bdg.green { background:#DCFCE7; color:#166534; }
.kpi-bdg.amber { background:#FEF3C7; color:#92400E; }
.kpi-bdg.blue  { background:#DBEAFE; color:#1E40AF; }

.kpi-lbl  { font-size: 10px; font-weight: 700; color: #9CA3AF; letter-spacing: .5px; text-transform: uppercase; margin-bottom: 6px; }
.kpi-val  { font-size: 38px; font-weight: 800; color: #111827; line-height: 1; font-family: 'JetBrains Mono', monospace; margin-bottom: 4px; }
.kpi-val.md { font-size: 24px; margin-bottom: 6px; }
.kpi-sub  { font-size: 12px; color: #6B7280; }
.kpi-rule { margin-top: 12px; display: inline-block; font-size: 10px; font-weight: 700; font-family: 'JetBrains Mono', monospace; padding: 4px 10px; border-radius: 8px; }
.kpi-rule.red   { background:#FEE2E2; color:#991B1B; }
.kpi-rule.green { background:#DCFCE7; color:#166534; }
.kpi-rule.amber { background:#FEF3C7; color:#92400E; }
.kpi-rule.blue  { background:#DBEAFE; color:#1E40AF; }
.kpi-arrow { font-size: 10.5px; color: #9CA3AF; margin-top: 6px; display: block; }

/* ═══ CHART CARDS ════════════════════════════════════════════════ */
.card {
  background: #fff; border-radius: 18px; padding: 24px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 4px rgba(0,0,0,.05), 0 4px 20px rgba(0,0,0,.04);
}
.card-hdr   { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 6px; }
.card-title { font-size: 13.5px; font-weight: 800; color: #111827; margin-bottom: 5px; }
.card-desc  { font-size: 11.5px; color: #6B7280; line-height: 1.75; }
.card-num   { font-size: 10px; font-weight: 800; letter-spacing: .5px; color: #9CA3AF; text-transform: uppercase; margin-bottom: 4px; }
.card-tag   {
  flex-shrink: 0; display: inline-block; font-size: 9.5px; font-weight: 800;
  font-family: 'JetBrains Mono', monospace; padding: 4px 10px; border-radius: 8px; white-space: nowrap;
}
.card-tag.red   { background:#FEE2E2; color:#991B1B; }
.card-tag.blue  { background:#DBEAFE; color:#1E40AF; }
.card-tag.amber { background:#FEF3C7; color:#92400E; }
.card-tag.green { background:#DCFCE7; color:#166534; }
.card-connector {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 10px; font-weight: 700; color: #9CA3AF; margin-top: 8px;
}
.card-connector::before { content: '→'; color: #D1D5DB; font-size: 12px; }

.formula-chip {
  display: inline-block; font-size: 10.5px; font-weight: 600;
  font-family: 'JetBrains Mono', monospace; padding: 5px 12px; border-radius: 8px;
  background: #F8FAFC; border: 1px solid #E2E8F0; color: #374151; margin-bottom: 14px;
}

.lgd  { display: flex; align-items: center; gap: 6px; font-size: 11px; font-weight: 600; color: #374151; }
.ldot { width: 10px; height: 10px; border-radius: 3px; display: inline-block; flex-shrink: 0; }

/* Chart heights */
.h-sm { height: 200px; position: relative; }
.h-md { height: 270px; position: relative; }
.h-lg { height: 340px; position: relative; }
.h-map{
  height: 480px; border-radius: 16px; overflow: hidden; position: relative;
  box-shadow: inset 0 0 0 1px rgba(0,0,0,.06);
}

/* ═══ TOGGLE TABS ════════════════════════════════════════════════ */
.tab-btn {
  padding: 7px 16px; font-size: 11.5px; font-weight: 700; border-radius: 10px;
  cursor: pointer; border: 1.5px solid #E2E8F0; background: #F8FAFC; color: #9CA3AF;
  transition: all .14s;
}
.tab-btn.ar { background:#FEE2E2; color:#991B1B; border-color:#FECACA; }
.tab-btn.ag { background:#DCFCE7; color:#166534; border-color:#BBF7D0; }

/* ═══ DRILLDOWN ══════════════════════════════════════════════════ */
.drill { margin-top: 14px; border-radius: 14px; padding: 14px 18px; border: 1.5px solid; display: none; }
.drill.red  { background:#FFF5F5; border-color:#FECACA; }
.drill.blue { background:#EFF6FF; border-color:#BFDBFE; }
.drill.show { display: block; }
.drill-lbl  { font-size: 9.5px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; color: #9CA3AF; margin-bottom: 4px; }
.drill-val  { font-size: 18px; font-weight: 800; font-family: 'JetBrains Mono', monospace; }
.drill-val.red  { color: #DC2626; }
.drill-val.blue { color: #2563EB; }
.drill-val.dark { color: #111827; }

/* ═══ RANKING TABLE ══════════════════════════════════════════════ */
.rk-bdg { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; flex-shrink: 0; }
.r1 { background:#FEF3C7; color:#92400E; }
.r2 { background:#F1F5F9; color:#334155; }
.r3 { background:#FEE2E2; color:#B91C1C; }
.rn { background:#F8FAFC; color:#9CA3AF; }
.arr-up   { color:#DC2626; font-weight:800; }
.arr-dn   { color:#16A34A; font-weight:800; }
.arr-same { color:#9CA3AF; }

/* ═══ SINTESIS ═══════════════════════════════════════════════════ */
.syn-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
@media (max-width:1100px) { .syn-grid { grid-template-columns: 1fr; } }

.syn {
  background: #fff; border-radius: 18px; padding: 26px;
  border: 1px solid #E2E8F0;
  box-shadow: 0 1px 4px rgba(0,0,0,.05), 0 4px 20px rgba(0,0,0,.04);
  display: flex; flex-direction: column; position: relative; overflow: hidden;
}
.syn::before { content:''; position:absolute; top:0;left:0;right:0;height:4px; border-radius:4px 4px 0 0; }
.syn.red::before   { background: linear-gradient(90deg,#DC2626,#F87171); }
.syn.blue::before  { background: linear-gradient(90deg,#2563EB,#60A5FA); }
.syn.green::before { background: linear-gradient(90deg,#16A34A,#4ADE80); }
.syn-tag  { font-size: 9.5px; font-weight: 800; letter-spacing: .7px; text-transform: uppercase; margin-bottom: 10px; }
.syn-tag.red   { color:#991B1B; }
.syn-tag.blue  { color:#1E40AF; }
.syn-tag.green { color:#166534; }
.syn-title { font-size: 14.5px; font-weight: 800; color: #111827; line-height: 1.45; margin-bottom: 10px; }
.syn-body  { font-size: 12.5px; color: #4B5563; line-height: 1.9; flex: 1; }
.syn-stat  { margin-top: 16px; border-radius: 12px; padding: 12px 14px; }
.syn-stat.red   { background:#FEF2F2; }
.syn-stat.blue  { background:#EFF6FF; }
.syn-stat.green { background:#F0FDF4; }
.syn-stat-lbl  { font-size: 9px; font-weight: 800; letter-spacing: .6px; text-transform: uppercase; opacity: .6; margin-bottom: 4px; }
.syn-stat-val  { font-size: 18px; font-weight: 800; color: #111827; font-family: 'JetBrains Mono', monospace; margin-bottom: 2px; }
.syn-stat-note { font-size: 10.5px; font-weight: 700; }
.syn-stat.red   .syn-stat-note { color:#991B1B; }
.syn-stat.blue  .syn-stat-note { color:#1E40AF; }
.syn-stat.green .syn-stat-note { color:#166534; }

/* ═══ DATA TABLE ═════════════════════════════════════════════════ */
.dtbl { width: 100%; border-collapse: collapse; font-size: 12px; }
.dtbl thead tr { background: #F8FAFC; }
.dtbl thead th { padding: 11px 14px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #9CA3AF; text-align: right; }
.dtbl thead th:nth-child(1), .dtbl thead th:nth-child(2) { text-align: left; }
.dtbl thead th:nth-child(6) { text-align: center; }
.dtbl tbody tr { border-bottom: 1px solid #F1F5F9; transition: background .12s; }
.dtbl tbody tr:hover { background: #FEF2F2; }
.dtbl td { padding: 11px 14px; }
.td-no   { color: #9CA3AF; font-family: 'JetBrains Mono', monospace; font-size: 11px; }
.td-nm   { font-weight: 700; color: #111827; }
.td-v25  { text-align: right; font-weight: 800; color: #DC2626; font-family: 'JetBrains Mono', monospace; }
.td-v23  { text-align: right; color: #9CA3AF; font-family: 'JetBrains Mono', monospace; }
.td-dff  { text-align: right; font-weight: 700; font-family: 'JetBrains Mono', monospace; }
.td-dff.up { color: #DC2626; }
.td-dff.dn { color: #16A34A; }
.td-sts  { text-align: center; }
.sts-bdg { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: 10px; font-weight: 700; }
.sts-bdg.atas  { background:#FEE2E2; color:#991B1B; }
.sts-bdg.bawah { background:#DCFCE7; color:#166534; }
.td-bar  { text-align: right; }
.bar-wr  { display: flex; align-items: center; justify-content: flex-end; gap: 6px; }
.bar-tr  { width: 52px; height: 5px; background: #F1F5F9; border-radius: 99px; overflow: hidden; flex-shrink: 0; }
.bar-fl  { height: 100%; border-radius: 99px; }
.bar-r   { background: #DC2626; }
.bar-g   { background: #16A34A; }
.td-pct  { font-family: 'JetBrains Mono', monospace; font-size: 10.5px; color: #9CA3AF; min-width: 30px; text-align: right; }

/* ═══ SEARCH ══════════════════════════════════════════════════════ */
.srch-wrap { position: relative; }
.srch-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; }
.srch-inp {
  padding: 8px 12px 8px 34px; border: 1.5px solid #E2E8F0; border-radius: 12px;
  font-size: 12.5px; font-family: 'Plus Jakarta Sans', sans-serif; color: #374151;
  width: 220px; outline: none; transition: border .15s, box-shadow .15s;
}
.srch-inp:focus { border-color: #DC2626; box-shadow: 0 0 0 3px rgba(220,38,38,.1); }

/* ═══ LEAFLET OVERRIDES ═══════════════════════════════════════════ */
.leaflet-popup-content-wrapper {
  border-radius: 14px !important; padding: 0 !important; border: none !important; overflow: hidden;
  box-shadow: 0 12px 48px rgba(0,0,0,.22) !important;
  font-family: 'Plus Jakarta Sans', sans-serif !important;
}
.leaflet-popup-tip-container { display: none !important; }
.leaflet-popup-content { margin: 0 !important; }
.leaflet-popup-close-button {
  top:14px!important; right:14px!important; color:rgba(255,255,255,.9)!important;
  font-size:20px!important; z-index:10; font-weight:300!important; line-height:1!important;
}
.leaflet-tooltip {
  background: #111827 !important; border: none !important; color: #F9FAFB !important;
  font-family: 'Plus Jakarta Sans', sans-serif !important; font-size: 11.5px !important; font-weight: 700 !important;
  border-radius: 8px !important; padding: 5px 10px !important; box-shadow: 0 4px 16px rgba(0,0,0,.25) !important;
}
.leaflet-tooltip::before { display:none!important; }
.leaflet-control-zoom {
  border: none !important;
  box-shadow: 0 4px 20px rgba(0,0,0,.15) !important;
  border-radius: 12px !important;
  overflow: hidden;
}
.leaflet-control-zoom-in,
.leaflet-control-zoom-out {
  font-family: 'Plus Jakarta Sans', sans-serif !important;
  font-weight: 800 !important; font-size: 16px !important;
  color: #374151 !important; border: none !important;
  width: 32px !important; height: 32px !important; line-height: 32px !important;
}
.leaflet-control-zoom-in:hover, .leaflet-control-zoom-out:hover { background: #F8FAFC !important; }

/* ═══ GRIDS ══════════════════════════════════════════════════════ */
.g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.g3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
@media (max-width: 920px) { .g2,.g3 { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div style="background:#F0F2F5;min-height:100vh;padding-bottom:80px;">

  {{-- ═══ TOPBAR ═══ --}}
  <div class="topbar">
    <div>
      <div class="breadcrumb">
        <a href="{{ route('home') }}">Dashboard</a>
        <span>›</span>
        <span>Kemiskinan &amp; Ketenagakerjaan</span>
      </div>
      <div class="topbar-title">Analisis Kemiskinan &amp; Ketenagakerjaan — Jawa Timur</div>
      <div class="topbar-sub">
  38 kabupaten/kota · Tahun
  {{ $selectedYear == 'semua' ? '2023 & 2025' : $selectedYear }}
  · Sumber: BPS Jawa Timur (T1, T3, T4)
</div>
    </div>
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">

  <span class="topbar-tag red">
    {{ $selectedYear == 'semua' ? '2023 & 2025' : $selectedYear }}
  </span>

  <span class="topbar-tag blue">38 Kab/Kota</span>

  <form method="GET" action="{{ url()->current() }}">
    <select
      name="tahun"
      onchange="this.form.submit()"
      style="
        padding:8px 14px;
        border-radius:12px;
        border:1.5px solid #E2E8F0;
        font-size:12px;
        font-weight:700;
        outline:none;
        background:white;
        color:#374151;
      "
    >
      <option value="2025" {{ $selectedYear == '2025' ? 'selected' : '' }}>
        2025
      </option>

      <option value="2023" {{ $selectedYear == '2023' ? 'selected' : '' }}>
        2023
      </option>

      <option value="semua" {{ $selectedYear == 'semua' ? 'selected' : '' }}>
        Semua Data
      </option>
    </select>
  </form>

</div>
  </div>

  <div style="padding:0 40px;">

  {{-- ═══════════════════════════════════════════════════════════════
       BAGIAN 1 — 4 KPI CARDS
       Pertanyaan: Seberapa parah kemiskinan Jatim secara keseluruhan?
  ══════════════════════════════════════════════════════════════════ --}}
  <div class="sec-hdr" style="margin-top:28px;">
    <span class="sec-pill red">Bagian 1 — 4 KPI Cards</span>
    <div class="sec-line"></div>
    <span class="sec-q">Seberapa parah kemiskinan Jatim secara keseluruhan?</span>
  </div>

  <div class="alur">
    <span class="alur-lbl">Alur baca</span>
    <span class="alur-step">KPI 1: rata-rata Jatim (angka patokan)</span>
    <span class="alur-arr">→</span>
    <span class="alur-step">KPI 2 &amp; 3: siapa di atas/bawah</span>
    <span class="alur-arr">→</span>
    <span class="alur-step">KPI 4: siapa tertinggi</span>
    <span class="alur-arr">→</span>
    <span style="color:#9CA3AF;font-size:10.5px;">detail di Bagian 2</span>
  </div>

  <div class="kpi-grid">

    {{-- KPI 1 --}}
    <div class="kpi red">
      <div class="kpi-top">
        <div class="kpi-ico red">📊</div>
        <span class="kpi-bdg red">KPI 1 </span>
      </div>
      <div class="kpi-lbl">Rata-rata kemiskinan Jatim</div>
      <div class="kpi-val">{{ number_format($rataRata2025/1000,2,',','.') }}</div>
      <div class="kpi-sub">ribu jiwa &nbsp;·&nbsp; mean 38 kab/kota</div>
      <div class="kpi-rule red">Rumus: Σ nilai ÷ 38</div>
      <span class="kpi-arrow">→ Angka patokan: jadi garis referensi di Chart 5, 6, 7</span>
    </div>

    {{-- KPI 2 --}}
    <div class="kpi green">
      <div class="kpi-top">
        <div class="kpi-ico green">✅</div>
        <span class="kpi-bdg green">KPI 2 </span>
      </div>
      <div class="kpi-lbl">Kab/kota di bawah rata-rata</div>
      <div class="kpi-val">{{ $diBawahRataRata }}</div>
      <div class="kpi-sub">dari {{ $totalKab }} kab/kota &nbsp;·&nbsp; {{ round($diBawahRataRata/$totalKab*100,1) }}% &mdash; kondisi lebih baik</div>
      <div class="kpi-rule green">hitung baris &lt; rata-rata</div>
      <span class="kpi-arrow">→ Divisualisasi di Chart 5 (hijau)</span>
    </div>

    {{-- KPI 3 --}}
    <div class="kpi amber">
      <div class="kpi-top">
        <div class="kpi-ico amber">⚠️</div>
        <span class="kpi-bdg amber">KPI 3 </span>
      </div>
      <div class="kpi-lbl">Kab/kota di atas rata-rata</div>
      <div class="kpi-val">{{ $diAtasRataRata }}</div>
      <div class="kpi-sub">dari {{ $totalKab }} kab/kota &nbsp;·&nbsp; {{ round($diAtasRataRata/$totalKab*100,1) }}% &mdash; perlu perhatian</div>
      <div class="kpi-rule amber">hitung baris &gt; rata-rata</div>
      <span class="kpi-arrow">→ Divisualisasi di Chart 5 (merah)</span>
    </div>

    {{-- KPI 4 --}}
    <div class="kpi blue">
      <div class="kpi-top">
        <div class="kpi-ico blue">🏆</div>
        <span class="kpi-bdg blue">KPI 4 </span>
      </div>
      <div class="kpi-lbl">Kemiskinan tertinggi Jatim</div>
      <div class="kpi-val md">{{ $tertinggi ? preg_replace('/^(Kabupaten|Kota)\s+/i','', $tertinggi->kabupatenKota) : '-' }}</div>
      <div class="kpi-sub mono">{{ $tertinggi ? number_format($tertinggi->jumlahPnddk/1000,2,',','.') : '-' }} ribu jiwa &mdash; MAX(nilai)</div>
      <div class="kpi-rule blue">MAX(nilai) dari 38</div>
      <span class="kpi-arrow">→ Nama + nilai tertinggi · preview Chart 6 (top 5)</span>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════════════
       BAGIAN 2 — 3 CHART WILAYAH + 1 PETA
       Pertanyaan: Wilayah mana yang paling terdampak & seberapa jauh dari rata-rata?
  ══════════════════════════════════════════════════════════════════ --}}
  <div class="sec-hdr">
    <span class="sec-pill blue">Bagian 2 — 3 Chart Wilayah + 1 Peta Interaktif</span>
    <div class="sec-line"></div>
    <span class="sec-q">Wilayah mana paling terdampak &amp; seberapa jauh dari rata-rata?</span>
  </div>

  {{-- <div class="alur">
    <span class="alur-lbl">Alur baca</span>
    <span class="alur-step">Chart 5: gambaran semua 38</span>
    <span class="alur-arr">→</span>
    <span class="alur-step">Chart 6: yang ekstrem</span>
    <span class="alur-arr">→</span>
    <span class="alur-step">Chart 7: seberapa jauh dari rata-rata</span>
    <span class="alur-arr">→</span>
    <span class="alur-step">Peta: pola geografisnya</span>
  </div> --}}

  {{-- ROW 1: Chart 5 + Chart 6 --}}
  <div class="g2" style="margin-bottom:16px;">

    {{-- CHART 5 --}}
    <div class="card">
      <div class="card-hdr">
        <div>
          <div class="card-num">Chart 5 </div>
          <div class="card-title">38 Kab/Kota vs Rata-rata Jatim</div>
          <div class="card-desc">
           Bar chart: merah = di atas rata-rata ({{ $diAtasRataRata }} kab),
            hijau = di bawah ({{ $diBawahRataRata }} kab). Garis oranye putus-putus = rata-rata Jatim
            <strong style="color:#111827;">({{ number_format($rataRata2025/1000,2,',','.') }} ribu jiwa)</strong>.
            Klik bar untuk detail kab/kota.
          </div>
        </div>
        <span class="card-tag red">nilai vs rata-rata</span>
      </div>
      <div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:14px;">
        <div class="lgd"><span class="ldot" style="background:#DC2626;"></span>Di atas rata-rata ({{ $diAtasRataRata }})</div>
        <div class="lgd"><span class="ldot" style="background:#16A34A;"></span>Di bawah rata-rata ({{ $diBawahRataRata }})</div>
        <div class="lgd"><span style="display:inline-block;width:18px;border-top:2px dashed #D97706;margin-top:5px;"></span>Rata-rata Jatim</div>
      </div>
      <div class="h-lg"><canvas id="cSemuaKab"></canvas></div>
    </div>

    {{-- CHART 6 --}}
    <div class="card">
      <div class="card-hdr">
        <div>
          <div class="card-num">Chart 6 </div>
          <div class="card-title">Top 5 Tertinggi &amp; Terendah</div>
          <div class="card-desc">
            Zoom in dari Chart 5 — fokus ke yang ekstrem. Bar horizontal 2 panel:
            <strong style="color:#DC2626;">5 merah = terparah</strong>, <strong style="color:#16A34A;">5 hijau = terbaik</strong>.
            Memperdalam detail KPI 4 — siapa saja yang ada di ujung distribusi?
          </div>
          <span class="card-connector">ranking DESC → top &amp; bottom 5 · detail KPI 4</span>
        </div>
        <span class="card-tag blue">ranking DESC</span>
      </div>
      <div style="display:flex;gap:8px;margin-bottom:16px;">
        <button class="tab-btn ar" id="btnT" onclick="showTop('tertinggi')">▲ 5 Tertinggi (terparah)</button>
        <button class="tab-btn"    id="btnR" onclick="showTop('terendah')">▼ 5 Terendah (terbaik)</button>
      </div>
      <div class="h-md"><canvas id="cTop5"></canvas></div>
    </div>
  </div>

  {{-- ROW 2: Chart 7 + Tabel ranking --}}
  <div class="g2" style="margin-bottom:16px;">

    {{-- CHART 7 --}}
    <div class="card">
      <div class="card-hdr">
        <div>
          <div class="card-num">Chart 7 </div>
          <div class="card-title">Deviasi dari Rata-rata Jatim</div>
          <div class="card-desc">
            Memperdalam Chart 5 &amp; 6: <em>seberapa jauh</em> tiap kab/kota dari rata-rata?
            Bar diverging — ke kanan <strong style="color:#DC2626;">merah = lebih parah</strong>,
            ke kiri <strong style="color:#16A34A;">hijau = lebih baik</strong>.
            Ini yang tidak terlihat dari Chart 5 — besarnya gap antar wilayah.
          </div>
          <span class="card-connector">nilai − rata-rata Jatim · SEBERAPA JAUH dari rata-rata</span>
        </div>
        <span class="card-tag amber">nilai − rata-rata</span>
      </div>
      <div class="formula-chip">Rumus: nilai kab/kota − {{ number_format($rataRata2025,0,',','.') }} jiwa &nbsp;·&nbsp; + = lebih parah, − = lebih baik</div>
      <div class="h-lg"><canvas id="cDeviasi"></canvas></div>
    </div>

    {{-- TABEL RANKING --}}
    <div class="card">
      <div class="card-title" style="margin-bottom:5px;">Perubahan Ranking Top 8 — 2023 vs 2025</div>
      <div class="card-desc" style="margin-bottom:14px;">
        Apakah posisi ranking bergeser dari 2023 ke 2025?
        <span style="color:#DC2626;font-weight:700;">↑ naik ranking</span> = makin parah.
        <span style="color:#16A34A;font-weight:700;">↓ turun ranking</span> = membaik relatif terhadap wilayah lain.
      </div>
      <div style="overflow-x:auto;">
        <table class="dtbl">
          <thead>
            <tr>
              <th style="text-align:center;">Rank '25</th>
              <th style="text-align:left;">Kab/Kota</th>
              <th>2025 (rb)</th>
              <th>2023 (rb)</th>
              <th style="text-align:center;">Δ Rank</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rankingGabung as $r)
            <tr>
              <td style="text-align:center;">
                <span class="rk-bdg {{ $r->rank2025==1?'r1':($r->rank2025==2?'r2':($r->rank2025==3?'r3':'rn')) }}">{{ $r->rank2025 }}</span>
              </td>
              <td class="td-nm">{{ preg_replace('/^(Kabupaten|Kota)\s+/i','',$r->kab) }}</td>
              <td class="td-v25">{{ number_format($r->nilai2025/1000,2,',','.') }}</td>
              <td class="td-v23">{{ $r->nilai2023 ? number_format($r->nilai2023/1000,2,',','.') : '–' }}</td>
              <td style="text-align:center;">
                @if($r->perubahan===null)<span class="arr-same">–</span>
                @elseif($r->perubahan>0)<span class="arr-up">↑ +{{ $r->perubahan }}</span>
                @elseif($r->perubahan<0)<span class="arr-dn">↓ {{ $r->perubahan }}</span>
                @else<span class="arr-same">→</span>@endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div style="margin-top:10px;font-size:10.5px;color:#9CA3AF;font-style:italic;line-height:1.65;">
        ↑ naik rank = posisi kemiskinan makin tinggi (makin parah). ↓ turun = membaik relatif terhadap wilayah lain.
      </div>
    </div>
  </div>

  {{-- PETA (Chart 8) --}}
  <div class="card" style="margin-bottom:16px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:16px;">
      <div>
        <div class="card-num">Chart 8 · MENTOR &nbsp;·&nbsp; Peta Interaktif</div>
        <div class="card-title">Peta Kemiskinan — Jawa Timur 2025</div>
        <div class="card-desc" style="margin-bottom:0;">
          Penutup bagian wilayah: lihat <strong>pola geografisnya</strong>. Apakah kemiskinan tinggi mengelompok di wilayah tertentu?
          Warna &amp; ukuran lingkaran = skala nilai kemiskinan (choropleth).
          <strong style="color:#DC2626;">Klik wilayah</strong> → nama, nilai, status atas/bawah rata-rata Jatim.
        </div>
        <span class="card-connector">warna = skala nilai kemiskinan · penutup bagian wilayah</span>
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;flex-shrink:0;">
        @foreach([['#7F1D1D','Sangat Tinggi'],['#DC2626','Tinggi'],['#F87171','Sedang'],['#86EFAC','Rendah'],['#16A34A','Sangat Rendah']] as [$c,$l])
        <div style="display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#374151;">
          <span style="width:11px;height:11px;border-radius:50%;background:{{ $c }};display:inline-block;"></span>{{ $l }}
        </div>
        @endforeach
      </div>
    </div>
    <div class="h-map" id="mapJatim"></div>
  </div>

  {{-- ═══════════════════════════════════════════════════════════════
       BAGIAN 3 — 2 CHART USIA
       Pertanyaan: Kelompok usia mana paling rentan?
  ══════════════════════════════════════════════════════════════════ --}}
  <div class="sec-hdr">
    <span class="sec-pill amber">Bagian 3 — 2 Chart Usia</span>
    <div class="sec-line"></div>
    <span class="sec-q">Kelompok usia mana yang paling rentan di pasar kerja Jatim?</span>
  </div>

  <div class="alur">
    <span class="alur-lbl">Alur baca</span>
    <span class="alur-step">Chart 9: siapa yang paling banyak menganggur per usia</span>
    <span class="alur-arr">→</span>
    <span class="alur-step">Chart 10: apakah kondisinya membaik dibanding 2023</span>
    <span class="alur-arr">·</span>
    <span style="color:#9CA3AF;font-size:10.5px;">Sumber: T3 (2025) &amp; T4 (2023)</span>
  </div>

  <div class="g2" style="margin-bottom:16px;">

    {{-- CHART 9 --}}
    <div class="card">
      <div class="card-hdr">
        <div>
          <div class="card-num">Chart 9 · MENTOR &nbsp;·&nbsp; Sumber: T3 (2025)</div>
          <div class="card-title">Tingkat Pengangguran per Kelompok Usia — 2025</div>
          <div class="card-desc">
            Pembuka bagian usia: <strong>usia mana % penganggurannya tertinggi?</strong>
            Bar <strong style="color:#DC2626;">merah gelap</strong> = pengangguran &gt;5% (perlu perhatian khusus), biru = lebih aman.
            Rumus sudah jelas di bawah. <strong style="color:#DC2626;">Klik bar</strong> untuk drilldown angka lengkap.
          </div>
          <span class="card-connector">pembuka bagian usia · siapa yang paling banyak menganggur?</span>
        </div>
        <span class="card-tag red">Pengangguran ÷ AK × 100</span>
      </div>
      <div class="formula-chip">Rumus: Pengangguran ÷ Jumlah AK × 100 &nbsp;·&nbsp; dihitung per baris usia</div>
      <div class="h-md"><canvas id="cPengUsia"></canvas></div>
      <div class="drill red" id="drillPeng">
        <div style="font-size:11px;font-weight:800;color:#991B1B;margin-bottom:10px;" id="dPengTitle"></div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
          <div><div class="drill-lbl">Jumlah AK</div><div class="drill-val dark" id="dPengAK"></div></div>
          <div><div class="drill-lbl">Pengangguran</div><div class="drill-val red" id="dPengJml"></div></div>
          <div><div class="drill-lbl">Tingkat (%)</div><div class="drill-val red" id="dPengPct"></div></div>
        </div>
      </div>
    </div>

    {{-- CHART 10 --}}
    <div class="card">
      <div class="card-hdr">
        <div>
          <div class="card-num">Chart 10 · MENTOR &nbsp;·&nbsp; Sumber: T3 (2025) &amp; T4 (2023)</div>
          <div class="card-title">% Bekerja per Kelompok Usia — 2023 vs 2025</div>
          <div class="card-desc">
            <strong>Penutup seluruh dashboard.</strong> Bar grouped 2 warna: apakah kondisi membaik dari 2023 ke 2025?
            Data sudah dihitung BPS — bandingkan langsung. Kalau biru (2025) lebih tinggi dari abu (2023), ada perbaikan.
            <strong style="color:#DC2626;">Klik bar</strong> untuk lihat perubahan &amp; arahnya.
          </div>
          <span class="card-connector">sudah dihitung BPS · apakah kondisi membaik?</span>
        </div>
        <span class="card-tag blue">T3 &amp; T4 · BPS</span>
      </div>
      <div style="display:flex;gap:14px;margin-bottom:14px;">
        <div class="lgd"><span class="ldot" style="background:#2563EB;"></span>2025</div>
        <div class="lgd"><span class="ldot" style="background:#94A3B8;"></span>2023</div>
      </div>
      <div class="h-md"><canvas id="cPctBek"></canvas></div>
      <div class="drill blue" id="drillBek">
        <div style="font-size:11px;font-weight:800;color:#1E40AF;margin-bottom:10px;" id="dBekTitle"></div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
          <div><div class="drill-lbl">2023 (%)</div><div class="drill-val dark" id="dBek23"></div></div>
          <div><div class="drill-lbl">2025 (%)</div><div class="drill-val blue" id="dBek25"></div></div>
          <div><div class="drill-lbl">Perubahan</div><div class="drill-val" id="dBekDiff" style="font-size:18px;font-weight:800;font-family:'JetBrains Mono',monospace;"></div></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════════════
       SINTESIS ANALITIK
  ══════════════════════════════════════════════════════════════════ --}}
  <div class="sec-hdr">
    <span class="sec-pill green">Sintesis Analitik</span>
    <div class="sec-line"></div>
    <span class="sec-q">Tiga insight yang saling menguatkan — dari data menuju rekomendasi</span>
  </div>

  <div style="text-align:center;font-size:11.5px;color:#9CA3AF;margin-bottom:18px;font-style:italic;">
    Insight 1 (gambaran umum) &nbsp;→&nbsp; Insight 2 (wilayah prioritas) &nbsp;→&nbsp; Insight 3 (kelompok usia rentan)
  </div>

  <div class="syn-grid" style="margin-bottom:16px;">

    <div class="syn red">
      <div class="syn-tag red">Insight 1 · Gambaran Umum</div>
      <div class="syn-title">Kemiskinan tidak merata — {{ $diAtasRataRata }} dari {{ $totalKab }} kab/kota masih di atas rata-rata Jatim</div>
      <div class="syn-body">
        Rata-rata kemiskinan Jawa Timur 2025 adalah <strong style="color:#111827;">{{ number_format($rataRata2025/1000,2,',','.') }} ribu jiwa</strong> per kab/kota.
        Sebanyak <strong style="color:#111827;">{{ $diAtasRataRata }} kab/kota ({{ round($diAtasRataRata/$totalKab*100,1) }}%)</strong> masih di atas angka ini.
        Sebaliknya, <strong style="color:#111827;">{{ $diBawahRataRata }} kab/kota</strong> sudah berhasil berada di bawah rata-rata.
      </div>
      <div class="syn-stat red">
        <div class="syn-stat-lbl">Rata-rata Jatim 2025</div>
        <div class="syn-stat-val">{{ number_format($rataRata2025/1000,2,',','.') }} ribu jiwa</div>
        <div class="syn-stat-note">{{ $diAtasRataRata }} kab di atas · {{ $diBawahRataRata }} kab di bawah</div>
      </div>
    </div>

    <div class="syn blue">
      <div class="syn-tag blue">Insight 2 · Wilayah Prioritas</div>
      <div class="syn-title">{{ $tertinggi ? preg_replace('/^(Kabupaten|Kota)\s+/i','', $tertinggi->kabupatenKota) : '-' }} kemiskinan tertinggi — ranking tidak selalu stabil</div>
      <div class="syn-body">
        <strong style="color:#111827;">{{ $tertinggi ? preg_replace('/^(Kabupaten|Kota)\s+/i','', $tertinggi->kabupatenKota) : '-' }}</strong>
        mencatat kemiskinan tertinggi dengan <strong style="color:#111827;">{{ $tertinggi ? number_format($tertinggi->jumlahPnddk/1000,2,',','.') : '-' }} ribu jiwa</strong>,
        sementara <strong style="color:#111827;">{{ $terendah ? preg_replace('/^(Kabupaten|Kota)\s+/i','', $terendah->kabupatenKota) : '-' }}</strong>
        terendah (<strong style="color:#111827;">{{ $terendah ? number_format($terendah->jumlahPnddk/1000,2,',','.') : '-' }} ribu jiwa</strong>).
        Analisis perubahan ranking 2023→2025 menunjukkan posisi bisa bergeser — kebijakan tepat terbukti mengubah posisi wilayah.
      </div>
      <div class="syn-stat blue">
        <div class="syn-stat-lbl">Rentang kemiskinan 2025</div>
        <div class="syn-stat-val">{{ $terendah ? number_format($terendah->jumlahPnddk/1000,2,',','.') : '-' }} – {{ $tertinggi ? number_format($tertinggi->jumlahPnddk/1000,2,',','.') : '-' }} rb</div>
        <div class="syn-stat-note">Gap: {{ $tertinggi&&$terendah ? number_format(($tertinggi->jumlahPnddk-$terendah->jumlahPnddk)/1000,2,',','.') : '-' }} ribu jiwa</div>
      </div>
    </div>

    <div class="syn green">
      <div class="syn-tag green">Insight 3 · Kelompok Usia Rentan</div>
      <div class="syn-title">Usia 15–24 tahun paling rentan — ada perbaikan nyata dari 2023 ke 2025</div>
      <div class="syn-body">
        Kelompok <strong style="color:#111827;">usia 15–24 tahun</strong> memiliki tingkat pengangguran tertinggi — wajar secara struktural karena baru masuk pasar kerja.
        Namun perbandingan 2023 vs 2025 menunjukkan <strong style="color:#111827;">tren positif</strong>: % bekerja di usia muda meningkat.
        Kebijakan vokasi &amp; pelatihan usia dini jadi kunci mempercepat perbaikan ini.
      </div>
      <div class="syn-stat green">
        <div class="syn-stat-lbl">Fokus intervensi</div>
        <div class="syn-stat-val">Usia 15–24 Tahun</div>
        <div class="syn-stat-note">Pengangguran tertinggi · Tren membaik 2023→2025</div>
      </div>
    </div>
  </div>

  {{-- ═══════════════════════════════════════════════════════════════
       TABEL DATA LENGKAP
  ══════════════════════════════════════════════════════════════════ --}}
  <div class="sec-hdr">
    <span class="sec-pill red">Data Lengkap</span>
    <div class="sec-line"></div>
    <span class="sec-q">Rincian semua 38 kab/kota — Tahun 2025</span>
  </div>

  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:18px;padding-bottom:16px;border-bottom:1px solid #F1F5F9;">
      <div>
        <div style="font-size:14px;font-weight:800;color:#111827;">Tabel Kemiskinan Per Kabupaten / Kota — 2025</div>
        <div style="font-size:11.5px;color:#9CA3AF;margin-top:2px;">Satuan: jiwa · Rata-rata Jatim: <strong style="color:#374151;">{{ number_format($rataRata2025,0,',','.') }} jiwa</strong></div>
      </div>
      <div class="srch-wrap">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="srchKab" class="srch-inp" placeholder="Cari kabupaten / kota…">
      </div>
    </div>

    @php $mxVal = $dataMiskin2025->max('jumlahPnddk') ?: 1; @endphp
    <div style="overflow-x:auto;">
      <table class="dtbl" id="tblKemiskinan">
        <thead>
          <tr>
            <th style="text-align:left;">#</th>
            <th style="text-align:left;">Kabupaten / Kota</th>
            <th>Jiwa Miskin 2025</th>
            <th>Jiwa Miskin 2023</th>
            <th>Perubahan</th>
            <th style="text-align:center;">Status vs Rata-rata</th>
            <th>Proporsi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($dataMiskin2025->sortByDesc('jumlahPnddk')->values() as $i => $r)
          @php
            $d23 = $dataMiskin2023->firstWhere('kabupatenKota', $r->kabupatenKota);
            $v23 = $d23 ? $d23->jumlahPnddk : null;
            $sel = $v23 ? $r->jumlahPnddk - $v23 : null;
            $ata = $r->jumlahPnddk > $rataRata2025;
            $bw  = ($r->jumlahPnddk / $mxVal) * 100;
          @endphp
          <tr>
            <td class="td-no">{{ $i+1 }}</td>
            <td class="td-nm">{{ $r->kabupatenKota }}</td>
            <td class="td-v25">{{ number_format($r->jumlahPnddk,0,',','.') }}</td>
            <td class="td-v23">{{ $v23 ? number_format($v23,0,',','.') : '–' }}</td>
            <td class="td-dff {{ $sel!==null?($sel>0?'up':($sel<0?'dn':'')):''; }}">
              @if($sel!==null){{ $sel>0?'+':'' }}{{ number_format($sel,0,',','.') }}@else –@endif
            </td>
            <td class="td-sts"><span class="sts-bdg {{ $ata?'atas':'bawah' }}">{{ $ata?'Di atas rata-rata':'Di bawah rata-rata' }}</span></td>
            <td class="td-bar">
              <div class="bar-wr">
                <span class="td-pct">{{ round($r->jumlahPnddk/$mxVal*100,1) }}%</span>
                <div class="bar-tr"><div class="bar-fl {{ $ata?'bar-r':'bar-g' }}" style="width:{{ $bw }}%;"></div></div>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  </div>{{-- /padding --}}
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ═══ DATA ════════════════════════════════════════════════════════
const RATA = {{ $rataRata2025 }};
const dKab  = @json($semuaKab);
const dTop5T= @json($top5Tertinggi);
const dTop5R= @json($top5Terendah);
const dDev  = @json($deviasi);
const dPU   = @json($tingkatPengangguranPerUsia);
const dB25  = @json($pctBekerjaPerUsia2025);
const dB23  = @json($pctBekerjaPerUsia2023);

// ═══ HELPERS ═════════════════════════════════════════════════════
const F  = v => Number(v).toLocaleString('id-ID');
const FD = v => Number(v).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});
const SN = s => (s||'').replace(/^(Kabupaten|Kota)\s+/i,'');
const FONT= "'Plus Jakarta Sans', sans-serif";

const RED  = '#DC2626', REDA = 'rgba(220,38,38,.16)';
const GRN  = '#16A34A', GRNA = 'rgba(22,163,74,.16)';
const BLU  = '#2563EB', BLUA = 'rgba(37,99,235,.16)';
const AMB  = '#D97706';
const GRD  = '#F1F5F9';

const axX = ()=>({grid:{display:false},ticks:{font:{size:10,family:FONT},color:'#9CA3AF'}});
const axY = cb=>({
  grid:{color:GRD,drawBorder:false},
  ticks:{font:{size:10,family:FONT},color:'#9CA3AF',callback:cb||(v=>F(v))}
});
const TT = {
  backgroundColor:'#111827',titleColor:'#F9FAFB',bodyColor:'#D1D5DB',
  padding:12,cornerRadius:10,boxPadding:4,
  titleFont:{size:12,family:FONT,weight:'bold'},bodyFont:{size:11,family:FONT}
};
const BASE = {responsive:true,maintainAspectRatio:false};

// ═══ CHART 5 ═════════════════════════════════════════════════════
new Chart(document.getElementById('cSemuaKab'), {
  type:'bar',
  data:{
    labels:dKab.map(r=>SN(r.kabupatenKota)),
    datasets:[
      {label:'Penduduk Miskin',data:dKab.map(r=>r.jumlahPnddk),
       backgroundColor:dKab.map(r=>r.statusRata==='atas'?REDA:GRNA),
       borderColor:dKab.map(r=>r.statusRata==='atas'?RED:GRN),
       borderWidth:1.5,borderRadius:3,barPercentage:.8,order:2},
      {label:'Rata-rata Jatim',data:dKab.map(()=>RATA),type:'line',
       borderColor:AMB,borderWidth:2.5,borderDash:[7,4],pointRadius:0,fill:false,tension:0,order:1}
    ]
  },
  options:{...BASE,
    onClick(e,els){if(!els.length)return;const r=dKab[els[0].index];popup(r.kabupatenKota,r.jumlahPnddk,r.statusRata);},
    plugins:{legend:{display:false},tooltip:{...TT,callbacks:{
      label:ctx=>ctx.datasetIndex===1?'Rata-rata: '+F(RATA)+' jiwa':F(ctx.raw)+' jiwa',
      afterLabel:ctx=>ctx.datasetIndex!==0?'':dKab[ctx.dataIndex].statusRata==='atas'?'⚠ Di atas rata-rata':'✓ Di bawah rata-rata'
    }}},
    scales:{x:axX(),y:axY()}
  }
});

// ═══ CHART 6 ═════════════════════════════════════════════════════
let c6=null;
function buildTop(mode){
  const d=mode==='tertinggi'?dTop5T:dTop5R;
  const col=mode==='tertinggi'?RED:GRN, colA=mode==='tertinggi'?REDA:GRNA;
  return {type:'bar',
    data:{labels:d.map(r=>SN(r.kabupatenKota)),datasets:[{label:'Penduduk Miskin',data:d.map(r=>r.jumlahPnddk),backgroundColor:colA,borderColor:col,borderWidth:2,borderRadius:8,barPercentage:.6}]},
    options:{...BASE,indexAxis:'y',
      onClick(e,els){if(!els.length)return;const r=d[els[0].index];popup(r.kabupatenKota,r.jumlahPnddk,r.jumlahPnddk>RATA?'atas':'bawah');},
      plugins:{legend:{display:false},tooltip:{...TT,callbacks:{label:ctx=>F(ctx.raw)+' jiwa'}}},
      scales:{x:axY(),y:{grid:{display:false},ticks:{font:{size:12,family:FONT,weight:'600'},color:'#374151'}}}
    }
  };
}
function showTop(mode){
  if(c6) c6.destroy();
  c6=new Chart(document.getElementById('cTop5'),buildTop(mode));
  document.getElementById('btnT').className='tab-btn'+(mode==='tertinggi'?' ar':'');
  document.getElementById('btnR').className='tab-btn'+(mode==='terendah'?' ag':'');
}
showTop('tertinggi');

// ═══ CHART 7 ═════════════════════════════════════════════════════
new Chart(document.getElementById('cDeviasi'), {
  type:'bar',
  data:{
    labels:dDev.map(r=>SN(r.kabupatenKota)),
    datasets:[{label:'Deviasi',data:dDev.map(r=>r.deviasi),
      backgroundColor:dDev.map(r=>r.deviasi>0?REDA:GRNA),
      borderColor:dDev.map(r=>r.deviasi>0?RED:GRN),
      borderWidth:1.5,borderRadius:3,barPercentage:.8}]
  },
  options:{...BASE,
    plugins:{legend:{display:false},tooltip:{...TT,callbacks:{
      label:ctx=>(ctx.raw>0?'+':'')+F(ctx.raw)+' jiwa dari rata-rata',
      afterLabel:ctx=>dDev[ctx.dataIndex].deviasi>0?'⚠ Di atas rata-rata':'✓ Di bawah rata-rata'
    }}},
    scales:{x:axX(),y:{grid:{color:GRD},ticks:{font:{size:10,family:FONT},color:'#9CA3AF',callback:v=>(v>0?'+':'')+F(v)}}}
  }
});

// ═══ CHART 9 ═════════════════════════════════════════════════════
new Chart(document.getElementById('cPengUsia'), {
  type:'bar',
  data:{
    labels:dPU.map(r=>r.usia),
    datasets:[{label:'% Pengangguran',data:dPU.map(r=>r.pct),
      backgroundColor:dPU.map(r=>r.pct>5?'rgba(220,38,38,.78)':BLUA),
      borderColor:dPU.map(r=>r.pct>5?RED:BLU),
      borderWidth:1.5,borderRadius:8,barPercentage:.65}]
  },
  options:{...BASE,
    onClick(e,els){
      if(!els.length)return;
      const r=dPU[els[0].index];
      document.getElementById('dPengTitle').textContent='Detail usia '+r.usia+' tahun — 2025';
      document.getElementById('dPengAK').textContent=F(r.akJml);
      document.getElementById('dPengJml').textContent=F(r.pengangguranJml);
      document.getElementById('dPengPct').textContent=FD(r.pct)+'%';
      document.getElementById('drillPeng').classList.add('show');
    },
    plugins:{legend:{display:false},tooltip:{...TT,callbacks:{
      label:ctx=>FD(ctx.raw)+'% tingkat pengangguran',
      afterLabel:ctx=>'Pengangguran: '+F(dPU[ctx.dataIndex].pengangguranJml)+' | AK: '+F(dPU[ctx.dataIndex].akJml)
    }}},
    scales:{x:axX(),y:{...axY(v=>v+'%'),title:{display:true,text:'% Tingkat Pengangguran',font:{size:10,family:FONT},color:'#9CA3AF'}}}
  }
});

// ═══ CHART 10 ════════════════════════════════════════════════════
const b23m={};
dB23.forEach(r=>{b23m[r.usia]=r.pct;});

new Chart(document.getElementById('cPctBek'), {
  type:'bar',
  data:{
    labels:dB25.map(r=>r.usia),
    datasets:[
      {label:'2025',data:dB25.map(r=>r.pct),backgroundColor:BLUA,borderColor:BLU,borderWidth:1.5,borderRadius:6,barPercentage:.45},
      {label:'2023',data:dB25.map(r=>b23m[r.usia]??null),backgroundColor:'rgba(148,163,184,.28)',borderColor:'#94A3B8',borderWidth:1.5,borderRadius:6,barPercentage:.45}
    ]
  },
  options:{...BASE,
    onClick(e,els){
      if(!els.length)return;
      const r=dB25[els[0].index], p23=b23m[r.usia]??null, diff=p23!==null?(r.pct-p23):null;
      document.getElementById('dBekTitle').textContent='Detail usia '+r.usia+' tahun';
      document.getElementById('dBek23').textContent=p23!==null?FD(p23)+'%':'–';
      document.getElementById('dBek25').textContent=FD(r.pct)+'%';
      const el=document.getElementById('dBekDiff');
      if(diff!==null){el.textContent=(diff>=0?'+':'')+FD(diff)+'%';el.style.color=diff>=0?'#16A34A':'#DC2626';}
      else{el.textContent='–';el.style.color='#9CA3AF';}
      document.getElementById('drillBek').classList.add('show');
    },
    plugins:{legend:{display:true,position:'top',labels:{font:{size:11,family:FONT},usePointStyle:true,boxWidth:10}},
      tooltip:{...TT,callbacks:{label:ctx=>ctx.dataset.label+': '+FD(ctx.raw)+'% bekerja'}}},
    scales:{x:axX(),y:{...axY(v=>v+'%'),title:{display:true,text:'% Bekerja terhadap AK',font:{size:10,family:FONT},color:'#9CA3AF'}}}
  }
});

// ═══ POPUP ═══════════════════════════════════════════════════════
function popup(kab,val,status){
  alert(kab+'\n\nPenduduk miskin 2025: '+F(val)+' jiwa ('+( val/1000).toFixed(2)+' ribu jiwa)\nStatus: '+(status==='atas'?'⚠ Di atas rata-rata':'✓ Di bawah rata-rata')+'\nRata-rata Jatim: '+F(Math.round(RATA))+' jiwa');
}

// ═══ PETA LEAFLET ═════════════════════════════════════════════════
const KOORD=[
  {n:"Kabupaten Pacitan",lt:-8.1845,ln:111.1032},{n:"Kabupaten Ponorogo",lt:-7.8650,ln:111.4617},
  {n:"Kabupaten Trenggalek",lt:-8.0513,ln:111.7079},{n:"Kabupaten Tulungagung",lt:-8.0652,ln:111.9020},
  {n:"Kabupaten Blitar",lt:-8.0984,ln:112.1684},{n:"Kabupaten Kediri",lt:-7.8306,ln:111.9479},
  {n:"Kabupaten Malang",lt:-8.1575,ln:112.6288},{n:"Kabupaten Lumajang",lt:-8.1336,ln:113.2235},
  {n:"Kabupaten Jember",lt:-8.1724,ln:113.6987},{n:"Kabupaten Banyuwangi",lt:-8.2198,ln:114.3691},
  {n:"Kabupaten Bondowoso",lt:-7.9089,ln:113.8232},{n:"Kabupaten Situbondo",lt:-7.7056,ln:114.0057},
  {n:"Kabupaten Probolinggo",lt:-7.7529,ln:113.2135},{n:"Kabupaten Pasuruan",lt:-7.6460,ln:112.9079},
  {n:"Kabupaten Sidoarjo",lt:-7.4478,ln:112.7183},{n:"Kabupaten Mojokerto",lt:-7.5271,ln:112.4345},
  {n:"Kabupaten Jombang",lt:-7.5501,ln:112.2344},{n:"Kabupaten Nganjuk",lt:-7.6043,ln:111.9028},
  {n:"Kabupaten Madiun",lt:-7.6297,ln:111.5228},{n:"Kabupaten Magetan",lt:-7.6484,ln:111.3289},
  {n:"Kabupaten Ngawi",lt:-7.3957,ln:111.4451},{n:"Kabupaten Bojonegoro",lt:-7.1506,ln:111.8812},
  {n:"Kabupaten Tuban",lt:-6.8985,ln:111.9000},{n:"Kabupaten Lamongan",lt:-7.1175,ln:112.4156},
  {n:"Kabupaten Gresik",lt:-7.1573,ln:112.6543},{n:"Kabupaten Bangkalan",lt:-7.0358,ln:112.7371},
  {n:"Kabupaten Sampang",lt:-7.1780,ln:113.2607},{n:"Kabupaten Pamekasan",lt:-7.1600,ln:113.4748},
  {n:"Kabupaten Sumenep",lt:-6.9958,ln:113.9723},{n:"Kota Kediri",lt:-7.8166,ln:112.0106},
  {n:"Kota Blitar",lt:-8.0953,ln:112.1608},{n:"Kota Malang",lt:-7.9666,ln:112.6326},
  {n:"Kota Probolinggo",lt:-7.7543,ln:113.2159},{n:"Kota Pasuruan",lt:-7.6462,ln:112.9090},
  {n:"Kota Mojokerto",lt:-7.4715,ln:112.4344},{n:"Kota Madiun",lt:-7.6298,ln:111.5234},
  {n:"Kota Surabaya",lt:-7.2575,ln:112.7521},{n:"Kota Batu",lt:-7.8685,ln:112.5248}
];

const normN=s=>(s||'').toLowerCase().replace(/kabupaten\s+/g,'').replace(/kota\s+/g,'').replace(/\s+/g,' ').trim();

const dMap=KOORD.map(k=>{
  let f=null,b=0;
  dKab.forEach(r=>{const a=normN(k.n),m=normN(r.kabupatenKota);
    if(a===m){f=r;b=100;}else if(b<80&&(a.includes(m)||m.includes(a))){f=r;b=80;}
  });
  return {...k,jp:f?f.jumlahPnddk:null,sr:f?f.statusRata:null,kk:f?f.kabupatenKota:k.n};
});

const vv=dMap.filter(r=>r.jp).map(r=>r.jp);
const MN=Math.min(...vv),MX=Math.max(...vv);

// Skala warna lebih smooth dengan 6 tier
const mc=v=>{
  if(!v)return '#CBD5E1';
  const t=(v-MN)/(MX-MN);
  if(t>.85)return '#7F1D1D';
  if(t>.68)return '#B91C1C';
  if(t>.50)return '#EF4444';
  if(t>.33)return '#FCA5A5';
  if(t>.16)return '#6EE7B7';
  return '#059669';
};
const mr=v=>v?8+((v-MN)/(MX-MN))*26:7;

const map=L.map('mapJatim',{
  center:[-7.5,112.5],zoom:8,zoomControl:true,scrollWheelZoom:false,
  attributionControl:true
});

// Tile: CartoDB Positron (putih bersih, elegan, data menonjol)
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png',{
  attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/">CARTO</a>',
  subdomains:'abcd', maxZoom:18, opacity:0.8
}).addTo(map);
// Label tipis di layer terpisah agar data tetap menonjol
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png',{
  subdomains:'abcd', maxZoom:18, opacity:0.7, pane:'shadowPane'
}).addTo(map);

dMap.forEach(r=>{
  if(!r.lt||!r.ln)return;
  const warna=mc(r.jp), rad=mr(r.jp), isAtas=r.sr==='atas';
  const rb=r.jp?(r.jp/1000).toFixed(2):'–', jw=r.jp?Number(r.jp).toLocaleString('id-ID'):'–';
  const ns=r.kk.replace(/^Kabupaten\s+/i,'Kab. ').replace(/^Kota\s+/i,'Kota ');
  const t=(r.jp?((r.jp-MN)/(MX-MN)):0);
  const pct=Math.round(t*100);

  // Lingkaran utama
  const ci=L.circleMarker([r.lt,r.ln],{
    radius:rad,
    fillColor:warna,
    color:'#fff',
    weight:2,
    opacity:1,
    fillOpacity:.88
  }).addTo(map);

  // Tooltip dark modern
  ci.bindTooltip(`<strong>${ns}</strong>`, {
    permanent:false, direction:'top', offset:[0,-rad-2], opacity:1,
    className:'leaflet-tooltip'
  });

  // Popup premium dengan progress bar & color header
  const hcLight=isAtas?'#FEF2F2':'#F0FDF4';
  const hcDark =isAtas?'#DC2626':'#16A34A';
  const devVal =r.jp?Math.round(r.jp-RATA):0;
  const devSign=devVal>0?'+':'';

  ci.bindPopup(`
    <div style="font-family:'Plus Jakarta Sans',sans-serif;min-width:250px;border-radius:14px;overflow:hidden;">
      <div style="background:${hcDark};padding:15px 18px 13px;">
        <div style="font-size:12px;color:rgba(255,255,255,.7);font-weight:600;letter-spacing:.4px;text-transform:uppercase;margin-bottom:3px;">
          ${isAtas?'⚠ Di atas rata-rata Jatim':'✓ Di bawah rata-rata Jatim'}
        </div>
        <div style="font-size:15px;font-weight:800;color:#fff;line-height:1.3;">${r.kk}</div>
      </div>
      <div style="padding:14px 18px 16px;background:#fff;">
        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:3px;">
          <span style="font-size:11px;color:#9CA3AF;font-weight:500;">Penduduk miskin 2025</span>
          <span style="font-size:15px;font-weight:800;color:#111827;font-family:'JetBrains Mono',monospace;">${rb} <span style="font-size:11px;color:#6B7280;">rb jiwa</span></span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
          <span style="font-size:10px;color:#D1D5DB;">&nbsp;</span>
          <span style="font-size:11px;font-weight:600;color:#9CA3AF;font-family:'JetBrains Mono',monospace;">(${jw} jiwa)</span>
        </div>
        <div style="background:#F8FAFC;border-radius:10px;padding:10px 12px;margin-bottom:10px;">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span style="font-size:10px;color:#9CA3AF;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">vs Rata-rata Jatim</span>
            <span style="font-size:12px;font-weight:800;color:#374151;font-family:'JetBrains Mono',monospace;">${(RATA/1000).toFixed(2)} rb</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:10px;color:#9CA3AF;font-weight:700;text-transform:uppercase;letter-spacing:.4px;">Deviasi</span>
            <span style="font-size:12px;font-weight:800;color:${isAtas?'#DC2626':'#16A34A'};font-family:'JetBrains Mono',monospace;">${devSign}${F(devVal)} jiwa</span>
          </div>
        </div>
        <div style="margin-bottom:4px;display:flex;justify-content:space-between;">
          <span style="font-size:10px;color:#9CA3AF;font-weight:600;">Proporsi terhadap maksimum</span>
          <span style="font-size:10px;font-weight:800;color:#374151;">${pct}%</span>
        </div>
        <div style="height:6px;background:#F1F5F9;border-radius:99px;overflow:hidden;">
          <div style="height:100%;width:${pct}%;background:${hcDark};border-radius:99px;transition:width .4s;"></div>
        </div>
      </div>
    </div>
  `,{maxWidth:290,className:'',closeButton:true});

  ci.on('mouseover',function(){
    this.setStyle({weight:3,color:'rgba(255,255,255,1)',fillOpacity:1,radius:rad+2});
    this.openTooltip();
  });
  ci.on('mouseout',function(){
    this.setStyle({weight:2,color:'#fff',fillOpacity:.88,radius:rad});
  });
  ci.on('click',function(){this.closeTooltip();});
});

// Legend lebih elegan
const leg=L.control({position:'bottomright'});
leg.onAdd=function(){
  const d=L.DomUtil.create('div','');
  d.style.cssText='background:#fff;padding:15px 18px;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.15);font-family:"Plus Jakarta Sans",sans-serif;font-size:11px;color:#374151;min-width:175px;border:1px solid #F1F5F9;';
  d.innerHTML=`
    <div style="font-weight:800;margin-bottom:10px;color:#111827;font-size:12px;display:flex;align-items:center;gap:6px;">
      <span style="width:8px;height:8px;background:#DC2626;border-radius:50%;display:inline-block;"></span>
      Jumlah Penduduk Miskin
    </div>
    ${[['#7F1D1D','Sangat Tinggi','> 85% maks'],['#B91C1C','Tinggi','68–85%'],['#EF4444','Sedang','50–68%'],['#FCA5A5','Rendah','33–50%'],['#6EE7B7','Cukup Rendah','16–33%'],['#059669','Sangat Rendah','< 16%']]
      .map(([c,l,s])=>`
        <div style="display:flex;align-items:center;gap:9px;margin-bottom:7px;">
          <span style="width:13px;height:13px;border-radius:50%;background:${c};display:inline-block;flex-shrink:0;box-shadow:0 1px 3px rgba(0,0,0,.2);"></span>
          <div>
            <span style="font-weight:700;">${l}</span>
            <span style="color:#9CA3AF;margin-left:4px;">${s}</span>
          </div>
        </div>`).join('')}
    <div style="margin-top:10px;padding-top:10px;border-top:1px solid #F1F5F9;font-size:10px;color:#9CA3AF;font-style:italic;line-height:1.5;">
      Ukuran lingkaran ∝ jumlah penduduk miskin<br>
      <span style="color:#374151;font-weight:600;">Klik wilayah</span> untuk detail lengkap
    </div>`;
  return d;
};
leg.addTo(map);

// Info box rata-rata - lebih compact & stylish
const info=L.control({position:'topleft'});
info.onAdd=function(){
  const d=L.DomUtil.create('div','');
  d.style.cssText='background:#fff;padding:12px 16px;border-radius:14px;box-shadow:0 4px 24px rgba(0,0,0,.13);font-family:"Plus Jakarta Sans",sans-serif;border:1px solid #F1F5F9;border-left:4px solid #DC2626;min-width:170px;';
  d.innerHTML=`
    <div style="font-size:9px;font-weight:800;color:#9CA3AF;text-transform:uppercase;letter-spacing:.7px;margin-bottom:2px;">📍 Rata-rata Jatim 2025</div>
    <div style="font-family:'JetBrains Mono',monospace;font-weight:800;color:#DC2626;font-size:18px;margin-bottom:2px;line-height:1.2;">${(RATA/1000).toFixed(2)} <span style="font-size:11px;color:#6B7280;font-family:'Plus Jakarta Sans',sans-serif;font-weight:600;">ribu jiwa</span></div>
    <div style="font-size:10px;color:#9CA3AF;border-top:1px solid #F1F5F9;padding-top:5px;margin-top:4px;">
      <span style="color:#DC2626;font-weight:700;">{{ $diAtasRataRata }} kab</span> di atas &nbsp;·&nbsp;
      <span style="color:#16A34A;font-weight:700;">{{ $diBawahRataRata }} kab</span> di bawah
    </div>`;
  return d;
};
info.addTo(map);

// ═══ SEARCH ══════════════════════════════════════════════════════
document.getElementById('srchKab').addEventListener('input',function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#tblKemiskinan tbody tr').forEach(row=>{
    row.style.display=row.cells[1].textContent.toLowerCase().includes(q)?'':'none';
  });
});
</script>
@endpush