@extends('layouts.app')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --navy:       #003963;
  --navy-mid:   #005088;
  --navy-pale:  #d1e4ff;
  --red:        #b6171e;
  --red-pale:   #ffdad6;
  --amber-pale: #fef3c7;
  --amber-text: #92400e;
  --green-pale: #d1fae5;
  --green-text: #065f46;
  --surface:    #f8f9fe;
  --surface-lo: #f3f3f9;
  --surface-hi: #e7e8ed;
  --border:     #c1c7d1;
  --border-lo:  #e1e2e7;
  --text-1:     #191c20;
  --text-2:     #414750;
  --text-3:     #727781;
  --text-4:     #9aa0ab;
  --radius:     6px;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
.benc { max-width: 1440px; margin: 0 auto; padding: 32px 28px 60px; font-family: 'Plus Jakarta Sans', sans-serif; }

/* ── Header ── */
.benc-header { display:flex; justify-content:space-between; align-items:flex-end; border-bottom:1px solid var(--border-lo); padding-bottom:14px; margin-bottom:24px; flex-wrap:wrap; gap:8px; }
.benc-title  { font-size:22px; font-weight:800; color:var(--text-1); letter-spacing:-.03em; }
.benc-sub    { font-size:13px; color:var(--text-3); margin-top:3px; }
.benc-date   { font-size:12px; color:var(--text-4); }

/* ── KPI 4 cols ── */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
.kpi-card { background:#fff; border:1px solid var(--border-lo); border-radius:var(--radius); padding:18px 20px 16px; position:relative; overflow:hidden; }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:var(--kpi-accent,var(--navy-mid)); }
.kpi-label { font-size:10px; font-weight:700; color:var(--text-4); text-transform:uppercase; letter-spacing:.06em; }
.kpi-val   { font-size:32px; font-weight:800; color:var(--text-1); line-height:1.1; margin:5px 0 3px; letter-spacing:-.03em; }
.kpi-desc  { font-size:11.5px; color:var(--text-3); margin-top:2px; line-height:1.4; }
.kpi-badge { display:inline-flex; align-items:center; gap:3px; font-size:11px; font-weight:700; padding:2px 7px; border-radius:99px; }
.kpi-badge.up   { background:var(--red-pale);   color:var(--red); }
.kpi-badge.down { background:var(--green-pale);  color:var(--green-text); }
.kpi-badge.info { background:var(--navy-pale);   color:var(--navy-mid); }

/* ── Map + Filter grid ── */
.map-row { display:grid; grid-template-columns:1fr 260px; gap:16px; margin-bottom:20px; }
@media(max-width:960px){ .map-row{ grid-template-columns:1fr; } }

.card { background:#fff; border:1px solid var(--border-lo); border-radius:var(--radius); padding:18px 20px; }
.card-hd { font-size:13.5px; font-weight:700; color:var(--text-1); margin-bottom:14px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px; }
.card-hd small { font-size:11px; color:var(--text-4); font-weight:500; }

#map { height:380px; border-radius:4px; border:1px solid var(--border-lo); }

/* ── Filter Side ── */
.filter-side { display:flex; flex-direction:column; gap:14px; }
.fs-title { font-size:13px; font-weight:700; color:var(--text-1); display:flex; align-items:center; gap:7px; padding-bottom:10px; border-bottom:1px solid var(--border-lo); }
.fg { display:flex; flex-direction:column; gap:4px; }
.fg label { font-size:10px; font-weight:700; color:var(--text-3); text-transform:uppercase; letter-spacing:.06em; }
.fg select {
  padding:7px 28px 7px 10px; border:1px solid var(--border-lo); border-radius:5px;
  font-size:12.5px; color:var(--text-1); font-family:inherit; appearance:none;
  background: var(--surface-lo) url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23414750' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") right 8px center/1.2em no-repeat;
  cursor:pointer;
}
.fg select:focus { outline:none; border-color:var(--navy-mid); }
.fp-note { font-size:11px; color:var(--text-4); line-height:1.5; padding:8px 10px; background:var(--surface-lo); border-radius:5px; border:1px solid var(--border-lo); }
.layer-section, .legend-section { padding-top:12px; border-top:1px solid var(--border-lo); }
.layer-section h5, .legend-section h5 { font-size:10px; font-weight:700; color:var(--text-3); text-transform:uppercase; letter-spacing:.06em; margin-bottom:8px; }
.layer-row { display:flex; align-items:center; justify-content:space-between; font-size:12.5px; color:var(--text-2); margin-bottom:7px; }
.toggle { width:34px; height:18px; border-radius:9px; background:var(--border); position:relative; cursor:pointer; transition:background .2s; flex-shrink:0; }
.toggle.on { background:var(--navy-mid); }
.toggle::after { content:''; position:absolute; top:2px; left:2px; width:14px; height:14px; border-radius:50%; background:#fff; transition:left .2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
.toggle.on::after { left:18px; }
.legend-bar { height:10px; border-radius:4px; background:linear-gradient(90deg,var(--navy-pale),var(--navy-mid)); }
.legend-labels { display:flex; justify-content:space-between; font-size:10px; color:var(--text-4); margin-top:3px; }

/* ── Section title ── */
.section-title { font-size:17px; font-weight:800; color:var(--text-1); letter-spacing:-.02em; margin:0 0 14px; padding:28px 0 10px; border-top:1px solid var(--border-lo); display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.scope-pill { font-size:10.5px; font-weight:700; padding:2px 9px; border-radius:99px; background:var(--navy-pale); color:var(--navy-mid); }
.scope-pill.neutral { background:var(--surface-hi); color:var(--text-3); }

/* ── Trend row (50/50) ── */
.trend-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }
@media(max-width:900px){ .trend-row{ grid-template-columns:1fr; } }

/* ── Grid 2 col ── */
.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px; }
@media(max-width:880px){ .grid-2{ grid-template-columns:1fr; } }

/* ── Scatter info box ── */
.scatter-info { margin-top:12px; display:flex; flex-wrap:wrap; gap:8px; }
.si-chip { padding:5px 12px; border-radius:6px; font-size:11.5px; font-weight:600; line-height:1.4; }
.si-chip.high   { background:var(--red-pale);   color:#991b1b; }
.si-chip.mid    { background:var(--amber-pale);  color:var(--amber-text); }
.si-chip.normal { background:var(--green-pale);  color:var(--green-text); }

/* ── Top 3 Terparah ── */
.top3-list { display:flex; flex-direction:column; gap:12px; padding:4px 0; }
.top3-item { display:flex; align-items:center; gap:14px; padding:14px 16px; border-radius:8px; background:var(--surface-lo); border:1px solid var(--border-lo); }
.top3-rank { font-size:22px; font-weight:800; color:var(--text-4); min-width:32px; text-align:center; }
.top3-rank.r1 { color:#b6171e; }
.top3-rank.r2 { color:#d97706; }
.top3-rank.r3 { color:#059669; }
.top3-info { flex:1; min-width:0; }
.top3-nama { font-size:13px; font-weight:700; color:var(--text-1); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.top3-sub  { font-size:11.5px; color:var(--text-3); margin-top:2px; }
.top3-sev  { font-size:24px; font-weight:800; text-align:right; }
.top3-sev .sev-unit { font-size:11px; font-weight:600; color:var(--text-4); display:block; text-align:right; }

/* ── Stacked bars ── */
.sbar-row { margin-bottom:14px; }
.sbar-name { font-size:13px; color:var(--text-2); font-weight:600; margin-bottom:5px; }
.sbar-track { height:26px; border-radius:99px; overflow:hidden; display:flex; background:var(--surface-hi); }
.sbar-seg { height:100%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#fff; overflow:hidden; min-width:0; }
.sbar-seg span { white-space:nowrap; overflow:hidden; padding:0 6px; }
.bar-legend { display:flex; flex-wrap:wrap; gap:7px 16px; margin-top:12px; }
.bl-item { display:flex; align-items:center; gap:5px; font-size:11.5px; color:var(--text-2); }
.bl-dot  { width:10px; height:10px; border-radius:2px; flex-shrink:0; }

/* ── Table ── */
.tbl-controls { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:12px; }
.tc-left { display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-size:12.5px; color:var(--text-2); }
.tc-select {
  padding:6px 26px 6px 10px; border:1px solid var(--border-lo); border-radius:5px;
  font-size:12.5px; font-family:inherit; color:var(--text-1); appearance:none;
  background:#fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23414750' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") right 6px center/1.1em no-repeat;
}
.tc-select:focus { outline:none; border-color:var(--navy-mid); }
.tc-btn { padding:6px 14px; border:1px solid var(--border-lo); border-radius:5px; background:#fff; font-size:12.5px; cursor:pointer; color:var(--text-2); font-family:inherit; display:flex; align-items:center; gap:5px; transition:border-color .15s; }
.tc-btn:hover { border-color:var(--navy-mid); color:var(--navy-mid); }
.table-wrap { overflow-x:auto; }
.data-tbl { width:100%; border-collapse:collapse; font-size:12.5px; }
.data-tbl thead th { padding:9px 12px; background:var(--surface-lo); color:var(--text-3); font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid var(--border-lo); text-align:left; white-space:nowrap; }
.data-tbl thead th:not(:first-child):not(:nth-child(2)) { text-align:right; }
.data-tbl tbody tr { border-bottom:1px solid var(--border-lo); transition:background .1s; }
.data-tbl tbody tr:hover { background:var(--surface-lo); }
.data-tbl tbody td { padding:9px 12px; color:var(--text-1); vertical-align:middle; }
.data-tbl tbody td:not(:first-child):not(:nth-child(2)) { text-align:right; font-family:'DM Mono',monospace; font-size:12px; }
.sev-bar { height:5px; border-radius:3px; background:var(--surface-hi); overflow:hidden; margin-top:3px; }
.sev-fill { height:100%; border-radius:3px; }
.tbl-footer { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-top:12px; font-size:12px; color:var(--text-3); }
.pager-btns { display:flex; gap:5px; }
.pg-btn { padding:5px 11px; border:1px solid var(--border-lo); border-radius:5px; background:#fff; font-size:12px; cursor:pointer; color:var(--text-2); font-family:inherit; transition:all .15s; }
.pg-btn:hover:not(:disabled) { border-color:var(--navy-mid); color:var(--navy-mid); }
.pg-btn.active { background:var(--navy-mid); border-color:var(--navy-mid); color:#fff; }
.pg-btn:disabled { opacity:.4; cursor:default; }

.unfiltered-divider { display:flex; align-items:center; gap:12px; margin:32px 0 0; font-size:11px; font-weight:700; color:var(--text-4); text-transform:uppercase; letter-spacing:.07em; }
.unfiltered-divider::before, .unfiltered-divider::after { content:''; flex:1; height:1px; background:var(--border-lo); }

@media(max-width:720px){ .kpi-row{ grid-template-columns:repeat(2,1fr); } }
</style>
@endpush

@section('content')
<div class="benc">

  {{-- ─── Header ─── --}}
  <div class="benc-header">
    <div>
      <div class="benc-title">Dashboard Analisis Bencana Jawa Timur</div>
      <div class="benc-sub">Comprehensive spatial and quantitative analysis of disaster events, impacts, and mitigation priorities.</div>
    </div>
    <div class="benc-date">Last updated: {{ now()->format('M Y') }}</div>
  </div>

  {{-- ─── KPI 4 cards (semua tahun, tidak difilter) ─── --}}
  <div class="kpi-row">
    <div class="kpi-card" style="--kpi-accent:#005088">
      <div class="kpi-label">Total Kejadian (Semua Tahun)</div>
      <div class="kpi-val">{{ number_format($kpi->total_kejadian) }}</div>
      <div class="kpi-desc">Seluruh jenis bencana yang tercatat di Jawa Timur</div>
    </div>
    <div class="kpi-card" style="--kpi-accent:#b6171e">
      <div class="kpi-label">Total Kerusakan Rumah</div>
      <div class="kpi-val">{{ number_format($kpi->total_rmh_rusak) }}</div>
      <div class="kpi-desc">Unit rumah rusak (berat + sedang + ringan)</div>
    </div>
    <div class="kpi-card" style="--kpi-accent:#d97706">
      <div class="kpi-label">Total Korban Jiwa</div>
      <div class="kpi-val">{{ number_format($kpi->total_korban) }}</div>
      <div class="kpi-desc">Meninggal/hilang + luka-luka seluruh bencana</div>
    </div>
    <div class="kpi-card" style="--kpi-accent:#6366f1">
      <div class="kpi-label">Daerah Korban Terbanyak</div>
      <div class="kpi-val" style="font-size:20px;margin-top:8px;line-height:1.2">
        {{ $korbanTerbanyak ? Str::limit(str_replace('Kabupaten ','',$korbanTerbanyak['nama']), 20) : '—' }}
      </div>
      <div class="kpi-desc">
        {{ $korbanTerbanyak ? number_format($korbanTerbanyak['total_korban']).' korban jiwa tercatat' : 'Data tidak tersedia' }}
      </div>
    </div>
  </div>

  {{-- ─── Map + Filter (KPI & Map tidak difilter) ─── --}}
  <div class="map-row">
    <div class="card" style="padding:16px">
      <div class="card-hd">
        Peta Sebaran Bencana <small>Semua Tahun</small>
      </div>
      <div id="map"></div>
    </div>

    <form method="GET" action="{{ route('bencana') }}" id="filterForm">
      <div class="card filter-side" style="height:100%">
        <div class="fs-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
          Pusat Komando Filter
        </div>

        <div class="fp-note">
          Filter berlaku untuk <strong>Tren Bencana</strong> dan <strong>Analisis Keparahan</strong>. KPI &amp; Peta selalu menampilkan data keseluruhan.
        </div>

        <div class="fg">
          <label>Tahun</label>
          <select name="tahun" onchange="this.form.submit()">
            <option value="">Semua Tahun</option>
            @foreach($tahunList as $t)
              <option value="{{ $t }}" @selected($t == $tahunDipilih)>{{ $t }}</option>
            @endforeach
          </select>
        </div>

        <div class="fg">
          <label>Kabupaten/Kota</label>
          <select name="kabupaten" onchange="this.form.submit()">
            <option value="">Semua Wilayah</option>
            @foreach($kabList as $k)
              <option value="{{ $k }}" @selected($k == $kabDipilih)>{{ $k }}</option>
            @endforeach
          </select>
        </div>

        @if($tahunDipilih || $kabDipilih)
        <a href="{{ route('bencana') }}" style="font-size:12px;color:var(--red);text-decoration:none;display:flex;align-items:center;gap:4px;font-weight:700;">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>Reset Filter
        </a>
        @endif

        <div class="layer-section">
          <h5>Visual Layer</h5>
          <div class="layer-row"><span>Intensity (Events)</span><div class="toggle on" onclick="this.classList.toggle('on')"></div></div>
          <div class="layer-row"><span>Severity (Damage)</span><div class="toggle" onclick="this.classList.toggle('on')"></div></div>
        </div>
        <div class="legend-section">
          <h5>Legend Intensity</h5>
          <div class="legend-bar"></div>
          <div class="legend-labels"><span>Low</span><span>High</span></div>
        </div>
      </div>
    </form>
  </div>

  {{-- ─── Tren Bencana (filtered) — 50/50 ─── --}}
  <div class="section-title">
    Tren Bencana
    @if($tahunDipilih || $kabDipilih)
      <span class="scope-pill">{{ $tahunDipilih ?? 'Semua Tahun' }}{{ $kabDipilih ? ' · '.Str::limit(str_replace('Kabupaten ','',$kabDipilih),18) : '' }}</span>
    @else
      <span class="scope-pill neutral">Semua data</span>
    @endif
  </div>
  <div class="trend-row">
    {{-- Grouped bar chart: total kejadian & korban per tahun --}}
    <div class="card">
      <div class="card-hd">
        Jumlah Kejadian &amp; Korban per Tahun
        <div style="display:flex;gap:14px;align-items:center">
          <span style="display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--text-3)">
            <span style="width:12px;height:12px;border-radius:2px;background:#005088;display:inline-block"></span>Total Kejadian
          </span>
          <span style="display:flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:var(--text-3)">
            <span style="width:12px;height:12px;border-radius:2px;background:#b6171e;display:inline-block"></span>Korban Jiwa
          </span>
        </div>
      </div>
      <canvas id="chartTrend" height="155"></canvas>
    </div>

    {{-- Proporsi jenis bencana — list style seperti referensi --}}
    <div class="card">
      <div class="card-hd">
        Proporsi Jenis Bencana
        <small>{{ $tahunDipilih ?? 'Semua Tahun' }}</small>
      </div>
      @if(count($komposisiTahun))
        <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px">
          @foreach($komposisiTahun as $jen)
          <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
              <span style="font-size:13px;color:var(--text-2);font-weight:500">{{ $jen['label'] }}</span>
              <span style="font-size:14px;font-weight:800;color:var(--text-1);font-family:'DM Mono',monospace">{{ $jen['persen'] }}%</span>
            </div>
            <div style="height:7px;border-radius:99px;background:var(--surface-hi);overflow:hidden">
              <div style="height:100%;border-radius:99px;background:{{ $jen['warna'] }};width:{{ $jen['persen'] }}%;transition:width .6s ease"></div>
            </div>
          </div>
          @endforeach
        </div>
      @else
        <p style="color:var(--text-4);font-size:13px">Tidak ada data</p>
      @endif
    </div>
  </div>

  {{-- ─── Analisis Keparahan (filtered) ─── --}}
  <div class="section-title">
    Analisis Keparahan &amp; Efisiensi Dampak
    @if($tahunDipilih || $kabDipilih)
      <span class="scope-pill">Filter aktif</span>
    @else
      <span class="scope-pill neutral">Semua data</span>
    @endif
  </div>
  <div class="grid-2">
    {{-- Scatter + insight --}}
    <div class="card">
      <div class="card-hd">Frekuensi vs Dampak Ekstrim <small>per Kabupaten</small></div>
      <canvas id="chartScatter" height="190"></canvas>
      <div class="scatter-info" id="scatterInfo"></div>
    </div>

    {{-- Top 3 terparah --}}
    <div class="card">
      <div class="card-hd">
        Top 3 Kabupaten/Kota Terparah
        <small>Peringkat diurutkan berdasarkan rata-rata jumlah kerusakan & korban per 1 kali kejadian bencana.</small>
      </div>
      <div class="top3-list">
        @forelse($top3Parah as $idx => $kab)
          @php
            $rankColors = ['#b6171e','#d97706','#059669'];
            $rc = $rankColors[$idx] ?? '#9aa0ab';
            // Rasio dampak asli per kejadian
            $impactRatio = round($kab['severity_raw']);
          @endphp
          <div class="top3-item">
            <div class="top3-rank r{{ $idx+1 }}">#{{ $idx+1 }}</div>
            <div class="top3-info">
              <div class="top3-nama">{{ str_replace('Kabupaten ','Kab. ', $kab['nama']) }}</div>
              <div class="top3-sub">
                {{ number_format($kab['total_kejadian']) }} kejadian ·
                {{ number_format($kab['total_korban']) }} korban ·
                {{ number_format($kab['total_rmh_rusak']) }} rmh rusak
              </div>
            </div>
            <div class="top3-sev" style="color:{{ $rc }}">
              ~{{ number_format($impactRatio) }}
              <span class="sev-unit">Dampak / Kejadian</span>
            </div>
          </div>
        @empty
          <p style="color:var(--text-4);font-size:13px">Tidak ada data</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════════
       DATA KESELURUHAN (tidak terpengaruh filter)
  ══════════════════════════════════════════════════ --}}
  <div class="unfiltered-divider">Data Keseluruhan · Tidak Terpengaruh Filter</div>

  {{-- Profil Risiko --}}
  <div class="section-title">
    Karakteristik &amp; Profil Risiko Wilayah
    <span class="scope-pill neutral">Semua tahun &amp; wilayah</span>
  </div>
  <div class="card" style="margin-bottom:20px">
    <div class="card-hd">
      Komposisi Bencana per Kabupaten <small>Top 5 Paling Terdampak</small>
      <div class="bar-legend" id="barLegend"></div>
    </div>
    <div id="stackedBars"></div>
  </div>

  {{-- Tabel Detail --}}
  <div class="section-title">
    Skala Prioritas Penanganan &amp; Alokasi
    <span class="scope-pill neutral">Semua tahun &amp; wilayah</span>
  </div>
  <div class="card">
    <div class="tbl-controls">
      <div class="card-hd" style="margin-bottom:0">Detail Data Kejadian</div>
      <div class="tc-left">
        Tampilkan:
        <select class="tc-select" id="perPage" onchange="renderTable()">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="999">Semua</option>
        </select>
        <select class="tc-select" id="tblSort" onchange="renderTable()">
          <option value="kejadian">Urutkan: Kejadian ↓</option>
          <option value="korban">Urutkan: Korban ↓</option>
          <option value="rumah">Urutkan: Rmh Rusak ↓</option>
          <option value="severity">Urutkan: Severity ↓</option>
        </select>
        <button class="tc-btn" onclick="resetTblFilter()">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>Reset
        </button>
      </div>
    </div>

    <div class="table-wrap">
      <table class="data-tbl">
        <thead>
          <tr>
            <th>#</th>
            <th>Kabupaten/Kota</th>
            <th>Total Kejadian</th>
            <th>Kerusakan Rumah</th>
            <th>Korban Jiwa</th>
            <th>Severity (%)</th>
          </tr>
        </thead>
        <tbody id="tblBody"></tbody>
      </table>
    </div>

    <div class="tbl-footer">
      <span id="pagerInfo"></span>
      <div class="pager-btns" id="pagerBtns"></div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Data ──────────────────────────────────────────────────────
const DATA_TREND        = @json($trend);
const DATA_KOMPOSISI_T  = @json($komposisiTahun);   // pie (filtered)
const DATA_SCATTER      = @json($scatter);           // filtered
const DATA_KOMPOSISI_ALL= @json($komposisiAll);      // stacked bar legend
const DATA_TOP5         = @json($top5);              // unfiltered
const DATA_DETAIL       = @json($detail);            // unfiltered

// ── 1. TREND GROUPED BAR (kejadian + korban per tahun) ───────
(function(){
  if(!DATA_TREND.length) return;
  new Chart(document.getElementById('chartTrend'),{
    type:'bar',
    data:{
      labels: DATA_TREND.map(d=>d.tahun),
      datasets:[
        {
          label:'Total Kejadian',
          data: DATA_TREND.map(d=>d.total),
          backgroundColor:'#005088',
          borderRadius:4,
          barPercentage:0.6,
          categoryPercentage:0.7,
          yAxisID:'y'
        },
        {
          label:'Korban Jiwa',
          data: DATA_TREND.map(d=>d.total_korban ?? 0),
          backgroundColor:'#b6171e',
          borderRadius:4,
          barPercentage:0.6,
          categoryPercentage:0.7,
          yAxisID:'y1'
        }
      ]
    },
    options:{
      responsive:true,
      interaction:{ mode:'index', intersect:false },
      plugins:{ legend:{ display:false } },
      scales:{
        y:{
          beginAtZero:true, grid:{color:'#f1f5f9'},
          ticks:{font:{size:10}, color:'#005088'},
          title:{display:true,text:'Jumlah Kejadian',font:{size:10},color:'#005088'}
        },
        y1:{
          beginAtZero:true, position:'right',
          grid:{drawOnChartArea:false},
          ticks:{font:{size:10}, color:'#b6171e'},
          title:{display:true,text:'Korban Jiwa',font:{size:10},color:'#b6171e'}
        },
        x:{ grid:{display:false}, ticks:{font:{size:10}} }
      }
    }
  });
})();

// ── 3. SCATTER + INFO CHIPS ───────────────────────────────────
(function(){
  if(!DATA_SCATTER.length) return;
  const maxF = Math.max(...DATA_SCATTER.map(d=>d.frekuensi));
  const maxD = Math.max(...DATA_SCATTER.map(d=>d.dampak));
  const cX=maxF*0.6, cY=maxD*0.6;

  // Sort by dampak desc for insight
  const sorted = DATA_SCATTER.slice().sort((a,b)=>b.dampak-a.dampak);
  const highest  = sorted[0];
  const highFreq = DATA_SCATTER.slice().sort((a,b)=>b.frekuensi-a.frekuensi)[0];

  new Chart(document.getElementById('chartScatter'),{
    type:'scatter',
    data:{ datasets:[{
      label:'Kabupaten',
      data: DATA_SCATTER.map(d=>({x:d.frekuensi,y:d.dampak,nama:d.nama})),
      backgroundColor: DATA_SCATTER.map(d=>d.frekuensi>=cX&&d.dampak>=cY?'#b6171ecc':'#005088aa'),
      pointRadius:7, pointHoverRadius:9
    }]},
    options:{
      plugins:{
        legend:{display:false},
        tooltip:{callbacks:{label:c=>`${c.raw.nama} — frek: ${c.raw.x}, dampak: ${c.raw.y}`}}
      },
      scales:{
        x:{title:{display:true,text:'Frequency (Jumlah Kejadian)',font:{size:10}},grid:{color:'#f1f5f9'},ticks:{font:{size:10}}},
        y:{title:{display:true,text:'Impact (Korban + Rmh Rusak)',font:{size:10}},grid:{color:'#f1f5f9'},ticks:{font:{size:10}}}
      }
    }
  });

  // Info chips
  const box = document.getElementById('scatterInfo');
  if(highest){
    box.innerHTML += `<div class="si-chip high">🔴 Dampak tertinggi: <strong>${highest.nama.replace('Kabupaten ','Kab. ')}</strong> (${highest.dampak.toLocaleString('id-ID')} jiwa+rmh)</div>`;
  }
  if(highFreq && highFreq.nama !== highest?.nama){
    box.innerHTML += `<div class="si-chip mid">🟡 Frekuensi tertinggi: <strong>${highFreq.nama.replace('Kabupaten ','Kab. ')}</strong> (${highFreq.frekuensi} kejadian)</div>`;
  }
  const critical = DATA_SCATTER.filter(d=>d.frekuensi>=cX&&d.dampak>=cY);
  if(critical.length){
    box.innerHTML += `<div class="si-chip high">⚠ ${critical.length} daerah masuk Critical Zone (frek &amp; dampak tinggi)</div>`;
  }
})();

// ── 4. STACKED BAR (unfiltered) ───────────────────────────────
(function(){
  const wrap=document.getElementById('stackedBars');
  const legend=document.getElementById('barLegend');
  if(!DATA_TOP5.length){ wrap.innerHTML='<p style="color:var(--text-4);font-size:13px">Tidak ada data</p>'; return; }
  const jenisList=DATA_KOMPOSISI_ALL.slice(0,5);
  jenisList.forEach(j=>{
    legend.innerHTML+=`<div class="bl-item"><div class="bl-dot" style="background:${j.warna}"></div>${j.label}</div>`;
  });
  DATA_TOP5.forEach(kab=>{
    const total=Object.values(kab.kejadian||{}).reduce((a,b)=>a+b,0)||1;
    let segs='';
    jenisList.forEach(j=>{
      const pct=((kab.kejadian?.[j.kode]??0)/total*100).toFixed(1);
      if(+pct>0) segs+=`<div class="sbar-seg" style="width:${pct}%;background:${j.warna}"><span>${+pct>9?pct+'%':''}</span></div>`;
    });
    const sumTop=jenisList.reduce((a,j)=>a+(kab.kejadian?.[j.kode]??0),0);
    const rest=Math.max(0,total-sumTop);
    const rPct=(rest/total*100).toFixed(1);
    if(+rPct>0) segs+=`<div class="sbar-seg" style="width:${rPct}%;background:#c1c7d1"></div>`;
    wrap.innerHTML+=`<div class="sbar-row"><div class="sbar-name">${kab.nama.replace('Kabupaten ','Kab. ')}</div><div class="sbar-track">${segs}</div></div>`;
  });
})();

// ── 5. TABLE (unfiltered, tanpa kolom Status) ─────────────────
let currentPage=1;
function getFiltered(){
  const sortMap={kejadian:'total_kejadian',korban:'total_korban',rumah:'total_rmh_rusak',severity:'severity_pct'};
  const key=sortMap[document.getElementById('tblSort').value]||'total_kejadian';
  return DATA_DETAIL.slice().sort((a,b)=>b[key]-a[key]);
}
function renderTable(){ currentPage=1; renderPage(); }
function renderPage(){
  const perPage=+document.getElementById('perPage').value||10;
  const filtered=getFiltered();
  const total=filtered.length;
  const pages=perPage>=999?1:Math.ceil(total/perPage);
  if(currentPage>pages) currentPage=pages||1;
  const start=perPage>=999?0:(currentPage-1)*perPage;
  const end=perPage>=999?total:Math.min(start+perPage,total);
  const rows=filtered.slice(start,end);

  document.getElementById('tblBody').innerHTML=rows.map((r,i)=>{
    const pct=r.severity_pct??0;
    const sevColor=pct>=70?'#b6171e':pct>=40?'#d97706':'#059669';
    return `<tr>
      <td style="color:var(--text-4)">${start+i+1}</td>
      <td style="font-weight:600">${r.nama}</td>
      <td>${r.total_kejadian.toLocaleString('id-ID')}</td>
      <td>${r.total_rmh_rusak.toLocaleString('id-ID')}</td>
      <td>${r.total_korban.toLocaleString('id-ID')}</td>
      <td>
        <span style="font-family:'DM Mono',monospace;font-weight:700;color:${sevColor}">${pct}%</span>
        <div class="sev-bar"><div class="sev-fill" style="width:${pct}%;background:${sevColor}"></div></div>
      </td>
    </tr>`;
  }).join('');

  document.getElementById('pagerInfo').textContent=
    total===0?'Tidak ada data':`Menampilkan ${start+1}–${end} dari ${total} wilayah`;

  const btnWrap=document.getElementById('pagerBtns');
  btnWrap.innerHTML='';
  if(pages<=1) return;
  const mk=(lbl,p,active,dis)=>{
    const b=document.createElement('button');
    b.className='pg-btn'+(active?' active':''); b.textContent=lbl; b.disabled=dis;
    b.onclick=()=>{currentPage=p;renderPage();}; btnWrap.appendChild(b);
  };
  mk('‹',currentPage-1,false,currentPage===1);
  const sp=Math.max(1,currentPage-2),ep=Math.min(pages,sp+4);
  for(let p=sp;p<=ep;p++) mk(p,p,p===currentPage,false);
  mk('›',currentPage+1,false,currentPage===pages);
}
function resetTblFilter(){ document.getElementById('tblSort').value='kejadian'; document.getElementById('perPage').value='10'; renderTable(); }
renderTable();

// ── 6. LEAFLET MAP (semua tahun, tidak difilter) ──────────────
(function(){
  if(typeof L==='undefined') return;
  const map=L.map('map').setView([-7.5,112.5],8);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OpenStreetMap',maxZoom:13}).addTo(map);
  fetch('/api/geojson')
    .then(r=>r.json())
    .then(geo=>{
      const vals=geo.features.map(f=>f.properties.total_kejadian||0);
      const maxV=Math.max(1,...vals);
      const getColor=v=>{const t=v/maxV;return t>.75?'#003963':t>.5?'#005088':t>.25?'#3b82f6':t>0?'#bfdbfe':'#f1f5f9';};
      L.geoJSON(geo,{
        style:f=>({fillColor:getColor(f.properties.total_kejadian||0),fillOpacity:.75,color:'#fff',weight:1}),
        onEachFeature:(f,layer)=>{
          const p=f.properties;
          layer.bindTooltip(`<b>${p.NAMOBJ||p.nama||'—'}</b><br>Kejadian: ${p.total_kejadian||0}<br>Korban: ${p.total_korban||0}<br>Rmh Rusak: ${p.rmh_rusak||0}`,{sticky:true});
        }
      }).addTo(map);
    })
    .catch(()=>{
      document.getElementById('map').innerHTML='<div style="height:100%;display:flex;align-items:center;justify-content:center;color:#9aa0ab;font-size:13px;flex-direction:column;gap:6px"><span>Map Visualization Placeholder</span><small>Letakkan GeoJSON di storage/app/geojson/jatim.geojson</small></div>';
    });
})();
</script>
@endpush