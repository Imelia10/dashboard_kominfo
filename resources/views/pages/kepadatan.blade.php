@extends('layouts.app')

@section('title', 'Kepadatan Penduduk - Jawa Timur')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
  /* ──────────────────────── TOKENS ──────────────────────── */
  :root {
    --c-bg:        #F0F4FA;
    --c-card:      #FFFFFF;
    --c-border:    #DDE3EF;
    --c-primary:   #1565C0;
    --c-primary-d: #0D47A1;
    --c-text:      #111827;
    --c-muted:     #6B7280;
    --c-tp:        #10B981;   /* tidak padat  */
    --c-kp:        #3B82F6;   /* kurang padat */
    --c-cp:        #F59E0B;   /* cukup padat  */
    --c-sp:        #EF4444;   /* sangat padat */
    --shadow:      0 2px 12px rgba(21,101,192,.09);
    --r:           14px;
  }

  /* ──────────────────────── LAYOUT ──────────────────────── */
  .kpd-wrap {
    padding: 24px;
    min-height: 100%;
    background: var(--c-bg);
    font-family: 'DM Sans', sans-serif;
  }

  /* ─── PAGE HEADER ─── */
  .kpd-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 22px;
  }
  .kpd-title { font-size: 20px; font-weight: 700; color: var(--c-text); }
  .kpd-subtitle {
    font-size: 12.5px; color: var(--c-muted);
    margin-top: 2px;
  }
  .kpd-source {
    font-size: 10.5px; color: var(--c-muted);
    background: #fff; border: 1px solid var(--c-border);
    padding: 4px 10px; border-radius: 20px;
    align-self: center;
  }
  .kpd-source a { color: var(--c-primary); text-decoration: none; }
  .kpd-source a:hover { text-decoration: underline; }

  /* ─── FILTER BAR ─── */
  .filter-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    background: #fff;
    border: 1px solid var(--c-border);
    border-radius: var(--r);
    padding: 12px 16px;
    margin-bottom: 20px;
    box-shadow: var(--shadow);
  }
  .filter-label { font-size: 12px; font-weight: 600; color: var(--c-muted); white-space: nowrap; }
  .filter-bar select, .filter-bar input[type=text] {
    height: 34px;
    padding: 0 10px;
    border: 1px solid var(--c-border);
    border-radius: 8px;
    font-size: 13px;
    color: var(--c-text);
    background: #F9FAFB;
    outline: none;
    cursor: pointer;
    transition: border-color .2s;
  }
  .filter-bar select:focus, .filter-bar input:focus { border-color: var(--c-primary); }
  .filter-bar input[type=text] { width: 200px; }

  .btn-reset {
    height: 34px; padding: 0 14px;
    background: #EFF6FF; color: var(--c-primary);
    border: 1px solid #BFDBFE;
    border-radius: 8px; font-size: 12.5px; font-weight: 600;
    cursor: pointer; transition: background .2s;
  }
  .btn-reset:hover { background: #DBEAFE; }

  .filter-count {
    margin-left: auto;
    font-size: 12px; color: var(--c-muted);
    white-space: nowrap;
  }
  .filter-count span { font-weight: 700; color: var(--c-text); }

  /* ─── STAT CARDS ─── */
  .stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
  }
  .stat-card {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--r);
    padding: 14px 16px;
    box-shadow: var(--shadow);
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
  }
  .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(21,101,192,.13); }
  .stat-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
  }
  .stat-card.blue::before  { background: var(--c-primary); }
  .stat-card.green::before { background: var(--c-tp); }
  .stat-card.cyan::before  { background: var(--c-kp); }
  .stat-card.amber::before { background: var(--c-cp); }
  .stat-card.red::before   { background: var(--c-sp); }
  .stat-card .sc-label { font-size: 11px; font-weight: 600; color: var(--c-muted); text-transform: uppercase; letter-spacing: .5px; }
  .stat-card .sc-value { font-size: 22px; font-weight: 800; color: var(--c-text); margin: 4px 0 0; line-height: 1; }
  .stat-card .sc-sub   { font-size: 11.5px; color: var(--c-muted); margin-top: 3px; }

  /* ─── LEGEND BADGE ─── */
  .legend-row {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin-bottom: 16px;
  }
  .legend-badge {
    display: flex; align-items: center; gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px; font-weight: 600;
    cursor: pointer;
    transition: opacity .2s, transform .15s;
    user-select: none;
  }
  .legend-badge:hover { transform: scale(1.04); }
  .legend-badge.active { opacity: 1; }
  .legend-badge.inactive { opacity: .4; }
  .lb-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }
  .lb-tp { background: #D1FAE5; color: #065F46; border: 1.5px solid #6EE7B7; }
  .lb-kp { background: #DBEAFE; color: #1E40AF; border: 1.5px solid #93C5FD; }
  .lb-cp { background: #FEF3C7; color: #92400E; border: 1.5px solid #FCD34D; }
  .lb-sp { background: #FEE2E2; color: #991B1B; border: 1.5px solid #FCA5A5; }

  /* ─── TWO-COLUMN GRID ─── */
  .main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 20px;
  }
  @media(max-width:900px){ .main-grid { grid-template-columns: 1fr; } }

  /* ─── CARD ─── */
  .kpd-card {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--r);
    box-shadow: var(--shadow);
    overflow: hidden;
  }
  .card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px 0;
  }
  .card-title { font-size: 13.5px; font-weight: 700; color: var(--c-text); }
  .card-body  { padding: 14px 18px 18px; }

  /* ─── MAP ─── */
  #kpd-map { height: 420px; border-radius: 10px; }

  /* ─── TABLE ─── */
  .kpd-table-wrap {
    overflow-x: auto;
    max-height: 420px;
    overflow-y: auto;
  }
  .kpd-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
  }
  .kpd-table thead th {
    position: sticky; top: 0;
    background: #F1F5FB;
    padding: 9px 12px;
    text-align: left;
    font-weight: 700;
    color: var(--c-muted);
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .4px;
    border-bottom: 2px solid var(--c-border);
    white-space: nowrap;
    cursor: pointer;
    user-select: none;
  }
  .kpd-table thead th:hover { background: #E8EFFE; color: var(--c-primary); }
  .kpd-table tbody tr {
    border-bottom: 1px solid #F3F4F6;
    transition: background .15s;
    cursor: pointer;
  }
  .kpd-table tbody tr:hover { background: #EFF6FF; }
  .kpd-table tbody tr.selected-row { background: #DBEAFE; }
  .kpd-table tbody td {
    padding: 8px 12px;
    color: var(--c-text);
    white-space: nowrap;
  }

  .badge-kpd {
    display: inline-block;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 11px; font-weight: 700;
  }
  .badge-tp { background: #D1FAE5; color: #065F46; }
  .badge-kp { background: #DBEAFE; color: #1E40AF; }
  .badge-cp { background: #FEF3C7; color: #92400E; }
  .badge-sp { background: #FEE2E2; color: #991B1B; }

  /* ─── CHART ─── */
  .chart-wrap { position: relative; width: 100%; }

  /* ─── BOTTOM GRID ─── */
  .bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 20px;
  }
  @media(max-width:900px){ .bottom-grid { grid-template-columns: 1fr; } }

  /* ─── DRILL DOWN PANEL ─── */
  #drill-panel {
    background: var(--c-card);
    border: 1px solid var(--c-border);
    border-radius: var(--r);
    box-shadow: var(--shadow);
    padding: 18px;
    margin-bottom: 20px;
    display: none;
  }
  #drill-panel.visible { display: block; }
  .drill-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px;
  }
  .drill-title { font-size: 14px; font-weight: 700; color: var(--c-text); }
  .drill-close {
    width: 28px; height: 28px;
    background: #F3F4F6; border: none;
    border-radius: 50%; cursor: pointer;
    font-size: 16px; color: var(--c-muted);
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
  }
  .drill-close:hover { background: #FEE2E2; color: #EF4444; }
  .drill-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  @media(max-width:700px){ .drill-grid { grid-template-columns: 1fr; } }

  /* ─── LOADING ─── */
  .loading-overlay {
    position: absolute; inset: 0;
    background: rgba(255,255,255,.7);
    display: flex; align-items: center; justify-content: center;
    border-radius: var(--r);
    z-index: 50;
    display: none;
  }
  .loading-overlay.active { display: flex; }
  .spinner {
    width: 28px; height: 28px;
    border: 3px solid #BFDBFE;
    border-top-color: var(--c-primary);
    border-radius: 50%;
    animation: spin .7s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ─── TOOLTIP ─── */
  .leaflet-tooltip-custom {
    background: #1e2d40;
    border: none;
    color: #fff;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-family: 'DM Sans', sans-serif;
    box-shadow: 0 4px 12px rgba(0,0,0,.3);
  }
</style>
@endpush

@section('content')
<div class="kpd-wrap">

  {{-- PAGE HEADER --}}
  <div class="kpd-header">
    <div>
      <div class="kpd-title">🗺️ Kepadatan Penduduk — Jawa Timur</div>
      <div class="kpd-subtitle">Distribusi & kepadatan penduduk per kabupaten/kota berdasarkan jumlah jiwa per km²</div>
    </div>
    <div class="kpd-source">
      Kategori kepadatan: <a href="https://dinkes.jogjaprov.go.id" target="_blank">Dinkes DIY</a>
      &nbsp;|&nbsp; 0–50 Tidak Padat · 51–250 Kurang Padat · 251–400 Cukup Padat · &gt;400 Sangat Padat
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="filter-bar">
    <span class="filter-label">Tahun</span>
    <select id="sel-tahun">
      @foreach($tahunList as $t)
        <option value="{{ $t }}" {{ $t == $tahunDefault ? 'selected' : '' }}>{{ $t }}</option>
      @endforeach
    </select>

    <span class="filter-label">Kategori</span>
    <select id="sel-kategori">
      <option value="">Semua</option>
      <option value="Tidak Padat">Tidak Padat</option>
      <option value="Kurang Padat">Kurang Padat</option>
      <option value="Cukup Padat">Cukup Padat</option>
      <option value="Sangat Padat">Sangat Padat</option>
    </select>

    <span class="filter-label">Cari</span>
    <input type="text" id="inp-search" placeholder="Nama kabupaten/kota...">

    <button class="btn-reset" onclick="resetFilter()">↺ Reset</button>
    <div class="filter-count">Menampilkan <span id="cnt-show">–</span> dari <span id="cnt-total">–</span> wilayah</div>
  </div>

  {{-- STAT CARDS --}}
  <div class="stat-grid" id="stat-cards">
    <div class="stat-card blue">
      <div class="sc-label">Total Penduduk</div>
      <div class="sc-value" id="sc-total">–</div>
      <div class="sc-sub">Jawa Timur</div>
    </div>
    <div class="stat-card blue">
      <div class="sc-label">Rata-rata Kepadatan</div>
      <div class="sc-value" id="sc-avg">–</div>
      <div class="sc-sub">jiwa/km²</div>
    </div>
    <div class="stat-card green">
      <div class="sc-label">Tidak Padat</div>
      <div class="sc-value" id="sc-tp">–</div>
      <div class="sc-sub">0–50 jiwa/km²</div>
    </div>
    <div class="stat-card cyan">
      <div class="sc-label">Kurang Padat</div>
      <div class="sc-value" id="sc-kp">–</div>
      <div class="sc-sub">51–250 jiwa/km²</div>
    </div>
    <div class="stat-card amber">
      <div class="sc-label">Cukup Padat</div>
      <div class="sc-value" id="sc-cp">–</div>
      <div class="sc-sub">251–400 jiwa/km²</div>
    </div>
    <div class="stat-card red">
      <div class="sc-label">Sangat Padat</div>
      <div class="sc-value" id="sc-sp">–</div>
      <div class="sc-sub">&gt;400 jiwa/km²</div>
    </div>
  </div>

  {{-- LEGEND FILTER --}}
  <div class="legend-row" id="legend-row">
    <div class="legend-badge lb-tp active" data-cat="Tidak Padat" onclick="toggleLegend(this)">
      <div class="lb-dot" style="background:#10B981"></div> Tidak Padat
    </div>
    <div class="legend-badge lb-kp active" data-cat="Kurang Padat" onclick="toggleLegend(this)">
      <div class="lb-dot" style="background:#3B82F6"></div> Kurang Padat
    </div>
    <div class="legend-badge lb-cp active" data-cat="Cukup Padat" onclick="toggleLegend(this)">
      <div class="lb-dot" style="background:#F59E0B"></div> Cukup Padat
    </div>
    <div class="legend-badge lb-sp active" data-cat="Sangat Padat" onclick="toggleLegend(this)">
      <div class="lb-dot" style="background:#EF4444"></div> Sangat Padat
    </div>
  </div>

  {{-- MAIN GRID: MAP + TABLE --}}
  <div class="main-grid">
    {{-- MAP --}}
    <div class="kpd-card">
      <div class="card-head">
        <div class="card-title">🗺 Peta Kepadatan</div>
        <div style="font-size:11px;color:var(--c-muted)">Klik wilayah untuk drill-down</div>
      </div>
      <div class="card-body" style="position:relative">
        <div class="loading-overlay active" id="map-loading"><div class="spinner"></div></div>
        <div id="kpd-map"></div>
      </div>
    </div>

    {{-- TABLE --}}
    <div class="kpd-card">
      <div class="card-head">
        <div class="card-title">📋 Tabel Kepadatan</div>
        <div style="font-size:11px;color:var(--c-muted)">Klik baris untuk drill-down</div>
      </div>
      <div class="card-body" style="padding-top:8px">
        <div class="kpd-table-wrap">
          <table class="kpd-table" id="kpd-table">
            <thead>
              <tr>
                <th onclick="sortTable('nama')">Wilayah ⇅</th>
                <th onclick="sortTable('total')">Total Jiwa ⇅</th>
                <th onclick="sortTable('luas')">Luas km² ⇅</th>
                <th onclick="sortTable('kepadatan')">Kepadatan ⇅</th>
                <th>Kategori</th>
              </tr>
            </thead>
            <tbody id="table-body">
              <tr><td colspan="5" style="text-align:center;padding:20px;color:var(--c-muted)">Memuat data...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- DRILL DOWN PANEL --}}
  <div id="drill-panel">
    <div class="drill-head">
      <div class="drill-title" id="drill-title">Detail Wilayah</div>
      <button class="drill-close" onclick="closeDrill()">✕</button>
    </div>
    <div class="drill-grid">
      {{-- Info cards --}}
      <div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px" id="drill-stats"></div>
        <div class="chart-wrap"><canvas id="chart-trend" height="180"></canvas></div>
      </div>
      {{-- Pie + gender --}}
      <div>
        <div class="chart-wrap" style="max-width:260px;margin:0 auto"><canvas id="chart-gender" height="220"></canvas></div>
      </div>
    </div>
  </div>

  {{-- BOTTOM CHARTS --}}
  <div class="bottom-grid">
    <div class="kpd-card">
      <div class="card-head"><div class="card-title">📊 Top 10 Kepadatan Tertinggi</div></div>
      <div class="card-body"><div class="chart-wrap"><canvas id="chart-top10" height="280"></canvas></div></div>
    </div>
    <div class="kpd-card">
      <div class="card-head"><div class="card-title">🍩 Komposisi Kategori</div></div>
      <div class="card-body"><div class="chart-wrap" style="max-width:280px;margin:0 auto"><canvas id="chart-donut" height="280"></canvas></div></div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
// ═══════════════════════════════════════════════════════
//  STATE
// ═══════════════════════════════════════════════════════
let allData       = [];
let filteredData  = [];
let geojsonLayer  = null;
let map           = null;
let sortCol       = 'kepadatan';
let sortDir       = -1;
let activeCats    = new Set(['Tidak Padat','Kurang Padat','Cukup Padat','Sangat Padat']);
let chartTop10    = null;
let chartDonut    = null;
let chartTrend    = null;
let chartGender   = null;
let selectedKode  = null;

// ═══════════════════════════════════════════════════════
//  COLOURS
// ═══════════════════════════════════════════════════════
function catColor(kat) {
  return { 'Tidak Padat':'#10B981','Kurang Padat':'#3B82F6','Cukup Padat':'#F59E0B','Sangat Padat':'#EF4444' }[kat] || '#9CA3AF';
}
function catBadge(kat) {
  return { 'Tidak Padat':'badge-tp','Kurang Padat':'badge-kp','Cukup Padat':'badge-cp','Sangat Padat':'badge-sp' }[kat] || '';
}

// ═══════════════════════════════════════════════════════
//  INIT MAP
// ═══════════════════════════════════════════════════════
function initMap() {
  map = L.map('kpd-map', { zoomControl: true, scrollWheelZoom: false })
         .setView([-7.5, 112.0], 8);

  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '© OpenStreetMap © CartoDB',
    maxZoom: 18
  }).addTo(map);
}

// ═══════════════════════════════════════════════════════
//  LOAD DATA
// ═══════════════════════════════════════════════════════
async function loadData() {
  const tahun = document.getElementById('sel-tahun').value;
  document.getElementById('map-loading').classList.add('active');

  try {
    const res  = await fetch(`/api/kepadatan?tahun=${tahun}`);
    const json = await res.json();
    allData = json.data;
    updateSummary(json.summary);
    applyFilter();
    loadGeoJSON(tahun);
  } catch(e) {
    console.error('Error loading data:', e);
  }
}

// ═══════════════════════════════════════════════════════
//  GEOJSON MAP LAYER
// ═══════════════════════════════════════════════════════
async function loadGeoJSON(tahun) {
  try {
    const res  = await fetch(`/api/geojson?tahun=${tahun}`);
    const geo  = await res.json();

    if (geojsonLayer) map.removeLayer(geojsonLayer);

    // Build lookup: kode -> row
    const lookup = {};
    allData.forEach(d => { lookup[d.kode_kabupaten_kota] = d; });

    geojsonLayer = L.geoJSON(geo, {
      style: feature => {
        const kode = feature.properties?.kode_kabupaten_kota;
        const row  = lookup[kode];
        const col  = row ? catColor(row.kategori_kepadatan) : '#CBD5E1';
        return { fillColor: col, fillOpacity: .65, color: '#fff', weight: 1 };
      },
      onEachFeature: (feature, layer) => {
        const kode = feature.properties?.kode_kabupaten_kota;
        const row  = lookup[kode];
        if (!row) return;

        layer.bindTooltip(`
          <b>${row.nama_kabupaten_kota}</b><br>
          Kepadatan: <b>${Number(row.kepadatan).toLocaleString('id')}</b> jiwa/km²<br>
          Kategori: <b>${row.kategori_kepadatan}</b><br>
          Total: ${Number(row.total_penduduk).toLocaleString('id')} jiwa
        `, { className: 'leaflet-tooltip-custom', direction: 'top' });

        layer.on({
          mouseover: e => { e.target.setStyle({ fillOpacity: .9, weight: 2 }); },
          mouseout:  e => { if (selectedKode !== kode) geojsonLayer.resetStyle(e.target); },
          click:     e => { openDrill(kode); highlightMap(kode); }
        });
      }
    }).addTo(map);

    document.getElementById('map-loading').classList.remove('active');
  } catch(e) {
    document.getElementById('map-loading').classList.remove('active');
    console.error('GeoJSON error:', e);
  }
}

function highlightMap(kode) {
  selectedKode = kode;
  if (!geojsonLayer) return;
  geojsonLayer.eachLayer(layer => {
    const k = layer.feature?.properties?.kode_kabupaten_kota;
    if (k === kode) {
      layer.setStyle({ fillOpacity: 1, weight: 3, color: '#1565C0' });
    } else {
      geojsonLayer.resetStyle(layer);
    }
  });
}

// ═══════════════════════════════════════════════════════
//  SUMMARY CARDS
// ═══════════════════════════════════════════════════════
function updateSummary(s) {
  document.getElementById('sc-total').textContent = Number(s.total_penduduk).toLocaleString('id');
  document.getElementById('sc-avg').textContent   = Number(s.rata_kepadatan).toLocaleString('id');
  document.getElementById('sc-tp').textContent    = s.tidak_padat;
  document.getElementById('sc-kp').textContent    = s.kurang_padat;
  document.getElementById('sc-cp').textContent    = s.cukup_padat;
  document.getElementById('sc-sp').textContent    = s.sangat_padat;
}

// ═══════════════════════════════════════════════════════
//  FILTER + RENDER TABLE
// ═══════════════════════════════════════════════════════
function applyFilter() {
  const cat    = document.getElementById('sel-kategori').value;
  const search = document.getElementById('inp-search').value.toLowerCase();

  filteredData = allData.filter(d => {
    const catOk    = cat    ? d.kategori_kepadatan === cat    : activeCats.has(d.kategori_kepadatan);
    const searchOk = search ? d.nama_kabupaten_kota.toLowerCase().includes(search) : true;
    return catOk && searchOk;
  });

  // Sort
  filteredData.sort((a, b) => {
    let va, vb;
    if      (sortCol === 'nama')      { va = a.nama_kabupaten_kota; vb = b.nama_kabupaten_kota; }
    else if (sortCol === 'total')     { va = +a.total_penduduk;     vb = +b.total_penduduk; }
    else if (sortCol === 'luas')      { va = +a.luas_km2;           vb = +b.luas_km2; }
    else                              { va = +a.kepadatan;          vb = +b.kepadatan; }
    if (va < vb) return -1 * sortDir;
    if (va > vb) return  1 * sortDir;
    return 0;
  });

  document.getElementById('cnt-show').textContent  = filteredData.length;
  document.getElementById('cnt-total').textContent = allData.length;
  renderTable();
  renderCharts();
}

function renderTable() {
  const tbody = document.getElementById('table-body');
  if (!filteredData.length) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:20px;color:var(--c-muted)">Tidak ada data.</td></tr>';
    return;
  }
  tbody.innerHTML = filteredData.map(d => `
    <tr onclick="openDrill('${d.kode_kabupaten_kota}')" class="${selectedKode===d.kode_kabupaten_kota?'selected-row':''}">
      <td><b>${d.nama_kabupaten_kota}</b></td>
      <td>${Number(d.total_penduduk).toLocaleString('id')}</td>
      <td>${Number(d.luas_km2).toLocaleString('id', {maximumFractionDigits:2})}</td>
      <td><b>${Number(d.kepadatan).toLocaleString('id')}</b></td>
      <td><span class="badge-kpd ${catBadge(d.kategori_kepadatan)}">${d.kategori_kepadatan}</span></td>
    </tr>
  `).join('');
}

// ═══════════════════════════════════════════════════════
//  CHARTS
// ═══════════════════════════════════════════════════════
function renderCharts() {
  // Top 10
  const top10 = [...filteredData].sort((a,b) => b.kepadatan - a.kepadatan).slice(0,10);
  if (chartTop10) chartTop10.destroy();
  chartTop10 = new Chart(document.getElementById('chart-top10'), {
    type: 'bar',
    data: {
      labels: top10.map(d => d.nama_kabupaten_kota.replace('Kabupaten ','Kab. ').replace('Kota ','Kota ')),
      datasets: [{
        label: 'Jiwa/km²',
        data: top10.map(d => d.kepadatan),
        backgroundColor: top10.map(d => catColor(d.kategori_kepadatan)),
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: { label: ctx => ` ${Number(ctx.raw).toLocaleString('id')} jiwa/km²` }
        }
      },
      scales: {
        x: { grid: { color: '#F3F4F6' }, ticks: { font: { size: 11 } } },
        y: { ticks: { font: { size: 11 } } }
      }
    }
  });

  // Donut komposisi
  const cats = ['Tidak Padat','Kurang Padat','Cukup Padat','Sangat Padat'];
  const counts = cats.map(c => filteredData.filter(d => d.kategori_kepadatan === c).length);
  if (chartDonut) chartDonut.destroy();
  chartDonut = new Chart(document.getElementById('chart-donut'), {
    type: 'doughnut',
    data: {
      labels: cats,
      datasets: [{ data: counts, backgroundColor: cats.map(catColor), borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
      cutout: '55%',
      responsive: true,
      plugins: {
        legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } },
        tooltip: {
          callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} wilayah` }
        }
      }
    }
  });
}

// ═══════════════════════════════════════════════════════
//  DRILL DOWN
// ═══════════════════════════════════════════════════════
async function openDrill(kode) {
  selectedKode = kode;
  highlightMap(kode);
  renderTable(); // re-render to highlight row

  const row = allData.find(d => d.kode_kabupaten_kota == kode);
  if (!row) return;

  document.getElementById('drill-title').textContent = `📍 ${row.nama_kabupaten_kota} — Detail & Tren`;
  document.getElementById('drill-panel').classList.add('visible');
  document.getElementById('drill-panel').scrollIntoView({ behavior:'smooth', block:'nearest' });

  // Stat mini cards
  document.getElementById('drill-stats').innerHTML = `
    <div style="background:#F0F4FA;border-radius:10px;padding:12px">
      <div style="font-size:10px;font-weight:700;color:var(--c-muted);text-transform:uppercase">Total Penduduk</div>
      <div style="font-size:20px;font-weight:800">${Number(row.total_penduduk).toLocaleString('id')}</div>
    </div>
    <div style="background:#F0F4FA;border-radius:10px;padding:12px">
      <div style="font-size:10px;font-weight:700;color:var(--c-muted);text-transform:uppercase">Kepadatan</div>
      <div style="font-size:20px;font-weight:800;color:${catColor(row.kategori_kepadatan)}">${Number(row.kepadatan).toLocaleString('id')}</div>
      <div style="font-size:11px;color:var(--c-muted)">jiwa/km²</div>
    </div>
    <div style="background:#F0F4FA;border-radius:10px;padding:12px">
      <div style="font-size:10px;font-weight:700;color:var(--c-muted);text-transform:uppercase">Luas Wilayah</div>
      <div style="font-size:20px;font-weight:800">${Number(row.luas_km2).toLocaleString('id',{maximumFractionDigits:2})}</div>
      <div style="font-size:11px;color:var(--c-muted)">km²</div>
    </div>
    <div style="background:${catColor(row.kategori_kepadatan)}20;border-radius:10px;padding:12px;border:1.5px solid ${catColor(row.kategori_kepadatan)}50">
      <div style="font-size:10px;font-weight:700;color:var(--c-muted);text-transform:uppercase">Kategori</div>
      <div style="font-size:16px;font-weight:800;color:${catColor(row.kategori_kepadatan)}">${row.kategori_kepadatan}</div>
    </div>
  `;

  // Gender pie
  if (chartGender) chartGender.destroy();
  chartGender = new Chart(document.getElementById('chart-gender'), {
    type: 'pie',
    data: {
      labels: ['Laki-laki','Perempuan'],
      datasets: [{
        data: [row.laki_laki, row.perempuan],
        backgroundColor: ['#3B82F6','#EC4899'],
        borderWidth: 2, borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom', labels: { font: { size: 12 }, padding: 12 } },
        tooltip: {
          callbacks: { label: ctx => ` ${ctx.label}: ${Number(ctx.raw).toLocaleString('id')} jiwa` }
        },
        title: { display: true, text: 'Komposisi Gender', font: { size: 13, weight: '700' }, padding: { bottom: 8 } }
      }
    }
  });

  // Trend chart
  try {
    const res  = await fetch(`/api/kepadatan/trend?kode=${kode}`);
    const json = await res.json();
    const tr   = json.trend;

    if (chartTrend) chartTrend.destroy();
    chartTrend = new Chart(document.getElementById('chart-trend'), {
      type: 'line',
      data: {
        labels: tr.map(d => d.tahun),
        datasets: [
          {
            label: 'Kepadatan (jiwa/km²)',
            data: tr.map(d => d.kepadatan),
            borderColor: catColor(row.kategori_kepadatan),
            backgroundColor: catColor(row.kategori_kepadatan) + '20',
            fill: true, tension: .35, pointRadius: 5, pointHoverRadius: 7
          },
          {
            label: 'Total Penduduk',
            data: tr.map(d => d.total_penduduk),
            borderColor: '#6366F1', borderDash: [4,4],
            fill: false, tension: .35, pointRadius: 4,
            yAxisID: 'y2'
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom', labels: { font: { size: 11 } } },
          title: { display: true, text: 'Tren Kepadatan & Penduduk', font: { size: 12, weight: '700' } }
        },
        scales: {
          y:  { title: { display: true, text: 'jiwa/km²', font: { size: 10 } }, grid: { color: '#F3F4F6' } },
          y2: { position: 'right', title: { display: true, text: 'Total jiwa', font: { size: 10 } }, grid: { display: false } }
        }
      }
    });
  } catch(e) { console.error('Trend error:', e); }
}

function closeDrill() {
  document.getElementById('drill-panel').classList.remove('visible');
  selectedKode = null;
  if (geojsonLayer) geojsonLayer.eachLayer(l => geojsonLayer.resetStyle(l));
  renderTable();
}

// ═══════════════════════════════════════════════════════
//  SORT
// ═══════════════════════════════════════════════════════
function sortTable(col) {
  if (sortCol === col) sortDir *= -1;
  else { sortCol = col; sortDir = -1; }
  applyFilter();
}

// ═══════════════════════════════════════════════════════
//  LEGEND TOGGLE
// ═══════════════════════════════════════════════════════
function toggleLegend(el) {
  const cat = el.dataset.cat;
  if (activeCats.has(cat)) {
    if (activeCats.size === 1) return; // keep at least 1
    activeCats.delete(cat);
    el.classList.remove('active');
    el.classList.add('inactive');
  } else {
    activeCats.add(cat);
    el.classList.add('active');
    el.classList.remove('inactive');
  }
  applyFilter();
}

// ═══════════════════════════════════════════════════════
//  RESET
// ═══════════════════════════════════════════════════════
function resetFilter() {
  document.getElementById('sel-kategori').value = '';
  document.getElementById('inp-search').value   = '';
  activeCats = new Set(['Tidak Padat','Kurang Padat','Cukup Padat','Sangat Padat']);
  document.querySelectorAll('.legend-badge').forEach(el => {
    el.classList.add('active'); el.classList.remove('inactive');
  });
  applyFilter();
}

// ═══════════════════════════════════════════════════════
//  EVENTS
// ═══════════════════════════════════════════════════════
document.getElementById('sel-tahun').addEventListener('change', loadData);
document.getElementById('sel-kategori').addEventListener('change', applyFilter);
document.getElementById('inp-search').addEventListener('input', applyFilter);

// ═══════════════════════════════════════════════════════
//  BOOT
// ═══════════════════════════════════════════════════════
window.addEventListener('DOMContentLoaded', () => {
  initMap();
  loadData();
});
</script>
@endpush