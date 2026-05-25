@extends('layouts.app')

@section('title', 'Analisis Akselerasi Desa Mandiri – Jawa Timur')

@push('styles')
<style>
:root {
    --clr-primary: #1D9E75;
    --clr-secondary: #378ADD;
    --clr-warning: #EF9F27;
    --clr-muted: #888780;
    --clr-danger: #A32D2D;
    --clr-text: #1a2535;
    --clr-text-light: #6b7a91;
    --clr-text-lighter: #0F6E56;
    --clr-bg: #f0f4f8;
    --clr-bg-alt: #f0faf5;
    --clr-border: #dde3ec;
    --clr-border-alt: #9FE1CB;
    --radius: 14px;
    --gap: 16px;
}

.app-main { overflow-y: auto !important; }
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.dm-wrap {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: var(--clr-bg);
    color: var(--clr-text);
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* ── Page container ── */
.dm-page { 
    width: 100%;
    max-width: 100%;   /* bebas full */
    padding: 28px 32px 0;
}

.dm-section-label {
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    color: var(--clr-primary); text-transform: uppercase;
    border-left: 3px solid var(--clr-primary);
    padding-left: 8px; margin-bottom: 6px;
    display: inline-block;
}

.dm-page-title { font-size: 26px; font-weight: 700; color: var(--clr-text); margin-bottom: 4px; line-height: 1.2; }
.dm-page-title span { color: var(--clr-primary); }
.dm-page-subtitle { font-size: 13px; color: var(--clr-text-light); margin-bottom: 24px; }

.dm-year-select { font-size: 13px; font-weight: 600; color: var(--clr-text); background: #fff; border: 1px solid var(--clr-border); border-radius: 8px; padding: 6px 16px; cursor: pointer; }

.dm-kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr) 1.5fr; gap: var(--gap); margin-bottom: 24px; clear: both; }
.dm-kpi-card { background: #fff; border-radius: var(--radius); padding: 22px; border: 1px solid var(--clr-border); }
.dm-kpi-ico { font-size: 22px; margin-bottom: 10px; }
.dm-kpi-label { font-size: 11px; color: var(--clr-text-light); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 6px; }
.dm-kpi-val { font-size: 34px; font-weight: 700; color: var(--clr-text); line-height: 1; }
.dm-kpi-val.green { color: var(--clr-primary); }
.dm-kpi-sub { font-size: 12px; margin-top: 8px; color: var(--clr-primary); }
.dm-kpi-sub.muted { color: var(--clr-text-light); }

.dm-exec-card { background: var(--clr-bg-alt); border: 1px solid var(--clr-border-alt); border-radius: var(--radius); padding: 20px 22px; }
.dm-exec-head { font-size: 11px; font-weight: 700; letter-spacing: .09em; color: var(--clr-text-lighter); text-transform: uppercase; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
.dm-exec-head::before { content: '★'; font-size: 13px; color: var(--clr-primary); }
.dm-exec-body { font-size: 13px; color: var(--clr-text); line-height: 1.65; }
.dm-exec-body strong { color: var(--clr-primary); }

.dm-card { background: #fff; border-radius: var(--radius); border: 1px solid var(--clr-border); padding: 24px; margin-bottom: 24px; }
.dm-map-card { position: relative; z-index: 1; margin-bottom: 24px; overflow: visible; }
.dm-card-title { font-size: 17px; font-weight: 700; color: var(--clr-text); margin-bottom: 4px; text-align: center; }
.dm-card-sub { font-size: 12px; color: var(--clr-text-light); margin-bottom: 18px; text-align: center; }

.dm-regional-floating { position: relative; z-index: 1200; margin-top: 0; padding-top: 12px; background: var(--clr-bg); isolation: isolate; }
#map { position: relative; z-index: 100; overflow: hidden; }
.dm-map-canvas {
    height: 400px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--clr-border);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}
.dm-map-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    border: 1px solid var(--clr-border);
    box-shadow: 0 14px 30px rgba(16, 24, 40, 0.14);
}
.dm-map-popup .leaflet-popup-content {
    margin: 10px 12px;
}
.dm-map-tooltip {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid var(--clr-border);
    color: var(--clr-text);
    font-weight: 600;
    border-radius: 8px;
    padding: 4px 8px;
    box-shadow: 0 8px 18px rgba(16, 24, 40, 0.12);
}
.dm-map-tooltip::before {
    border-top-color: var(--clr-border);
}
.dm-map-legend {
    background: rgba(255, 255, 255, 0.95);
    padding: 10px 12px;
    border: 1px solid var(--clr-border);
    border-radius: 12px;
    box-shadow: 0 10px 24px rgba(16, 24, 40, 0.08);
}
.dm-header { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:6px; }
.dm-header form { padding-top:4px; }

.dm-legend { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; justify-content: center; }
.dm-pill { font-size: 12px; border-radius: 20px; padding: 4px 14px; border: 1px solid; font-weight: 500; cursor: pointer; transition: opacity .2s; }
.dm-pill.pill-darkred { color: #4A1B0C; border-color: var(--clr-danger); background: #FAECE7; }
.dm-pill.pill-gray { color: #2C2C2A; border-color: var(--clr-muted); background: #F1EFE8; }
.dm-pill.pill-orange { color: #412402; border-color: var(--clr-warning); background: #FAEEDA; }
.dm-pill.pill-blue { color: #042C53; border-color: var(--clr-secondary); background: #E6F1FB; }
.dm-pill.pill-green { color: #04342C; border-color: var(--clr-primary); background: #E1F5EE; }
.dm-pill.inactive { opacity: .35; }

.dm-section-title { font-size: 18px; font-weight: 700; color: var(--clr-text); margin-bottom: 4px; }
.dm-section-sub { font-size: 12px; color: var(--clr-text-light); margin-bottom: 14px; }

.dm-rank-grid { display: grid; grid-template-columns: 1fr 1fr; gap: var(--gap); margin-bottom: 24px; }
.dm-rank-card { background: #fff; border-radius: var(--radius); border: 1px solid var(--clr-border); padding: 22px; }
.dm-rank-head { font-size: 14px; font-weight: 700; color: var(--clr-text); display: flex; align-items: center; gap: 8px; margin-bottom: 18px; }
.ico-up { color: var(--clr-primary); font-size: 18px; }
.ico-warn { color: var(--clr-warning); font-size: 16px; }

.dm-rank-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--clr-bg); font-size: 13px; }
.dm-rank-row:last-child { border-bottom: none; }
.rank-no { width: 22px; color: var(--clr-text-light); font-weight: 600; flex-shrink: 0; }
.rank-name { color: var(--clr-text); flex: 1; }
.rank-bar { width: 80px; height: 6px; background: var(--clr-bg); border-radius: 4px; margin: 0 12px; overflow: hidden; flex-shrink: 0; }
.rank-bar-fill { height: 100%; border-radius: 4px; }
.rank-num { font-weight: 700; min-width: 56px; text-align: right; }
.rank-num.green { color: var(--clr-primary); }
.rank-num.red { color: #E24B4A; }

.dm-donut-map-wrap { display: flex; gap: 24px; align-items: flex-start; }
.dm-donut-side { flex: 0 0 280px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
.dm-map-side { flex: 1 1 0; min-width: 0; overflow: visible; }
.dm-donut-wrap { display: flex; align-items: center; justify-content: center; gap: 32px; padding: 8px 0 4px; }
.dm-donut-wrap canvas { width: 200px !important; height: 200px !important; max-width: 200px; max-height: 200px; }
.dm-donut-legends { display: flex; flex-direction: column; gap: 16px; }
.dm-donut-item { display: flex; align-items: center; gap: 12px; }
.dm-donut-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.dm-donut-label { font-size: 14px; color: var(--clr-text); line-height: 1.2; }
.dm-donut-pct { font-size: 17px; font-weight: 700; color: var(--clr-text); }
.dm-donut-count { font-size: 11px; color: var(--clr-text-light); }

@media (max-width: 960px) {
    .dm-donut-map-wrap { flex-direction: column; }
    .dm-donut-side { flex: none; width: 100%; }
}

.dm-bakorwil-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 32px; }
.dm-bakorwil-card { background: #fff; border-radius: var(--radius); border: 1px solid var(--clr-border); padding: 20px 16px 16px; position: relative; }
.dm-bakorwil-card.top { border: 2px solid var(--clr-primary); }
.dm-top-badge { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--clr-primary); color: #fff; font-size: 9px; font-weight: 700; letter-spacing: .08em; border-radius: 20px; padding: 3px 10px; white-space: nowrap; text-transform: uppercase; }
.dm-bak-name { font-size: 12px; font-weight: 700; color: var(--clr-text); margin-bottom: 14px; line-height: 1.4; text-align: center; }
.dm-bak-stat { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 12px; }
.dm-bak-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dm-bak-label { color: var(--clr-text-light); flex: 1; }
.dm-bak-val { font-weight: 700; color: var(--clr-text); }
.bak-bar { height: 6px; border-radius: 4px; display: flex; margin-top: 12px; }
.bak-bar-m { background: var(--clr-primary); }
.bak-bar-mj { background: var(--clr-secondary); }
.bak-bar-b { background: var(--clr-warning); }

@media (max-width: 960px) {
    .dm-kpi-grid { grid-template-columns: repeat(2, 1fr); }
    .dm-bakorwil-grid { grid-template-columns: repeat(2, 1fr); }
    .dm-map-card { margin-bottom: 28px; }
    .dm-regional-floating { margin-top: 0; padding-top: 8px; }
}
@media (max-width: 640px) {
    .dm-kpi-grid { grid-template-columns: 1fr; }
    .dm-rank-grid { grid-template-columns: 1fr; }
    .dm-bakorwil-grid { grid-template-columns: 1fr; }
    .dm-page { padding: 16px 12px 0; }
    .dm-donut-wrap { flex-direction: column; gap: 24px; }
}
</style>
@endpush

@section('content')
<div class="dm-wrap">
<div class="dm-page">

    <div class="dm-header">
        <div>
            <div class="dm-section-label">DASHBOARD</div>
            <h1 class="dm-page-title">Analisis Akselerasi <span>Desa Mandiri</span> Jawa Timur</h1>
            <p class="dm-page-subtitle">Analisis Rencana vs Realisasi Kabupaten/Kota</p>
        </div>
        <form method="GET" action="{{ route('demographics') }}">
            <select name="year" class="dm-year-select" onchange="this.form.submit()">
                @foreach([2022, 2023, 2024, 2025] as $yr)
                    <option value="{{ $yr }}" @selected($data['year'] == $yr)>{{ $yr }} ▾</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ══ KPI CARDS ══ --}}
    <div class="dm-kpi-grid">

        <div class="dm-kpi-card">
            <div class="dm-kpi-ico">🏘️</div>
            <div class="dm-kpi-label">Total Desa Mandiri</div>
            <div class="dm-kpi-val green">{{ $data['totalDesaMandiri'] }}</div>
            <div class="dm-kpi-sub">
                {{ $data['growthRaw'] >= 0 ? '▲' : '▼' }}
                {{ $data['growthRate'] }} vs {{ $data['year'] - 1 }}
            </div>
        </div>

        <div class="dm-kpi-card">
            <div class="dm-kpi-ico">📈</div>
            <div class="dm-kpi-label">Growth Rate (%)</div>
            <div class="dm-kpi-val green">{{ $data['growthRate'] }}</div>
            <div class="dm-kpi-sub muted">✓ Steady acceleration</div>
        </div>

        <div class="dm-kpi-card">
            <div class="dm-kpi-ico">✅</div>
            <div class="dm-kpi-label">Zero Tertinggal Status</div>
            <div class="dm-kpi-val {{ $data['zeroTertinggal'] == 0 ? 'green' : '' }}">
                {{ $data['zeroTertinggal'] }}
            </div>
            <div class="dm-kpi-sub muted">
                {{ $data['zeroTertinggal'] == 0 ? '✓ All villages progressing' : '⚠ Perlu perhatian' }}
            </div>
        </div>

        <div class="dm-exec-card">
            <div class="dm-exec-head">Executive Summary</div>
            <p class="dm-exec-body">
                Jawa Timur mencapai milestone signifikan di tahun <strong>{{ $data['year'] }}</strong>
                dengan total <strong>{{ $data['totalDesaMandiri'] }}</strong> Desa Mandiri,
                tumbuh <strong>{{ $data['growthRate'] }}</strong> dibanding tahun sebelumnya.
                Proporsi Mandiri mencapai <strong>{{ $data['mandiriPercent'] }}%</strong>
                dari total <strong>{{ $data['grandTotal'] }}</strong> desa aktif.
            </p>
        </div>

    </div>

    {{-- ══ TREND CHART ══ --}}
    <div class="dm-card">
        <div class="dm-card-title">Category-Based Trends</div>
        <div class="dm-card-sub">Perkembangan status desa 2022–2025</div>

        <div class="dm-legend">
            <span class="dm-pill pill-darkred" data-ds="0" onclick="toggleDs(0)">Sangat Tertinggal</span>
            <span class="dm-pill pill-gray"    data-ds="1" onclick="toggleDs(1)">Tertinggal</span>
            <span class="dm-pill pill-orange"  data-ds="2" onclick="toggleDs(2)">Berkembang</span>
            <span class="dm-pill pill-blue"    data-ds="3" onclick="toggleDs(3)">Maju</span>
            <span class="dm-pill pill-green"   data-ds="4" onclick="toggleDs(4)">Mandiri</span>
        </div>

        <canvas id="trendChart" height="80"></canvas>
    </div>

    {{-- ══ RANKINGS ══ --}}
    <p class="dm-section-title">Top &amp; Bottom Rankings</p>
    <p class="dm-section-sub">Comparative analysis of Kabupaten performance across East Java · Tahun {{ $data['year'] }}</p>

    <div class="dm-rank-grid">

        <div class="dm-rank-card">
            <div class="dm-rank-head">
                <span class="ico-up">↑</span> Top 5 Kabupaten (Highest Growth)
            </div>
            @php $maxTop = $data['topKabupaten']->max('growth') ?: 1; @endphp
            @foreach($data['topKabupaten'] as $i => $kab)
            <div class="dm-rank-row">
                <span class="rank-no">{{ $i + 1 }}.</span>
                <span class="rank-name">{{ $kab['name'] }}</span>
                <div class="rank-bar">
                    <div class="rank-bar-fill"
                         style="width:{{ round(abs($kab['growth']) / abs($maxTop) * 100) }}%;
                                background:#1D9E75;"></div>
                </div>
                <span class="rank-num green">{{ $kab['growth_label'] }}</span>
            </div>
            @endforeach
        </div>

        <div class="dm-rank-card">
            <div class="dm-rank-head">
                <span class="ico-warn">⚠</span> Bottom 5 Kabupaten (Need Attention)
            </div>
            @php $maxBot = $data['bottomKabupaten']->max('growth') ?: 1; @endphp
            @foreach($data['bottomKabupaten'] as $i => $kab)
            <div class="dm-rank-row">
                <span class="rank-no">{{ $i + 1 }}.</span>
                <span class="rank-name">{{ $kab['name'] }}</span>
                <div class="rank-bar">
                    <div class="rank-bar-fill"
                         style="width:{{ round(abs($kab['growth']) / abs($maxBot) * 100) }}%;
                                background:#E24B4A;"></div>
                </div>
                <span class="rank-num red">{{ $kab['growth_label'] }}</span>
            </div>
            @endforeach
        </div>

    </div>

    {{-- ══ IDM PROPORTIONS ══ --}}
    <div class="dm-card dm-map-card">
        <div class="dm-card-title">Current IDM Proportions</div>
        <div class="dm-card-sub">Snapshot of village status in {{ $data['year'] }} · Total: {{ $data['grandTotal'] }} desa</div>

        {{-- Layout: donut kiri, map kanan --}}
        <div class="dm-donut-map-wrap">

            {{-- Kiri: Donut chart + legend --}}
            <div class="dm-donut-side">
                <div class="dm-donut-wrap">
                    <canvas id="donutChart" width="200" height="200"></canvas>
                    <div class="dm-donut-legends">
                        @foreach($data['idmProportions'] as $item)
                        <div class="dm-donut-item">
                            <div class="dm-donut-dot" style="background:{{ $item['color'] }};"></div>
                            <div>
                                <div class="dm-donut-label">{{ $item['label'] }}</div>
                                <div class="dm-donut-pct">{{ $item['percent'] }}%</div>
                                <div class="dm-donut-count">{{ number_format($item['count']) }} desa</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Kanan: Peta choropleth --}}
            <div class="dm-map-side">
                <div id="map" class="dm-map-canvas"></div>
            </div>

        </div>
    </div>

    {{-- ══ REGIONAL PERFORMANCE GRID ══ --}}
    <div class="dm-regional-floating">
        <p class="dm-section-title">Regional Performance Grid</p>
        <p class="dm-section-sub">Data breakdown by Bakorwil (Regional Coordination Offices) · {{ $data['year'] }}</p>

        <div class="dm-bakorwil-grid">
            @foreach($data['bakorwil'] as $bak)
            <div class="dm-bakorwil-card {{ $bak['topPerformer'] ? 'top' : '' }}">
                @if($bak['topPerformer'])
                    <div class="dm-top-badge">TOP PERFORMER</div>
                @endif
                <div class="dm-bak-name">{{ $bak['name'] }}</div>

                <div class="dm-bak-stat">
                    <div class="dm-bak-dot" style="background:#1D9E75;"></div>
                    <span class="dm-bak-label">Mandiri</span>
                    <span class="dm-bak-val">{{ number_format($bak['mandiri']) }}</span>
                </div>
                <div class="dm-bak-stat">
                    <div class="dm-bak-dot" style="background:#378ADD;"></div>
                    <span class="dm-bak-label">Maju</span>
                    <span class="dm-bak-val">{{ number_format($bak['maju']) }}</span>
                </div>
                <div class="dm-bak-stat">
                    <div class="dm-bak-dot" style="background:#EF9F27;"></div>
                    <span class="dm-bak-label">Berkembang</span>
                    <span class="dm-bak-val">{{ number_format($bak['berkembang']) }}</span>
                </div>

                @php
                    $total = $bak['mandiri'] + $bak['maju'] + $bak['berkembang'];
                    $pctM  = $total > 0 ? round($bak['mandiri']    / $total * 100) : 0;
                    $pctMj = $total > 0 ? round($bak['maju']       / $total * 100) : 0;
                    $pctB  = $total > 0 ? round($bak['berkembang'] / $total * 100) : 0;
                @endphp
                <div class="bak-bar">
                    <div class="bak-bar-m" style="width:{{ $pctM }}%"></div>
                    <div class="bak-bar-mj" style="width:{{ $pctMj }}%"></div>
                    <div class="bak-bar-b" style="width:{{ $pctB }}%"></div>
                </div>
                <div style="font-size:10px; color:#6b7a91; margin-top:5px; text-align:right;">
                    Total: {{ number_format($total) }} desa
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>{{-- /dm-page --}}
</div>{{-- /dm-wrap --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {

    const trend = @json($data['trendData']);

    /* ── Trend Chart ── */
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trend.years,
            datasets: [
                {
                    label: 'Sangat Tertinggal',
                    data: trend.sangat_tertinggal,
                    borderColor: '#A32D2D',
                    backgroundColor: 'transparent',
                    borderWidth: 2.5, tension: 0.4,
                    pointRadius: 4, pointHoverRadius: 6,
                },
                {
                    label: 'Tertinggal',
                    data: trend.tertinggal,
                    borderColor: '#888780',
                    backgroundColor: 'transparent',
                    borderWidth: 2.5, tension: 0.4,
                    pointRadius: 4, pointHoverRadius: 6,
                },
                {
                    label: 'Berkembang',
                    data: trend.berkembang,
                    borderColor: '#EF9F27',
                    backgroundColor: 'transparent',
                    borderWidth: 2.5, tension: 0.4,
                    pointRadius: 4, pointHoverRadius: 6,
                },
                {
                    label: 'Maju',
                    data: trend.maju,
                    borderColor: '#378ADD',
                    backgroundColor: 'transparent',
                    borderWidth: 2.5, tension: 0.4,
                    pointRadius: 4, pointHoverRadius: 6,
                },
                {
                    label: 'Mandiri',
                    data: trend.mandiri,
                    borderColor: '#1D9E75',
                    backgroundColor: 'transparent',
                    borderWidth: 3, tension: 0.4,
                    pointRadius: 4, pointHoverRadius: 6,
                },
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1a2535',
                    bodyColor: '#1a2535',
                    borderColor: '#dde3ec',
                    borderWidth: 1,
                    padding: 12,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#6b7a91', font: { size: 12 } }
                },
                y: {
                    grid: { color: '#eef1f6' },
                    ticks: { color: '#6b7a91', font: { size: 12 } }
                }
            }
        }
    });

    /* ── Toggle legend pills ── */
    window.toggleDs = function(index) {
        const meta = trendChart.getDatasetMeta(index);
        meta.hidden = !meta.hidden;
        trendChart.update();
        document.querySelectorAll('.dm-pill[data-ds]').forEach(p => {
            const i = parseInt(p.dataset.ds);
            p.classList.toggle('inactive', !!trendChart.getDatasetMeta(i).hidden);
        });
    };

    /* ── Donut Chart ── */
    const donutRaw = @json($data['idmProportions']), mandiriPct = {{ $data['mandiriPercent'] }};
    const donutCtx = document.getElementById('donutChart').getContext('2d');

    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: donutRaw.map(d => d.label),
            datasets: [{
                data: donutRaw.map(d => d.percent),
                backgroundColor: donutRaw.map(d => d.color),
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1a2535',
                    bodyColor: '#1a2535',
                    borderColor: '#dde3ec',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed}%`
                    }
                },
            }
        },
        plugins: [{
            id: 'centerText',
            beforeDraw(chart) {
                const { ctx, chartArea: { left, top, right, bottom } } = chart;
                const cx = (left + right) / 2;
                const cy = (top + bottom) / 2;
                ctx.save();
                ctx.textAlign    = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillStyle    = '#1a2535';
                ctx.font         = '700 30px Segoe UI, sans-serif';
                ctx.fillText(mandiriPct + '%', cx, cy - 12);
                ctx.font      = '400 12px Segoe UI, sans-serif';
                ctx.fillStyle = '#6b7a91';
                ctx.fillText('MANDIRI', cx, cy + 14);
                ctx.restore();
            }
        }]
    });

})();
</script>

<script>
// ===== PETA CHOROPLETH + HOVER DATA DB =====
(function() {
    const mapElement = document.getElementById('map');
    if (!mapElement || typeof L === 'undefined') {
        return;
    }

    const map = L.map('map', {
        dragging: true,
        touchZoom: true,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: true,
        zoomSnap: 0.25,
        zoomDelta: 0.25,
        zoomControl: true,
        attributionControl: true
    }).setView([-7.5, 112.5], 8);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        maxZoom: 19
    }).addTo(map);

    const year = @json($data['year']);
    let geojsonLayer = null;
    let selectedLayer = null;
    let statistikData = new Map();
    const JATIM_LOCK_BOUNDS = L.latLngBounds(
        L.latLng(-8.95, 111.00),
        L.latLng(-5.45, 116.45)
    );
    const JATIM_CENTER = L.latLng(-7.45, 113.35);
    const JATIM_LOCK_ZOOM = 8.25;

    const loadingInfo = document.createElement('div');
    loadingInfo.style.position = 'absolute';
    loadingInfo.style.top = '12px';
    loadingInfo.style.left = '50%';
    loadingInfo.style.transform = 'translateX(-50%)';
    loadingInfo.style.zIndex = '500';
    loadingInfo.style.padding = '8px 12px';
    loadingInfo.style.borderRadius = '8px';
    loadingInfo.style.fontSize = '12px';
    loadingInfo.style.fontWeight = '600';
    loadingInfo.style.background = 'rgba(255,255,255,0.92)';
    loadingInfo.style.border = '1px solid #dde3ec';
    loadingInfo.style.color = '#1a2535';
    loadingInfo.textContent = 'Memuat peta Jawa Timur...';
    mapElement.style.position = 'relative';
    mapElement.appendChild(loadingInfo);

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    const normalizeKode = v => String(v ?? '').trim().padStart(4, '0');
    const toNumber = v => { const p = Number(v); return Number.isFinite(p) ? p : 0; };

    function getFeatureKode(feature) {
        const properties = feature?.properties ?? {};
        return normalizeKode(properties.CC_2 || properties.kode_kabupaten_kota || properties.kode || properties.KODE || '');
    }

    function getFeatureName(f) { const p = f?.properties ?? {}; return p.NAME_2 || p.nama_kabupaten_kota || p.name || 'Unknown'; }

    function getStatistik(feature) {
        return statistikData.get(getFeatureKode(feature));
    }

    function getTotal(row) {
        return toNumber(row.sangat_tertinggal)
            + toNumber(row.tertinggal)
            + toNumber(row.berkembang)
            + toNumber(row.maju)
            + toNumber(row.mandiri);
    }

    function getMandiriPercent(row) {
        const total = getTotal(row);
        return total > 0 ? Math.round((toNumber(row.mandiri) / total) * 100) : 0;
    }

    function getColor(pct) {
        return pct >= 80 ? '#1D9E75' :
               pct >= 60 ? '#378ADD' :
               pct >= 40 ? '#EF9F27' :
               pct >= 20 ? '#888780' :
                           '#A32D2D';
    }

    function style(feature) {
        const row = getStatistik(feature);
        const pct = row ? getMandiriPercent(row) : 0;

        return {
            fillColor: getColor(pct),
            weight: 1.3,
            opacity: 1,
            color: '#64748b',
            dashArray: '3',
            fillOpacity: row ? 0.78 : 0.3
        };
    }

    function buildPopupContent(feature) {
        const row = getStatistik(feature);
        const nama = escapeHtml(getFeatureName(feature));
        const kode = escapeHtml(getFeatureKode(feature));

        if (!row) {
            return `
                <div style="min-width:220px">
                    <div style="font-size:14px;font-weight:700;color:#1a2535;margin-bottom:4px">${nama}</div>
                    <div style="font-size:11px;color:#6b7a91;margin-bottom:10px">Kode ${kode}</div>
                    <div style="font-size:12px;color:#6b7a91">Data tidak tersedia</div>
                </div>
            `;
        }

        const total = getTotal(row);
        const mandiriPct = getMandiriPercent(row);

        return `
            <div style="min-width:240px">
                <div style="font-size:14px;font-weight:700;color:#1a2535;margin-bottom:4px">${nama}</div>
                <div style="font-size:11px;color:#6b7a91;margin-bottom:10px">Kode ${kode} · Tahun ${year}</div>
                <div style="display:grid;grid-template-columns:1fr auto;gap:6px 12px;font-size:12px;color:#1a2535">
                    <span>Sangat Tertinggal</span><strong>${toNumber(row.sangat_tertinggal)}</strong>
                    <span>Tertinggal</span><strong>${toNumber(row.tertinggal)}</strong>
                    <span>Berkembang</span><strong>${toNumber(row.berkembang)}</strong>
                    <span>Maju</span><strong>${toNumber(row.maju)}</strong>
                    <span>Mandiri</span><strong style="color:#1D9E75">${toNumber(row.mandiri)}</strong>
                    <span>Total</span><strong>${total}</strong>
                </div>
                <div style="margin-top:10px;padding-top:10px;border-top:1px solid #e5e9f0;font-size:12px;color:#0F6E56;font-weight:700">
                    Mandiri: ${mandiriPct}%
                </div>
            </div>
        `;
    }

    function highlightFeature(layer, keepSelected = false) {
        layer.setStyle({
            weight: 3,
            color: '#1a2535',
            dashArray: '',
            fillOpacity: keepSelected ? 0.94 : 0.9
        });

        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
            layer.bringToFront();
        }
    }

    function resetFeatureStyle(layer) {
        if (geojsonLayer) {
            geojsonLayer.resetStyle(layer);
        }
    }

    function clearSelection() {
        if (!selectedLayer) {
            return;
        }
        resetFeatureStyle(selectedLayer);
        selectedLayer.closePopup();
        selectedLayer = null;
    }

    function onMouseOver(e) {
        const layer = e.target;
        if (selectedLayer === layer) {
            return;
        }
        highlightFeature(layer);
        layer.openPopup();
    }

    function onMouseOut(e) {
        const layer = e.target;
        if (selectedLayer === layer) {
            return;
        }
        resetFeatureStyle(layer);
        layer.closePopup();
    }

    function onFeatureClick(e) {
        const layer = e.target;

        if (selectedLayer === layer) {
            clearSelection();
            return;
        }

        clearSelection();
        selectedLayer = layer;
        highlightFeature(layer, true);
        layer.openPopup();

        const targetBounds = layer.getBounds();
        if (targetBounds.isValid()) {
            map.fitBounds(targetBounds.pad(0.18), {
                maxZoom: JATIM_LOCK_ZOOM + 0.7,
                animate: true
            });
        }
    }

    function onEachFeature(feature, layer) {
        layer.bindPopup(buildPopupContent(feature), {
            closeButton: false,
            autoPan: true,
            offset: L.point(0, -6),
            className: 'dm-map-popup'
        });

        layer.bindTooltip(getFeatureName(feature), {
            sticky: true,
            direction: 'top',
            opacity: 0.95,
            className: 'dm-map-tooltip'
        });

        layer.on({
            mouseover: onMouseOver,
            mouseout: onMouseOut,
            click: onFeatureClick
        });
    }

    function addLegend() {
        const legend = L.control({ position: 'bottomright' });

        legend.onAdd = function() {
            const div = L.DomUtil.create('div', 'dm-map-legend');
            div.innerHTML = `
                <div style="font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#6b7a91;margin-bottom:8px">Legenda</div>
                <div style="display:flex;flex-direction:column;gap:6px;font-size:11px;color:#1a2535">
                    <div><span style="display:inline-block;width:12px;height:12px;background:#1D9E75;border-radius:3px;margin-right:8px;vertical-align:middle"></span>80% - 100%</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#378ADD;border-radius:3px;margin-right:8px;vertical-align:middle"></span>60% - 79%</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#EF9F27;border-radius:3px;margin-right:8px;vertical-align:middle"></span>40% - 59%</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#888780;border-radius:3px;margin-right:8px;vertical-align:middle"></span>20% - 39%</div>
                    <div><span style="display:inline-block;width:12px;height:12px;background:#A32D2D;border-radius:3px;margin-right:8px;vertical-align:middle"></span>0% - 19%</div>
                </div>
            `;

            return div;
        };

        legend.addTo(map);
    }

    async function loadMap(tahun) {
        const [geojsonResponse, statistikResponse] = await Promise.all([
            fetch('/api/geojson'),
            fetch(`/api/statistik?tahun=${tahun}`)
        ]);

        if (!geojsonResponse.ok) {
            throw new Error('Gagal memuat GeoJSON peta');
        }

        if (!statistikResponse.ok) {
            throw new Error('Gagal memuat statistik peta');
        }

        const geojson = await geojsonResponse.json();
        const statistik = await statistikResponse.json();

        statistikData = new Map(
            statistik.map(row => [normalizeKode(row.kode_kabupaten_kota), row])
        );

        if (geojsonLayer) {
            map.removeLayer(geojsonLayer);
        }

        geojsonLayer = L.geoJSON(geojson, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(map);

        // Fit ke geometri aktual agar framing peta konsisten dengan data terbaru.
        const geoBounds = geojsonLayer.getBounds();
        if (geoBounds.isValid()) {
            map.fitBounds(geoBounds.pad(0.05), {
                maxZoom: JATIM_LOCK_ZOOM,
                animate: false
            });
        }

        map.setMaxBounds(JATIM_LOCK_BOUNDS);
        map.options.maxBoundsViscosity = 1.0;
        map.setMinZoom(JATIM_LOCK_ZOOM);
        map.setMaxZoom(JATIM_LOCK_ZOOM + 1.2);

        map.on('click', event => {
            if (!event.originalEvent.target.closest('.leaflet-interactive')) {
                clearSelection();
                map.setView(JATIM_CENTER, JATIM_LOCK_ZOOM, { animate: true });
            }
        });

        loadingInfo.remove();
    }

    addLegend();

    loadMap(year).catch(error => {
        console.error(error);
        loadingInfo.style.background = 'rgba(255, 245, 245, 0.97)';
        loadingInfo.style.border = '1px solid #fecaca';
        loadingInfo.style.color = '#b91c1c';
        loadingInfo.textContent = 'Gagal memuat peta. Cek endpoint /api/geojson dan /api/statistik.';
    });
})();
</script>
@endpush