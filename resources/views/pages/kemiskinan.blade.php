@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
<style>
/* ── RESET ─────────────────────────────── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body,html{font-family:'DM Sans',sans-serif;background:#FAFAF8;color:#1C1917;}

/* ── DESIGN TOKENS ─────────────────────── */
:root{
  /* Palette – warm neutral base */
  --ink:       #1C1917;
  --ink-2:     #44403C;
  --ink-3:     #78716C;
  --ink-4:     #A8A29E;
  --surface:   #FFFFFF;
  --bg:        #FAFAF8;
  --bg-2:      #F5F4F0;
  --line:      rgba(28,25,23,.08);
  --line-2:    rgba(28,25,23,.14);

  /* Accent – vermilion (punchy, not basic red) */
  --acc:       #D63B1F;
  --acc-lt:    #FDF1EE;
  --acc-md:    #F9C9BF;
  --acc-dk:    #A02D16;

  /* Positive – sage green */
  --pos:       #2D7D53;
  --pos-lt:    #EEF7F2;
  --pos-md:    #B5DFC9;
  --pos-dk:    #1F5C3A;

  /* Caution – warm amber */
  --cau:       #C47B0A;
  --cau-lt:    #FDF6E8;
  --cau-md:    #F8DFA0;

  /* Info – slate blue */
  --inf:       #3456A8;
  --inf-lt:    #EEF2FB;
  --inf-md:    #BCC9F0;

  /* Geometry */
  --r:  10px;
  --r2: 6px;
  --r3: 16px;

  /* Shadow – very subtle, warm-tinted */
  --sh: 0 1px 2px rgba(28,25,23,.04), 0 4px 12px rgba(28,25,23,.06);
  --sh2:0 8px 24px rgba(28,25,23,.1);
}

/* ── TYPE SYSTEM ───────────────────────── */
.f-display{font-family:'Sora',sans-serif;}
.f-mono   {font-family:'DM Mono',monospace;}

/* ── TOPBAR ────────────────────────────── */


.yr-sel:focus{
  border-color:var(--acc);
  box-shadow:0 0 0 3px rgba(214,59,31,.12);
}

/* ── PAGE BODY ─────────────────────────── */
.page{padding:32px 36px 96px;max-width:1440px;margin:0 auto;}

/* ── SECTION DIVIDER ───────────────────── */
.sec{margin:40px 0 20px;display:flex;align-items:center;gap:16px;}
.sec-rule{flex:1;height:1px;background:var(--line);}
.sec-q{
  font-family:'Sora',sans-serif;font-size:16px;font-weight:600;
  color:var(--ink);white-space:nowrap;
}
.sec-tag{
  font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;
  padding:4px 10px;border-radius:99px;white-space:nowrap;
}
.sec-tag.acc  {background:var(--acc-lt);color:var(--acc-dk);}
.sec-tag.pos  {background:var(--pos-lt);color:var(--pos-dk);}
.sec-tag.inf  {background:var(--inf-lt);color:var(--inf);}

/* ── KPI STRIP ─────────────────────────── */
.kpi-strip{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
@media(max-width:768px){.kpi-strip{grid-template-columns:1fr;}}

.kpi{
  background:var(--surface);border:1px solid var(--line);border-radius:var(--r3);
  padding:24px;position:relative;overflow:hidden;transition:box-shadow .2s,transform .2s;
}
.kpi:hover{box-shadow:var(--sh2);transform:translateY(-2px);}

/* Left border accent instead of top stripe */
.kpi.v-acc {border-left:3px solid var(--acc);}
.kpi.v-pos {border-left:3px solid var(--pos);}
.kpi.v-cau {border-left:3px solid var(--cau);}

.kpi-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;}
.kpi-icon{
  width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;
  font-size:17px;
}
.kpi.v-acc .kpi-icon{background:var(--acc-lt);}
.kpi.v-pos .kpi-icon{background:var(--pos-lt);}
.kpi.v-cau .kpi-icon{background:var(--cau-lt);}

.kpi-num-label{
  font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;
  padding:3px 8px;border-radius:4px;
}
.kpi.v-acc .kpi-num-label{background:var(--acc-lt);color:var(--acc-dk);}
.kpi.v-pos .kpi-num-label{background:var(--pos-lt);color:var(--pos-dk);}
.kpi.v-cau .kpi-num-label{background:var(--cau-lt);color:var(--cau);}

.kpi-lbl{font-size:16px;font-weight:600;letter-spacing:.4px;text-transform:uppercase;color:var(--ink-3);margin-bottom:6px;}
.kpi-val{
  font-family:'Sora',sans-serif;font-size:40px;font-weight:700;
  color:var(--ink);line-height:1;margin-bottom:5px;
}
.kpi.v-acc .kpi-val{color:var(--acc);}
.kpi.v-pos .kpi-val{color:var(--pos);}
.kpi.v-cau .kpi-val{color:var(--cau);}
.kpi-sub{font-size:14px;color:var(--ink-4);}

/* ── CARD ──────────────────────────────── */
.card{
  background:var(--surface);border:1px solid var(--line);
  border-radius:var(--r3);padding:24px;
}
.card-hd{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:18px;}
.card-meta{font-size:10px;font-weight:600;letter-spacing:.4px;text-transform:uppercase;color:var(--ink-4);margin-bottom:4px;}
.card-title{font-family:'Sora',sans-serif;font-size:14px;font-weight:600;color:var(--ink);margin-bottom:3px;}
.card-desc{font-size:11px;color:var(--ink-3);line-height:1.6;}
.card-badge{
  flex-shrink:0;font-size:10px;font-weight:700;padding:4px 10px;border-radius:6px;
  font-family:'DM Mono',monospace;white-space:nowrap;
}
.card-badge.acc  {background:var(--acc-lt);color:var(--acc-dk);}
.card-badge.pos  {background:var(--pos-lt);color:var(--pos-dk);}
.card-badge.cau  {background:var(--cau-lt);color:var(--cau);}
.card-badge.inf  {background:var(--inf-lt);color:var(--inf);}

/* ── GRID LAYOUTS ──────────────────────── */
.g2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
@media(max-width:960px){.g2{grid-template-columns:1fr;}}

/* ── CHART HEIGHTS ─────────────────────── */
.h-sm{height:180px;position:relative;}
.h-md{height:250px;position:relative;}
.h-lg{height:320px;position:relative;}
.h-map{height:480px;border-radius:12px;overflow:hidden;position:relative;}

/* ── LEGEND ────────────────────────────── */
.lgd{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:500;color:var(--ink-2);}
.ldot{width:8px;height:8px;border-radius:2px;flex-shrink:0;}

/* ── TAB BUTTONS ───────────────────────── */
.tab-btn{
  padding:6px 14px;font-size:11px;font-weight:600;border-radius:7px;cursor:pointer;
  border:1px solid var(--line-2);background:var(--bg-2);color:var(--ink-3);
  transition:all .14s;font-family:'DM Sans',sans-serif;
}
.tab-btn.is-acc{background:var(--acc-lt);color:var(--acc-dk);border-color:var(--acc-md);}
.tab-btn.is-pos{background:var(--pos-lt);color:var(--pos-dk);border-color:var(--pos-md);}

/* ── RANK TABLE ────────────────────────── */
.rk-no{
  width:24px;height:24px;border-radius:50%;display:inline-flex;
  align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;
}
.rk-1{background:#FEF0C7;color:#92400E;}
.rk-2{background:#F1F5F9;color:#334155;}
.rk-3{background:var(--acc-lt);color:var(--acc-dk);}
.rk-n{background:var(--bg-2);color:var(--ink-4);}
.arr-up{color:var(--acc);font-weight:700;font-size:11px;}
.arr-dn{color:var(--pos);font-weight:700;font-size:11px;}
.arr-eq{color:var(--ink-4);font-size:11px;}

/* ── DATA TABLE ────────────────────────── */
.dtbl{width:100%;border-collapse:collapse;font-size:12px;}
.dtbl thead tr{background:var(--bg-2);}
.dtbl thead th{
  padding:10px 14px;font-size:9px;font-weight:700;text-transform:uppercase;
  letter-spacing:.8px;color:var(--ink-4);text-align:right;white-space:nowrap;
}
.dtbl thead th:first-child,.dtbl thead th:nth-child(2){text-align:left;}
.dtbl tbody tr{border-bottom:1px solid var(--bg-2);transition:background .1s;}
.dtbl tbody tr:hover{background:var(--acc-lt);}
.dtbl td{padding:10px 14px;vertical-align:middle;}
.td-no  {color:var(--ink-4);font-family:'DM Mono',monospace;font-size:10px;}
.td-nm  {font-weight:600;color:var(--ink);}
.td-v25 {text-align:right;font-weight:700;color:var(--acc);font-family:'DM Mono',monospace;}
.td-v23 {text-align:right;color:var(--ink-4);font-family:'DM Mono',monospace;}
.td-dff {text-align:right;font-weight:700;font-family:'DM Mono',monospace;}
.td-dff.up{color:var(--acc);}
.td-dff.dn{color:var(--pos);}
.td-sts {text-align:center;}

/* ── STATUS BADGE ──────────────────────── */
.sts-badge{display:inline-block;padding:3px 9px;border-radius:99px;font-size:10px;font-weight:600;}
.sts-badge.atas  {background:var(--acc-lt);color:var(--acc-dk);}
.sts-badge.bawah {background:var(--pos-lt);color:var(--pos-dk);}
.sts-badge.th25  {background:var(--inf-lt);color:var(--inf);}
.sts-badge.th23  {background:var(--cau-lt);color:var(--cau);}

/* ── SEARCH ────────────────────────────── */
.srch-wrap{position:relative;}
.srch-ico{position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;}
.srch-inp{
  padding:8px 12px 8px 32px;border:1px solid var(--line-2);border-radius:8px;
  font-size:12px;font-family:'DM Sans',sans-serif;color:var(--ink);width:200px;
  outline:none;transition:border-color .15s;background:var(--surface);
}
.srch-inp:focus{border-color:var(--acc);box-shadow:0 0 0 3px rgba(214,59,31,.07);}

/* ── PAGINATION ────────────────────────── */
.pagi-wrap{
  display:flex;align-items:center;justify-content:space-between;gap:12px;
  flex-wrap:wrap;margin-top:16px;padding-top:14px;border-top:1px solid var(--line);
}
.pagi-info{font-size:11px;color:var(--ink-4);}
.pagi-btns{display:flex;gap:4px;}
.pagi-btn{
  width:30px;height:30px;border-radius:7px;border:1px solid var(--line-2);
  background:var(--surface);color:var(--ink-2);font-size:11px;font-weight:600;
  cursor:pointer;display:flex;align-items:center;justify-content:center;
  transition:all .14s;font-family:'DM Sans',sans-serif;
}
.pagi-btn:hover{border-color:var(--acc);color:var(--acc);}
.pagi-btn.active{background:var(--acc);color:#fff;border-color:var(--acc);}
.pagi-btn:disabled{opacity:.3;cursor:not-allowed;}

/* ── MODAL OVERLAY ─────────────────────── */
.drill-ov{
  display:none;position:fixed;inset:0;z-index:9000;
  background:rgba(28,25,23,.5);backdrop-filter:blur(6px);
  align-items:center;justify-content:center;
}
.drill-ov.show{display:flex;animation:ov-in .18s ease;}
@keyframes ov-in{from{opacity:0;}to{opacity:1;}}

.drill-card{
  background:var(--surface);border:1px solid var(--line);border-radius:20px;
  width:400px;max-width:calc(100vw - 32px);overflow:hidden;
  box-shadow:0 40px 100px rgba(28,25,23,.25),0 4px 16px rgba(28,25,23,.1);
  animation:card-in .22s cubic-bezier(.22,1,.36,1);
}
@keyframes card-in{
  from{opacity:0;transform:scale(.93) translateY(12px);}
  to  {opacity:1;transform:scale(1)   translateY(0);}
}

.drill-top{
  padding:20px 22px 18px;border-bottom:1px solid var(--line);
  display:flex;align-items:flex-start;justify-content:space-between;gap:12px;
}
.drill-top.t-acc{background:var(--acc-lt);border-bottom-color:var(--acc-md);}
.drill-top.t-inf{background:var(--inf-lt);border-bottom-color:var(--inf-md);}
.drill-top.t-pos{background:var(--pos-lt);border-bottom-color:var(--pos-md);}

.drill-eyebrow{
  font-size:9px;font-weight:700;letter-spacing:.9px;text-transform:uppercase;
  display:flex;align-items:center;gap:5px;margin-bottom:5px;
}
.t-acc .drill-eyebrow{color:var(--acc-dk);}
.t-inf .drill-eyebrow{color:var(--inf);}
.t-pos .drill-eyebrow{color:var(--pos-dk);}

.drill-eyebrow-dot{width:6px;height:6px;border-radius:50%;}
.t-acc .drill-eyebrow-dot{background:var(--acc);}
.t-inf .drill-eyebrow-dot{background:var(--inf);}
.t-pos .drill-eyebrow-dot{background:var(--pos);}

.drill-title{font-family:'Sora',sans-serif;font-size:17px;font-weight:700;color:var(--ink);}
.drill-sub  {font-size:11px;color:var(--ink-3);margin-top:2px;}

.drill-close{
  width:28px;height:28px;border-radius:7px;border:1px solid var(--line-2);
  background:rgba(255,255,255,.6);color:var(--ink-3);font-size:13px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;transition:all .12s;flex-shrink:0;
}
.drill-close:hover{background:var(--surface);color:var(--ink);}

.drill-body{padding:18px 22px;display:flex;flex-direction:column;gap:12px;}
.drill-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}

.drill-stat{background:var(--bg-2);border:1px solid var(--line);border-radius:10px;padding:14px;}
.drill-stat-lbl{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--ink-4);margin-bottom:6px;}
.drill-stat-val{font-family:'Sora',sans-serif;font-size:26px;font-weight:700;line-height:1;margin-bottom:3px;}
.drill-stat-sub{font-size:10px;color:var(--ink-4);}
.c-acc .drill-stat-val{color:var(--acc);}
.c-pos .drill-stat-val{color:var(--pos);}
.c-inf .drill-stat-val{color:var(--inf);}
.c-dk  .drill-stat-val{color:var(--ink);}

.drill-hl{border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;}
.drill-hl.hl-acc{background:var(--acc-lt);border:1px solid var(--acc-md);}
.drill-hl.hl-pos{background:var(--pos-lt);border:1px solid var(--pos-md);}
.drill-hl.hl-inf{background:var(--inf-lt);border:1px solid var(--inf-md);}
.drill-hl.hl-cau{background:var(--cau-lt);border:1px solid var(--cau-md);}
.drill-hl-txt{font-size:12px;font-weight:500;color:var(--ink-2);line-height:1.6;}
.drill-hl-num{font-family:'Sora',sans-serif;font-size:34px;font-weight:700;white-space:nowrap;flex-shrink:0;}
.hl-acc .drill-hl-num{color:var(--acc);}
.hl-pos .drill-hl-num{color:var(--pos);}
.hl-inf .drill-hl-num{color:var(--inf);}
.hl-cau .drill-hl-num{color:var(--cau);}

/* ── LEAFLET OVERRIDES ─────────────────── */
.leaflet-popup-content-wrapper{border-radius:14px!important;padding:0!important;border:none!important;overflow:hidden;box-shadow:0 20px 60px rgba(28,25,23,.22)!important;font-family:'DM Sans',sans-serif!important;}
.leaflet-popup-tip-container{display:none!important;}
.leaflet-popup-content{margin:0!important;}
.leaflet-popup-close-button{top:12px!important;right:12px!important;color:rgba(255,255,255,.8)!important;font-size:16px!important;z-index:10;font-weight:400!important;}
.leaflet-tooltip{background:var(--ink)!important;border:none!important;color:#F5F4F0!important;font-family:'DM Sans',sans-serif!important;font-size:11px!important;font-weight:600!important;border-radius:7px!important;padding:5px 10px!important;box-shadow:0 4px 16px rgba(28,25,23,.2)!important;}
.leaflet-tooltip::before{display:none!important;}
.leaflet-control-zoom{border:none!important;box-shadow:var(--sh)!important;border-radius:9px!important;overflow:hidden;}
.leaflet-control-zoom-in,.leaflet-control-zoom-out{font-family:'DM Sans',sans-serif!important;font-weight:600!important;color:var(--ink-2)!important;border:none!important;width:30px!important;height:30px!important;line-height:30px!important;}
</style>
@endpush

@section('content')
@php
  $labelTahun = $selectedYear == 'semua' ? '2023 & 2025' : $selectedYear;
  $isSemua    = $selectedYear == 'semua';
@endphp

<div style="background:var(--bg);min-height:100vh;">

  {{-- ══ TOPBAR ══ --}}
{{-- ══ TOPBAR ══ --}}
<div class="flex items-center justify-between flex-wrap gap-4 px-10 py-4 bg-white border-b sticky top-0 z-20"
     style="border-color: rgba(28,25,23,.08);">

  {{-- LEFT --}}
  <div class="flex items-start gap-4">
    {{-- TEXT --}}
    <div class="flex flex-col">

      <div class="flex items-center gap-2 text-[11.5px] font-semibold mb-1">
        <span style="color:#D63B1F;">Dashboard</span>

        <span style="color:#D6D3D1;">/</span>

        <span style="color:#78716C;">
          Kemiskinan & Ketenagakerjaan
        </span>
      </div>

      <div
        class="font-extrabold tracking-tight leading-none"
        style="
          font-family:'Sora',sans-serif;
          font-size:18px;
          color:#1C1917;
        "
      >
        Analisis Kemiskinan — Jawa Timur
      </div>

      <div
        class="text-[11.5px] mt-1"
        style="color:#A8A29E;"
      >
        Data kemiskinan regional · Tahun {{ $labelTahun }}
      </div>

    </div>

  </div>

  {{-- RIGHT --}}
  <div class="flex items-center gap-2 flex-wrap">

    {{-- CHIP --}}
    <span
      class="text-[11px] font-bold px-3 py-[7px] rounded-full"
      style="
        background:#FDF1EE;
        color:#A02D16;
        border:1px solid #F9C9BF;
      "
    >
      {{ $labelTahun }}
    </span>

    <span
      class="text-[11px] font-bold px-3 py-[7px] rounded-full"
      style="
        background:#EEF2FB;
        color:#3456A8;
        border:1px solid #BCC9F0;
      "
    >
      38 Kab/Kota
    </span>

    {{-- SELECT --}}
    <form method="GET" action="{{ url()->current() }}">
      <div class="relative">

        <select
          name="tahun"
          onchange="this.form.submit()"
          class="rounded-[10px] py-2 pl-4 pr-10 text-[13px] font-bold outline-none transition-all"
          style="
            background:#FDF1EE;
            border:1.5px solid #F9C9BF;
            color:#A02D16;
            font-family:'Sora',sans-serif;
          "
        >
          <option value="2025" {{ $selectedYear=='2025'?'selected':'' }}>2025</option>
          <option value="2023" {{ $selectedYear=='2023'?'selected':'' }}>2023</option>
          <option value="semua" {{ $selectedYear=='semua'?'selected':'' }}>Semua</option>
        </select>

        <svg
          class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"
          width="12"
          height="12"
          viewBox="0 0 24 24"
          fill="none"
          stroke="#D63B1F"
          stroke-width="2.5"
        >
          <polyline points="6 9 12 15 18 9"/>
        </svg>

      </div>
    </form>

  </div>
</div>

  <div class="page">

    {{-- KPI STRIP --}}
    <div class="sec" style="margin-top:4px;">
      <div class="sec-rule"></div>
      <div class="sec-q">Gambaran kemiskinan Jawa Timur</div>
      <div class="sec-rule"></div>
    </div>

    <div class="kpi-strip">
      <div class="kpi v-acc">
        <div class="kpi-header">
          <div class="kpi-icon">📊</div>
          <span class="kpi-num-label">Rata-rata</span>
        </div>
        <div class="kpi-lbl">Rata-rata Kemiskinan</div>
        <div class="kpi-val f-display">{{ number_format($rataRata2025/1000,2,',','.') }}</div>
        <div class="kpi-sub">ribu jiwa </div>
      </div>
      <div class="kpi v-pos">
        <div class="kpi-header">
          <div class="kpi-icon">✅</div>
          <span class="kpi-num-label">Kondisi Baik</span>
        </div>
        <div class="kpi-lbl">Di Bawah Rata-rata</div>
        <div class="kpi-val f-display">{{ $diBawahRataRata }}</div>
        <div class="kpi-sub">dari {{ $totalKab }}</div>
      </div>
      <div class="kpi v-cau">
        <div class="kpi-header">
          <div class="kpi-icon">⚠️</div>
          <span class="kpi-num-label">Perlu Perhatian</span>
        </div>
        <div class="kpi-lbl">Di Atas Rata-rata</div>
        <div class="kpi-val f-display">{{ $diAtasRataRata }}</div>
        <div class="kpi-sub">dari {{ $totalKab }}</div>
      </div>
    </div>

    {{-- ANALISIS WILAYAH --}}
    <div class="sec">
      <div class="sec-rule"></div>
      <div class="sec-q">Wilayah mana paling terdampak?</div>
      <div class="sec-rule"></div>
    </div>

    <div class="g2" style="margin-bottom:14px;">
      <div class="card">
        <div class="card-hd">
          <div>
            <div class="card-meta">Grafik 5 · {{ $isSemua ? '38 Kab/Kota × 2 Tahun' : '38 Kab/Kota' }}</div>
            <div class="card-title">Distribusi vs Rata-rata Jatim</div>
          </div>
          <span class="card-badge acc">nilai vs rata-rata</span>
        </div>
        <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:14px;">
          <div class="lgd"><span class="ldot" style="background:var(--acc);"></span>Di atas rata-rata ({{ $diAtasRataRata }})</div>
          <div class="lgd"><span class="ldot" style="background:var(--pos);"></span>Di bawah rata-rata ({{ $diBawahRataRata }})</div>
          <div class="lgd">
            <span style="display:inline-block;width:18px;border-top:2px dashed var(--cau);margin-top:5px;"></span>
            Rata-rata Jatim
          </div>
        </div>
        <div class="h-lg"><canvas id="cSemuaKab"></canvas></div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div>
            <div class="card-meta">Grafik 6 · Ranking</div>
            <div class="card-title">Top 5 Tertinggi & Terendah</div>
            <div class="card-desc">Klik untuk melihat perbandingan detail antar wilayah.</div>
          </div>
          <span class="card-badge inf">ranking</span>
        </div>
        <div style="display:flex;gap:8px;margin-bottom:14px;">
          <button class="tab-btn is-acc" id="btnT" onclick="showTop('tertinggi')">↑ 5 Tertinggi</button>
          <button class="tab-btn"        id="btnR" onclick="showTop('terendah')">↓ 5 Terendah</button>
        </div>
        <div class="h-md"><canvas id="cTop5"></canvas></div>
      </div>
    </div>

    <div class="g2" style="margin-bottom:14px;">
      <div class="card">
        <div class="card-hd">
          <div>
            <div class="card-meta">Grafik 7 · Deviasi</div>
            <div class="card-title">Jarak dari Rata-rata Jatim</div>
            <div class="card-desc">
              <span style="color:var(--acc);font-weight:600;">Merah</span> = di atas rata-rata ·
              <span style="color:var(--pos);font-weight:600;">Hijau</span> = di bawah rata-rata
            </div>
          </div>
          <span class="card-badge cau">nilai − rata</span>
        </div>
        <div class="h-lg"><canvas id="cDeviasi"></canvas></div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div>
            <div class="card-meta">Perubahan Posisi · Top 8</div>
            <div class="card-title">Perubahan Ranking 2023 vs 2025</div>
          </div>
        </div>
        <div style="overflow-x:auto;">
          <table class="dtbl">
            <thead><tr>
              <th style="text-align:center;">#</th>
              <th style="text-align:left;">Kab/Kota</th>
              <th>2025 (rb)</th>
              <th>2023 (rb)</th>
              <th style="text-align:center;">Δ</th>
            </tr></thead>
            <tbody>
              @foreach($rankingGabung as $r)
              <tr>
                <td style="text-align:center;">
                  <span class="rk-no {{ $r->rank2025==1?'rk-1':($r->rank2025==2?'rk-2':($r->rank2025==3?'rk-3':'rk-n')) }}">{{ $r->rank2025 }}</span>
                </td>
                <td class="td-nm">{{ $r->kab }}</td>
                <td class="td-v25">{{ number_format($r->nilai2025/1000,2,',','.') }}</td>
                <td class="td-v23">{{ $r->nilai2023 ? number_format($r->nilai2023/1000,2,',','.') : '–' }}</td>
                <td style="text-align:center;">
                  @if($r->perubahan===null)<span class="arr-eq">–</span>
                  @elseif($r->perubahan>0)<span class="arr-up">↑ +{{ $r->perubahan }}</span>
                  @elseif($r->perubahan<0)<span class="arr-dn">↓ {{ $r->perubahan }}</span>
                  @else<span class="arr-eq">→</span>@endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div style="margin-top:10px;font-size:10px;color:var(--ink-4);font-style:italic;">
          ↑ naik rank = posisi kemiskinan makin tinggi · ↓ turun = membaik relatif
        </div>
      </div>
    </div>

    {{-- PETA --}}
    <div class="card" style="margin-bottom:14px;">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:18px;">
        <div>
          <div class="card-meta">Grafik 8 · Peta Interaktif</div>
          <div class="card-title">Sebaran Kemiskinan — Jawa Timur {{ $labelTahun }}</div>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
          @foreach([['#7F1D1D','Sangat Tinggi'],['#D63B1F','Tinggi'],['#F87171','Sedang'],['#86EFAC','Rendah'],['#2D7D53','Sangat Rendah']] as [$c,$l])
          <div style="display:flex;align-items:center;gap:5px;font-size:10.5px;font-weight:500;color:var(--ink-2);">
            <span style="width:9px;height:9px;border-radius:50%;background:{{ $c }};display:inline-block;"></span>{{ $l }}
          </div>
          @endforeach
        </div>
      </div>
      <div class="h-map" id="mapJatim"></div>
    </div>

    {{-- KETENAGAKERJAAN --}}
    <div class="sec">
      <div class="sec-rule"></div>
      <div class="sec-q">Kelompok usia mana paling rentan di pasar kerja?</div>
      <div class="sec-rule"></div>
    </div>

    <div class="g2" style="margin-bottom:14px;">
      <div class="card">
        <div class="card-hd">
          <div>
            <div class="card-meta">Grafik 9 · Ketenagakerjaan ({{ $labelTahun }})</div>
            <div class="card-title">% Belum Dapat Kerja per Kelompok Usia</div>
            <div class="card-desc">Klik bar untuk detail lengkap.</div>
          </div>
          <span class="card-badge acc">% belum kerja</span>
        </div>
        <div class="h-md"><canvas id="cPengUsia"></canvas></div>
      </div>

      <div class="card">
        <div class="card-hd">
          <div>
            <div class="card-meta">Grafik 10 · 2023 vs 2025</div>
            <div class="card-title">% Bekerja per Kelompok Usia</div>
            <div class="card-desc">Batang biru lebih tinggi = ada perbaikan. Klik untuk perbandingan detail.</div>
          </div>
          <span class="card-badge inf">T3 & T4 · BPS</span>
        </div>
        <div style="display:flex;gap:16px;margin-bottom:14px;">
          <div class="lgd"><span class="ldot" style="background:var(--inf);"></span>2025</div>
          <div class="lgd"><span class="ldot" style="background:#94A3B8;"></span>2023</div>
        </div>
        <div class="h-md"><canvas id="cPctBek"></canvas></div>
      </div>
    </div>

    {{-- TABEL DATA LENGKAP --}}
    <div class="sec">
      <div class="sec-tag pos">Data Lengkap</div>
      <div class="sec-rule"></div>
      <div class="sec-q">38 Kab/Kota — {{ $labelTahun }}</div>
    </div>

    <div class="card">
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--line);">
        <div>
          <div style="font-family:'Sora',sans-serif;font-size:14px;font-weight:600;color:var(--ink);">
            Tabel Kemiskinan Per Kabupaten / Kota — {{ $labelTahun }}
          </div>
          <div style="font-size:11px;color:var(--ink-4);margin-top:3px;">
            Satuan: jiwa · Rata-rata Jatim:
            <strong style="color:var(--ink-2);font-weight:600;">{{ number_format($rataRata2025,0,',','.') }} jiwa</strong>
            @if($isSemua)
              <span style="margin-left:8px;font-size:9px;background:var(--inf-lt);color:var(--inf);padding:2px 8px;border-radius:99px;font-weight:700;">2023 & 2025</span>
            @endif
          </div>
        </div>
        <div class="srch-wrap">
          <svg class="srch-ico" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--ink-4)" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="srchKab" class="srch-inp" placeholder="Cari kabupaten / kota…">
        </div>
      </div>

      <div style="overflow-x:auto;">
        <table class="dtbl" id="tblKemiskinan">
          <thead>
            <tr>
              <th style="text-align:left;">#</th>
              @if($isSemua)<th style="text-align:center;">Tahun</th>@endif
              <th style="text-align:left;">Kabupaten / Kota</th>
              <th>Jiwa Miskin</th>
              @if(!$isSemua)
                <th>vs {{ $selectedYear=='2025'?'2023':'2025' }}</th>
                <th>Δ Perubahan</th>
              @endif
              <th style="text-align:center;">Status</th>
            </tr>
          </thead>
          <tbody id="tblBody">
            @if($isSemua)
              @foreach($dataMiskin->sortByDesc('jumlahPnddk')->values() as $i => $r)
              @php $ata = $r->jumlahPnddk > $rataRata2025; @endphp
              <tr>
                <td class="td-no">{{ $i+1 }}</td>
                <td class="td-sts"><span class="sts-badge {{ isset($r->tahun)&&$r->tahun==2025?'th25':'th23' }}">{{ $r->tahun ?? '-' }}</span></td>
                <td class="td-nm">{{ $r->kabupatenKota }}</td>
                <td class="td-v25">{{ number_format($r->jumlahPnddk,0,',','.') }}</td>
                <td class="td-sts"><span class="sts-badge {{ $ata?'atas':'bawah' }}">{{ $ata?'Di atas':'Di bawah' }}</span></td>
              </tr>
              @endforeach
            @else
              @foreach($dataMiskin->sortByDesc('jumlahPnddk')->values() as $i => $r)
              @php
                $d_lain = $selectedYear == '2025'
                  ? $dataMiskin2023->firstWhere('kabupatenKota', $r->kabupatenKota)
                  : $dataMiskin2025->firstWhere('kabupatenKota', $r->kabupatenKota);
                $v_lain = $d_lain ? $d_lain->jumlahPnddk : null;
                $sel    = $v_lain !== null ? $r->jumlahPnddk - $v_lain : null;
                $ata    = $r->jumlahPnddk > $rataRata2025;
              @endphp
              <tr>
                <td class="td-no">{{ $i+1 }}</td>
                <td class="td-nm">{{ $r->kabupatenKota }}</td>
                <td class="td-v25">{{ number_format($r->jumlahPnddk,0,',','.') }}</td>
                <td class="td-v23">{{ $v_lain !== null ? number_format($v_lain,0,',','.') : '–' }}</td>
                <td class="td-dff {{ $sel!==null?($sel>0?'up':($sel<0?'dn':'')):''; }}">
                  @if($sel!==null){{ $sel>0?'+':'' }}{{ number_format($sel,0,',','.') }}@else –@endif
                </td>
                <td class="td-sts"><span class="sts-badge {{ $ata?'atas':'bawah' }}">{{ $ata?'Di atas':'Di bawah' }}</span></td>
              </tr>
              @endforeach
            @endif
          </tbody>
        </table>
      </div>
      <div class="pagi-wrap" id="pagiWrap">
        <div class="pagi-info" id="pagiInfo"></div>
        <div class="pagi-btns" id="pagiBtns"></div>
      </div>
    </div>

  </div>{{-- /page --}}
</div>

{{-- ── MODALS ─────────────────────────────────────── --}}

{{-- Modal Chart 9 --}}
<div class="drill-ov" id="drillPengOv" role="dialog" aria-modal="true" aria-labelledby="dPengTitle">
  <div class="drill-card">
    <div class="drill-top t-acc">
      <div>
        <div class="drill-eyebrow">
          <span class="drill-eyebrow-dot"></span>
          <span id="dPengEyebrow">Ketenagakerjaan</span>
        </div>
        <div class="drill-title" id="dPengTitle">—</div>
        <div class="drill-sub"   id="dPengSub">—</div>
      </div>
      <button class="drill-close" onclick="closeDrill('drillPengOv')" aria-label="Tutup">✕</button>
    </div>
    <div class="drill-body">
      <div class="drill-row">
        <div class="drill-stat c-dk">
          <div class="drill-stat-lbl">Angkatan Kerja</div>
          <div class="drill-stat-val" id="dPengAK">—</div>
          <div class="drill-stat-sub">aktif mencari kerja</div>
        </div>
        <div class="drill-stat c-acc">
          <div class="drill-stat-lbl">Pengangguran</div>
          <div class="drill-stat-val" id="dPengJml">—</div>
          <div class="drill-stat-sub">belum mendapat kerja</div>
        </div>
      </div>
      <div class="drill-hl hl-acc" id="dPengHL">
        <div class="drill-hl-txt" id="dPengKalimat">—</div>
        <div class="drill-hl-num" id="dPengPct">—</div>
      </div>
    </div>
  </div>
</div>

{{-- Modal Chart 10 --}}
<div class="drill-ov" id="drillBekOv" role="dialog" aria-modal="true" aria-labelledby="dBekTitle">
  <div class="drill-card">
    <div class="drill-top t-inf">
      <div>
        <div class="drill-eyebrow">
          <span class="drill-eyebrow-dot"></span>
          Perbandingan Dua Tahun
        </div>
        <div class="drill-title" id="dBekTitle">—</div>
        <div class="drill-sub">Tingkat keberhasilan kerja dari angkatan kerja</div>
      </div>
      <button class="drill-close" onclick="closeDrill('drillBekOv')" aria-label="Tutup">✕</button>
    </div>
    <div class="drill-body">
      <div class="drill-row">
        <div class="drill-stat c-dk">
          <div class="drill-stat-lbl">% Bekerja 2023</div>
          <div class="drill-stat-val" id="dBek23">—</div>
          <div class="drill-stat-sub">dari angkatan kerja</div>
        </div>
        <div class="drill-stat c-inf">
          <div class="drill-stat-lbl">% Bekerja 2025</div>
          <div class="drill-stat-val" id="dBek25">—</div>
          <div class="drill-stat-sub">dari angkatan kerja</div>
        </div>
      </div>
      <div class="drill-hl hl-inf" id="dBekHL">
        <div class="drill-hl-txt" id="dBekDiffText">Perubahan 2023 → 2025</div>
        <div class="drill-hl-num" id="dBekDiff">—</div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
/* ── CONSTANTS ──────────────────────────── */
const RATA  = {{ $rataRata2025 }};
const TAHUN = '{{ $labelTahun }}';
const dKab  = @json($semuaKab);
const dTop5T= @json($top5Tertinggi);
const dTop5R= @json($top5Terendah);
const dDev  = @json($deviasi);
const dPU   = @json($tingkatPengangguranPerUsia);
const dB25  = @json($pctBekerjaPerUsia2025);
const dB23  = @json($pctBekerjaPerUsia2023);

/* ── HELPERS ────────────────────────────── */
const F  = v => Number(v).toLocaleString('id-ID');
const FD = v => Number(v).toLocaleString('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2});

const FONT = "'DM Sans', sans-serif";
const ACC  = '#D63B1F', ACCA = 'rgba(214,59,31,.14)';
const POS  = '#2D7D53', POSA = 'rgba(45,125,83,.14)';
const INF  = '#3456A8', INFA = 'rgba(52,86,168,.14)';
const CAU  = '#C47B0A';
const GRAY = 'rgba(28,25,23,.06)';

const axX = () => ({ grid:{display:false}, ticks:{font:{size:10,family:FONT},color:'#A8A29E'} });
const axY = cb => ({ grid:{color:GRAY,drawBorder:false}, ticks:{font:{size:10,family:FONT},color:'#A8A29E',callback:cb||(v=>F(v))} });
const TT  = {
  backgroundColor:'#1C1917',titleColor:'#F5F4F0',bodyColor:'#A8A29E',
  padding:12,cornerRadius:10,boxPadding:4,
  titleFont:{size:12,family:FONT,weight:'600'},
  bodyFont:{size:11,family:FONT}
};
const BASE = {responsive:true,maintainAspectRatio:false};

/* ── MODAL ──────────────────────────────── */
function openDrill(id){
  const el=document.getElementById(id);
  el.style.display='flex';el.offsetHeight;el.classList.add('show');
  document.body.style.overflow='hidden';
}
function closeDrill(id){
  const el=document.getElementById(id);
  el.classList.remove('show');
  setTimeout(()=>{el.style.display='none';},200);
  document.body.style.overflow='';
}
document.querySelectorAll('.drill-ov').forEach(ov=>{
  ov.addEventListener('click',function(e){if(e.target===this)closeDrill(this.id);});
});
document.addEventListener('keydown',e=>{
  if(e.key==='Escape'){
    ['drillPengOv','drillBekOv'].forEach(id=>{
      if(document.getElementById(id).classList.contains('show'))closeDrill(id);
    });
  }
});

/* ── CHART 5 : All Kab vs Rata ─────────── */
new Chart(document.getElementById('cSemuaKab'),{
  type:'bar',
  data:{
    labels:dKab.map(r=>r.kabupatenKota),
    datasets:[
      {
        label:'Penduduk Miskin',
        data:dKab.map(r=>r.jumlahPnddk),
        backgroundColor:dKab.map(r=>r.statusRata==='atas'?ACCA:POSA),
        borderColor:    dKab.map(r=>r.statusRata==='atas'?ACC:POS),
        borderWidth:1.5,borderRadius:3,barPercentage:.8,order:2
      },
      {
        label:'Rata-rata Jatim',data:dKab.map(()=>RATA),
        type:'line',borderColor:CAU,borderWidth:2,borderDash:[6,4],
        pointRadius:0,fill:false,tension:0,order:1
      }
    ]
  },
  options:{
    ...BASE,
    plugins:{legend:{display:false},tooltip:{...TT,callbacks:{
      label:ctx=>ctx.datasetIndex===1?'Rata-rata: '+F(RATA)+' jiwa':F(ctx.raw)+' jiwa',
      afterLabel:ctx=>ctx.datasetIndex!==0?'':dKab[ctx.dataIndex].statusRata==='atas'?'⚠ Di atas rata-rata':'✓ Di bawah rata-rata'
    }}},
    scales:{x:axX(),y:axY()}
  }
});

/* ── CHART 6 : Top 5 ───────────────────── */
let c6=null;
function buildTop(mode){
  const d=mode==='tertinggi'?dTop5T:dTop5R;
  const col=mode==='tertinggi'?ACC:POS;
  const colA=mode==='tertinggi'?ACCA:POSA;
  return{
    type:'bar',
    data:{labels:d.map(r=>r.kabupatenKota),datasets:[{label:'Penduduk Miskin',data:d.map(r=>r.jumlahPnddk),backgroundColor:colA,borderColor:col,borderWidth:2,borderRadius:8,barPercentage:.6}]},
    options:{...BASE,indexAxis:'y',
      plugins:{legend:{display:false},tooltip:{...TT,callbacks:{label:ctx=>F(ctx.raw)+' jiwa'}}},
      scales:{x:axY(),y:{grid:{display:false},ticks:{font:{size:11,family:FONT,weight:'600'},color:'#3D4352'}}}
    }
  };
}
function showTop(mode){
  if(c6)c6.destroy();
  c6=new Chart(document.getElementById('cTop5'),buildTop(mode));
  document.getElementById('btnT').className='tab-btn'+(mode==='tertinggi'?' is-acc':'');
  document.getElementById('btnR').className='tab-btn'+(mode==='terendah' ?' is-pos':'');
}
showTop('tertinggi');

/* ── CHART 7 : Deviasi ─────────────────── */
new Chart(document.getElementById('cDeviasi'),{
  type:'bar',
  data:{
    labels:dDev.map(r=>r.kabupatenKota),
    datasets:[{label:'Deviasi',data:dDev.map(r=>r.deviasi),
      backgroundColor:dDev.map(r=>r.deviasi>0?ACCA:POSA),
      borderColor:    dDev.map(r=>r.deviasi>0?ACC:POS),
      borderWidth:1.5,borderRadius:3,barPercentage:.8
    }]
  },
  options:{...BASE,
    plugins:{legend:{display:false},tooltip:{...TT,callbacks:{
      label:ctx=>(ctx.raw>0?'+':'')+F(ctx.raw)+' jiwa dari rata-rata',
      afterLabel:ctx=>dDev[ctx.dataIndex].deviasi>0?'⚠ Di atas rata-rata':'✓ Di bawah rata-rata'
    }}},
    scales:{x:axX(),y:{grid:{color:GRAY},ticks:{font:{size:10,family:FONT},color:'#A8A29E',callback:v=>(v>0?'+':'')+F(v)}}}
  }
});

/* ── CHART 9 : Pengangguran per Usia ───── */
new Chart(document.getElementById('cPengUsia'),{
  type:'bar',
  data:{
    labels:dPU.map(r=>r.usia+' thn'),
    datasets:[{label:'% Belum Dapat Kerja',data:dPU.map(r=>r.pct),
      backgroundColor:dPU.map(r=>r.pct>5?'rgba(214,59,31,.75)':INFA),
      borderColor:    dPU.map(r=>r.pct>5?ACC:INF),
      borderWidth:1.5,borderRadius:8,barPercentage:.65
    }]
  },
  options:{...BASE,
    onClick(e,els){
      if(!els.length)return;
      const r=dPU[els[0].index];
      const pctBulat=Math.round(r.pct);
      const hlCls=r.pct>15?'hl-acc':r.pct>7?'hl-cau':'hl-pos';
      document.getElementById('dPengTitle').textContent='Usia '+r.usia+' tahun';
      document.getElementById('dPengSub').textContent='Data '+TAHUN+' · % belum dapat pekerjaan';
      document.getElementById('dPengAK').textContent=F(r.akJml);
      document.getElementById('dPengJml').textContent=F(r.pengangguranJml);
      document.getElementById('dPengPct').textContent=FD(r.pct)+'%';
      document.getElementById('dPengKalimat').innerHTML='Dari 100 pencari kerja usia ini,<br><strong>'+pctBulat+' orang</strong> belum mendapat pekerjaan.';
      document.getElementById('dPengHL').className='drill-hl '+hlCls;
      openDrill('drillPengOv');
    },
    plugins:{legend:{display:false},tooltip:{...TT,callbacks:{
      title:ctx=>'Usia '+dPU[ctx[0].dataIndex].usia+' tahun',
      label:ctx=>'  '+FD(ctx.raw)+'% belum dapat kerja',
      afterLabel:ctx=>{
        const r=dPU[ctx.dataIndex];
        return['  Pencari kerja: '+F(r.akJml)+' orang','  Pengangguran: '+F(r.pengangguranJml)+' orang',r.pct>5?'  ⚠ Di atas 5%':'  ✓ Di bawah 5%'];
      }
    }}},
    scales:{x:axX(),y:{...axY(v=>v+'%'),title:{display:true,text:'% belum dapat pekerjaan',font:{size:10,family:FONT},color:'#A8A29E'}}}
  }
});

/* ── CHART 10 : % Bekerja 2023 vs 2025 ── */
const b23m={};
dB23.forEach(r=>{b23m[r.usia]=r.pct;});

new Chart(document.getElementById('cPctBek'),{
  type:'bar',
  data:{
    labels:dB25.map(r=>r.usia+' thn'),
    datasets:[
      {label:'2025',data:dB25.map(r=>r.pct),backgroundColor:INFA,borderColor:INF,borderWidth:1.5,borderRadius:6,barPercentage:.45},
      {label:'2023',data:dB25.map(r=>b23m[r.usia]??null),backgroundColor:'rgba(148,163,184,.2)',borderColor:'#94A3B8',borderWidth:1.5,borderRadius:6,barPercentage:.45}
    ]
  },
  options:{...BASE,
    onClick(e,els){
      if(!els.length)return;
      const r=dB25[els[0].index];
      const p23=b23m[r.usia]??null;
      const diff=p23!==null?(r.pct-p23):null;
      document.getElementById('dBekTitle').textContent='Usia '+r.usia+' tahun';
      document.getElementById('dBek23').textContent=p23!==null?FD(p23)+'%':'–';
      document.getElementById('dBek25').textContent=FD(r.pct)+'%';
      const elDiff=document.getElementById('dBekDiff');
      const elHL=document.getElementById('dBekHL');
      const elDiffTxt=document.getElementById('dBekDiffText');
      if(diff!==null){
        elDiff.textContent=(diff>=0?'+':'')+FD(diff)+'%';
        elHL.className='drill-hl '+(diff>=0?'hl-pos':'hl-acc');
        elDiffTxt.textContent=diff>=0?'Ada perbaikan dari 2023 ke 2025 ✓':'Ada penurunan dari 2023 ke 2025 ⚠';
      }else{
        elDiff.textContent='–';
        elHL.className='drill-hl hl-inf';
        elDiffTxt.textContent='Data 2023 tidak tersedia';
      }
      openDrill('drillBekOv');
    },
    plugins:{
      legend:{display:true,position:'top',labels:{font:{size:11,family:FONT},usePointStyle:true,boxWidth:9}},
      tooltip:{...TT,callbacks:{
        title:ctx=>'Usia '+dB25[ctx[0].dataIndex].usia+' tahun',
        label:ctx=>ctx.dataset.label+': '+FD(ctx.raw)+'%'
      }}
    },
    scales:{x:axX(),y:{...axY(v=>v+'%'),title:{display:true,text:'% berhasil bekerja',font:{size:10,family:FONT},color:'#A8A29E'}}}
  }
});

/* ── PETA LEAFLET ────────────────────────── */
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
  dKab.forEach(r=>{
    const a=normN(k.n),m=normN(r.kabupatenKota);
    if(a===m){f=r;b=100;}else if(b<80&&(a.includes(m)||m.includes(a))){f=r;b=80;}
  });
  return{...k,jp:f?f.jumlahPnddk:null,sr:f?f.statusRata:null,kk:f?f.kabupatenKota:k.n};
});

const vv=dMap.filter(r=>r.jp).map(r=>r.jp);
const MN=Math.min(...vv),MX=Math.max(...vv);
const mc=v=>{
  if(!v)return '#CBD5E1';
  const t=(v-MN)/(MX-MN);
  if(t>.85)return '#7F1D1D';if(t>.68)return '#D63B1F';
  if(t>.50)return '#F87171';if(t>.33)return '#86EFAC';
  if(t>.16)return '#4ADE80';return '#2D7D53';
};
const mr=v=>v?8+((v-MN)/(MX-MN))*26:7;

const map=L.map('mapJatim',{center:[-7.5,112.5],zoom:8,zoomControl:true,scrollWheelZoom:false,attributionControl:true});
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png',{attribution:'© OpenStreetMap © CARTO',subdomains:'abcd',maxZoom:18,opacity:0.75}).addTo(map);
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png',{subdomains:'abcd',maxZoom:18,opacity:0.65,pane:'shadowPane'}).addTo(map);

dMap.forEach(r=>{
  if(!r.lt||!r.ln)return;
  const warna=mc(r.jp),rad=mr(r.jp),isAtas=r.sr==='atas';
  const rb=r.jp?(r.jp/1000).toFixed(2):'–';
  const hcDark=isAtas?'#1C1917':'#1C1917';
  const accentColor=isAtas?'#D63B1F':'#2D7D53';
  const devVal=r.jp?(r.jp-RATA)/1000:0,devSign=devVal>0?'+':'';

  const ci=L.circleMarker([r.lt,r.ln],{radius:rad,fillColor:warna,color:'#fff',weight:2,opacity:1,fillOpacity:.85}).addTo(map);
  ci.bindTooltip('<strong>'+r.kk+'</strong>',{permanent:false,direction:'top',offset:[0,-rad-2],opacity:1,className:'leaflet-tooltip'});
  ci.bindPopup(`
    <div style="font-family:'DM Sans',sans-serif;min-width:240px;">
      <div style="background:#1C1917;padding:14px 16px 12px;">
        <div style="font-size:9px;color:rgba(245,244,240,.6);font-weight:700;letter-spacing:.8px;text-transform:uppercase;margin-bottom:4px;">
          ${isAtas?'⚠ Di atas rata-rata':'✓ Di bawah rata-rata'}
        </div>
        <div style="font-size:15px;font-weight:700;color:#F5F4F0;font-family:'Sora',sans-serif;">${r.kk}</div>
        <div style="width:24px;height:3px;background:${accentColor};border-radius:2px;margin-top:8px;"></div>
      </div>
      <div style="padding:14px 16px;background:#fff;">
        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:10px;">
          <span style="font-size:11px;color:#A8A29E;">Penduduk miskin ${TAHUN}</span>
          <span style="font-size:16px;font-weight:700;color:#1C1917;font-family:'Sora',sans-serif;">${rb} <span style="font-size:10px;color:#A8A29E;font-family:'DM Sans',sans-serif;font-weight:400;">rb jiwa</span></span>
        </div>
        <div style="background:#FAFAF8;border-radius:8px;padding:10px 12px;border:1px solid rgba(28,25,23,.08);">
          <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
            <span style="font-size:9px;color:#A8A29E;font-weight:700;text-transform:uppercase;letter-spacing:.6px;">Rata-rata Jatim</span>
            <span style="font-size:11px;font-weight:600;color:#44403C;font-family:'DM Mono',monospace;">${(RATA/1000).toFixed(2)} rb</span>
          </div>
          <div style="display:flex;justify-content:space-between;">
            <span style="font-size:9px;color:#A8A29E;font-weight:700;text-transform:uppercase;letter-spacing:.6px;">Deviasi</span>
            <span style="font-size:11px;font-weight:700;color:${accentColor};font-family:'DM Mono',monospace;">${devSign}${devVal.toFixed(2)} rb</span>
          </div>
        </div>
      </div>
    </div>`,{maxWidth:270,className:'',closeButton:true});
  ci.on('mouseover',function(){this.setStyle({weight:3,color:'rgba(255,255,255,1)',fillOpacity:1,radius:rad+2});this.openTooltip();});
  ci.on('mouseout', function(){this.setStyle({weight:2,color:'#fff',fillOpacity:.85,radius:rad});});
  ci.on('click',    function(){this.closeTooltip();});
});

/* Legenda Peta */
const leg=L.control({position:'bottomright'});
leg.onAdd=function(){
  const d=L.DomUtil.create('div','');
  d.style.cssText='background:#fff;padding:13px 16px;border-radius:12px;box-shadow:0 4px 20px rgba(28,25,23,.1);font-family:"DM Sans",sans-serif;font-size:11px;color:#44403C;min-width:170px;border:1px solid rgba(28,25,23,.08);';
  d.innerHTML=`<div style="font-family:'Sora',sans-serif;font-weight:600;margin-bottom:8px;color:#1C1917;font-size:12px;">Jumlah Penduduk Miskin</div>`+
    [['#7F1D1D','Sangat Tinggi','> 85%'],['#D63B1F','Tinggi','68–85%'],['#F87171','Sedang','50–68%'],['#86EFAC','Rendah','33–50%'],['#4ADE80','Cukup Rendah','16–33%'],['#2D7D53','Sangat Rendah','< 16%']].map(([c,l,s])=>`<div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;"><span style="width:10px;height:10px;border-radius:50%;background:${c};flex-shrink:0;"></span><div><span style="font-weight:600;">${l}</span><span style="color:#A8A29E;margin-left:4px;font-size:10px;">${s}</span></div></div>`).join('')+
    `<div style="margin-top:8px;padding-top:8px;border-top:1px solid rgba(28,25,23,.08);font-size:10px;color:#A8A29E;">Ukuran ∝ jumlah penduduk miskin</div>`;
  return d;
};
leg.addTo(map);

/* Info box rata-rata */
const info=L.control({position:'topleft'});
info.onAdd=function(){
  const d=L.DomUtil.create('div','');
  d.style.cssText='background:#fff;padding:10px 14px;border-radius:10px;box-shadow:0 4px 20px rgba(28,25,23,.1);font-family:"DM Sans",sans-serif;border:1px solid rgba(28,25,23,.08);border-left:3px solid #D63B1F;min-width:160px;';
  d.innerHTML=`<div style="font-size:9px;font-weight:700;color:#A8A29E;text-transform:uppercase;letter-spacing:.7px;margin-bottom:2px;">Rata-rata Jatim ${TAHUN}</div>`+
    `<div style="font-family:'Sora',sans-serif;font-weight:700;color:#D63B1F;font-size:18px;line-height:1.2;">${(RATA/1000).toFixed(2)} <span style="font-size:10px;color:#A8A29E;font-family:'DM Sans',sans-serif;font-weight:400;">ribu jiwa</span></div>`+
    `<div style="font-size:10px;color:#A8A29E;border-top:1px solid rgba(28,25,23,.08);padding-top:5px;margin-top:4px;"><span style="color:#D63B1F;font-weight:600;">{{ $diAtasRataRata }} kab</span> di atas &nbsp;·&nbsp; <span style="color:#2D7D53;font-weight:600;">{{ $diBawahRataRata }} kab</span> di bawah</div>`;
  return d;
};
info.addTo(map);

/* ── PAGINATION ──────────────────────────── */
(function(){
  const ROWS=10,VISIBLE=3;
  const tbody=document.getElementById('tblBody');
  if(!tbody)return;
  let allRows=Array.from(tbody.querySelectorAll('tr'));
  let filtered=[...allRows];
  let page=1;

  function total(){return Math.ceil(filtered.length/ROWS);}
  function render(){
    const tp=total(),start=(page-1)*ROWS,end=start+ROWS;
    allRows.forEach(r=>r.style.display='none');
    filtered.slice(start,end).forEach(r=>r.style.display='');
    const info=document.getElementById('pagiInfo');
    const btns=document.getElementById('pagiBtns');
    info.textContent=filtered.length===0?'Tidak ada data':'Menampilkan '+(start+1)+'–'+Math.min(end,filtered.length)+' dari '+filtered.length+' data';
    btns.innerHTML='';
    const mk=(txt,act,dis,cb)=>{
      const b=document.createElement('button');
      b.className='pagi-btn'+(act?' active':'');
      b.textContent=txt;b.disabled=dis;b.onclick=cb;btns.appendChild(b);
    };
    mk('‹',false,page===1,()=>{page--;render();});
    const half=Math.floor(VISIBLE/2);
    let ps=Math.max(1,page-half),pe=Math.min(tp,ps+VISIBLE-1);
    if(pe-ps+1<VISIBLE)ps=Math.max(1,pe-VISIBLE+1);
    if(ps>1){mk('1',false,false,()=>{page=1;render();});if(ps>2)mk('…',false,true,()=>{});}
    for(let p=ps;p<=pe;p++){const pp=p;mk(p,p===page,false,()=>{page=pp;render();});}
    if(pe<tp){if(pe<tp-1)mk('…',false,true,()=>{});mk(tp,false,false,()=>{page=tp;render();});}
    mk('›',false,page===tp,()=>{page++;render();});
  }
  document.getElementById('srchKab').addEventListener('input',function(){
    const q=this.value.toLowerCase();
    filtered=allRows.filter(row=>Array.from(row.querySelectorAll('td')).some(td=>td.textContent.toLowerCase().includes(q)));
    page=1;render();
  });
  render();
})();
</script>
@endpush