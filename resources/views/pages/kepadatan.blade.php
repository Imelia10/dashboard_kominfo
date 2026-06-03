@extends('layouts.app')

@section('title', 'Analisis Kependudukan Jawa Timur')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Sora:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
:root {
  --navy:       #0B1B3D;
  --navy-mid:   #112150;
  --navy-light: #1A3066;
  --cyan:       #00C8D4;
  --cyan-soft:  #00E5F0;
  --teal:       #0284c7;
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

.kpd-page {
  font-family: var(--font);
  background: linear-gradient(160deg, #EEF3FC 0%, #F4F7FF 50%, #EBF2FF 100%);
  min-height: 100vh;
  padding: 28px 28px 40px;
}

/* ── HEADER ── */
.pg-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 24px;
}
.pg-breadcrumb { display:flex; align-items:center; gap:8px; margin-bottom:6px; }
.pg-breadcrumb-bar { width:28px; height:3px; background:#1565C0; border-radius:2px; }
.pg-breadcrumb-label { font-size:10.5px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; color:#1565C0; }
.pg-title { font-family:'Plus Jakarta Sans',sans-serif; font-size:28px; font-weight:900; color:#0f1a2e; line-height:1.15; margin:0 0 4px; }
.pg-subtitle { font-size:14px; color:#8a97b0; font-weight:400; margin:0; }

.year-sel {
  display:flex; align-items:center; gap:8px;
  background:var(--white); border:1.5px solid var(--grey-200);
  border-radius:12px; padding:8px 14px; box-shadow:var(--shadow);
}
.year-sel-label { font-size:11.5px; font-weight:700; color:var(--grey-400); text-transform:uppercase; letter-spacing:.5px; }
.year-sel select { border:none; background:transparent; font-size:14px; font-weight:700; font-family:var(--font); color:var(--navy); outline:none; cursor:pointer; padding:0 4px; }

/* ══════════════════════════════════════════
   SECTION LABEL
══════════════════════════════════════════ */
.section-label {
  font-size: 13px;
  font-weight: 800;
  color: var(--navy);
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.section-label::before {
  content: '';
  width: 4px; height: 16px;
  border-radius: 2px;
  background: linear-gradient(var(--teal), var(--cyan));
  flex-shrink: 0;
}

/* ══════════════════════════════════════════
   TOP AREA: left stat + right chart (2-col)
══════════════════════════════════════════ */
.top-area {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 20px;
  margin-bottom: 16px;
  align-items: stretch;
}
@media(max-width:860px){ .top-area { grid-template-columns:1fr; } }

/* ── STAT CARD (total penduduk) ── */
.stat-card {
  background: var(--white);
  border: 1px solid var(--grey-200);
  border-radius: var(--r);
  padding: 22px 20px 20px;
  box-shadow: var(--shadow);
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: transform .22s var(--ease), box-shadow .22s var(--ease);
  display: flex;
  flex-direction: column;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
.stat-card::after {
  content:''; position:absolute; top:0; left:0; right:0; height:3px;
  background: linear-gradient(90deg, var(--teal), var(--cyan));
  border-radius: var(--r) var(--r) 0 0;
}
.sc-icon {
  width:44px; height:44px; border-radius:12px;
  background: linear-gradient(135deg,#E0F7FA,#B2EBF2);
  display:flex; align-items:center; justify-content:center;
  margin-bottom: 12px;
}
.sc-label { font-size:11px; font-weight:700; color:var(--grey-400); text-transform:uppercase; letter-spacing:.6px; }
.sc-value { font-size:30px; font-weight:800; color:var(--navy); line-height:1.1; margin:6px 0 0; }

/* ── GENDER PANEL ── */
.gender-panel { margin-top: 16px; }
.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--grey-400); margin-bottom:10px; flex-wrap:wrap; }
.breadcrumb span { color:var(--teal); font-weight:600; cursor:pointer; }
.breadcrumb span:hover { text-decoration:underline; }
.breadcrumb .sep { color:var(--grey-200); }
.gender-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.gender-card {
  background: var(--grey-100); border-radius:10px; padding:12px;
  border: 1.5px solid transparent; transition: all .2s; cursor:pointer;
}
.gender-card:hover, .gender-card.active { border-color:var(--cyan); background:#E0F9FA; }
.gender-label { font-size:11px; font-weight:700; color:var(--grey-400); text-transform:uppercase; }
.gender-val { font-size:18px; font-weight:800; color:var(--navy); margin-top:2px; }
.gender-pct { font-size:11px; color:var(--grey-400); }

/* ── CHART CARD (right) ── */
.chart-card {
  background: var(--white);
  border: 1px solid var(--grey-200);
  border-radius: var(--r);
  box-shadow: var(--shadow);
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.chart-card-head {
  display:flex; align-items:center; justify-content:space-between;
  padding: 16px 20px 0;
  flex-shrink: 0;
}
.chart-card-title { font-size:13.5px; font-weight:700; color:var(--navy); }
.chart-card-body { padding: 12px 20px 18px; flex:1; }

/* ══════════════════════════════════════════
   STRUKTUR PENDUDUK — 3 cards full-width row
══════════════════════════════════════════ */
.struktur-row {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}
@media(max-width:700px){ .struktur-row { grid-template-columns:1fr; } }

.cat-card {
  background: var(--white);
  border: 1.5px solid var(--grey-200);
  border-radius: var(--r);
  padding: 18px 20px 16px;
  box-shadow: var(--shadow);
  cursor: pointer;
  transition: all .2s var(--ease);
  position: relative;
  overflow: hidden;
}
.cat-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-lg); }
.cat-card.active { border-color: var(--cyan); }
.cat-card::after {
  content:''; position:absolute; bottom:0; left:0; right:0; height:3px;
  border-radius: 0 0 var(--r) var(--r);
  opacity: 0; transition: opacity .2s;
}
.cat-card.muda::after    { background: linear-gradient(90deg,#3B82F6,#60A5FA); opacity:1; }
.cat-card.prod::after    { background: linear-gradient(90deg,#10B981,#34D399); opacity:1; }
.cat-card.nonprod::after { background: linear-gradient(90deg,#F59E0B,#FBBF24); opacity:1; }

.cat-badge {
  display:inline-flex; align-items:center; gap:5px;
  padding:4px 11px; border-radius:20px; font-size:11px; font-weight:700;
  margin-bottom:10px;
}
.badge-muda    { background:#DBEAFE; color:#1E40AF; }
.badge-prod    { background:#D1FAE5; color:#065F46; }
.badge-nonprod { background:#FEF3C7; color:#92400E; }

.cat-pct   { font-size:32px; font-weight:800; color:var(--navy); line-height:1; }
.cat-total { font-size:12px; color:var(--grey-400); margin-top:3px; }
.cat-range { font-size:11px; color:var(--grey-600); margin-top:1px; }

.cat-prog-bar { height:7px; background:var(--grey-100); border-radius:4px; overflow:hidden; margin-top:14px; }
.cat-prog-fill { height:100%; border-radius:4px; transition: width 1s var(--ease); }

/* ── DRILL PANEL (full-width, below struktur row) ── */
.drill-panel {
  background: var(--white);
  border: 1px solid var(--grey-200);
  border-radius: var(--r);
  box-shadow: var(--shadow);
  overflow: hidden;
  margin-bottom: 16px;
}
.drill-head {
  display:flex; align-items:center; justify-content:space-between;
  padding: 14px 20px;
  border-bottom: 1px solid var(--grey-100);
}
.drill-title { font-size:13px; font-weight:700; color:var(--navy); }
.drill-body { padding: 16px 20px 18px; display:grid; grid-template-columns:200px 1fr; gap:20px; align-items:center; }
@media(max-width:600px){ .drill-body { grid-template-columns:1fr; } }
.drill-stats { display:flex; flex-direction:column; gap:10px; }
.drill-stat-box {
  background: var(--grey-100); border-radius:10px; padding:12px 14px;
}
.drill-stat-label { font-size:10.5px; font-weight:700; margin-bottom:2px; }
.drill-stat-val { font-size:20px; font-weight:800; color:var(--navy); }
.insight-box {
  background: linear-gradient(135deg,#EEF9FF,#E8F5FF);
  border: 1px solid #B3E5FC; border-radius:10px; padding:12px 14px; margin-top:12px;
  font-size:12px; color:#0369A1; line-height:1.6;
}

/* ── BTN ── */
.btn-sm {
  height:28px; padding:0 12px; border-radius:7px;
  background:#EEF2FF; border:1px solid #C7D2FE;
  color:#4338CA; font-size:11px; font-weight:600;
  cursor:pointer; transition:all .2s; font-family:var(--font);
}
.btn-sm:hover { background:#4338CA; color:#fff; }

/* ══════════════════════════════════════════
   SOURCE BOX
══════════════════════════════════════════ */
.source-box {
  background: #F0F9FF;
  border: 1px solid #BAE6FD;
  border-radius: 10px;
  padding: 11px 16px;
  margin-bottom: 22px;
  font-size: 11.5px;
  color: #0369A1;
  line-height: 1.7;
}
.source-box a { color:var(--teal); font-weight:600; text-decoration:none; }
.source-box a:hover { text-decoration:underline; }

/* ══════════════════════════════════════════
   KEPADATAN SUMMARY
══════════════════════════════════════════ */
.density-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}
.density-card {
  background: var(--white);
  border: 1px solid var(--grey-200);
  border-radius: var(--r);
  padding: 18px 20px;
  box-shadow: var(--shadow);
  position: relative;
  overflow: hidden;
  transition: transform .2s var(--ease), box-shadow .2s var(--ease);
}
.density-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-lg); }
.density-card::after {
  content:''; position:absolute; top:0; left:0; right:0; height:3px;
  border-radius: var(--r) var(--r) 0 0;
}
.dc-rendah::after { background:linear-gradient(90deg,#10B981,#34D399); }
.dc-sedang::after { background:linear-gradient(90deg,#F59E0B,#FBBF24); }
.dc-tinggi::after { background:linear-gradient(90deg,#F97316,#FB923C); }
.dc-sangat::after { background:linear-gradient(90deg,#EF4444,#F87171); }
.density-card-label { font-size:10.5px; font-weight:700; color:var(--grey-400); text-transform:uppercase; letter-spacing:.6px; margin-bottom:6px; }
.density-card-val   { font-size:26px; font-weight:800; color:var(--navy); line-height:1; font-family:var(--mono); }
.density-card-sub   { font-size:11px; color:var(--grey-400); margin-top:4px; }
.density-badge { display:inline-block; margin-top:8px; padding:3px 10px; border-radius:20px; font-size:10.5px; font-weight:700; }

/* ══════════════════════════════════════════
   TABLE
══════════════════════════════════════════ */
.tbl-card { background:var(--white); border:1px solid var(--grey-200); border-radius:var(--r); box-shadow:var(--shadow); overflow:hidden; margin-bottom:24px; }
.tbl-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--grey-200); flex-wrap:wrap; gap:12px; }
.tbl-toolbar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.tbl-search { display:flex; align-items:center; gap:8px; background:var(--grey-100); border:1.5px solid var(--grey-200); border-radius:9px; padding:6px 12px; transition:border-color .2s; }
.tbl-search:focus-within { border-color:var(--cyan); }
.tbl-search input { border:none; background:transparent; outline:none; font-size:12.5px; font-family:var(--font); color:var(--navy); width:180px; }
.rows-sel { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--grey-400); }
.rows-sel select { height:32px; padding:0 8px; border:1.5px solid var(--grey-200); border-radius:8px; font-size:12.5px; font-family:var(--font); color:var(--navy); background:var(--white); outline:none; cursor:pointer; }
.rows-sel select:focus { border-color:var(--cyan); }
.kpd-table-wrap { overflow-x:auto; }
.kpd-table { width:100%; border-collapse:collapse; font-size:12.5px; font-family:var(--font); }
.kpd-table thead th { position:sticky; top:0; z-index:2; background:#F0F4FF; padding:11px 14px; font-size:10.5px; font-weight:700; color:var(--grey-400); text-transform:uppercase; letter-spacing:.5px; border-bottom:2px solid var(--grey-200); white-space:nowrap; cursor:pointer; user-select:none; transition:background .15s,color .15s; }
.kpd-table thead th:hover { background:#E0E9FF; color:var(--teal); }
.kpd-table thead th .sort-icon { margin-left:4px; opacity:.4; }
.kpd-table thead th.asc .sort-icon::after  { content:'↑'; opacity:1; color:var(--teal); }
.kpd-table thead th.desc .sort-icon::after { content:'↓'; opacity:1; color:var(--teal); }
.kpd-table tbody tr { border-bottom:1px solid #F0F4FF; transition:background .15s; cursor:pointer; }
.kpd-table tbody tr:hover { background:#EEF6FF; }
.kpd-table tbody td { padding:10px 14px; white-space:nowrap; }
.rank-badge { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:7px; font-size:11px; font-weight:800; font-family:var(--mono); }
.rank-1 { background:linear-gradient(135deg,#FFD700,#FFA000); color:#fff; }
.rank-2 { background:linear-gradient(135deg,#B0BEC5,#78909C); color:#fff; }
.rank-3 { background:linear-gradient(135deg,#CD7F32,#A0522D); color:#fff; }
.rank-n { background:var(--grey-100); color:var(--grey-600); }
.kab-name { font-weight:600; color:var(--navy); font-size:13px; }
.kab-type { font-size:10.5px; color:var(--grey-400); }
.num-cell { font-family:var(--mono); font-size:12px; color:var(--grey-600); }
.badge-kpd { display:inline-block; padding:3px 10px; border-radius:20px; font-size:10.5px; font-weight:700; white-space:nowrap; }
.bk-rendah { background:#D1FAE5; color:#065F46; }
.bk-sedang { background:#FEF3C7; color:#92400E; }
.bk-tinggi { background:#FFEDD5; color:#9A3412; }
.bk-sangat { background:#FEE2E2; color:#991B1B; }
.prog-mini { width:80px; height:6px; background:var(--grey-200); border-radius:3px; overflow:hidden; display:inline-block; vertical-align:middle; margin-left:6px; }
.prog-mini-fill { height:100%; border-radius:3px; }
.btn-detail { height:28px; padding:0 10px; border-radius:7px; background:#EEF2FF; border:1px solid #C7D2FE; color:#4338CA; font-size:11px; font-weight:600; cursor:pointer; transition:all .2s; font-family:var(--font); }
.btn-detail:hover { background:#4338CA; color:#fff; transform:scale(1.04); }
.pagination-row { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; flex-wrap:wrap; gap:8px; border-top:1px solid var(--grey-200); }
.page-info { font-size:12px; color:var(--grey-400); }
.page-btns { display:flex; gap:4px; }
.page-btn { width:30px; height:30px; border-radius:8px; border:1px solid var(--grey-200); background:var(--white); font-size:12px; font-weight:600; color:var(--grey-600); cursor:pointer; transition:all .15s; font-family:var(--font); }
.page-btn:hover { border-color:var(--cyan); color:var(--teal); }
.page-btn.active { background:var(--teal); color:#fff; border-color:var(--teal); }

/* ── SECTION TITLE (larger headings) ── */
.section-title {
  font-size:16px; font-weight:800; color:var(--navy);
  margin-bottom:16px; display:flex; align-items:center; gap:8px;
}
.section-title::before { content:''; width:4px; height:18px; border-radius:2px; background:linear-gradient(var(--teal),var(--cyan)); }

/* ── MODAL ── */
.modal-overlay { position:fixed; inset:0; background:rgba(7,14,32,.6); backdrop-filter:blur(4px); z-index:999; display:none; align-items:center; justify-content:center; padding:24px; }
.modal-overlay.open { display:flex; }
.modal-box { background:var(--white); border-radius:20px; width:100%; max-width:680px; box-shadow:0 24px 80px rgba(0,0,0,.3); overflow:hidden; animation:modalIn .3s var(--ease); }
@keyframes modalIn { from{opacity:0;transform:scale(.95) translateY(20px)} to{opacity:1;transform:none} }
.modal-head { background:linear-gradient(135deg,var(--navy),var(--navy-light)); padding:20px 24px; display:flex; align-items:center; justify-content:space-between; }
.modal-title { font-size:15px; font-weight:700; color:#fff; }
.modal-close { width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,.15); border:none; color:#fff; cursor:pointer; font-size:16px; display:flex; align-items:center; justify-content:center; }
.modal-close:hover { background:rgba(255,255,255,.3); }
.modal-body { padding:20px 24px 24px; }
.modal-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:16px; }
.modal-stat { background:var(--grey-100); border-radius:10px; padding:14px; text-align:center; }
.modal-stat-val { font-size:18px; font-weight:800; color:var(--navy); font-family:var(--mono); }
.modal-stat-label { font-size:10.5px; color:var(--grey-400); margin-top:2px; }
.modal-insight { background:linear-gradient(135deg,#EEF9FF,#E8F5FF); border:1px solid #B3E5FC; border-radius:10px; padding:12px 14px; margin-top:14px; font-size:12px; color:#0369A1; line-height:1.6; }

/* ── SKELETON ── */
.skel { background:linear-gradient(90deg,var(--grey-100) 25%,var(--grey-200) 50%,var(--grey-100) 75%); background-size:200% 100%; animation:skel-shimmer 1.4s infinite; border-radius:6px; }
@keyframes skel-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ── SUMBER FOOTER ── */
.sumber-footer { margin-top:32px; border-top:1px solid var(--grey-200); padding-top:20px; }
.sumber-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:12px; }
.source-card { background:var(--grey-100); border:1px solid var(--grey-200); border-radius:var(--r); padding:16px 20px; display:flex; align-items:flex-start; gap:14px; }
.source-icon { width:36px; height:36px; border-radius:9px; background:linear-gradient(135deg,var(--teal),var(--cyan)); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.source-title { font-size:12px; font-weight:700; color:var(--navy); }
.source-desc { font-size:11.5px; color:var(--grey-400); margin-top:3px; line-height:1.5; }
.source-desc a { color:var(--teal); font-weight:600; text-decoration:none; }
.source-desc a:hover { text-decoration:underline; }
</style>
@endpush

@section('content')
<div class="kpd-page" id="kpdPage">

  {{-- ── HEADER ── --}}
  <div class="pg-header">
    <div>
      <div class="pg-breadcrumb">
        <div class="pg-breadcrumb-bar"></div>
        <span class="pg-breadcrumb-label">Dashboard</span>
      </div>
      <h1 class="pg-title">Analisis <span style="color:#1565C0">Kependudukan</span> Jawa Timur</h1>
      <p class="pg-subtitle">Visualisasi Struktur Penduduk & Kepadatan Kabupaten/Kota</p>
    </div>
    <form method="GET" action="{{ route('kepadatan') }}">
      <div class="year-sel">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span class="year-sel-label">Tahun</span>
        <select name="tahun" onchange="this.form.submit()">
          @foreach($tahunList as $t)
            <option value="{{ $t }}" {{ $t == $tahunDefault ? 'selected' : '' }}>{{ $t }}</option>
          @endforeach
        </select>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
    </form>
  </div>

  {{-- ══════════════════════════════════════════
       SECTION LABEL: Struktur Penduduk
  ══════════════════════════════════════════ --}}
  <div class="section-label">Struktur Penduduk</div>

  {{-- ══════════════════════════════════════════
       TOP AREA: Total Penduduk (kiri) + Chart Umur (kanan)
  ══════════════════════════════════════════ --}}
  <div class="top-area">

    {{-- LEFT: Total Penduduk card --}}
    <div class="stat-card" onclick="toggleDrillGender()">
      <div class="sc-icon">
        <svg width="22" height="22" fill="none" stroke="#0891B2" stroke-width="2" viewBox="0 0 24 24">
          <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
        </svg>
      </div>
      <div class="sc-label">Total Penduduk Jawa Timur</div>
      <div class="sc-value" id="sc-total">–</div>
      <div style="font-size:11px;color:var(--grey-400);margin-top:10px">
        Klik untuk lihat breakdown gender ↓
      </div>

      {{-- Gender breakdown (inside card, collapsible) --}}
      <div id="gender-panel" class="gender-panel" style="display:none">
        <div class="breadcrumb" id="breadcrumb">
          <span onclick="event.stopPropagation();resetDrill()">Total</span>
          <span class="sep">›</span>
          <span>Breakdown Gender</span>
        </div>
        <div class="gender-grid">
          <div class="gender-card" id="card-laki" onclick="event.stopPropagation();drillGender('laki')">
            <div class="gender-label">
              <svg width="11" height="11" fill="none" stroke="#3B82F6" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block;margin-right:3px"><circle cx="12" cy="8" r="4"/><path d="M16 20v-2a4 4 0 00-8 0v2"/></svg>
              Laki-laki
            </div>
            <div class="gender-val" id="val-laki">–</div>
            <div class="gender-pct" id="pct-laki">–%</div>
          </div>
          <div class="gender-card" id="card-perempuan" onclick="event.stopPropagation();drillGender('perempuan')">
            <div class="gender-label">
              <svg width="11" height="11" fill="none" stroke="#EC4899" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block;margin-right:3px"><circle cx="12" cy="8" r="4"/><path d="M16 20v-2a4 4 0 00-8 0v2"/></svg>
              Perempuan
            </div>
            <div class="gender-val" id="val-perempuan">–</div>
            <div class="gender-pct" id="pct-perempuan">–%</div>
          </div>
        </div>
        <div style="font-size:11px;color:var(--grey-400);margin-top:8px">
          Klik gender untuk lihat distribusi umur ↓
        </div>
      </div>
    </div>

    {{-- RIGHT: Chart Kelompok Umur --}}
    <div class="chart-card">
      <div class="chart-card-head">
        <div class="chart-card-title" id="chart-umur-title">Distribusi Kelompok Umur — Jawa Timur</div>
        <div style="font-size:11px;color:var(--grey-400)" id="chart-umur-sub">Total (Laki + Perempuan)</div>
      </div>
      <div class="chart-card-body">
        <div id="chart-umur" style="min-height:300px"></div>
      </div>
    </div>

  </div>
  {{-- END TOP AREA --}}

  {{-- ══════════════════════════════════════════
       STRUKTUR ROW: 3 kartu full-width
  ══════════════════════════════════════════ --}}
  <div class="struktur-row">

    <div class="cat-card muda" id="cat-muda" onclick="drillKategori('muda')">
      <div>
        <span class="cat-badge badge-muda">
          <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
          Muda
        </span>
      </div>
      <div class="cat-pct" id="cat-muda-pct">–%</div>
      <div class="cat-total" id="cat-muda-total">– rb jiwa</div>
      <div class="cat-range">Usia &lt;15 th</div>
      <div class="cat-prog-bar"><div class="cat-prog-fill" id="fill-muda" style="background:#3B82F6;width:0%"></div></div>
    </div>

    <div class="cat-card prod" id="cat-prod" onclick="drillKategori('prod')">
      <div>
        <span class="cat-badge badge-prod">
          <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          Produktif
        </span>
      </div>
      <div class="cat-pct" id="cat-prod-pct">–%</div>
      <div class="cat-total" id="cat-prod-total">– rb jiwa</div>
      <div class="cat-range">Usia 15–64 th</div>
      <div class="cat-prog-bar"><div class="cat-prog-fill" id="fill-prod" style="background:#10B981;width:0%"></div></div>
    </div>

    <div class="cat-card nonprod" id="cat-nonprod" onclick="drillKategori('nonprod')">
      <div>
        <span class="cat-badge badge-nonprod">
          <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
          Non Produktif
        </span>
      </div>
      <div class="cat-pct" id="cat-nonprod-pct">–%</div>
      <div class="cat-total" id="cat-nonprod-total">– rb jiwa</div>
      <div class="cat-range">Usia &gt;64 th</div>
      <div class="cat-prog-bar"><div class="cat-prog-fill" id="fill-nonprod" style="background:#F59E0B;width:0%"></div></div>
    </div>

  </div>

  {{-- Drill panel (full width, below struktur row) --}}
  <div id="prod-drill" style="display:none">
    <div class="drill-panel">
      <div class="drill-head">
        <div class="drill-title" id="prod-drill-title">Detail Kelompok</div>
        <button class="btn-sm" onclick="closeProdDrill()">✕ Tutup</button>
      </div>
      <div class="drill-body">
        <div class="drill-stats">
          <div class="drill-stat-box">
            <div class="drill-stat-label" style="color:#3B82F6">♂ Laki-laki</div>
            <div class="drill-stat-val" id="drill-laki">–</div>
          </div>
          <div class="drill-stat-box">
            <div class="drill-stat-label" style="color:#EC4899">♀ Perempuan</div>
            <div class="drill-stat-val" id="drill-perempuan">–</div>
          </div>
          <div class="insight-box" id="prod-insight"></div>
        </div>
        <div id="chart-prod-drill" style="min-height:160px"></div>
      </div>
    </div>
  </div>

  {{-- Sumber Kategori Umur --}}
  <div class="source-box">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block;vertical-align:middle;margin-right:6px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <b>Kategori Umur Penduduk</b> mengacu pada Badan Pusat Statistik (BPS):
    Penduduk muda: &lt;15 th · Penduduk produktif: 15–64 th · Non produktif: &gt;64 th.
    &nbsp;&nbsp;<a href="https://repository.poltekkes-denpasar.ac.id/10991/4/BAB%20II%20Tinjauan%20Pustaka.pdf" target="_blank" rel="noopener">Lihat sumber referensi ↗</a>
  </div>

  {{-- ══════════════════════════════════════════
       KEPADATAN PENDUDUK
  ══════════════════════════════════════════ --}}
  <div class="section-title">Kepadatan Penduduk Jawa Timur</div>

  <div class="density-summary">
    <div class="density-card dc-rendah">
      <div class="density-card-label">Tidak Padat</div>
      <div class="density-card-val" id="dc-rendah-val">–</div>
      <div class="density-card-sub">kabupaten/kota</div>
      <span class="density-badge" style="background:#D1FAE5;color:#065F46">&lt; 150 jiwa/ha</span>
    </div>
    <div class="density-card dc-sedang">
      <div class="density-card-label">Sedang</div>
      <div class="density-card-val" id="dc-sedang-val">–</div>
      <div class="density-card-sub">kabupaten/kota</div>
      <span class="density-badge" style="background:#FEF3C7;color:#92400E">151–200 jiwa/ha</span>
    </div>
    <div class="density-card dc-tinggi">
      <div class="density-card-label">Tinggi</div>
      <div class="density-card-val" id="dc-tinggi-val">–</div>
      <div class="density-card-sub">kabupaten/kota</div>
      <span class="density-badge" style="background:#FFEDD5;color:#9A3412">201–400 jiwa/ha</span>
    </div>
    <div class="density-card dc-sangat">
      <div class="density-card-label">Sangat Padat</div>
      <div class="density-card-val" id="dc-sangat-val">–</div>
      <div class="density-card-sub">kabupaten/kota</div>
      <span class="density-badge" style="background:#FEE2E2;color:#991B1B">&gt; 400 jiwa/ha</span>
    </div>
  </div>

  <div class="source-box">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block;vertical-align:middle;margin-right:6px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <b>Klasifikasi Kepadatan Penduduk</b> mengacu pada standar PUSLITBANG Permukiman 2011 (Dinas Pekerjaan Umum):
    Rendah &lt;150 jiwa/ha · Sedang 151–200 jiwa/ha · Tinggi 201–400 jiwa/ha · Sangat Padat &gt;400 jiwa/ha.
    &nbsp;&nbsp;<a href="https://eprints.ums.ac.id/91069/3/BAB%20I.pdf" target="_blank" rel="noopener">Lihat sumber referensi ↗</a>
  </div>

  {{-- ── TABLE ── --}}
  <div class="tbl-card">
    <div class="tbl-header">
      <div style="display:flex;align-items:center;gap:8px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
        <span style="font-size:14px;font-weight:700;color:var(--navy)">Data Detail Per Wilayah</span>
      </div>
      <div class="tbl-toolbar">
        <div class="tbl-search">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" id="tblSearch" placeholder="Cari kabupaten/kota..." oninput="applyTableSearch()">
        </div>
        <div class="rows-sel">
          Tampilkan
          <select id="tblRows" onchange="currentPage=1;renderTable()">
            <option value="6" selected>6</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="999">Semua</option>
          </select>
          baris
        </div>
      </div>
    </div>
    <div class="kpd-table-wrap">
      <table class="kpd-table">
        <thead>
          <tr>
            <th onclick="sortBy('rank')">#<span class="sort-icon"></span></th>
            <th onclick="sortBy('nama')">Kabupaten/Kota<span class="sort-icon"></span></th>
            <th onclick="sortBy('total')">Total Jiwa<span class="sort-icon"></span></th>
            <th onclick="sortBy('luas')">Luas (km²)<span class="sort-icon"></span></th>
            <th onclick="sortBy('kepadatan')">Jiwa/km²<span class="sort-icon asc"></span></th>
            <th onclick="sortBy('kepadatan_ha')">Jiwa/ha<span class="sort-icon"></span></th>
            <th>Kategori</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="table-body">
          <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--grey-400)">
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

  {{-- ── SUMBER DATA ── --}}
  <div class="sumber-footer">
    <div class="section-title" style="font-size:13px;margin-bottom:14px">Sumber Data</div>
    <div class="sumber-grid">
      <div class="source-card">
        <div class="source-icon">
          <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div>
        <div>
          <div class="source-title">Data Penduduk (Jenis Kelamin)</div>
          <div class="source-desc">
            Jumlah Penduduk Jawa Timur Berdasarkan Jenis Kelamin — Open Data Jawa Timur<br>
            <a href="https://opendata.jatimprov.go.id/dataset/jumlah-penduduk-jawa-timur-berdasarkan-jenis-kelamin" target="_blank" rel="noopener">opendata.jatimprov.go.id ↗</a>
          </div>
        </div>
      </div>
      <div class="source-card">
        <div class="source-icon">
          <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="9" y1="4" x2="9" y2="22"/></svg>
        </div>
        <div>
          <div class="source-title">Data Penduduk (Kelompok Umur)</div>
          <div class="source-desc">
            Jumlah Penduduk Menurut Kelompok Umur & Jenis Kelamin — BPS Jawa Timur<br>
            <a href="https://jatim.bps.go.id/id/statistics-table/3/WVc0MGEyMXBkVFUxY25KeE9HdDZkbTQzWkVkb1p6MDkjMw==/jumlah-penduduk-menurut-kelompok-umur-dan-jenis-kelamin--ribu-jiwa--di-provinsi-jawa-timur--2024.html?year=2025" target="_blank" rel="noopener">jatim.bps.go.id ↗</a>
          </div>
        </div>
      </div>
      <div class="source-card">
        <div class="source-icon">
          <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
        </div>
        <div>
          <div class="source-title">Luas Wilayah</div>
          <div class="source-desc">
            Luas Daerah & Jumlah Pulau Menurut Kabupaten/Kota di Provinsi Jawa Timur — BPS Jawa Timur<br>
            <a href="https://jatim.bps.go.id/id/statistics-table/3/VUZwV01tSlpPVlpsWlRKbmMxcFhhSGhEVjFoUFFUMDkjMw==/luas-daerah-dan-jumlah-pulau-menurut-kabupaten-kota-di-provinsi-jawa-timur--2023.html" target="_blank" rel="noopener">jatim.bps.go.id ↗</a>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ══ MODAL ══ --}}
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
<script>
// ═══════════ STATE ═══════════
let allData      = [];
let umurData     = [];
let filteredData = [];
let sortCol      = 'kepadatan';
let sortDir      = 1;
let currentPage  = 1;
let drillGenderMode = null;

let chartUmur, chartProdDrill, chartModalTrend;

const fmt  = n => Number(n||0).toLocaleString('id');
const fmt2 = n => Number(n||0).toLocaleString('id',{maximumFractionDigits:2});

const TAHUN_AKTIF = '{{ $tahunDefault }}';

window.addEventListener('DOMContentLoaded', () => {
  const sel = document.querySelector('.year-sel select');
  if (sel) sel.value = TAHUN_AKTIF;
  loadAll(TAHUN_AKTIF);
});

async function loadAll(tahun) {
  await Promise.all([loadKepadatan(tahun), loadUmur(tahun)]);
}

// ═══════════ HELPER: kategori berdasarkan jiwa/ha ═══════════
// Konsisten digunakan oleh density cards, tabel, dan modal
function getKategoriHa(kepadatan_km2) {
  const h = +kepadatan_km2 / 100;
  if (h > 400)  return 'Sangat Padat';
  if (h > 200)  return 'Tinggi';
  if (h >= 150) return 'Sedang';
  return 'Tidak Padat';
}

// ═══════════ KEPADATAN API ═══════════
async function loadKepadatan(tahun) {
  const res  = await fetch(`/api/kepadatan?tahun=${tahun}`);
  const json = await res.json();
  allData = json.data || [];

  const sum = json.summary || {};
  document.getElementById('sc-total').textContent = fmt(sum.total_penduduk);

  const totalLaki = allData.reduce((a,d)=>a+(+d.laki_laki||0),0);
  const totalPrp  = allData.reduce((a,d)=>a+(+d.perempuan||0),0);
  const totalAll  = totalLaki + totalPrp;
  document.getElementById('val-laki').textContent      = fmt(totalLaki);
  document.getElementById('val-perempuan').textContent  = fmt(totalPrp);
  document.getElementById('pct-laki').textContent      = totalAll ? (totalLaki/totalAll*100).toFixed(1)+'%' : '–%';
  document.getElementById('pct-perempuan').textContent  = totalAll ? (totalPrp/totalAll*100).toFixed(1)+'%' : '–%';

  // FIX: hitung kategori dari jiwa/ha (kepadatan_km2 / 100) agar sesuai threshold di UI
  const rendah = allData.filter(d => (+d.kepadatan / 100) <  150).length;
  const sedang = allData.filter(d => { const h = +d.kepadatan / 100; return h >= 150 && h <= 200; }).length;
  const tinggi = allData.filter(d => { const h = +d.kepadatan / 100; return h >  200 && h <= 400; }).length;
  const sangat = allData.filter(d => (+d.kepadatan / 100) >  400).length;
  document.getElementById('dc-rendah-val').textContent = rendah;
  document.getElementById('dc-sedang-val').textContent = sedang;
  document.getElementById('dc-tinggi-val').textContent = tinggi;
  document.getElementById('dc-sangat-val').textContent = sangat;

  filteredData = [...allData];
  sortData();
  renderTable();
}

// ═══════════ UMUR API ═══════════
async function loadUmur(tahun) {
  try {
    let res  = await fetch(`/api/kepadatan/umur?tahun=${tahun}`);
    let json = await res.json();
    if (!json.data || json.data.length === 0) {
      const fallbacks = [2024, 2023, 2025].filter(y => y != tahun);
      for (const fb of fallbacks) {
        res  = await fetch(`/api/kepadatan/umur?tahun=${fb}`);
        json = await res.json();
        if (json.data && json.data.length > 0) break;
      }
    }
    umurData = json.data || [];

    // FIX: sort numerik agar urutan 0-4, 5-9, 10-14, ... benar (bukan sort string)
    umurData.sort((a, b) => {
      const parseAge = s => {
        if (s === '75+' || s === '65+') return parseInt(s);
        return parseInt(s.split('-')[0]);
      };
      return parseAge(a.Kelompok_Umur) - parseAge(b.Kelompok_Umur);
    });

    renderUmurChart('total');
    calcProduktivitas();
  } catch(e) { console.error('umur error', e); }
}

// ═══════════ CHART UMUR ═══════════
function renderUmurChart(mode) {
  const field = mode === 'laki'      ? 'Penduduk (Laki-Laki) (Ribu)'
              : mode === 'perempuan' ? 'Penduduk (Perempuan) (Ribu)'
              :                        'Penduduk (Laki-Laki + Perempuan) (Ribu)';
  const labels = umurData.map(d => d.Kelompok_Umur);
  const vals   = umurData.map(d => +(d[field]||0));
  const color  = mode === 'laki' ? '#3B82F6' : mode === 'perempuan' ? '#EC4899' : '#0891B2';

  if (chartUmur) chartUmur.destroy();
  chartUmur = new ApexCharts(document.getElementById('chart-umur'), {
    chart: { type:'bar', height:300, toolbar:{show:false}, animations:{enabled:true,speed:600} },
    series: [{ name: mode==='total'?'Total':mode==='laki'?'Laki-laki':'Perempuan', data: vals }],
    xaxis: { categories: labels, labels:{style:{fontSize:'11px',fontFamily:'Sora'}} },
    yaxis: { labels:{formatter:v=>fmt(v)+' rb',style:{fontSize:'10px',fontFamily:'Sora'}} },
    colors: [color],
    fill: { type:'gradient', gradient:{shade:'light',type:'vertical',shadeIntensity:.3,stops:[0,100]} },
    plotOptions: { bar:{ borderRadius:5, columnWidth:'60%' } },
    tooltip: { y:{formatter:v=>fmt(v)+' ribu jiwa'}, style:{fontFamily:'Sora'} },
    grid: { borderColor:'#EEF2F9', strokeDashArray:4 },
    dataLabels: { enabled:false }
  });
  chartUmur.render();
}

// ═══════════ PRODUKTIVITAS ═══════════
function calcProduktivitas() {
  if (!umurData.length) return;
  const field  = 'Penduduk (Laki-Laki + Perempuan) (Ribu)';
  const fieldL = 'Penduduk (Laki-Laki) (Ribu)';
  const fieldP = 'Penduduk (Perempuan) (Ribu)';

  let muda=0,prod=0,nonprod=0,mudaL=0,mudaP=0,prodL=0,prodP=0,npL=0,npP=0;
  umurData.forEach(d => {
    const age   = d.Kelompok_Umur;
    const v     = +(d[field]||0);
    const l     = +(d[fieldL]||0);
    const p     = +(d[fieldP]||0);
    const start = parseInt(age.split('-')[0]);
    if (age === '65+' || start >= 65) { nonprod+=v; npL+=l; npP+=p; }
    else if (start < 15)              { muda+=v; mudaL+=l; mudaP+=p; }
    else                              { prod+=v; prodL+=l; prodP+=p; }
  });
  const total = muda+prod+nonprod;

  const pMuda    = total ? (muda/total*100).toFixed(1)    : 0;
  const pProd    = total ? (prod/total*100).toFixed(1)    : 0;
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

  window._prodData = { muda,prod,nonprod,mudaL,mudaP,prodL,prodP,npL,npP };
}

// ═══════════ DRILL KATEGORI ═══════════
function drillKategori(cat) {
  document.querySelectorAll('.cat-card').forEach(c=>c.classList.remove('active'));
  document.getElementById('cat-'+cat)?.classList.add('active');
  const d = window._prodData;
  if (!d) return;

  let l, p, title, insight;
  if (cat==='muda')      { l=d.mudaL; p=d.mudaP; title='Penduduk Muda (<15 th)';          insight='Kelompok ini merupakan generasi penerus yang memerlukan investasi di bidang pendidikan dan kesehatan.'; }
  else if (cat==='prod') { l=d.prodL; p=d.prodP; title='Penduduk Produktif (15–64 th)';   insight='Mayoritas penduduk Jawa Timur berada pada usia produktif, mendukung potensi bonus demografi yang perlu dimanfaatkan secara maksimal.'; }
  else                   { l=d.npL;   p=d.npP;   title='Penduduk Non Produktif (>64 th)'; insight='Peningkatan penduduk lansia memerlukan perhatian khusus pada layanan kesehatan dan jaminan sosial.'; }

  document.getElementById('prod-drill-title').textContent = title;
  document.getElementById('drill-laki').textContent       = fmt(l)+' rb';
  document.getElementById('drill-perempuan').textContent  = fmt(p)+' rb';
  document.getElementById('prod-insight').textContent     = insight;
  document.getElementById('prod-drill').style.display     = 'block';

  if (chartProdDrill) chartProdDrill.destroy();
  chartProdDrill = new ApexCharts(document.getElementById('chart-prod-drill'), {
    chart: { type:'bar', height:160, toolbar:{show:false} },
    series: [{ data:[+l,+p] }],
    xaxis: { categories:['Laki-laki','Perempuan'] },
    colors: ['#3B82F6','#EC4899'],
    plotOptions: { bar:{ borderRadius:6, columnWidth:'40%', distributed:true } },
    legend: { show:false },
    dataLabels: { enabled:false },
    tooltip: { y:{formatter:v=>fmt(v)+' rb'}, style:{fontFamily:'Sora'} },
    grid: { borderColor:'#EEF2F9' }
  });
  chartProdDrill.render();
}

function closeProdDrill() {
  document.getElementById('prod-drill').style.display='none';
  document.querySelectorAll('.cat-card').forEach(c=>c.classList.remove('active'));
  if (chartProdDrill) { chartProdDrill.destroy(); chartProdDrill=null; }
}

// ═══════════ GENDER DRILL ═══════════
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
  bc.innerHTML = `<span onclick="event.stopPropagation();resetDrill()">Total</span><span class="sep">›</span><span>Breakdown Gender</span><span class="sep">›</span><span>${gender==='laki'?'Laki-laki':'Perempuan'}</span>`;
  renderUmurChart(gender);
}

function resetDrill() {
  drillGenderMode = null;
  document.getElementById('card-laki').classList.remove('active');
  document.getElementById('card-perempuan').classList.remove('active');
  document.getElementById('chart-umur-title').textContent = 'Distribusi Kelompok Umur — Jawa Timur';
  document.getElementById('chart-umur-sub').textContent   = 'Total (Laki + Perempuan)';
  const bc = document.getElementById('breadcrumb');
  bc.innerHTML = `<span onclick="event.stopPropagation();resetDrill()">Total</span><span class="sep">›</span><span>Breakdown Gender</span>`;
  renderUmurChart('total');
}

// ═══════════ TABLE ═══════════
function applyTableSearch() {
  const search = document.getElementById('tblSearch').value.toLowerCase();
  filteredData = allData.filter(d =>
    !search || d.nama_kabupaten_kota.toLowerCase().includes(search)
  );
  sortData();
  currentPage = 1;
  renderTable();
}

function sortBy(col) {
  if (sortCol === col) sortDir *= -1;
  else { sortCol = col; sortDir = 1; }
  document.querySelectorAll('.kpd-table thead th').forEach(th=>th.classList.remove('asc','desc'));
  const map = {rank:0,nama:1,total:2,luas:3,kepadatan:4,kepadatan_ha:5};
  const idx = map[col];
  const ths = document.querySelectorAll('.kpd-table thead th');
  if (ths[idx]) ths[idx].classList.add(sortDir===1?'asc':'desc');
  sortData();
  renderTable();
}

function sortData() {
  filteredData.sort((a,b) => {
    let va, vb;
    if      (sortCol==='nama')         { va=a.nama_kabupaten_kota; vb=b.nama_kabupaten_kota; }
    else if (sortCol==='total')        { va=+a.total_penduduk;     vb=+b.total_penduduk; }
    else if (sortCol==='luas')         { va=+a.luas_km2;           vb=+b.luas_km2; }
    else if (sortCol==='kepadatan_ha') { va=+a.kepadatan/100;      vb=+b.kepadatan/100; }
    else                               { va=+a.kepadatan;          vb=+b.kepadatan; }
    if(va<vb) return -1*sortDir; if(va>vb) return 1*sortDir; return 0;
  });
}

function renderTable() {
  const perpage = +document.getElementById('tblRows').value;
  const rows    = filteredData;
  const total   = rows.length;
  const pages   = Math.ceil(total / perpage);
  const start   = (currentPage-1)*perpage;
  const slice   = rows.slice(start, start+perpage);
  const maxKpd  = Math.max(...rows.map(d=>+d.kepadatan||0), 1);

  function rankBadge(i) {
    const r   = start+i+1;
    const cls = r===1?'rank-1':r===2?'rank-2':r===3?'rank-3':'rank-n';
    return `<span class="rank-badge ${cls}">${r}</span>`;
  }
  function catBadge(k) {
    const m = {'Tidak Padat':'bk-rendah','Sedang':'bk-sedang','Tinggi':'bk-tinggi','Sangat Padat':'bk-sangat'};
    return `<span class="badge-kpd ${m[k]||'bk-rendah'}">${k}</span>`;
  }
  function progBar(kpd) {
    const pct = Math.min(+kpd/maxKpd*100,100).toFixed(1);
    const col = +kpd<15000?'#10B981':+kpd<=20000?'#F59E0B':+kpd<=40000?'#F97316':'#EF4444';
    return `<div style="display:flex;align-items:center;gap:6px">
      <span style="font-family:'JetBrains Mono',monospace;font-weight:700;font-size:13px;color:var(--navy)">${fmt2(kpd)}</span>
      <div class="prog-mini"><div class="prog-mini-fill" style="width:${pct}%;background:${col}"></div></div>
    </div>`;
  }
  function jiwaHa(kpd) {
    if (!kpd) return '<span style="color:var(--grey-400)">–</span>';
    const val = (+kpd/100).toFixed(2);
    return `<span style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--grey-600)">${Number(val).toLocaleString('id',{maximumFractionDigits:2})}</span>`;
  }
  function nameParts(n) {
    if(n.startsWith('Kota '))      return `<div class="kab-name">${n.replace('Kota ','')}</div><div class="kab-type">Kota</div>`;
    if(n.startsWith('Kabupaten ')) return `<div class="kab-name">${n.replace('Kabupaten ','')}</div><div class="kab-type">Kabupaten</div>`;
    return `<div class="kab-name">${n}</div>`;
  }

  const tbody = document.getElementById('table-body');
  if (!slice.length) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:var(--grey-400)">Tidak ada data</td></tr>';
  } else {
    tbody.innerHTML = slice.map((d,i) => `
      <tr onclick="openModal('${d.kode_kabupaten_kota}')">
        <td>${rankBadge(i)}</td>
        <td>${nameParts(d.nama_kabupaten_kota)}</td>
        <td class="num-cell">${fmt(d.total_penduduk)}</td>
        <td class="num-cell">${d.luas_km2 ? fmt2(d.luas_km2) : '<span style="color:var(--grey-400)">–</span>'}</td>
        <td>${progBar(d.kepadatan)}</td>
        <td>${jiwaHa(d.kepadatan)}</td>
        <td>${catBadge(getKategoriHa(d.kepadatan))}</td>
        <td><button class="btn-detail" onclick="event.stopPropagation();openModal('${d.kode_kabupaten_kota}')">Detail →</button></td>
      </tr>
    `).join('');
  }

  document.getElementById('page-info').textContent =
    `Menampilkan ${Math.min(start+1,total)}–${Math.min(start+perpage,total)} dari ${total} wilayah`;

  const btns = document.getElementById('page-btns');
  let html = '';
  if(currentPage>1)     html+=`<button class="page-btn" onclick="goPage(${currentPage-1})">‹</button>`;
  for(let p=Math.max(1,currentPage-2);p<=Math.min(pages,currentPage+2);p++){
    html+=`<button class="page-btn ${p===currentPage?'active':''}" onclick="goPage(${p})">${p}</button>`;
  }
  if(currentPage<pages) html+=`<button class="page-btn" onclick="goPage(${currentPage+1})">›</button>`;
  btns.innerHTML = html;
}

function goPage(p) {
  currentPage = p;
  renderTable();
  window.scrollTo({top:document.getElementById('table-body').offsetTop-120,behavior:'smooth'});
}

// ═══════════ MODAL ═══════════
async function openModal(kode) {
  const row = allData.find(d=>d.kode_kabupaten_kota==kode);
  if (!row) return;

  const kpdHa    = row.kepadatan ? (+row.kepadatan/100).toLocaleString('id',{maximumFractionDigits:2}) : '–';
  const kategori = getKategoriHa(row.kepadatan);

  document.getElementById('modal-title').textContent = row.nama_kabupaten_kota;
  document.getElementById('modal-stats').innerHTML = `
    <div class="modal-stat"><div class="modal-stat-val">${fmt(row.total_penduduk)}</div><div class="modal-stat-label">Total Penduduk</div></div>
    <div class="modal-stat"><div class="modal-stat-val" style="color:#3B82F6">${fmt(row.laki_laki)}</div><div class="modal-stat-label">Laki-laki</div></div>
    <div class="modal-stat"><div class="modal-stat-val" style="color:#EC4899">${fmt(row.perempuan)}</div><div class="modal-stat-label">Perempuan</div></div>
    <div class="modal-stat"><div class="modal-stat-val">${row.luas_km2 ? fmt2(row.luas_km2) : '–'}</div><div class="modal-stat-label">Luas (km²)</div></div>
    <div class="modal-stat"><div class="modal-stat-val" style="color:#0891B2">${fmt2(row.kepadatan)}</div><div class="modal-stat-label">Jiwa/km²</div></div>
    <div class="modal-stat"><div class="modal-stat-val" style="color:#6366F1">${kpdHa}</div><div class="modal-stat-label">Jiwa/ha</div></div>
  `;

  const kpdHaNum = +row.kepadatan / 100;
  let insightEmoji, insightText;
  if (kategori === 'Sangat Padat') {
    insightEmoji = '⚠️'; insightText = `Wilayah ${row.nama_kabupaten_kota} masuk kategori <b>Sangat Padat</b> dengan kepadatan ${kpdHa} jiwa/ha. Perlu pengelolaan tata ruang yang ketat.`;
  } else if (kategori === 'Tinggi') {
    insightEmoji = '📊'; insightText = `${row.nama_kabupaten_kota} termasuk wilayah dengan kepadatan <b>Tinggi</b> (${kpdHa} jiwa/ha). Perlu perhatian pada infrastruktur dan layanan publik.`;
  } else if (kategori === 'Sedang') {
    insightEmoji = '🟡'; insightText = `${row.nama_kabupaten_kota} termasuk wilayah <b>Sedang</b> dengan kepadatan ${kpdHa} jiwa/ha. Masih dalam batas yang dapat dikelola dengan baik.`;
  } else {
    insightEmoji = '✅'; insightText = `${row.nama_kabupaten_kota} termasuk wilayah <b>Tidak Padat</b> dengan kepadatan ${kpdHa} jiwa/ha. Potensi besar untuk pengembangan wilayah.`;
  }
  document.getElementById('modal-insight').innerHTML = `${insightEmoji} ${insightText}`;
  document.getElementById('modal-detail').classList.add('open');

  try {
    const res   = await fetch(`/api/kepadatan/trend?kode=${kode}`);
    const json  = await res.json();
    const trend = json.trend || [];
    if (chartModalTrend) chartModalTrend.destroy();
    chartModalTrend = new ApexCharts(document.getElementById('chart-modal-trend'), {
      chart: { type:'area', height:200, toolbar:{show:false}, animations:{speed:500} },
      series: [
        { name:'Kepadatan (jiwa/km²)', data: trend.map(d=>+d.kepadatan) },
        { name:'Total Jiwa',           data: trend.map(d=>+d.total_penduduk) }
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
  if (chartModalTrend) { chartModalTrend.destroy(); chartModalTrend=null; }
}

document.getElementById('modal-detail').addEventListener('click', function(e){
  if (e.target===this) closeModal();
});
</script>
@endpush