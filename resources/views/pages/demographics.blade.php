@extends('layouts.app')

@section('title', 'Analisis Akselerasi Desa Mandiri – Jawa Timur')

@push('styles')
<style>

/* ── Fix scroll khusus demographics ── */
.app-main {
    overflow-y: auto !important;
}
/* ... sisa style yang sudah ada tetap sama ... */

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.dm-wrap {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: #f0f4f8;
    color: #1a2535;
    min-height: 100vh;
    padding-bottom: 3rem;
}

/* ── Page container ── */
.dm-page { 
    width: 100%;
    max-width: 100%;   /* bebas full */
    padding: 28px 32px 0;
}

/* ── Section label ── */
.dm-section-label {
    font-size: 11px; font-weight: 700; letter-spacing: .1em;
    color: #1D9E75; text-transform: uppercase;
    border-left: 3px solid #1D9E75;
    padding-left: 8px; margin-bottom: 6px;
    display: inline-block;
}

/* ── Page title ── */
.dm-page-title {
    font-size: 26px; font-weight: 700;
    color: #1a2535; margin-bottom: 4px; line-height: 1.2;
}
.dm-page-title span { color: #1D9E75; }
.dm-page-subtitle   { font-size: 13px; color: #6b7a91; margin-bottom: 24px; }

/* ── Year dropdown ── */
.dm-year-select {
    font-size: 13px; font-weight: 600; color: #1a2535;
    background: #fff;
    border: 1px solid #dde3ec;
    border-radius: 8px;
    padding: 6px 16px;
    cursor: pointer;
}

/* ── KPI grid ── */
.dm-kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr) 1.5fr;
    gap: 16px;
    margin-bottom: 24px;
    clear: both;
}
.dm-kpi-card {
    background: #fff;
    border-radius: 14px;
    padding: 22px;
    border: 1px solid #dde3ec;
}
.dm-kpi-ico   { font-size: 22px; margin-bottom: 10px; }
.dm-kpi-label {
    font-size: 11px; color: #6b7a91;
    text-transform: uppercase; letter-spacing: .07em;
    margin-bottom: 6px;
}
.dm-kpi-val   { font-size: 34px; font-weight: 700; color: #1a2535; line-height: 1; }
.dm-kpi-val.green { color: #1D9E75; }
.dm-kpi-sub   { font-size: 12px; margin-top: 8px; color: #1D9E75; }
.dm-kpi-sub.muted { color: #6b7a91; }

/* ── Executive summary card ── */
.dm-exec-card {
    background: #f0faf5;
    border: 1px solid #9FE1CB;
    border-radius: 14px;
    padding: 20px 22px;
}
.dm-exec-head {
    font-size: 11px; font-weight: 700; letter-spacing: .09em;
    color: #0F6E56; text-transform: uppercase;
    margin-bottom: 10px;
    display: flex; align-items: center; gap: 6px;
}
.dm-exec-head::before { content: '★'; font-size: 13px; color: #1D9E75; }
.dm-exec-body { font-size: 13px; color: #1a2535; line-height: 1.65; }
.dm-exec-body strong { color: #1D9E75; }

/* ── Generic card ── */
.dm-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #dde3ec;
    padding: 24px;
    margin-bottom: 24px;
}
.dm-card-title { font-size: 17px; font-weight: 700; color: #1a2535; margin-bottom: 4px; text-align: center; }
.dm-card-sub   { font-size: 12px; color: #6b7a91; margin-bottom: 18px; text-align: center; }

/* ── Legend pills ── */
.dm-legend {
    display: flex; gap: 10px; flex-wrap: wrap;
    margin-bottom: 18px; justify-content: center;
}
.dm-pill {
    font-size: 12px; border-radius: 20px;
    padding: 4px 14px; border: 1px solid;
    font-weight: 500; cursor: pointer;
    transition: opacity .2s;
}
.dm-pill.pill-darkred { color: #4A1B0C; border-color: #A32D2D; background: #FAECE7; }
.dm-pill.pill-gray    { color: #2C2C2A; border-color: #888780; background: #F1EFE8; }
.dm-pill.pill-orange  { color: #412402; border-color: #EF9F27; background: #FAEEDA; }
.dm-pill.pill-blue    { color: #042C53; border-color: #378ADD; background: #E6F1FB; }
.dm-pill.pill-green   { color: #04342C; border-color: #1D9E75; background: #E1F5EE; }
.dm-pill.inactive     { opacity: .35; }

/* ── Section headings ── */
.dm-section-title { font-size: 18px; font-weight: 700; color: #1a2535; margin-bottom: 4px; }
.dm-section-sub   { font-size: 12px; color: #6b7a91; margin-bottom: 14px; }

/* ── Rankings ── */
.dm-rank-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
.dm-rank-card {
    background: #fff; border-radius: 14px;
    border: 1px solid #dde3ec; padding: 22px;
}
.dm-rank-head {
    font-size: 14px; font-weight: 700; color: #1a2535;
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 18px;
}
.ico-up   { color: #1D9E75; font-size: 18px; }
.ico-warn { color: #EF9F27; font-size: 16px; }

.dm-rank-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f4f8;
    font-size: 13px;
}
.dm-rank-row:last-child { border-bottom: none; }
.rank-no   { width: 22px; color: #6b7a91; font-weight: 600; flex-shrink: 0; }
.rank-name { color: #1a2535; flex: 1; }
.rank-bar  {
    width: 80px; height: 6px; background: #f0f4f8;
    border-radius: 4px; margin: 0 12px; overflow: hidden; flex-shrink: 0;
}
.rank-bar-fill { height: 100%; border-radius: 4px; }
.rank-num  { font-weight: 700; min-width: 56px; text-align: right; }
.rank-num.green { color: #1D9E75; }
.rank-num.red   { color: #E24B4A; }

/* ── Donut ── */
.dm-donut-wrap {
    display: flex; align-items: center;
    justify-content: center; gap: 32px;
    padding: 8px 0 4px;
}
.dm-donut-wrap canvas {
    width: 200px !important;
    height: 200px !important;
    max-width: 200px;
    max-height: 200px;
}
.dm-donut-legends { display: flex; flex-direction: column; gap: 16px; }
.dm-donut-item  { display: flex; align-items: center; gap: 12px; }
.dm-donut-dot   { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
.dm-donut-label { font-size: 14px; color: #1a2535; line-height: 1.2; }
.dm-donut-pct   { font-size: 17px; font-weight: 700; color: #1a2535; }
.dm-donut-count { font-size: 11px; color: #6b7a91; }

/* ── Regional grid ── */
.dm-bakorwil-grid {
    display: grid; grid-template-columns: repeat(5, 1fr);
    gap: 14px; margin-bottom: 32px;
}
.dm-bakorwil-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #dde3ec;
    padding: 20px 16px 16px;
    position: relative;
}
.dm-bakorwil-card.top { border: 2px solid #1D9E75; }
.dm-top-badge {
    position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
    background: #1D9E75; color: #fff;
    font-size: 9px; font-weight: 700; letter-spacing: .08em;
    border-radius: 20px; padding: 3px 10px;
    white-space: nowrap; text-transform: uppercase;
}
.dm-bak-name {
    font-size: 12px; font-weight: 700; color: #1a2535;
    margin-bottom: 14px; line-height: 1.4; text-align: center;
}
.dm-bak-stat {
    display: flex; align-items: center;
    gap: 8px; margin-bottom: 8px; font-size: 12px;
}
.dm-bak-dot   { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dm-bak-label { color: #6b7a91; flex: 1; }
.dm-bak-val   { font-weight: 700; color: #1a2535; }

/* ── Responsive ── */
@media (max-width: 960px) {
    .dm-kpi-grid      { grid-template-columns: repeat(2, 1fr); }
    .dm-bakorwil-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .dm-kpi-grid      { grid-template-columns: 1fr; }
    .dm-rank-grid     { grid-template-columns: 1fr; }
    .dm-bakorwil-grid { grid-template-columns: 1fr; }
    .dm-page          { padding: 16px 12px 0; }
    .dm-donut-wrap    { flex-direction: column; gap: 24px; }
}
</style>
@endpush

@section('content')
<div class="dm-wrap">
<div class="dm-page">

    {{-- ══ HEADER ══ --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:6px;">
        <div>
            <div class="dm-section-label">DASHBOARD</div>
            <h1 class="dm-page-title">Analisis Akselerasi <span>Desa Mandiri</span> Jawa Timur</h1>
            <p class="dm-page-subtitle">Analisis Rencana vs Realisasi Kabupaten/Kota</p>
        </div>
        <form method="GET" action="{{ route('demographics') }}" style="padding-top:4px;">
            <select name="year" class="dm-year-select" onchange="this.form.submit()">
                @foreach([2022, 2023, 2024, 2025] as $yr)
                    <option value="{{ $yr }}" {{ $data['year'] == $yr ? 'selected' : '' }}>
                        {{ $yr }} ▾
                    </option>
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
    <div class="dm-card">
        <div class="dm-card-title">Current IDM Proportions</div>
        <div class="dm-card-sub">Snapshot of village status in {{ $data['year'] }} · Total: {{ $data['grandTotal'] }} desa</div>

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

    {{-- ══ REGIONAL PERFORMANCE GRID ══ --}}
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
            <div style="margin-top:12px; height:6px; border-radius:4px; overflow:hidden; display:flex;">
                <div style="width:{{ $pctM }}%;  background:#1D9E75;"></div>
                <div style="width:{{ $pctMj }}%; background:#378ADD;"></div>
                <div style="width:{{ $pctB }}%;  background:#EF9F27;"></div>
            </div>
            <div style="font-size:10px; color:#6b7a91; margin-top:5px; text-align:right;">
                Total: {{ number_format($total) }} desa
            </div>
        </div>
        @endforeach
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
    const donutRaw   = @json($data['idmProportions']);
    const donutCtx   = document.getElementById('donutChart').getContext('2d');
    const mandiriPct = {{ $data['mandiriPercent'] }};

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
@endpush