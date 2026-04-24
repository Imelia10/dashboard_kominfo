@extends('layouts.app')

@push('styles')
<style>
:root {
  --blue-deep:#0f2d5e;--blue-mid:#1565C0;--blue-light:#E3F2FD;
  --red:#e53935;--red-light:#FFEBEE;
  --amber:#f59e0b;--amber-light:#FFF8E1;
  --green:#16a34a;--green-light:#F0FDF4;
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
.vbar-group{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;min-width:0;}
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

/* Highlight cards right */
.hcard{border-radius:14px;padding:16px 18px;margin-bottom:10px;}
.hcard:last-child{margin-bottom:0;}
.hcard.hc-purple{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;}
.hcard.hc-rose  {background:linear-gradient(135deg,#e11d48,#f43f5e);color:#fff;}
.hcard-tag{font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;opacity:.8;margin-bottom:4px;}
.hcard-loc{font-family:'Plus Jakarta Sans',sans-serif;font-size:12.5px;font-weight:800;margin-bottom:5px;line-height:1.3;}
.hcard-val{font-family:'Plus Jakarta Sans',sans-serif;font-size:24px;font-weight:800;line-height:1;}
.hcard-val sup{font-size:12px;font-weight:400;opacity:.8;margin-left:2px;}
.hcard-desc{font-size:11px;opacity:.85;margin-top:6px;line-height:1.55;}

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

  {{-- KPI 4 cards --}}
  <div class="kpi-grid">
    <div class="kc kc-red">
      <div class="kc-icon">
        <svg viewBox="0 0 24 24"><path d="M12 2c0 6-6 8-6 14a6 6 0 0012 0c0-6-6-8-6-14z"/><path d="M12 12c0 3-2 4-2 7a2 2 0 004 0c0-3-2-4-2-7z"/></svg>
      </div>
      <div class="kc-lbl">Total Luas Terbakar</div>
      <div class="kc-val">{{ number_format($totalLuas,0,',','.') }}</div>
      <div class="kc-unit">Ha ({{ $tahunList->min() }}–{{ $tahunList->max() }})</div>
    </div>
    <div class="kc kc-amber">
      <div class="kc-icon">
        <svg viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      </div>
      <div class="kc-lbl">Luas Tertinggi {{ $tahunTertinggi }}</div>
      <div class="kc-val">{{ number_format($luasTertinggi,0,',','.') }}</div>
      <div class="kc-unit">Ha</div>
      <span class="kc-badge badge-amber">⚠ Puncak Tertinggi</span>
    </div>
    <div class="kc kc-blue">
      <div class="kc-icon">
        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
      </div>
      <div class="kc-lbl">Rata-rata Luas / Tahun</div>
      <div class="kc-val">{{ number_format($rataRata,0,',','.') }}</div>
      <div class="kc-unit">Ha/Thn</div>
    </div>
    <div class="kc kc-red">
      <div class="kc-icon">
        <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
      </div>
      <div class="kc-lbl">Tahun Terparah</div>
      <div class="kc-val">{{ $tahunTertinggi }}</div>
      <div class="kc-unit">Kebakaran terluas tercatat</div>
      <span class="kc-badge badge-red">Sangat Tinggi</span>
    </div>
  </div>

  {{-- Charts Row --}}
  <div class="grid2">

    {{-- Vertical Bar per Tahun --}}
    <div class="ecard">
      <div class="ecard-title">Luas Kebakaran per Tahun</div>
      <div class="ecard-sub">{{ $tahunList->min() }}–{{ $tahunList->max() }} · Total: {{ number_format($totalLuas,0,',','.') }} Ha</div>
      @php $maxY = $perTahun->max('total'); @endphp
      <div class="vchart-wrap">
        @foreach($perTahun as $row)
        @php
          $barH = $maxY > 0 ? max(4, round(($row->total/$maxY)*130)) : 4;
          $isPk = $row->tahun == $tahunTertinggi;
          $v = $row->total >= 1000 ? round($row->total/1000,1).'k' : number_format($row->total,0,',','.');
        @endphp
        <div class="vbar-group" data-year="{{ $row->tahun }}">
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
      <div class="lchart-wrap">
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
    {{-- Left card --}}
    <div class="ecard">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px;">
        <div>
          <div class="ecard-title">Kontribusi Wilayah Terhadap Kebakaran</div>
          <div class="ecard-sub">Distribusi luas kebakaran per kawasan pengelolaan khusus</div>
        </div>
        <span class="data-ext-tag">Data Kumulatif</span>
      </div>
      @php
        $maxL = $perLokasi->max('total');
        $colors = ['#e53935','#f59e0b','#1565C0','#60a5fa','#a78bfa'];
      @endphp
      @foreach($perLokasi->take(5) as $lok)
      @php
        $pctL = $maxL>0 ? round($lok->total/$maxL*100,1) : 0;
        $pctT = $totalLuas>0 ? round($lok->total/$totalLuas*100,1) : 0;
        $c = $colors[$loop->index] ?? '#60a5fa';
        $isTop = $loop->first;
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
          <div class="prog-fill" style="width:{{ $pctL }}%;background:{{ $c }}"></div>
        </div>
        @if($isTop)
        <div class="prog-note">⚑ Kawasan dengan luas kebakaran terbesar secara kumulatif</div>
        @endif
      </div>
      @endforeach
    </div>

    {{-- Right highlight cards --}}
    <div>
      <div class="hcard hc-purple">
        <div class="hcard-tag">🔺 Terpukul</div>
        <div class="hcard-loc">{{ $topLokasi->lokasi }}</div>
        <div class="hcard-val">~{{ number_format($topLokasi->total,0,',','.') }}<sup>Ha</sup></div>
        <div class="hcard-desc">Kawasan dengan luas kebakaran terbesar secara kumulatif dalam data historis Jawa Timur.</div>
      </div>
      @if($perLokasi->count() > 1)
      @php $sec2 = $perLokasi->skip(1)->first(); @endphp
      <div class="hcard hc-rose">
        <div class="hcard-tag">🔥 Dampak Signifikan</div>
        <div class="hcard-loc">{{ $sec2->lokasi }}</div>
        <div class="hcard-val">{{ number_format($sec2->total,0,',','.') }}<sup>Ha</sup></div>
        <div class="hcard-desc">Kawasan kedua terdampak dengan kontribusi besar terhadap total kebakaran historis.</div>
      </div>
      @endif
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
      <div class="ins-text">Terjadi penurunan pada 2020–2021, kemudian lonjakan tajam mencapai {{ number_format($luasTertinggi,0,',','.') }} Ha pada tahun {{ $tahunTertinggi }} — titik puncak dalam rentang data yang tercatat.</div>
    </div>
    <div class="ins ins-g">
      <div class="ins-icon">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/></svg>
      </div>
      <div class="ins-title">Pengaruh Musim Kemarau</div>
      <div class="ins-text">Kebakaran meningkat signifikan saat musim kemarau panjang. Pemantauan intensif diperlukan setiap Agustus–Oktober untuk menekan risiko kebakaran meluas.</div>
    </div>
    <div class="ins ins-l">
      <div class="ins-icon">
        <svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
      </div>
      <div class="ins-title">Wilayah Prioritas Pengawasan</div>
      <div class="ins-text">{{ $topLokasi->lokasi }} dan kawasan sekitarnya memerlukan prioritas patroli. Peningkatan pengawasan di zona merah sangat direkomendasikan.</div>
    </div>
  </div>

  {{-- Tabel --}}
  <div class="ecard">
    <div class="ecard-title">Tabel Data Sektoral Kebakaran Hutan</div>
    <div class="ecard-sub">Seluruh data historis dari database kebakaranhutan</div>
    <div style="overflow-x:auto;">
      <table class="dtable">
        <thead>
          <tr>
            <th>Tahun</th>
            <th>Lokasi / Kawasan</th>
            <th>Luas (Ha)</th>
            <th>Status Kerawanan</th>
          </tr>
        </thead>
        <tbody id="tBody">
          @foreach($rawData as $row)
          @php
            $luas = floatval($row->luas_areal_kebakaran);
            $isPk = ($row->tahun == $tahunTertinggi && $luas > 1000);
            if($luas >= 2000)    { $sc='sp-st'; $sl='Sangat Tinggi'; }
            elseif($luas >= 500) { $sc='sp-t';  $sl='Tinggi'; }
            elseif($luas >= 100) { $sc='sp-s';  $sl='Sedang'; }
            elseif($luas > 0)    { $sc='sp-r';  $sl='Rendah'; }
            else                 { $sc='sp-p';  $sl='Aman'; }
          @endphp
          <tr class="data-row {{ $isPk?'tr-pk':'' }}"
            data-year="{{ $row->tahun }}"
            data-lokasi="{{ $row->lokasi }}">
            <td>{{ $row->tahun ?: '–' }}</td>
            <td>{{ $row->lokasi ?: '–' }}</td>
            <td>{{ $luas>0 ? number_format($luas,2,',','.') : '0' }}</td>
            <td><span class="spill {{ $sc }}">{{ $sl }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
const filters = { tahun:'', wilayah:'' };

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
  document.getElementById('lbl-'+key).textContent=label||(key==='tahun'?'Tahun':'Wilayah');
  document.querySelectorAll('#drop-'+key+' .fp-option').forEach(el=>el.classList.toggle('selected',el.dataset.val===val));
  document.getElementById('drop-'+key).classList.remove('open');
  document.getElementById('btn-'+key).classList.remove('open');
  applyFilter();
}

function applyFilter(){
  // Table rows
  document.querySelectorAll('#tBody .data-row').forEach(row=>{
    const my=!filters.tahun||row.dataset.year==filters.tahun;
    const ml=!filters.wilayah||row.dataset.lokasi===filters.wilayah;
    row.classList.toggle('hidden-row',!(my&&ml));
  });
  // Bar chart dim
  document.querySelectorAll('.vbar-group').forEach(g=>{
    g.style.opacity=(!filters.tahun||g.dataset.year==filters.tahun)?'1':'0.2';
  });
}

document.addEventListener('click',e=>{
  if(!e.target.closest('.fp-wrap')){
    document.querySelectorAll('.fp-dropdown').forEach(d=>d.classList.remove('open'));
    document.querySelectorAll('.fp-btn').forEach(b=>b.classList.remove('open'));
  }
});
</script>
@endpush