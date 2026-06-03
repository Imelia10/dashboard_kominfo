@extends('layouts.app')

@push('styles')
<style>
:root {
  --blue-deep:#0f2d5e;--blue-mid:#1565C0;--blue-light:#E3F2FD;
  --red:#e53935;--red-light:#FFEBEE;
  --amber:#f59e0b;--amber-light:#FFF8E1;
  --green:#16a34a;--green-light:#F0FDF4;
  --purple:#4f46e5;--rose:#e11d48;
  --gray-bg:#F4F6FA;--gray-card:#fff;--gray-border:#e4e8f0;
  --text-main:#111827;--text-muted:#6b7280;
}
.ep{padding:24px 28px 56px;min-height:100%;background:var(--gray-bg);font-family:'DM Sans',sans-serif;}

/* Breadcrumb */
.ep-bc{display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--text-muted);margin-bottom:10px;}
.ep-bc a{color:var(--blue-mid);text-decoration:none;}

/* Header row */
.ep-head{display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;}
.ep-tag{font-size:10.5px;font-weight:700;color:var(--blue-mid);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:3px;}
.ep-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:clamp(18px,2.2vw,26px);font-weight:800;color:var(--text-main);line-height:1.2;margin-bottom:3px;}
.ep-title span{color:var(--blue-mid);}
.ep-sub{font-size:12.5px;color:var(--text-muted);}

/* Filter buttons */
.ep-filters{display:flex;gap:8px;flex-wrap:wrap;align-items:center;position:relative;}
.fp-wrap{position:relative;}
.fp-btn{
  padding:7px 18px;border-radius:8px;font-size:13px;font-weight:600;
  border:1.5px solid var(--gray-border);background:#fff;color:var(--text-muted);
  cursor:pointer;transition:all .15s;display:flex;align-items:center;gap:6px;
}
.fp-btn:hover,.fp-btn.open{border-color:var(--blue-mid);color:var(--blue-mid);background:var(--blue-light);}
.fp-btn svg{width:12px;height:12px;stroke:currentColor;fill:none;transition:transform .2s;}
.fp-btn.open svg{transform:rotate(180deg);}
.fp-dropdown{
  position:absolute;top:calc(100% + 6px);right:0;
  background:#fff;border:1px solid var(--gray-border);border-radius:10px;
  box-shadow:0 8px 24px rgba(0,0,0,0.1);
  min-width:200px;max-height:260px;overflow-y:auto;z-index:200;
  opacity:0;pointer-events:none;transform:translateY(-6px);
  transition:all .18s;
}
.fp-dropdown.open{opacity:1;pointer-events:auto;transform:translateY(0);}
.fp-option{
  padding:9px 16px;font-size:12.5px;cursor:pointer;color:var(--text-main);
  transition:background .12s;
}
.fp-option:hover{background:var(--blue-light);color:var(--blue-mid);}
.fp-option.selected{background:var(--blue-light);color:var(--blue-mid);font-weight:600;}
.fp-all{color:var(--text-muted);font-size:12px;border-bottom:1px solid var(--gray-border);}

/* Active filter tags */
.filter-tags{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:14px;min-height:0;}
.ftag{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:var(--blue-light);color:var(--blue-mid);border-radius:20px;font-size:11.5px;font-weight:600;}
.ftag-x{cursor:pointer;font-size:13px;line-height:1;opacity:.7;}
.ftag-x:hover{opacity:1;}

/* KPI Grid */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;}
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,1fr);}}
.kc{background:#fff;border-radius:14px;border:1px solid var(--gray-border);padding:16px 18px;position:relative;overflow:hidden;transition:box-shadow .2s,transform .2s;}
.kc:hover{box-shadow:0 6px 20px rgba(21,101,192,0.1);transform:translateY(-2px);}
.kc::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;border-radius:14px 14px 0 0;}
.kc.kc-red::before{background:var(--red);}
.kc.kc-amber::before{background:var(--amber);}
.kc.kc-blue::before{background:var(--blue-mid);}
.kc.kc-green::before{background:var(--green);}
.kc-icon{width:34px;height:34px;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;}
.kc-red   .kc-icon{background:var(--red-light);}
.kc-amber .kc-icon{background:var(--amber-light);}
.kc-blue  .kc-icon{background:var(--blue-light);}
.kc-green .kc-icon{background:#dcfce7;}
.kc-icon svg{width:17px;height:17px;fill:none;stroke-width:2;}
.kc-red   .kc-icon svg{stroke:var(--red);}
.kc-amber .kc-icon svg{stroke:var(--amber);}
.kc-blue  .kc-icon svg{stroke:var(--blue-mid);}
.kc-green .kc-icon svg{stroke:var(--green);}
.kc-lbl{font-size:10.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:4px;}
.kc-val{font-family:'Plus Jakarta Sans',sans-serif;font-size:28px;font-weight:800;line-height:1;color:var(--text-main);}
.kc-red   .kc-val{color:var(--red);}
.kc-amber .kc-val{color:var(--amber);}
.kc-unit{font-size:11.5px;color:var(--text-muted);margin-top:3px;}
.kc-badge{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;font-weight:700;padding:2px 9px;border-radius:10px;margin-top:6px;}
.badge-amber{background:var(--amber-light);color:#92400e;}
.badge-red  {background:var(--red-light);  color:#c62828;}

/* Two col */
.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;}
@media(max-width:850px){.grid2{grid-template-columns:1fr;}}

/* Card base */
.ecard{background:#fff;border-radius:16px;border:1px solid var(--gray-border);padding:20px 22px;box-shadow:0 1px 6px rgba(21,101,192,0.04);}
.ecard-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:14px;font-weight:700;color:var(--text-main);margin-bottom:2px;}
.ecard-sub{font-size:11.5px;color:var(--text-muted);margin-bottom:14px;}

/* Vertical Bar Chart */
.vchart-wrap{display:flex;align-items:flex-end;gap:6px;height:160px;padding-bottom:22px;position:relative;}
.vchart-wrap::after{content:'';position:absolute;bottom:22px;left:0;right:0;height:1px;background:var(--gray-border);}
.vbar-group{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;min-width:0;transition:opacity .3s;}
.vbar{width:100%;max-width:44px;border-radius:5px 5px 0 0;background:linear-gradient(180deg,#93c5fd,var(--blue-mid));transition:opacity .3s;}
.vbar.vb-peak{background:linear-gradient(180deg,#ff8a80,var(--red));}
.vbar-val{font-size:9px;font-weight:700;color:var(--text-muted);text-align:center;white-space:nowrap;overflow:hidden;}
.vbar-val.rv{color:var(--red);}
.vbar-lbl{font-size:9.5px;color:var(--text-muted);font-weight:500;white-space:nowrap;}
.vbar-lbl.rl{color:var(--red);font-weight:700;}

/* Line Chart SVG */
.lchart-wrap{width:100%;height:160px;}
.lchart-wrap svg{width:100%;height:100%;}

/* Section title */
.sec-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:16px;font-weight:800;color:var(--text-main);margin:20px 0 14px;}

/* Kawasan grid */
.kawasan-grid{display:grid;grid-template-columns:3fr 2fr;gap:14px;margin-bottom:16px;}
@media(max-width:900px){.kawasan-grid{grid-template-columns:1fr;}}

/* Progress bars */
.prog-row{margin-bottom:12px;}
.prog-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;}
.prog-name{font-size:12px;font-weight:600;color:var(--text-main);display:flex;align-items:center;gap:5px;}
.prog-dot{display:inline-block;width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.prog-val{font-size:11.5px;font-weight:700;color:var(--text-muted);}
.prog-val em{font-style:normal;font-weight:500;font-size:10.5px;}
.prog-track{height:8px;border-radius:4px;background:var(--gray-bg);overflow:hidden;}
.prog-fill{height:100%;border-radius:4px;transition:width 1s cubic-bezier(0.4,0,0.2,1);}
.prog-note{font-size:10.5px;color:var(--text-muted);font-style:italic;margin-top:2px;}
.data-ext-tag{display:inline-block;font-size:9px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 8px;border-radius:6px;background:var(--red-light);color:var(--red);}

/* ── Rank cards (3 terbesar / 3 terkecil) ── */
.rank-panel{display:flex;flex-direction:column;gap:10px;}

.rank-card{border-radius:14px;padding:14px 16px;}
.rank-card.rc-purple{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;}
.rank-card.rc-rose  {background:linear-gradient(135deg,#e11d48,#f43f5e);color:#fff;}

.rc-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;}
.rc-tag{font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;opacity:.8;}
.rc-badge{font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
  background:rgba(255,255,255,.18);border-radius:20px;padding:2px 8px;}

.rc-list{display:flex;flex-direction:column;gap:5px;}
.rc-item{display:flex;align-items:center;gap:8px;}
.rc-rank{width:18px;height:18px;border-radius:50%;background:rgba(255,255,255,.22);
  font-size:9.5px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.rc-name{flex:1;font-size:11.5px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.rc-val{font-size:11px;font-weight:700;opacity:.95;white-space:nowrap;}

/* Insight 3 */
.insight3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:16px;}
@media(max-width:850px){.insight3{grid-template-columns:1fr;}}
.ins{border-radius:14px;padding:18px 20px;border:1px solid;}
.ins.ins-y{background:#FFFBEB;border-color:#fde68a;}
.ins.ins-g{background:#F0FDF4;border-color:#bbf7d0;}
.ins.ins-l{background:#EEF2FF;border-color:#c7d2fe;}
.ins-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;}
.ins-y .ins-icon{background:#fef3c7;}
.ins-g .ins-icon{background:#dcfce7;}
.ins-l .ins-icon{background:#e0e7ff;}
.ins-icon svg{width:17px;height:17px;fill:none;stroke-width:2;}
.ins-y .ins-icon svg{stroke:#d97706;}
.ins-g .ins-icon svg{stroke:#16a34a;}
.ins-l .ins-icon svg{stroke:#4f46e5;}
.ins-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:700;color:var(--text-main);margin-bottom:5px;}
.ins-text{font-size:12px;color:#374151;line-height:1.65;}

/* Table */
.tbl-search-wrap{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
.tbl-search{
  flex:1;padding:8px 14px;border-radius:9px;border:1.5px solid var(--gray-border);
  font-size:12.5px;font-family:'DM Sans',sans-serif;color:var(--text-main);
  background:#fff;outline:none;transition:border-color .15s;
}
.tbl-search:focus{border-color:var(--blue-mid);}
.tbl-search::placeholder{color:var(--text-muted);}
.tbl-count{font-size:11.5px;color:var(--text-muted);white-space:nowrap;}

.dtable{width:100%;border-collapse:collapse;font-size:12.5px;}
.dtable th{background:var(--gray-bg);color:var(--text-muted);font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;padding:10px 14px;text-align:left;border-bottom:1px solid var(--gray-border);}
.dtable td{padding:10px 14px;border-bottom:1px solid #f3f4f6;color:var(--text-main);}
.dtable tr:last-child td{border-bottom:none;}
.dtable tr:hover td{background:#fafbfc;}
.dtable tr.tr-pk td{color:var(--red);font-weight:700;}
.spill{display:inline-block;padding:2px 10px;border-radius:10px;font-size:11px;font-weight:600;}
.sp-st{background:#FFEBEE;color:#c62828;}
.sp-t {background:#FFF3E0;color:#e65100;}
.sp-s {background:#FFFDE7;color:#f57f17;}
.sp-r {background:#E8F5E9;color:#2e7d32;}
.sp-p {background:#E3F2FD;color:var(--blue-mid);}
.data-row.hidden-row{display:none;}

/* No result row */
.no-result-row td{text-align:center;color:var(--text-muted);padding:24px;font-size:12.5px;}
</style>
@endpush

@section('content')
<div class="ep">

  {{-- Breadcrumb --}}
  <div class="ep-bc">
    <a href="{{ route('home') }}">Home</a><span>›</span><span>Environment</span>
  </div>

  {{-- Header --}}
  <div class="ep-head">
    <div>
      <div class="ep-tag">▪ Dashboard</div>
      <h1 class="ep-title">Dashboard Analisis <span>Kebakaran Hutan</span> Jawa Timur</h1>
      <p class="ep-sub">Analisis Rencana vs Realisasi Kabupaten/Kota</p>
    </div>
    <div class="ep-filters">
      {{-- Filter Tahun --}}
      <div class="fp-wrap">
        <button class="fp-btn" id="btn-tahun" onclick="toggleDrop('tahun')">
          <span id="lbl-tahun">Tahun</span>
          <svg viewBox="0 0 24 24" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="fp-dropdown" id="drop-tahun">
          <div class="fp-option fp-all" onclick="setFilter('tahun','','Tahun')">Semua Tahun</div>
          @foreach($tahunList as $t)
          <div class="fp-option" data-val="{{ $t }}" onclick="setFilter('tahun','{{ $t }}','{{ $t }}')">{{ $t }}</div>
          @endforeach
        </div>
      </div>
      {{-- Filter Wilayah --}}
      <div class="fp-wrap">
        <button class="fp-btn" id="btn-wilayah" onclick="toggleDrop('wilayah')">
          <span id="lbl-wilayah">Wilayah</span>
          <svg viewBox="0 0 24 24" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="fp-dropdown" id="drop-wilayah">
          <div class="fp-option fp-all" onclick="setFilter('wilayah','','Wilayah')">Semua Wilayah</div>
          @foreach($lokasiList as $l)
          <div class="fp-option" data-val="{{ $l }}" onclick="setFilter('wilayah','{{ $l }}','{{ Str::limit($l,22) }}')">{{ $l }}</div>
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- Active Filter Tags --}}
  <div class="filter-tags" id="filterTags"></div>

  {{-- KPI 4 cards --}}
  <div class="kpi-grid">
    <div class="kc kc-red">
      <div class="kc-icon">
        <svg viewBox="0 0 24 24"><path d="M12 2c0 6-6 8-6 14a6 6 0 0012 0c0-6-6-8-6-14z"/><path d="M12 12c0 3-2 4-2 7a2 2 0 004 0c0-3-2-4-2-7z"/></svg>
      </div>
      <div class="kc-lbl">Total Luas Terbakar</div>
      <div class="kc-val" id="kpi-total">{{ number_format($totalLuas,0,',','.') }}</div>
      <div class="kc-unit">Ha ({{ $tahunList->min() }}–{{ $tahunList->max() }})</div>
    </div>
    <div class="kc kc-amber">
      <div class="kc-icon">
        <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      </div>
      <div class="kc-lbl" id="kpi-peak-lbl">Luas Tertinggi {{ $tahunTertinggi }}</div>
      <div class="kc-val" id="kpi-peak">{{ number_format($luasTertinggi,0,',','.') }}</div>
      <div class="kc-unit">Ha</div>
      <span class="kc-badge badge-amber">⚠ Puncak Tertinggi</span>
    </div>
    <div class="kc kc-blue">
      <div class="kc-icon">
        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      </div>
      <div class="kc-lbl">Rata-rata Luas / Tahun</div>
      <div class="kc-val" id="kpi-avg">{{ number_format($rataRata,0,',','.') }}</div>
      <div class="kc-unit">Ha/Thn</div>
    </div>
    <div class="kc kc-red">
      <div class="kc-icon">
        <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
      </div>
      <div class="kc-lbl">Tahun Terparah</div>
      <div class="kc-val" id="kpi-worst">{{ $tahunTertinggi }}</div>
      <div class="kc-unit">Kebakaran terluas tercatat</div>
      <span class="kc-badge badge-red">Sangat Tinggi</span>
    </div>
  </div>

  {{-- Charts Row --}}
  <div class="grid2">

    {{-- Vertical Bar per Tahun --}}
    <div class="ecard">
      <div class="ecard-title">Luas Kebakaran per Tahun</div>
      <div class="ecard-sub" id="bar-sub">{{ $tahunList->min() }}–{{ $tahunList->max() }} · Total: {{ number_format($totalLuas,0,',','.') }} Ha</div>
      @php $maxY = $perTahun->max('total'); @endphp
      <div class="vchart-wrap" id="barChart">
        @foreach($perTahun as $row)
        @php
          $barH = $maxY > 0 ? max(4, round(($row->total/$maxY)*130)) : 4;
          $isPk = $row->tahun == $tahunTertinggi;
          $v = $row->total >= 1000 ? round($row->total/1000,1).'k' : number_format($row->total,0,',','.');
        @endphp
        <div class="vbar-group" data-year="{{ $row->tahun }}" data-total="{{ $row->total }}">
          <div class="vbar-val {{ $isPk?'rv':'' }}">{{ $v }}</div>
          <div class="vbar {{ $isPk?'vb-peak':'' }}" style="height:{{ $barH }}px"></div>
          <div class="vbar-lbl {{ $isPk?'rl':'' }}">{{ $row->tahun }}</div>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Line Chart Tren --}}
    <div class="ecard">
      <div class="ecard-title">Tren Luas Kebakaran</div>
      <div class="ecard-sub">{{ $tahunList->min() }}–{{ $tahunList->max() }}</div>
      <div class="lchart-wrap" id="lineChartWrap">
        @php
          $td   = $perTahun->values();
          $tMax = $td->max('total');
          $tMin = $td->min('total');
          $tR   = $tMax - $tMin ?: 1;
          $n    = $td->count();
          $W=280; $H=130; $pL=22; $pB=22; $pT=14; $pRt=8;
          $iW   = $W-$pL-$pRt; $iH = $H-$pT-$pB;
          $pts  = [];
          foreach($td as $i=>$row){
            $x = $pL + ($n>1 ? $i/($n-1) : .5)*$iW;
            $y = $pT + (1-($row->total-$tMin)/$tR)*$iH;
            $pts[] = [$x,$y,$row->tahun,$row->total,$row->tahun==$tahunTertinggi];
          }
          $pl = implode(' ', array_map(fn($p)=>"{$p[0]},{$p[1]}", $pts));
          $ap = "M {$pts[0][0]},{$pts[0][1]}";
          foreach(array_slice($pts,1) as $p) $ap .= " L {$p[0]},{$p[1]}";
          $lx=end($pts)[0]; $fx=$pts[0][0]; $by=$pT+$iH;
          $ap .= " L $lx,$by L $fx,$by Z";
        @endphp
        <svg viewBox="0 0 {{ $W }} {{ $H }}" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <linearGradient id="tg" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#16a34a" stop-opacity=".18"/>
              <stop offset="100%" stop-color="#16a34a" stop-opacity="0"/>
            </linearGradient>
          </defs>
          @for($gi=0;$gi<=3;$gi++)
          @php $gy=$pT+$gi/3*$iH; @endphp
          <line x1="{{ $pL }}" y1="{{ $gy }}" x2="{{ $pL+$iW }}" y2="{{ $gy }}" stroke="#e4e8f0" stroke-width=".5"/>
          @endfor
          <path d="{{ $ap }}" fill="url(#tg)"/>
          <polyline points="{{ $pl }}" fill="none" stroke="#16a34a" stroke-width="1.8" stroke-linejoin="round"/>
          @foreach($pts as $p)
          <circle cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="{{ $p[4]?3.2:2 }}"
            fill="{{ $p[4]?'#e53935':'#16a34a' }}" stroke="#fff" stroke-width="1"/>
          @endforeach
          @foreach($pts as $p)
          <text x="{{ $p[0] }}" y="{{ $pT+$iH+12 }}" text-anchor="middle"
            font-size="6" fill="{{ $p[4]?'#e53935':'#6b7280' }}"
            font-weight="{{ $p[4]?'700':'400' }}" font-family="DM Sans,sans-serif">{{ $p[2] }}</text>
          @endforeach
        </svg>
      </div>
    </div>

  </div>

  {{-- Analisis Kawasan --}}
  <div class="sec-title">Analisis Kawasan Konservasi &amp; UPT</div>

  <div class="kawasan-grid">
    {{-- Left card: progress bars (rendered by JS) --}}
    <div class="ecard">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px;">
        <div>
          <div class="ecard-title">Kontribusi Wilayah Terhadap Kebakaran</div>
          <div class="ecard-sub">Distribusi luas kebakaran per kawasan pengelolaan khusus</div>
        </div>
        <span class="data-ext-tag">Data Kumulatif</span>
      </div>
      {{-- Initial static render from PHP (will be overwritten by JS on filter) --}}
      <div id="progBars">
        @php
          $maxL   = $perLokasi->max('total');
          $colors = ['#e53935','#f59e0b','#1565C0','#60a5fa','#a78bfa'];
        @endphp
        @foreach($perLokasi->take(5) as $lok)
        @php
          $pctL = $maxL>0 ? round($lok->total/$maxL*100,1) : 0;
          $pctT = $totalLuas>0 ? round($lok->total/$totalLuas*100,1) : 0;
          $c    = $colors[$loop->index] ?? '#60a5fa';
          $isTop= $loop->first;
        @endphp
        <div class="prog-row">
          <div class="prog-top">
            <span class="prog-name" style="{{ $isTop?'color:var(--red)':'' }}">
              <span class="prog-dot" style="background:{{ $c }}"></span>
              {{ $lok->lokasi }}
            </span>
            <span class="prog-val" style="{{ $isTop?'color:var(--red)':'' }}">
              {{ number_format($lok->total,0,',','.') }} Ha <em>(+{{ $pctT }}%)</em>
            </span>
          </div>
          <div class="prog-track">
            <div class="prog-fill" style="width:0%;background:{{ $c }}" data-target="{{ $pctL }}"></div>
          </div>
          @if($isTop)
          <div class="prog-note">⚑ Kawasan dengan luas kebakaran terbesar secara kumulatif</div>
          @endif
        </div>
        @endforeach
      </div>
    </div>

    {{-- Right: rank cards (rendered by JS) --}}
    <div class="rank-panel">

      <div class="rank-card rc-purple">
        <div class="rc-header">
          <span class="rc-tag">🔺 Paling Terdampak</span>
          <span class="rc-badge">Top 3 Terbesar</span>
        </div>
        <div class="rc-list" id="rankTop3">
          @foreach($perLokasi->take(3) as $item)
          <div class="rc-item">
            <div class="rc-rank">{{ $loop->iteration }}</div>
            <div class="rc-name">{{ $item->lokasi }}</div>
            <div class="rc-val">{{ number_format($item->total,0,',','.') }} Ha</div>
          </div>
          @endforeach
        </div>
      </div>

      @php $bot3 = $perLokasi->filter(fn($r)=>$r->total > 0)->sortBy('total')->take(3)->values(); @endphp
      <div class="rank-card rc-rose">
        <div class="rc-header">
          <span class="rc-tag">✅ Paling Aman</span>
          <span class="rc-badge">Top 3 Terkecil</span>
        </div>
        <div class="rc-list" id="rankBot3">
          @foreach($bot3 as $item)
          <div class="rc-item">
            <div class="rc-rank">{{ $loop->iteration }}</div>
            <div class="rc-name">{{ $item->lokasi }}</div>
            <div class="rc-val">{{ number_format($item->total,0,',','.') }} Ha</div>
          </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>

  {{-- Analisis & Wawasan --}}
  <div class="sec-title">Analisis &amp; Wawasan</div>
  <div class="insight3">
    <div class="ins ins-y">
      <div class="ins-icon">
        <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      </div>
      <div class="ins-title">Pola Tren Kebakaran</div>
      <div class="ins-text">
        Dalam rentang data {{ $tahunList->min() }}–{{ $tahunList->max() }}, tren kebakaran membentuk pola <strong>U-Curve</strong> dengan penurunan bertahap pada 2020–2021, kemudian lonjakan tajam mencapai <strong>{{ number_format($luasTertinggi,0,',','.') }} Ha</strong> pada tahun <strong>{{ $tahunTertinggi }}</strong> — menjadi titik puncak tertinggi dalam seluruh rentang data historis yang tersimpan. Lonjakan ini jauh melampaui rata-rata tahunan sebesar {{ number_format($rataRata,0,',','.') }} Ha, mengindikasikan adanya faktor luar biasa yang mendorong eskalasi kebakaran secara masif di tahun tersebut.
      </div>
    </div>
    <div class="ins ins-g">
      <div class="ins-icon">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/></svg>
      </div>
      <div class="ins-title">Pengaruh Musim Kemarau &amp; El Niño</div>
      <div class="ins-text">
        Analisis historis menunjukkan korelasi kuat antara <strong>musim kemarau panjang</strong> dan lonjakan luas kebakaran. Periode El Niño memperparah kondisi dengan menurunkan curah hujan secara signifikan, sehingga vegetasi hutan menjadi sangat rentan terbakar. Pemantauan intensif <strong>diprioritaskan setiap Agustus–Oktober</strong> — jendela waktu kritis ketika suhu meningkat dan kelembaban tanah berada di titik terendah sepanjang tahun di wilayah Jawa Timur.
      </div>
    </div>
    <div class="ins ins-l">
      <div class="ins-icon">
        <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
      </div>
      <div class="ins-title">Wilayah Prioritas Pengawasan</div>
      <div class="ins-text">
        <strong>{{ $topLokasi->lokasi }}</strong> secara konsisten mencatat luas kebakaran tertinggi secara kumulatif, menjadikannya zona merah utama yang memerlukan patroli intensif. Selain itu, kawasan-kawasan dengan tren kenaikan tahun ke tahun perlu dipetakan ulang risiko ekologisnya. Rekomendasi strategis: <strong>tambah titik pemantauan dini</strong>, perkuat kolaborasi lintas UPT, dan percepat program firebreak di perbatasan vegetasi rawan.
      </div>
    </div>
  </div>

  {{-- Tabel --}}
  <div class="ecard">
    <div class="ecard-title">Tabel Data Sektoral Kebakaran Hutan</div>
    <div class="ecard-sub">Seluruh data historis dari database kebakaranhutan</div>

    {{-- Search bar --}}
    <div class="tbl-search-wrap">
      <input
        type="text"
        class="tbl-search"
        id="tblSearch"
        placeholder="🔍  Cari tahun, lokasi..."
        oninput="applyFilter()"
      />
      <span class="tbl-count" id="tblCount"></span>
    </div>

    <div style="overflow-x:auto;">
      <table class="dtable">
        <thead>
          <tr>
            <th>Tahun</th>
            <th>Lokasi / Kawasan</th>
            <th>Luas (Ha)</th>
          
          </tr>
        </thead>
        <tbody id="tBody">
    @foreach($rawData as $row)
    @php
        $luas = floatval($row->luas_areal_kebakaran);
        $isPk = ($row->tahun == $tahunTertinggi && $luas > 1000);
    @endphp

    <tr class="data-row {{ $isPk ? 'tr-pk' : '' }}"
        data-year="{{ $row->tahun }}"
        data-lokasi="{{ $row->lokasi }}"
        data-search="{{ strtolower($row->tahun.' '.$row->lokasi) }}">

        <td>{{ $row->tahun ?: '–' }}</td>
        <td>{{ $row->lokasi ?: '–' }}</td>
        <td>{{ $luas > 0 ? number_format($luas, 2, ',', '.') : '0' }}</td>
    </tr>
    @endforeach

    <tr class="no-result-row" id="noResult" style="display:none;">
        <td colspan="3">
            Tidak ada data yang sesuai dengan pencarian / filter yang dipilih.
        </td>
    </tr>
</tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
// ── Raw data injected from PHP ──
// perTahun: aggregated per year
const allRows = @json($perTahun->map(fn($r)=>['tahun'=>$r->tahun,'total'=>$r->total])->values());

// rawData: every individual row (tahun + lokasi + luas)
const rawData = @json($rawData->map(fn($r)=>['tahun'=>$r->tahun,'lokasi'=>$r->lokasi,'luas'=>floatval($r->luas_areal_kebakaran)])->values());

const BAR_COLORS = ['#e53935','#f59e0b','#1565C0','#60a5fa','#a78bfa','#34d399','#f87171'];
const filters = { tahun:'', wilayah:'' };

// ════════════════════════════════════════════
// Dropdown helpers
// ════════════════════════════════════════════
function toggleDrop(key){
  ['tahun','wilayah'].forEach(k=>{
    const d=document.getElementById('drop-'+k);
    const b=document.getElementById('btn-'+k);
    if(k===key){ const o=d.classList.contains('open'); d.classList.toggle('open',!o); b.classList.toggle('open',!o); }
    else{ d.classList.remove('open'); b.classList.remove('open'); }
  });
}

function setFilter(key,val,label){
  filters[key]=val;
  document.getElementById('lbl-'+key).textContent = label || (key==='tahun'?'Tahun':'Wilayah');
  document.querySelectorAll('#drop-'+key+' .fp-option').forEach(el=>el.classList.toggle('selected', el.dataset.val===val));
  document.getElementById('drop-'+key).classList.remove('open');
  document.getElementById('btn-'+key).classList.remove('open');
  renderFilterTags();
  applyFilter();
}

// ════════════════════════════════════════════
// Filter tag pills
// ════════════════════════════════════════════
function renderFilterTags(){
  const wrap = document.getElementById('filterTags');
  wrap.innerHTML = '';
  if(filters.tahun){
    const t = document.createElement('span');
    t.className = 'ftag';
    t.innerHTML = `Tahun: <strong>${filters.tahun}</strong> <span class="ftag-x" onclick="setFilter('tahun','','Tahun')">×</span>`;
    wrap.appendChild(t);
  }
  if(filters.wilayah){
    const t = document.createElement('span');
    t.className = 'ftag';
    const label = filters.wilayah.length > 24 ? filters.wilayah.slice(0,24)+'…' : filters.wilayah;
    t.innerHTML = `Wilayah: <strong>${label}</strong> <span class="ftag-x" onclick="setFilter('wilayah','','Wilayah')">×</span>`;
    wrap.appendChild(t);
  }
}

// ════════════════════════════════════════════
// Helper: get filtered rawData rows
// ════════════════════════════════════════════
function getFilteredRaw(fy, fw){
  return rawData.filter(r=>{
    const my = !fy || String(r.tahun) === String(fy);
    const ml = !fw || r.lokasi === fw;
    return my && ml;
  });
}

// ════════════════════════════════════════════
// Re-render kawasan section (progress + rank)
// ════════════════════════════════════════════
function renderKawasan(fy, fw){
  const filtered = getFilteredRaw(fy, fw);

  // Aggregate by lokasi
  const map = {};
  filtered.forEach(r=>{
    if(!r.lokasi || r.lokasi==='0') return;
    map[r.lokasi] = (map[r.lokasi]||0) + r.luas;
  });

  // Sort descending
  const sorted = Object.entries(map)
    .map(([lokasi,total])=>({lokasi,total}))
    .sort((a,b)=>b.total-a.total);

  const grandTotal = sorted.reduce((s,r)=>s+r.total,0);
  const maxVal     = sorted[0]?.total || 1;

  // ── Progress bars ──
  const progWrap = document.getElementById('progBars');
  if(progWrap){
    progWrap.innerHTML = '';
    if(sorted.length === 0){
      progWrap.innerHTML = '<p style="font-size:12px;color:var(--text-muted);padding:8px 0;">Tidak ada data untuk filter yang dipilih.</p>';
    } else {
      sorted.slice(0,5).forEach((lok,i)=>{
        const pctL = maxVal > 0 ? Math.round(lok.total/maxVal*1000)/10 : 0;
        const pctT = grandTotal > 0 ? Math.round(lok.total/grandTotal*1000)/10 : 0;
        const c    = BAR_COLORS[i] || '#60a5fa';
        const isTop= i===0;
        progWrap.innerHTML += `
        <div class="prog-row">
          <div class="prog-top">
            <span class="prog-name" ${isTop?'style="color:var(--red)"':''}>
              <span class="prog-dot" style="background:${c}"></span>
              ${lok.lokasi}
            </span>
            <span class="prog-val" ${isTop?'style="color:var(--red)"':''}>
              ${fmt(lok.total)} Ha <em>(+${pctT}%)</em>
            </span>
          </div>
          <div class="prog-track">
            <div class="prog-fill" style="width:0%;background:${c}" data-target="${pctL}"></div>
          </div>
          ${isTop ? '<div class="prog-note">⚑ Kawasan dengan luas kebakaran terbesar secara kumulatif</div>' : ''}
        </div>`;
      });
      // Animate progress bars after DOM paint
      requestAnimationFrame(()=>{
        progWrap.querySelectorAll('.prog-fill').forEach(el=>{
          el.style.width = el.dataset.target + '%';
        });
      });
    }
  }

  // ── Top 3 terbesar ──
  const top3El = document.getElementById('rankTop3');
  if(top3El){
    const top3 = sorted.slice(0,3);
    top3El.innerHTML = top3.length === 0
      ? '<div class="rc-item" style="opacity:.7;font-size:11.5px;">Tidak ada data</div>'
      : top3.map((r,i)=>`
        <div class="rc-item">
          <div class="rc-rank">${i+1}</div>
          <div class="rc-name">${r.lokasi}</div>
          <div class="rc-val">${fmt(r.total)} Ha</div>
        </div>`).join('');
  }

  // ── Top 3 terkecil (non-zero) ──
  const bot3El = document.getElementById('rankBot3');
  if(bot3El){
    const bot3 = [...sorted].filter(r=>r.total>0).sort((a,b)=>a.total-b.total).slice(0,3);
    bot3El.innerHTML = bot3.length === 0
      ? '<div class="rc-item" style="opacity:.7;font-size:11.5px;">Tidak ada data</div>'
      : bot3.map((r,i)=>`
        <div class="rc-item">
          <div class="rc-rank">${i+1}</div>
          <div class="rc-name">${r.lokasi}</div>
          <div class="rc-val">${fmt(r.total)} Ha</div>
        </div>`).join('');
  }
}

// ════════════════════════════════════════════
// Main applyFilter
// ════════════════════════════════════════════
function applyFilter(){
  const fy   = filters.tahun;
  const fw   = filters.wilayah;
  const srch = (document.getElementById('tblSearch')?.value||'').toLowerCase().trim();

  // ── Table rows ──
  const rows = document.querySelectorAll('#tBody .data-row');
  let visible = 0;
  rows.forEach(row=>{
    const my = !fy || row.dataset.year == fy;
    const ml = !fw || row.dataset.lokasi === fw;
    const ms = !srch || (row.dataset.search||'').includes(srch);
    const show = my && ml && ms;
    row.classList.toggle('hidden-row', !show);
    if(show) visible++;
  });
  const noRes = document.getElementById('noResult');
  if(noRes) noRes.style.display = visible===0 ? '' : 'none';
  const countEl = document.getElementById('tblCount');
  if(countEl) countEl.textContent = `${visible} baris`;

  // ── Bar chart dim ──
  document.querySelectorAll('.vbar-group').forEach(g=>{
    const match = !fy || g.dataset.year == fy;
    g.style.opacity = match ? '1' : '0.18';
  });

  // ── KPI: filter by tahun only (wilayah affects kawasan section, not totals) ──
  let kpiRows = allRows;
  if(fy) kpiRows = kpiRows.filter(r=> String(r.tahun)===String(fy));

  // If wilayah filter active, recalc totals from rawData
  let totalVal, peakLuas, peakTahun, avgVal;
  if(fw){
    const fRaw = getFilteredRaw(fy, fw);
    totalVal  = fRaw.reduce((s,r)=>s+r.luas, 0);
    // peak year for this wilayah
    const byYear = {};
    fRaw.forEach(r=>{ byYear[r.tahun]=(byYear[r.tahun]||0)+r.luas; });
    const peakEntry = Object.entries(byYear).sort((a,b)=>b[1]-a[1])[0];
    peakTahun = peakEntry?.[0] || '-';
    peakLuas  = peakEntry?.[1] || 0;
    avgVal    = Object.keys(byYear).length > 0 ? totalVal / Object.keys(byYear).length : 0;
  } else {
    totalVal  = kpiRows.reduce((s,r)=>s+r.total, 0);
    const peakRow = kpiRows.reduce((a,b)=>b.total>a.total?b:a, {tahun:'-',total:0});
    peakTahun = peakRow.tahun || '-';
    peakLuas  = peakRow.total || 0;
    avgVal    = kpiRows.length > 0 ? totalVal / kpiRows.length : 0;
  }

  document.getElementById('kpi-total').textContent    = fmt(totalVal);
  document.getElementById('kpi-peak').textContent     = fmt(peakLuas);
  document.getElementById('kpi-peak-lbl').textContent = `Luas Tertinggi ${peakTahun}`;
  document.getElementById('kpi-avg').textContent      = fmt(Math.round(avgVal));
  document.getElementById('kpi-worst').textContent    = peakTahun;

  const years = fy ? [fy] : kpiRows.map(r=>r.tahun);
  document.getElementById('bar-sub').textContent = fy
    ? `Tahun ${fy} · Total: ${fmt(totalVal)} Ha`
    : (years.length ? `${years[0]}–${years[years.length-1]} · Total: ${fmt(totalVal)} Ha` : '');

  // ── Kawasan section (reactive) ──
  renderKawasan(fy, fw);
}

function fmt(n){
  return Number(n).toLocaleString('id-ID');
}

// ── Close dropdown on outside click ──
document.addEventListener('click',e=>{
  if(!e.target.closest('.fp-wrap')){
    document.querySelectorAll('.fp-dropdown').forEach(d=>d.classList.remove('open'));
    document.querySelectorAll('.fp-btn').forEach(b=>b.classList.remove('open'));
  }
});

// ── Init ──
document.addEventListener('DOMContentLoaded',()=>{
  const rows = document.querySelectorAll('#tBody .data-row');
  const countEl = document.getElementById('tblCount');
  if(countEl) countEl.textContent = `${rows.length} baris`;
  // Animate initial progress bars
  requestAnimationFrame(()=>{
    document.querySelectorAll('.prog-fill[data-target]').forEach(el=>{
      el.style.width = el.dataset.target + '%';
    });
  });
});
</script>
@endpush