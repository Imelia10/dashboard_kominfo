@extends('layouts.app')

@section('title', 'Analisis Kependudukan Jawa Timur')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
:root {
  --navy:       #0B1B3D;
  --navy-mid:   #112150;
  --navy-light: #1A3066;
  --cyan:       #00C8D4;
  --cyan-soft:  #00E5F0;
  --teal:       #0891B2;
  --white:      #F8FBFF;
  --grey-100:   #EEF2F9;
  --grey-200:   #D6DDF0;
  --grey-400:   #8899BB;
  --grey-600:   #4A5C82;
  --green:      #10B981;
  --yellow:     #F59E0B;
  --orange:     #F97316;
  --red:        #EF4444;
  --font:       'Sora', sans-serif;
  --mono:       'JetBrains Mono', monospace;
  --r:          16px;
  --r-sm:       10px;
  --shadow:     0 4px 24px rgba(11,27,61,.12);
  --shadow-lg:  0 8px 40px rgba(11,27,61,.18);
  --ease:       cubic-bezier(.4,0,.2,1);
}

/* DARK MODE */
body.dark-mode {
  --white:     #0D1B38;
  --grey-100:  #112150;
  --grey-200:  #1A3066;
  --grey-400:  #6B80AA;
  --grey-600:  #A8B8D8;
  background: #070E20 !important;
  color: #C8D8F0;
}
body.dark-mode .kpd-card,
body.dark-mode .filter-bar,
body.dark-mode .stat-card { background: #0D1B38 !important; border-color: #1A3066 !important; }
body.dark-mode .kpd-table thead th { background: #112150 !important; }
body.dark-mode .kpd-table tbody tr:hover { background: #112150 !important; }

/* WRAP */
.kpd-page {
  font-family: var(--font);
  background: linear-gradient(160deg, #EEF3FC 0%, #F4F7FF 50%, #EBF2FF 100%);
  min-height: 100vh;
  padding: 28px 28px 40px;
}
body.dark-mode .kpd-page { background: linear-gradient(160deg,#070E20 0%,#0B1630 100%); }

/* ── HEADER ── */
.dash-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  flex-wrap: wrap; gap: 16px; margin-bottom: 28px;
}
.dash-title {
  font-size: 22px; font-weight: 800;
  color: var(--navy); line-height: 1.2;
  letter-spacing: -.4px;
}
body.dark-mode .dash-title { color: #C8D8F0; }
.dash-subtitle { font-size: 12.5px; color: var(--grey-400); margin-top: 3px; }
.dash-actions { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }

/* Buttons */
.btn {
  display: inline-flex; align-items: center; gap: 6px;
  height: 36px; padding: 0 14px;
  border-radius: 9px; font-size: 12.5px; font-weight: 600;
  cursor: pointer; border: none; transition: all .2s var(--ease);
  font-family: var(--font);
}
.btn-outline { background: var(--white); border: 1.5px solid var(--grey-200); color: var(--grey-600); }
.btn-outline:hover { border-color: var(--cyan); color: var(--teal); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,200,212,.15); }
.btn-cyan { background: linear-gradient(135deg,var(--teal),var(--cyan)); color: #fff; }
.btn-cyan:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(0,200,212,.35); }
.btn-dark { background: var(--navy); color: #fff; }
.btn-dark:hover { background: var(--navy-light); }

/* ── FILTER BAR ── */
.filter-bar {
  display: flex; align-items: center; flex-wrap: wrap; gap: 10px;
  background: var(--white); border: 1px solid var(--grey-200);
  border-radius: var(--r); padding: 14px 18px;
  margin-bottom: 24px;
  box-shadow: var(--shadow);
}
.filter-bar label { font-size: 11px; font-weight: 700; color: var(--grey-400); text-transform: uppercase; letter-spacing: .6px; }
.filter-bar select,
.filter-bar input[type=text] {
  height: 36px; padding: 0 12px;
  border: 1.5px solid var(--grey-200); border-radius: 9px;
  font-size: 13px; font-family: var(--font);
  color: var(--navy); background: var(--grey-100);
  outline: none; transition: border-color .2s;
}
.filter-bar select:focus,
.filter-bar input:focus { border-color: var(--cyan); }
body.dark-mode .filter-bar select,
body.dark-mode .filter-bar input { color: #C8D8F0; }
.filter-bar input[type=text] { width: 220px; }
.filter-sep { width: 1px; height: 24px; background: var(--grey-200); margin: 0 4px; }
.filter-count { margin-left:auto; font-size:12px; color:var(--grey-400); }
.filter-count b { color: var(--navy); }
body.dark-mode .filter-count b { color: #C8D8F0; }

/* ── TOP GRID ── */
.top-grid {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 20px;
  margin-bottom: 20px;
}
@media(max-width:900px){ .top-grid { grid-template-columns:1fr; } }

/* ── STAT CARD ── */
.stat-card {
  background: var(--white);
  border: 1px solid var(--grey-200);
  border-radius: var(--r); padding: 20px;
  box-shadow: var(--shadow);
  transition: transform .22s var(--ease), box-shadow .22s var(--ease);
  cursor: pointer; position: relative; overflow: hidden;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
.stat-card::after {
  content:''; position:absolute; top:0; left:0; right:0; height:3px;
  background: linear-gradient(90deg, var(--teal), var(--cyan));
  border-radius: var(--r) var(--r) 0 0;
}
.sc-icon {
  width:44px; height:44px; border-radius:12px;
  background: linear-gradient(135deg, #E0F7FA, #B2EBF2);
  display:flex; align-items:center; justify-content:center;
  margin-bottom: 12px;
}
body.dark-mode .sc-icon { background: linear-gradient(135deg,#0B2A3A,#0D3B4A); }
.sc-label { font-size:11px; font-weight:700; color:var(--grey-400); text-transform:uppercase; letter-spacing:.6px; }
.sc-value { font-size:28px; font-weight:800; color:var(--navy); line-height:1.1; margin:6px 0 4px; }
body.dark-mode .sc-value { color:#C8D8F0; }
.sc-delta {
  display:inline-flex; align-items:center; gap:4px;
  font-size:11.5px; font-weight:600; padding:2px 8px;
  border-radius:20px;
}
.delta-up   { background:#D1FAE5; color:#065F46; }
.delta-down { background:#FEE2E2; color:#991B1B; }
.sc-sub { font-size:11px; color:var(--grey-400); margin-top:4px; }

/* ── GENDER BREAKDOWN CARD ── */
.gender-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:14px; }
.gender-card {
  background: var(--grey-100); border-radius:10px; padding:12px;
  border: 1.5px solid transparent; transition: all .2s; cursor:pointer;
}
.gender-card:hover,
.gender-card.active { border-color: var(--cyan); background: #E0F9FA; }
body.dark-mode .gender-card { background: #112150; }
body.dark-mode .gender-card:hover,
body.dark-mode .gender-card.active { background: #0D2A45; }
.gender-label { font-size:11px; font-weight:700; color:var(--grey-400); text-transform:uppercase; }
.gender-val { font-size:18px; font-weight:800; color:var(--navy); margin-top:2px; }
body.dark-mode .gender-val { color:#C8D8F0; }
.gender-pct { font-size:11px; color:var(--grey-400); }

/* Breadcrumb */
.breadcrumb {
  display:flex; align-items:center; gap:6px;
  font-size:12px; color:var(--grey-400); margin-bottom:10px;
  flex-wrap:wrap;
}
.breadcrumb span { color:var(--teal); font-weight:600; cursor:pointer; }
.breadcrumb span:hover { text-decoration:underline; }
.breadcrumb .sep { color:var(--grey-200); }

/* ── CHART CARD ── */
.kpd-card {
  background: var(--white); border: 1px solid var(--grey-200);
  border-radius: var(--r); box-shadow: var(--shadow);
  overflow: hidden;
}
.card-head {
  display:flex; align-items:center; justify-content:space-between;
  padding: 16px 20px 0;
}
.card-title { font-size:13.5px; font-weight:700; color:var(--navy); }
body.dark-mode .card-title { color:#C8D8F0; }
.card-body { padding: 16px 20px 20px; }

/* ── PRODUCTIVITY SECTION ── */
.prod-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}
@media(max-width:900px){ .prod-grid { grid-template-columns:1fr; } }

.prod-cat-card {
  background: var(--white); border: 1px solid var(--grey-200);
  border-radius: var(--r); padding: 18px; box-shadow: var(--shadow);
  cursor: pointer; transition: all .2s var(--ease);
  position: relative; overflow: hidden;
}
.prod-cat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
.prod-cat-card.active { border-color: var(--cyan); }

.cat-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
.cat-badge {
  display:inline-flex; align-items:center; gap:6px;
  padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700;
}
.badge-muda   { background:#DBEAFE; color:#1E40AF; }
.badge-prod   { background:#D1FAE5; color:#065F46; }
.badge-nonprod{ background:#FEF3C7; color:#92400E; }

.cat-pct { font-size:32px; font-weight:800; color:var(--navy); line-height:1; }
body.dark-mode .cat-pct { color:#C8D8F0; }
.cat-total { font-size:12px; color:var(--grey-400); margin-top:3px; }
.cat-prog-wrap { margin-top:12px; }
.cat-prog-label { display:flex; justify-content:space-between; font-size:11px; color:var(--grey-400); margin-bottom:4px; }
.cat-prog-bar { height:8px; background:var(--grey-100); border-radius:4px; overflow:hidden; }
.cat-prog-fill { height:100%; border-radius:4px; transition: width 1s var(--ease); }

.insight-box {
  background: linear-gradient(135deg, #EEF9FF, #E8F5FF);
  border: 1px solid #B3E5FC; border-radius:10px; padding:12px 14px;
  margin-top:12px;
}
body.dark-mode .insight-box { background: linear-gradient(135deg,#0B2A3A,#0D2040); border-color:#0D4060; }
.insight-text { font-size:11.5px; color:#0369A1; line-height:1.5; }
body.dark-mode .insight-text { color:#7DD3FC; }

/* Dependency ratio */
.dep-ratio-card {
  background: linear-gradient(135deg, var(--navy), var(--navy-light));
  border-radius: var(--r); padding: 20px; color: #fff;
  box-shadow: var(--shadow-lg);
}
.dep-val { font-size:40px; font-weight:800; color:var(--cyan); line-height:1; }
.dep-label { font-size:12px; color:#A8B8D8; margin-top:4px; }

/* Source card */
.source-card {
  background: var(--grey-100); border: 1px solid var(--grey-200);
  border-radius: var(--r); padding: 16px 20px;
  display:flex; align-items:flex-start; gap:14px;
  margin-bottom: 24px;
}
.source-icon { width:36px; height:36px; border-radius:9px; background:linear-gradient(135deg,var(--teal),var(--cyan)); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.source-title { font-size:12px; font-weight:700; color:var(--navy); }
body.dark-mode .source-title { color:#C8D8F0; }
.source-desc { font-size:11.5px; color:var(--grey-400); margin-top:3px; line-height:1.5; }

/* ── TABLE SECTION ── */
.section-title {
  font-size:16px; font-weight:800; color:var(--navy);
  margin-bottom:16px; display:flex; align-items:center; gap:8px;
}
body.dark-mode .section-title { color:#C8D8F0; }
.section-title::before {
  content:''; width:4px; height:18px; border-radius:2px;
  background:linear-gradient(var(--teal),var(--cyan));
}

.table-filter-row {
  display:flex; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:14px;
}
.table-filter-row select {
  height:34px; padding:0 10px; border:1.5px solid var(--grey-200);
  border-radius:9px; font-size:12.5px; font-family:var(--font);
  color:var(--navy); background:var(--white); outline:none; cursor:pointer;
}
.table-filter-row select:focus { border-color:var(--cyan); }

.kpd-table-wrap { overflow-x:auto; border-radius:var(--r); }
.kpd-table {
  width:100%; border-collapse:collapse; font-size:12.5px;
  font-family:var(--font);
}
.kpd-table thead th {
  position:sticky; top:0; z-index:2;
  background:#F0F4FF; padding:11px 14px;
  font-size:10.5px; font-weight:700; color:var(--grey-400);
  text-transform:uppercase; letter-spacing:.5px;
  border-bottom:2px solid var(--grey-200);
  white-space:nowrap; cursor:pointer; user-select:none;
  transition: background .15s, color .15s;
}
.kpd-table thead th:hover { background:#E0E9FF; color:var(--teal); }
.kpd-table thead th .sort-icon { margin-left:4px; opacity:.4; }
.kpd-table thead th.asc .sort-icon::after  { content:'↑'; opacity:1; color:var(--teal); }
.kpd-table thead th.desc .sort-icon::after { content:'↓'; opacity:1; color:var(--teal); }
.kpd-table tbody tr {
  border-bottom:1px solid #F0F4FF;
  transition: background .15s, transform .15s;
  cursor:pointer;
}
.kpd-table tbody tr:hover { background:#EEF6FF; }
body.dark-mode .kpd-table tbody tr:hover { background:#112150; }
.kpd-table tbody td { padding:10px 14px; white-space:nowrap; }

.rank-badge {
  display:inline-flex; align-items:center; justify-content:center;
  width:26px; height:26px; border-radius:7px;
  font-size:11px; font-weight:800; font-family:var(--mono);
}
.rank-1 { background:linear-gradient(135deg,#FFD700,#FFA000); color:#fff; }
.rank-2 { background:linear-gradient(135deg,#B0BEC5,#78909C); color:#fff; }
.rank-3 { background:linear-gradient(135deg,#CD7F32,#A0522D); color:#fff; }
.rank-n { background:var(--grey-100); color:var(--grey-600); }

.kab-name { font-weight:600; color:var(--navy); font-size:13px; }
body.dark-mode .kab-name { color:#C8D8F0; }
.kab-type { font-size:10.5px; color:var(--grey-400); }

.num-cell { font-family:var(--mono); font-size:12px; color:var(--grey-600); }
.kpd-val  { font-family:var(--mono); font-size:13px; font-weight:700; color:var(--navy); }
body.dark-mode .kpd-val { color:#C8D8F0; }

.badge-kpd {
  display:inline-block; padding:3px 10px; border-radius:20px;
  font-size:10.5px; font-weight:700; white-space:nowrap;
}
.bk-rendah  { background:#D1FAE5; color:#065F46; }
.bk-sedang  { background:#FEF3C7; color:#92400E; }
.bk-tinggi  { background:#FFEDD5; color:#9A3412; }
.bk-sangat  { background:#FEE2E2; color:#991B1B; }

.prog-mini { width:80px; height:6px; background:var(--grey-200); border-radius:3px; overflow:hidden; display:inline-block; vertical-align:middle; margin-left:6px; }
.prog-mini-fill { height:100%; border-radius:3px; }

.sparkline-cell canvas { display:block; }

.btn-detail {
  height:28px; padding:0 10px; border-radius:7px;
  background:#EEF2FF; border:1px solid #C7D2FE;
  color:#4338CA; font-size:11px; font-weight:600;
  cursor:pointer; transition:all .2s; font-family:var(--font);
}
.btn-detail:hover { background:#4338CA; color:#fff; transform:scale(1.04); }

/* Pagination */
.pagination-row {
  display:flex; align-items:center; justify-content:space-between;
  padding:12px 16px; flex-wrap:wrap; gap:8px;
  border-top:1px solid var(--grey-200);
}
.page-info { font-size:12px; color:var(--grey-400); }
.page-btns { display:flex; gap:4px; }
.page-btn {
  width:30px; height:30px; border-radius:8px;
  border:1px solid var(--grey-200); background:var(--white);
  font-size:12px; font-weight:600; color:var(--grey-600);
  cursor:pointer; transition:all .15s; font-family:var(--font);
}
.page-btn:hover { border-color:var(--cyan); color:var(--teal); }
.page-btn.active { background:var(--teal); color:#fff; border-color:var(--teal); }

/* ── MODAL ── */
.modal-overlay {
  position:fixed; inset:0;
  background:rgba(7,14,32,.6); backdrop-filter:blur(4px);
  z-index:999; display:none; align-items:center; justify-content:center;
  padding:24px;
}
.modal-overlay.open { display:flex; }
.modal-box {
  background:var(--white); border-radius:20px;
  width:100%; max-width:680px;
  box-shadow:0 24px 80px rgba(0,0,0,.3);
  overflow:hidden; animation: modalIn .3s var(--ease);
}
@keyframes modalIn { from{opacity:0;transform:scale(.95) translateY(20px)} to{opacity:1;transform:none} }
.modal-head {
  background:linear-gradient(135deg,var(--navy),var(--navy-light));
  padding:20px 24px; display:flex; align-items:center; justify-content:space-between;
}
.modal-title { font-size:15px; font-weight:700; color:#fff; }
.modal-close {
  width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,.15);
  border:none; color:#fff; cursor:pointer; font-size:16px; display:flex;
  align-items:center; justify-content:center;
}
.modal-close:hover { background:rgba(255,255,255,.3); }
.modal-body { padding:20px 24px 24px; }
.modal-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px; }
.modal-stat { background:var(--grey-100); border-radius:10px; padding:14px; text-align:center; }
.modal-stat-val { font-size:18px; font-weight:800; color:var(--navy); font-family:var(--mono); }
body.dark-mode .modal-stat-val { color:#C8D8F0; }
.modal-stat-label { font-size:10.5px; color:var(--grey-400); margin-top:2px; }
.modal-insight {
  background:linear-gradient(135deg,#EEF9FF,#E8F5FF);
  border:1px solid #B3E5FC; border-radius:10px; padding:12px 14px;
  margin-top:14px; font-size:12px; color:#0369A1; line-height:1.6;
}
body.dark-mode .modal-insight { background:linear-gradient(135deg,#0B2A3A,#0D2040); border-color:#0D4060; color:#7DD3FC; }

/* ── SKELETON ── */
.skel {
  background:linear-gradient(90deg, var(--grey-100) 25%, var(--grey-200) 50%, var(--grey-100) 75%);
  background-size:200% 100%;
  animation:skel-shimmer 1.4s infinite;
  border-radius:6px;
}
@keyframes skel-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ── LEGEND ── */
.legend-strip {
  display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px;
}
.legend-item {
  display:flex; align-items:center; gap:6px;
  padding:5px 12px; border-radius:20px; font-size:11.5px; font-weight:600;
  cursor:pointer; transition:all .15s; user-select:none;
}
.legend-item.inactive { opacity:.35; }
</style>
@endpush

@section('content')
<div class="kpd-page" id="kpdPage">

  {{-- ═══ HEADER ═══ --}}
  <div class="dash-header">
    <div>
      <div class="dash-title">Dashboard Analisis Kependudukan Jawa Timur</div>
      <div class="dash-subtitle">Visualisasi Interaktif Struktur Penduduk & Kepadatan Kabupaten/Kota</div>
    </div>
    <div class="dash-actions">
      <button class="btn btn-outline" onclick="exportExcel()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
        Excel
      </button>
      <button class="btn btn-outline" onclick="exportPDF()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        PDF
      </button>
      <button class="btn btn-dark" id="btn-darkmode" onclick="toggleDark()">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
        Dark
      </button>
    </div>
  </div>

  {{-- ═══ FILTER BAR ═══ --}}
  <div class="filter-bar">
    <label>Tahun</label>
    <select id="sel-tahun">
      @foreach($tahunList as $t)
        <option value="{{ $t }}" {{ $t == $tahunDefault ? 'selected' : '' }}>{{ $t }}</option>
      @endforeach
    </select>
    <div class="filter-sep"></div>
    <label>Kategori</label>
    <select id="sel-kat">
      <option value="">Semua</option>
      <option value="Tidak Padat">Tidak Padat</option>
      <option value="Kurang Padat">Kurang Padat</option>
      <option value="Cukup Padat">Cukup Padat</option>
      <option value="Sangat Padat">Sangat Padat</option>
    </select>
    <div class="filter-sep"></div>
    <label>Cari</label>
    <input type="text" id="inp-search" placeholder="Nama kabupaten/kota...">
    <button class="btn btn-outline" onclick="resetFilter()">↺ Reset</button>
    <div class="filter-count">Tampil <b id="cnt-show">–</b> dari <b id="cnt-total">–</b> wilayah</div>
  </div>

  {{-- ═══ TOP GRID ═══ --}}
  <div class="top-grid">

    {{-- LEFT: TOTAL PENDUDUK CARD + DRILL DOWN --}}
    <div>
      <div class="stat-card" style="margin-bottom:16px" onclick="toggleDrillGender()">
        <div class="sc-icon">
          <svg width="22" height="22" fill="none" stroke="#0891B2" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div class="sc-label">Total Penduduk Jawa Timur</div>
        <div class="sc-value" id="sc-total">–</div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
          <span class="sc-delta delta-up" id="sc-delta">–</span>
          <span class="sc-sub">vs tahun sebelumnya</span>
        </div>
        <div id="mini-trend-chart" style="margin-top:12px"></div>
        <div style="font-size:11px;color:var(--grey-400);margin-top:8px;text-align:center">
          Klik untuk lihat breakdown gender ↓
        </div>
      </div>

      {{-- GENDER BREAKDOWN --}}
      <div id="gender-panel" style="display:none">
        <div class="breadcrumb" id="breadcrumb">
          <span onclick="resetDrill()">Total</span>
          <span class="sep">›</span>
          <span>Breakdown Gender</span>
        </div>
        <div class="gender-grid">
          <div class="gender-card" id="card-laki" onclick="drillGender('laki')">
            <div class="gender-label">
              <svg width="12" height="12" fill="none" stroke="#3B82F6" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block;margin-right:4px"><circle cx="12" cy="8" r="4"/><path d="M16 20v-2a4 4 0 00-8 0v2"/></svg>
              Laki-laki
            </div>
            <div class="gender-val" id="val-laki">–</div>
            <div class="gender-pct" id="pct-laki">–%</div>
          </div>
          <div class="gender-card" id="card-perempuan" onclick="drillGender('perempuan')">
            <div class="gender-label">
              <svg width="12" height="12" fill="none" stroke="#EC4899" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block;margin-right:4px"><circle cx="12" cy="8" r="4"/><path d="M16 20v-2a4 4 0 00-8 0v2"/></svg>
              Perempuan
            </div>
            <div class="gender-val" id="val-perempuan">–</div>
            <div class="gender-pct" id="pct-perempuan">–%</div>
          </div>
        </div>
        <div style="font-size:11px;color:var(--grey-400);margin-top:8px;text-align:center">Klik gender untuk lihat distribusi umur ↓</div>
      </div>
    </div>

    {{-- RIGHT: CHART UMUR --}}
    <div class="kpd-card">
      <div class="card-head">
        <div class="card-title" id="chart-umur-title">Distribusi Kelompok Umur — Jawa Timur</div>
        <div style="font-size:11px;color:var(--grey-400)" id="chart-umur-sub">Total (Laki + Perempuan)</div>
      </div>
      <div class="card-body">
        <div id="chart-umur" style="min-height:320px"></div>
      </div>
    </div>
  </div>

  {{-- ═══ PRODUKTIVITAS SECTION ═══ --}}
  <div class="prod-grid">
    {{-- 3 kategori cards --}}
    <div>
      <div class="section-title" style="margin-bottom:14px">Analisis Produktivitas Penduduk</div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:14px">
        <div class="prod-cat-card" id="cat-muda" onclick="drillKategori('muda')">
          <div class="cat-header">
            <span class="cat-badge badge-muda">
              <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg>
              Muda
            </span>
          </div>
          <div class="cat-pct" id="cat-muda-pct">–%</div>
          <div class="cat-total" id="cat-muda-total">– jiwa</div>
          <div class="cat-total">Usia &lt;15 th</div>
          <div class="cat-prog-wrap">
            <div class="cat-prog-bar"><div class="cat-prog-fill" id="fill-muda" style="background:#3B82F6;width:0%"></div></div>
          </div>
        </div>
        <div class="prod-cat-card" id="cat-prod" onclick="drillKategori('prod')">
          <div class="cat-header">
            <span class="cat-badge badge-prod">
              <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
              Produktif
            </span>
          </div>
          <div class="cat-pct" id="cat-prod-pct">–%</div>
          <div class="cat-total" id="cat-prod-total">– jiwa</div>
          <div class="cat-total">Usia 15–64 th</div>
          <div class="cat-prog-wrap">
            <div class="cat-prog-bar"><div class="cat-prog-fill" id="fill-prod" style="background:#10B981;width:0%"></div></div>
          </div>
        </div>
        <div class="prod-cat-card" id="cat-nonprod" onclick="drillKategori('nonprod')">
          <div class="cat-header">
            <span class="cat-badge badge-nonprod">
              <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
              Non Produktif
            </span>
          </div>
          <div class="cat-pct" id="cat-nonprod-pct">–%</div>
          <div class="cat-total" id="cat-nonprod-total">– jiwa</div>
          <div class="cat-total">Usia &gt;64 th</div>
          <div class="cat-prog-wrap">
            <div class="cat-prog-bar"><div class="cat-prog-fill" id="fill-nonprod" style="background:#F59E0B;width:0%"></div></div>
          </div>
        </div>
      </div>
      {{-- Drill produktivitas --}}
      <div id="prod-drill" style="display:none">
        <div class="kpd-card">
          <div class="card-head">
            <div class="card-title" id="prod-drill-title">Detail Kelompok</div>
            <button class="btn btn-outline" style="height:28px;font-size:11px" onclick="closeProdDrill()">✕ Tutup</button>
          </div>
          <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
              <div style="background:var(--grey-100);border-radius:10px;padding:12px;text-align:center">
                <div style="font-size:11px;color:#3B82F6;font-weight:700">♂ Laki-laki</div>
                <div style="font-size:20px;font-weight:800;color:var(--navy)" id="drill-laki">–</div>
              </div>
              <div style="background:var(--grey-100);border-radius:10px;padding:12px;text-align:center">
                <div style="font-size:11px;color:#EC4899;font-weight:700">♀ Perempuan</div>
                <div style="font-size:20px;font-weight:800;color:var(--navy)" id="drill-perempuan">–</div>
              </div>
            </div>
            <div id="chart-prod-drill" style="min-height:160px"></div>
            <div class="insight-box" id="prod-insight"></div>
          </div>
        </div>
      </div>
    </div>

    {{-- Donut + Dep Ratio --}}
    <div style="display:flex;flex-direction:column;gap:14px">
      <div class="kpd-card" style="flex:1">
        <div class="card-head"><div class="card-title">Komposisi Produktivitas</div></div>
        <div class="card-body"><div id="chart-donut" style="min-height:220px"></div></div>
      </div>
      <div class="dep-ratio-card">
        <div style="font-size:11px;font-weight:700;color:#A8B8D8;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px">Dependency Ratio</div>
        <div class="dep-val" id="dep-ratio">–</div>
        <div class="dep-label">per 100 penduduk produktif</div>
        <div id="dep-insight" style="font-size:11.5px;color:#7DD3FC;margin-top:10px;line-height:1.5"></div>
      </div>
    </div>
  </div>

  {{-- SOURCE CARD --}}
  <div class="source-card">
    <div class="source-icon">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div>
      <div class="source-title">Sumber Data & Metodologi</div>
      <div class="source-desc">
        <b>Badan Pusat Statistik (BPS)</b> — Data Kependudukan Jawa Timur &nbsp;|&nbsp;
        Penduduk muda: usia &lt;15 th · Penduduk produktif: 15–64 th · Non produktif: &gt;64 th (klasifikasi demografi kependudukan) &nbsp;|&nbsp;
        Kategori kepadatan: Tidak Padat ≤50 · Kurang Padat 51–250 · Cukup Padat 251–400 · Sangat Padat &gt;400 jiwa/km²
        <a href="https://dinkes.jogjaprov.go.id" target="_blank" style="color:var(--teal);margin-left:4px">(Sumber: Dinkes DIY)</a>
      </div>
    </div>
  </div>

  {{-- ═══ TABEL KEPADATAN ═══ --}}
  <div class="section-title">Analisis Kepadatan Penduduk Kabupaten/Kota</div>

  <div class="table-filter-row">
    <label style="font-size:11px;font-weight:700;color:var(--grey-400);text-transform:uppercase;letter-spacing:.6px">Kategori</label>
    <select id="tbl-kat" onchange="renderTable()">
      <option value="">Semua Kategori</option>
      <option value="Tidak Padat">Tidak Padat</option>
      <option value="Kurang Padat">Kurang Padat</option>
      <option value="Cukup Padat">Cukup Padat</option>
      <option value="Sangat Padat">Sangat Padat</option>
    </select>
    <label style="font-size:11px;font-weight:700;color:var(--grey-400);text-transform:uppercase;letter-spacing:.6px">Per halaman</label>
    <select id="tbl-perpage" onchange="currentPage=1;renderTable()">
      <option value="10">10</option>
      <option value="20" selected>20</option>
      <option value="50">50</option>
    </select>
    <div style="margin-left:auto;font-size:12px;color:var(--grey-400)">
      Klik header untuk sort · Klik baris untuk detail
    </div>
  </div>

  <div class="kpd-card">
    <div class="kpd-table-wrap">
      <table class="kpd-table">
        <thead>
          <tr>
            <th onclick="sortBy('rank')">#<span class="sort-icon"></span></th>
            <th onclick="sortBy('nama')">Kabupaten/Kota<span class="sort-icon"></span></th>
            <th onclick="sortBy('total')">Total Jiwa<span class="sort-icon"></span></th>
            <th onclick="sortBy('laki')">Laki-laki<span class="sort-icon"></span></th>
            <th onclick="sortBy('perempuan')">Perempuan<span class="sort-icon"></span></th>
            <th onclick="sortBy('luas')">Luas (km²)<span class="sort-icon"></span></th>
            <th onclick="sortBy('kepadatan')">Kepadatan<span class="sort-icon desc"></span></th>
            <th>Kategori</th>
            <th>Persentase</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <tr><td colspan="10" style="text-align:center;padding:30px;color:var(--grey-400)">
            <div class="skel" style="height:16px;width:60%;margin:0 auto"></div>
          </td></tr>
        </tbody>
      </table>
    </div>
    <div class="pagination-row">
      <div class="page-info" id="page-info">–</div>
      <div class="page-btns" id="page-btns"></div>
    </div>
  </div>

</div>

{{-- ═══ MODAL DETAIL ═══ --}}
<div class="modal-overlay" id="modal-detail">
  <div class="modal-box">
    <div class="modal-head">
      <div class="modal-title" id="modal-title">Detail Wilayah</div>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="modal-stats" id="modal-stats"></div>
      <div id="chart-modal-trend" style="min-height:200px"></div>
      <div class="modal-insight" id="modal-insight"></div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
// ═══════════════════════ STATE ═══════════════════════
let allData    = [];
let umurData   = [];
let filteredData = [];
let sortCol    = 'kepadatan';
let sortDir    = -1;
let currentPage = 1;
let drillGenderMode = null;

// Charts
let chartUmur, chartDonut, chartMiniTrend, chartProdDrill, chartModalTrend;

// ═══════════════════════ FORMAT ═══════════════════════
const fmt  = n => Number(n||0).toLocaleString('id');
const fmt2 = n => Number(n||0).toLocaleString('id',{maximumFractionDigits:2});

// ═══════════════════════ INIT ═══════════════════════
window.addEventListener('DOMContentLoaded', () => {
  document.getElementById('sel-tahun').addEventListener('change', () => { currentPage=1; loadAll(); });
  document.getElementById('sel-kat').addEventListener('change',   applyFilter);
  document.getElementById('inp-search').addEventListener('input',  applyFilter);
  loadAll();
});

async function loadAll() {
  const tahun = document.getElementById('sel-tahun').value;
  await Promise.all([
    loadKepadatan(tahun),
    loadUmur(tahun)
  ]);
}

// ═══════════════════════ KEPADATAN API ═══════════════════════
async function loadKepadatan(tahun) {
  const res  = await fetch(`/api/kepadatan?tahun=${tahun}`);
  const json = await res.json();
  allData = json.data || [];

  const sum = json.summary || {};
  document.getElementById('sc-total').textContent = fmt(sum.total_penduduk);
  document.getElementById('cnt-total').textContent = allData.length;

  // Delta vs prev year
  const prevTahun = parseInt(tahun) - 1;
  try {
    const rp = await fetch(`/api/kepadatan?tahun=${prevTahun}`);
    const jp = await rp.json();
    const prev = jp.summary?.total_penduduk || 0;
    const curr = sum.total_penduduk || 0;
    if (prev > 0) {
      const delta = ((curr - prev) / prev * 100).toFixed(2);
      const el = document.getElementById('sc-delta');
      el.textContent = (delta > 0 ? '+' : '') + delta + '%';
      el.className = 'sc-delta ' + (delta >= 0 ? 'delta-up' : 'delta-down');
    }
  } catch(e){}

  // Gender totals
  const totalLaki = allData.reduce((a,d)=>a+(+d.laki_laki||0),0);
  const totalPrp  = allData.reduce((a,d)=>a+(+d.perempuan||0),0);
  const totalAll  = totalLaki + totalPrp;
  document.getElementById('val-laki').textContent     = fmt(totalLaki);
  document.getElementById('val-perempuan').textContent = fmt(totalPrp);
  document.getElementById('pct-laki').textContent     = totalAll ? (totalLaki/totalAll*100).toFixed(1)+'%' : '–%';
  document.getElementById('pct-perempuan').textContent = totalAll ? (totalPrp/totalAll*100).toFixed(1)+'%' : '–%';

  renderMiniTrend(tahun);
  applyFilter();
}

// ═══════════════════════ UMUR API ═══════════════════════
async function loadUmur(tahun) {
  try {
    const res  = await fetch(`/api/kepadatan/umur?tahun=${tahun}`);
    const json = await res.json();
    umurData = json.data || [];
    renderUmurChart('total');
    calcProduktivitas();
  } catch(e){ console.error('umur error',e); }
}

// ═══════════════════════ CHARTS ═══════════════════════
function renderUmurChart(mode) {
  const field = mode === 'laki' ? 'Penduduk (Laki-Laki) (Ribu)'
              : mode === 'perempuan' ? 'Penduduk (Perempuan) (Ribu)'
              : 'Penduduk (Laki-Laki + Perempuan) (Ribu)';

  const labels = umurData.map(d => d.Kelompok_Umur);
  const vals   = umurData.map(d => +(d[field]||0));
  const color  = mode === 'laki' ? '#3B82F6' : mode === 'perempuan' ? '#EC4899' : '#0891B2';

  if (chartUmur) chartUmur.destroy();
  chartUmur = new ApexCharts(document.getElementById('chart-umur'), {
    chart: { type:'bar', height:320, toolbar:{show:false}, animations:{enabled:true,speed:600} },
    series: [{ name: mode==='total'?'Total':mode==='laki'?'Laki-laki':'Perempuan', data: vals }],
    xaxis: { categories: labels, labels:{style:{fontSize:'11px',fontFamily:'Sora'}} },
    yaxis: { labels:{formatter:v=>fmt(v)+' rb',style:{fontSize:'10px',fontFamily:'Sora'}} },
    colors: [color],
    fill: { type:'gradient', gradient:{shade:'light',type:'vertical',shadeIntensity:.3,stops:[0,100]} },
    plotOptions: { bar:{ borderRadius:5, columnWidth:'60%' } },
    tooltip: {
      y: { formatter: v => fmt(v) + ' ribu jiwa' },
      style: { fontFamily:'Sora' }
    },
    grid: { borderColor:'#EEF2F9', strokeDashArray:4 },
    dataLabels: { enabled:false }
  });
  chartUmur.render();
}

function renderMiniTrend(tahun) {
  const years = [2022,2023,2024,2025].filter(y=>y<=parseInt(tahun));
  if (chartMiniTrend) chartMiniTrend.destroy();
  // Fetch all years
  Promise.all(years.map(y=>fetch(`/api/kepadatan?tahun=${y}`).then(r=>r.json())))
    .then(results => {
      const totals = results.map(r => r.summary?.total_penduduk || 0);
      chartMiniTrend = new ApexCharts(document.getElementById('mini-trend-chart'), {
        chart: { type:'area', height:70, sparkline:{enabled:true}, animations:{speed:600} },
        series: [{ data: totals }],
        colors: ['#00C8D4'],
        fill: { type:'gradient', gradient:{shadeIntensity:1,opacityFrom:.4,opacityTo:0} },
        stroke: { curve:'smooth', width:2 },
        tooltip: { x:{show:false}, y:{formatter:v=>'Total: '+fmt(v)} }
      });
      chartMiniTrend.render();
    }).catch(e=>{});
}

function calcProduktivitas() {
  if (!umurData.length) return;
  const field = 'Penduduk (Laki-Laki + Perempuan) (Ribu)';
  const fieldL = 'Penduduk (Laki-Laki) (Ribu)';
  const fieldP = 'Penduduk (Perempuan) (Ribu)';

  let muda=0, prod=0, nonprod=0, mudaL=0,mudaP=0, prodL=0,prodP=0, npL=0,npP=0;
  umurData.forEach(d => {
    const age = d.Kelompok_Umur;
    const v = +(d[field]||0);
    const l = +(d[fieldL]||0);
    const p = +(d[fieldP]||0);
    const start = parseInt(age.split('-')[0]);
    if (age === '65+' || start >= 65) { nonprod+=v; npL+=l; npP+=p; }
    else if (start < 15)              { muda+=v; mudaL+=l; mudaP+=p; }
    else                              { prod+=v; prodL+=l; prodP+=p; }
  });
  const total = muda+prod+nonprod;

  const pMuda    = total ? (muda/total*100).toFixed(1) : 0;
  const pProd    = total ? (prod/total*100).toFixed(1) : 0;
  const pNonprod = total ? (nonprod/total*100).toFixed(1) : 0;

  document.getElementById('cat-muda-pct').textContent     = pMuda+'%';
  document.getElementById('cat-prod-pct').textContent     = pProd+'%';
  document.getElementById('cat-nonprod-pct').textContent  = pNonprod+'%';
  document.getElementById('cat-muda-total').textContent   = fmt(muda)+' rb jiwa';
  document.getElementById('cat-prod-total').textContent   = fmt(prod)+' rb jiwa';
  document.getElementById('cat-nonprod-total').textContent= fmt(nonprod)+' rb jiwa';

  setTimeout(()=>{
    document.getElementById('fill-muda').style.width    = pMuda+'%';
    document.getElementById('fill-prod').style.width    = pProd+'%';
    document.getElementById('fill-nonprod').style.width = pNonprod+'%';
  }, 300);

  // Dependency ratio
  const depRatio = prod > 0 ? ((muda+nonprod)/prod*100).toFixed(1) : '–';
  document.getElementById('dep-ratio').textContent = depRatio;
  document.getElementById('dep-insight').textContent = +depRatio < 50
    ? 'Dependency ratio rendah — Jawa Timur berpotensi memanfaatkan bonus demografi secara optimal.'
    : 'Setiap 100 penduduk produktif menanggung ' + depRatio + ' penduduk non-produktif.';

  // Donut
  if (chartDonut) chartDonut.destroy();
  chartDonut = new ApexCharts(document.getElementById('chart-donut'), {
    chart: { type:'donut', height:220, toolbar:{show:false} },
    series: [+pMuda, +pProd, +pNonprod],
    labels: ['Muda (<15)', 'Produktif (15–64)', 'Non Produktif (>64)'],
    colors: ['#3B82F6','#10B981','#F59E0B'],
    legend: { position:'bottom', fontFamily:'Sora', fontSize:'11px' },
    plotOptions: { pie:{ donut:{ size:'58%' } } },
    tooltip: { y:{formatter:v=>v.toFixed(1)+'%'}, style:{fontFamily:'Sora'} },
    dataLabels: { style:{fontFamily:'Sora',fontSize:'11px'} }
  });
  chartDonut.render();

  // Store for drill
  window._prodData = { muda,prod,nonprod,mudaL,mudaP,prodL,prodP,npL,npP };
}

function drillKategori(cat) {
  document.querySelectorAll('.prod-cat-card').forEach(c=>c.classList.remove('active'));
  document.getElementById('cat-'+cat)?.classList.add('active');
  const d = window._prodData;
  if (!d) return;
  let l,p,title,insight;
  if (cat==='muda')    { l=d.mudaL; p=d.mudaP; title='Penduduk Muda (<15 th)'; insight='Kelompok ini merupakan generasi penerus yang memerlukan investasi di bidang pendidikan dan kesehatan.'; }
  else if (cat==='prod') { l=d.prodL; p=d.prodP; title='Penduduk Produktif (15–64 th)'; insight='Mayoritas penduduk Jawa Timur berada pada usia produktif, mendukung potensi bonus demografi yang perlu dimanfaatkan secara maksimal.'; }
  else { l=d.npL; p=d.npP; title='Penduduk Non Produktif (>64 th)'; insight='Peningkatan penduduk lansia memerlukan perhatian khusus pada layanan kesehatan dan jaminan sosial.'; }

  document.getElementById('prod-drill-title').textContent = title;
  document.getElementById('drill-laki').textContent     = fmt(l)+' rb';
  document.getElementById('drill-perempuan').textContent = fmt(p)+' rb';
  document.getElementById('prod-insight').textContent   = insight;
  document.getElementById('prod-drill').style.display   = 'block';

  if (chartProdDrill) chartProdDrill.destroy();
  chartProdDrill = new ApexCharts(document.getElementById('chart-prod-drill'), {
    chart: { type:'bar', height:150, toolbar:{show:false} },
    series: [{ data:[+l,+p] }],
    xaxis: { categories:['Laki-laki','Perempuan'] },
    colors: ['#3B82F6','#EC4899'],
    plotOptions: { bar:{borderRadius:6,columnWidth:'40%',distributed:true} },
    legend: { show:false },
    dataLabels: { enabled:false },
    tooltip: { y:{formatter:v=>fmt(v)+' rb'}, style:{fontFamily:'Sora'} },
    grid: { borderColor:'#EEF2F9' }
  });
  chartProdDrill.render();
}

function closeProdDrill() {
  document.getElementById('prod-drill').style.display='none';
  document.querySelectorAll('.prod-cat-card').forEach(c=>c.classList.remove('active'));
}

// ═══════════════════════ GENDER DRILL ═══════════════════════
function toggleDrillGender() {
  const panel = document.getElementById('gender-panel');
  panel.style.display = panel.style.display==='none' ? 'block' : 'none';
}

function drillGender(gender) {
  drillGenderMode = gender;
  document.getElementById('card-laki').classList.toggle('active', gender==='laki');
  document.getElementById('card-perempuan').classList.toggle('active', gender==='perempuan');
  document.getElementById('chart-umur-title').textContent =
    'Distribusi Kelompok Umur — ' + (gender==='laki'?'Laki-laki':'Perempuan');
  document.getElementById('chart-umur-sub').textContent =
    gender==='laki' ? 'Penduduk Laki-laki' : 'Penduduk Perempuan';

  const bc = document.getElementById('breadcrumb');
  bc.innerHTML = `<span onclick="resetDrill()">Total</span><span class="sep">›</span><span>Breakdown Gender</span><span class="sep">›</span><span>${gender==='laki'?'Laki-laki':'Perempuan'}</span>`;

  renderUmurChart(gender);
}

function resetDrill() {
  drillGenderMode = null;
  document.getElementById('card-laki').classList.remove('active');
  document.getElementById('card-perempuan').classList.remove('active');
  document.getElementById('chart-umur-title').textContent = 'Distribusi Kelompok Umur — Jawa Timur';
  document.getElementById('chart-umur-sub').textContent = 'Total (Laki + Perempuan)';
  const bc = document.getElementById('breadcrumb');
  bc.innerHTML = `<span onclick="resetDrill()">Total</span><span class="sep">›</span><span>Breakdown Gender</span>`;
  renderUmurChart('total');
}

// ═══════════════════════ TABLE ═══════════════════════
function applyFilter() {
  const kat    = document.getElementById('sel-kat').value;
  const search = document.getElementById('inp-search').value.toLowerCase();
  filteredData = allData.filter(d => {
    const katOk    = kat    ? d.kategori_kepadatan === kat : true;
    const searchOk = search ? d.nama_kabupaten_kota.toLowerCase().includes(search) : true;
    return katOk && searchOk;
  });
  sortData();
  currentPage = 1;
  renderTable();
  document.getElementById('cnt-show').textContent = filteredData.length;
}

function sortBy(col) {
  if (sortCol === col) sortDir *= -1;
  else { sortCol = col; sortDir = -1; }
  document.querySelectorAll('.kpd-table thead th').forEach(th => {
    th.classList.remove('asc','desc');
  });
  const map={rank:0,nama:1,total:2,laki:3,perempuan:4,luas:5,kepadatan:6};
  const idx = map[col];
  const ths = document.querySelectorAll('.kpd-table thead th');
  if(ths[idx]) ths[idx].classList.add(sortDir===1?'asc':'desc');
  sortData();
  renderTable();
}

function sortData() {
  filteredData.sort((a,b) => {
    let va,vb;
    if      (sortCol==='nama')      { va=a.nama_kabupaten_kota; vb=b.nama_kabupaten_kota; }
    else if (sortCol==='total')     { va=+a.total_penduduk;     vb=+b.total_penduduk; }
    else if (sortCol==='laki')      { va=+a.laki_laki;          vb=+b.laki_laki; }
    else if (sortCol==='perempuan') { va=+a.perempuan;          vb=+b.perempuan; }
    else if (sortCol==='luas')      { va=+a.luas_km2;           vb=+b.luas_km2; }
    else                            { va=+a.kepadatan;          vb=+b.kepadatan; }
    if(va<vb) return -1*sortDir; if(va>vb) return 1*sortDir; return 0;
  });
}

function renderTable() {
  const perpage = +document.getElementById('tbl-perpage').value;
  const tblKat  = document.getElementById('tbl-kat').value;
  let rows = tblKat ? filteredData.filter(d=>d.kategori_kepadatan===tblKat) : filteredData;
  const total = rows.length;
  const pages = Math.ceil(total / perpage);
  const start = (currentPage-1)*perpage;
  const slice = rows.slice(start, start+perpage);
  const maxKpd = Math.max(...rows.map(d=>+d.kepadatan||0), 1);

  function rankBadge(i) {
    const r = start+i+1;
    const cls = r===1?'rank-1':r===2?'rank-2':r===3?'rank-3':'rank-n';
    return `<span class="rank-badge ${cls}">${r}</span>`;
  }
  function catBadge(k) {
    const m={'Tidak Padat':'bk-rendah','Kurang Padat':'bk-sedang','Cukup Padat':'bk-tinggi','Sangat Padat':'bk-sangat'};
    return `<span class="badge-kpd ${m[k]||''}">${k}</span>`;
  }
  function progBar(kpd) {
    const pct = Math.min(+kpd/maxKpd*100,100).toFixed(1);
    const col = +kpd<=50?'#10B981':+kpd<=250?'#F59E0B':+kpd<=400?'#F97316':'#EF4444';
    return `<div style="display:flex;align-items:center;gap:6px">
      <span style="font-family:'JetBrains Mono',monospace;font-weight:700;font-size:13px;color:var(--navy)">${fmt2(kpd)}</span>
      <div class="prog-mini"><div class="prog-mini-fill" style="width:${pct}%;background:${col}"></div></div>
    </div>`;
  }
  function nameParts(n) {
    if(n.startsWith('Kota '))       return [`<div class="kab-name">${n.replace('Kota ','')}</div><div class="kab-type">Kota</div>`];
    if(n.startsWith('Kabupaten '))  return [`<div class="kab-name">${n.replace('Kabupaten ','')}</div><div class="kab-type">Kabupaten</div>`];
    return [`<div class="kab-name">${n}</div>`];
  }

  const tbody = document.getElementById('table-body');
  if(!slice.length){
    tbody.innerHTML='<tr><td colspan="10" style="text-align:center;padding:30px;color:var(--grey-400)">Tidak ada data</td></tr>';
  } else {
    tbody.innerHTML = slice.map((d,i) => `
      <tr onclick="openModal('${d.kode_kabupaten_kota}')">
        <td>${rankBadge(i)}</td>
        <td>${nameParts(d.nama_kabupaten_kota)}</td>
        <td class="num-cell">${fmt(d.total_penduduk)}</td>
        <td class="num-cell">${fmt(d.laki_laki)}</td>
        <td class="num-cell">${fmt(d.perempuan)}</td>
        <td class="num-cell">${fmt2(d.luas_km2)}</td>
        <td>${progBar(d.kepadatan)}</td>
        <td>${catBadge(d.kategori_kepadatan)}</td>
        <td>
          <div style="font-size:11.5px;font-weight:700;color:var(--teal)">${(+d.kepadatan/maxKpd*100).toFixed(1)}%</div>
          <div style="font-size:10px;color:var(--grey-400)">dari tertinggi</div>
        </td>
        <td><button class="btn-detail" onclick="event.stopPropagation();openModal('${d.kode_kabupaten_kota}')">Detail →</button></td>
      </tr>
    `).join('');
  }

  // Page info
  document.getElementById('page-info').textContent =
    `Menampilkan ${Math.min(start+1,total)}–${Math.min(start+perpage,total)} dari ${total} wilayah`;

  // Pagination buttons
  const btns = document.getElementById('page-btns');
  let html = '';
  if(currentPage>1) html+=`<button class="page-btn" onclick="goPage(${currentPage-1})">‹</button>`;
  for(let p=Math.max(1,currentPage-2);p<=Math.min(pages,currentPage+2);p++){
    html+=`<button class="page-btn ${p===currentPage?'active':''}" onclick="goPage(${p})">${p}</button>`;
  }
  if(currentPage<pages) html+=`<button class="page-btn" onclick="goPage(${currentPage+1})">›</button>`;
  btns.innerHTML=html;
}

function goPage(p){ currentPage=p; renderTable(); window.scrollTo({top:document.getElementById('table-body').offsetTop-120,behavior:'smooth'}); }

// ═══════════════════════ MODAL ═══════════════════════
async function openModal(kode) {
  const row = allData.find(d=>d.kode_kabupaten_kota==kode);
  if(!row) return;

  document.getElementById('modal-title').textContent = row.nama_kabupaten_kota;
  document.getElementById('modal-stats').innerHTML = `
    <div class="modal-stat"><div class="modal-stat-val">${fmt(row.total_penduduk)}</div><div class="modal-stat-label">Total Penduduk</div></div>
    <div class="modal-stat"><div class="modal-stat-val" style="color:#3B82F6">${fmt(row.laki_laki)}</div><div class="modal-stat-label">Laki-laki</div></div>
    <div class="modal-stat"><div class="modal-stat-val" style="color:#EC4899">${fmt(row.perempuan)}</div><div class="modal-stat-label">Perempuan</div></div>
    <div class="modal-stat"><div class="modal-stat-val">${fmt2(row.luas_km2)}</div><div class="modal-stat-label">Luas (km²)</div></div>
    <div class="modal-stat"><div class="modal-stat-val" style="color:#0891B2">${fmt2(row.kepadatan)}</div><div class="modal-stat-label">Jiwa/km²</div></div>
    <div class="modal-stat"><div class="modal-stat-val">${row.jumlah_pulau||0}</div><div class="modal-stat-label">Jumlah Pulau</div></div>
  `;

  const insight = +row.kepadatan > 400
    ? `⚠️ Wilayah ${row.nama_kabupaten_kota} masuk kategori <b>Sangat Padat</b> dengan kepadatan ${fmt2(row.kepadatan)} jiwa/km². Kondisi ini berpotensi memberikan tekanan pada infrastruktur, transportasi, dan layanan publik.`
    : +row.kepadatan > 250
    ? `📊 ${row.nama_kabupaten_kota} termasuk wilayah <b>Cukup Padat</b>. Perencanaan tata ruang perlu diperhatikan untuk mengantisipasi pertumbuhan penduduk lebih lanjut.`
    : `✅ Kepadatan penduduk ${row.nama_kabupaten_kota} tergolong <b>rendah/sedang</b>, masih memiliki kapasitas daya tampung yang cukup besar.`;
  document.getElementById('modal-insight').innerHTML = insight;

  document.getElementById('modal-detail').classList.add('open');

  // Trend
  try {
    const res  = await fetch(`/api/kepadatan/trend?kode=${kode}`);
    const json = await res.json();
    const trend = json.trend || [];
    if(chartModalTrend) chartModalTrend.destroy();
    chartModalTrend = new ApexCharts(document.getElementById('chart-modal-trend'), {
      chart: { type:'area', height:200, toolbar:{show:false}, animations:{speed:500} },
      series: [
        { name:'Kepadatan', data: trend.map(d=>+d.kepadatan) },
        { name:'Total Jiwa', data: trend.map(d=>+d.total_penduduk) }
      ],
      xaxis: { categories: trend.map(d=>d.tahun), labels:{style:{fontFamily:'Sora'}} },
      colors: ['#0891B2','#6366F1'],
      stroke: { curve:'smooth', width:[2,2] },
      fill: { type:'gradient', gradient:{opacityFrom:.3,opacityTo:0} },
      legend: { position:'top', fontFamily:'Sora', fontSize:'11px' },
      yaxis: [
        { title:{text:'jiwa/km²',style:{fontFamily:'Sora',fontSize:'10px'}}, labels:{style:{fontFamily:'JetBrains Mono'}} },
        { opposite:true, title:{text:'total jiwa',style:{fontFamily:'Sora',fontSize:'10px'}}, labels:{style:{fontFamily:'JetBrains Mono'}} }
      ],
      tooltip: { shared:true, style:{fontFamily:'Sora'} },
      grid: { borderColor:'#EEF2F9', strokeDashArray:4 }
    });
    chartModalTrend.render();
  } catch(e){}
}

function closeModal() {
  document.getElementById('modal-detail').classList.remove('open');
  if(chartModalTrend) { chartModalTrend.destroy(); chartModalTrend=null; }
}

document.getElementById('modal-detail').addEventListener('click', function(e){
  if(e.target===this) closeModal();
});

// ═══════════════════════ DARK MODE ═══════════════════════
function toggleDark() {
  document.body.classList.toggle('dark-mode');
  const btn = document.getElementById('btn-darkmode');
  const isDark = document.body.classList.contains('dark-mode');
  btn.textContent = isDark ? '☀ Light' : '🌙 Dark';
}

// ═══════════════════════ RESET ═══════════════════════
function resetFilter() {
  document.getElementById('sel-kat').value = '';
  document.getElementById('inp-search').value = '';
  applyFilter();
}

// ═══════════════════════ EXPORT ═══════════════════════
function exportExcel() {
  const ws = XLSX.utils.json_to_sheet(filteredData.map((d,i)=>({
    'No':i+1,'Nama':d.nama_kabupaten_kota,'Total':d.total_penduduk,
    'Laki':d.laki_laki,'Perempuan':d.perempuan,'Luas km2':d.luas_km2,
    'Kepadatan':d.kepadatan,'Kategori':d.kategori_kepadatan
  })));
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb,'Kepadatan',ws);
  XLSX.writeFile(wb,`kepadatan_jatim_${document.getElementById('sel-tahun').value}.xlsx`);
}

function exportPDF() { window.print(); }
</script>
@endpush