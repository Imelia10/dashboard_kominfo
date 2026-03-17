@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-wrapper">
    <div class="page-header">
      <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span>›</span>
        <span>Economy</span>
      </div>
      <h1 class="page-title">Data <span>Ekonomi</span></h1>
      <p class="page-subtitle">Analisis data ekonomi Provinsi Jawa Timur · Update: Maret 2025</p>
    </div>

    <div class="kpi-grid">
      <div class="kpi-card blue">
        <div class="kpi-label">PDRB</div>
        <div class="kpi-value">Rp 2.710 T</div>
        <span class="kpi-change up">▲ 5.02%</span>
      </div>
      <div class="kpi-card green">
        <div class="kpi-label">Ekspor</div>
        <div class="kpi-value">$18.4 M</div>
        <span class="kpi-change up">▲ 8.3%</span>
      </div>
      <div class="kpi-card orange">
        <div class="kpi-label">Inflasi</div>
        <div class="kpi-value">2.74%</div>
        <span class="kpi-change down">▼ 0.3%</span>
      </div>
      <div class="kpi-card purple">
        <div class="kpi-label">Investasi</div>
        <div class="kpi-value">Rp 89.6 T</div>
        <span class="kpi-change up">▲ 12.1%</span>
      </div>
    </div>

    <div class="charts-grid">
      <div class="chart-card">
        <div class="card-title">Pertumbuhan PDRB per Tahun</div>
        <div class="card-subtitle">2019 – 2024 (Triliun Rupiah)</div>
        <svg class="bar-chart" viewBox="0 0 460 180">
          <line x1="40" y1="10" x2="40" y2="150" stroke="#e4e8f0" stroke-width="1"/>
          <line x1="40" y1="150" x2="455" y2="150" stroke="#e4e8f0" stroke-width="1"/>
          <line x1="40" y1="110" x2="455" y2="110" stroke="#f0f4fb" stroke-width="1" stroke-dasharray="4"/>
          <line x1="40" y1="70"  x2="455" y2="70"  stroke="#f0f4fb" stroke-width="1" stroke-dasharray="4"/>
          <line x1="40" y1="30"  x2="455" y2="30"  stroke="#f0f4fb" stroke-width="1" stroke-dasharray="4"/>
          <text x="34" y="154" font-size="9" fill="#9aacca" text-anchor="end">0</text>
          <text x="34" y="114" font-size="9" fill="#9aacca" text-anchor="end">1000</text>
          <text x="34" y="74"  font-size="9" fill="#9aacca" text-anchor="end">2000</text>
          <text x="34" y="34"  font-size="9" fill="#9aacca" text-anchor="end">3000</text>
          <rect x="55"  y="68" width="50" height="82"  rx="5" fill="url(#bg1)" opacity="0.85"/>
          <rect x="120" y="82" width="50" height="68"  rx="5" fill="url(#bg1)" opacity="0.85"/>
          <rect x="185" y="72" width="50" height="78"  rx="5" fill="url(#bg1)" opacity="0.85"/>
          <rect x="250" y="62" width="50" height="88"  rx="5" fill="url(#bg1)" opacity="0.85"/>
          <rect x="315" y="50" width="50" height="100" rx="5" fill="url(#bg1)" opacity="0.9"/>
          <rect x="380" y="38" width="50" height="112" rx="5" fill="url(#bg1)"/>
          <text x="80"  y="64"  font-size="8" fill="#1565C0" text-anchor="middle" font-weight="700">2.106</text>
          <text x="145" y="78"  font-size="8" fill="#1565C0" text-anchor="middle" font-weight="700">1.968</text>
          <text x="210" y="68"  font-size="8" fill="#1565C0" text-anchor="middle" font-weight="700">2.115</text>
          <text x="275" y="58"  font-size="8" fill="#1565C0" text-anchor="middle" font-weight="700">2.311</text>
          <text x="340" y="46"  font-size="8" fill="#1565C0" text-anchor="middle" font-weight="700">2.532</text>
          <text x="405" y="34"  font-size="8" fill="#1565C0" text-anchor="middle" font-weight="700">2.710</text>
          <text x="80"  y="165" font-size="9" fill="#9aacca" text-anchor="middle">2019</text>
          <text x="145" y="165" font-size="9" fill="#9aacca" text-anchor="middle">2020</text>
          <text x="210" y="165" font-size="9" fill="#9aacca" text-anchor="middle">2021</text>
          <text x="275" y="165" font-size="9" fill="#9aacca" text-anchor="middle">2022</text>
          <text x="340" y="165" font-size="9" fill="#9aacca" text-anchor="middle">2023</text>
          <text x="405" y="165" font-size="9" fill="#9aacca" text-anchor="middle">2024</text>
          <defs>
            <linearGradient id="bg1" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stop-color="#2196F3"/>
              <stop offset="100%" stop-color="#1565C0"/>
            </linearGradient>
          </defs>
        </svg>
      </div>
      <div class="chart-card">
        <div class="card-title">Sektor Unggulan</div>
        <div class="card-subtitle">Kontribusi terhadap PDRB</div>
        <div class="pie-visual">
          <div class="pie-item">
            <div class="pie-dot" style="background:#1565C0"></div>
            <span style="flex:1;color:#4a5568">Industri Pengolahan</span>
            <div class="pie-bar-wrap"><div class="pie-bar-fill" style="background:#1565C0;width:30%"></div></div>
            <span class="pie-pct">30%</span>
          </div>
          <div class="pie-item">
            <div class="pie-dot" style="background:#2196F3"></div>
            <span style="flex:1;color:#4a5568">Perdagangan</span>
            <div class="pie-bar-wrap"><div class="pie-bar-fill" style="background:#2196F3;width:20%"></div></div>
            <span class="pie-pct">20%</span>
          </div>
          <div class="pie-item">
            <div class="pie-dot" style="background:#66BB6A"></div>
            <span style="flex:1;color:#4a5568">Pertanian</span>
            <div class="pie-bar-wrap"><div class="pie-bar-fill" style="background:#66BB6A;width:12%"></div></div>
            <span class="pie-pct">12%</span>
          </div>
          <div class="pie-item">
            <div class="pie-dot" style="background:#FFA726"></div>
            <span style="flex:1;color:#4a5568">Konstruksi</span>
            <div class="pie-bar-wrap"><div class="pie-bar-fill" style="background:#FFA726;width:9%"></div></div>
            <span class="pie-pct">9%</span>
          </div>
          <div class="pie-item">
            <div class="pie-dot" style="background:#AB47BC"></div>
            <span style="flex:1;color:#4a5568">Lainnya</span>
            <div class="pie-bar-wrap"><div class="pie-bar-fill" style="background:#AB47BC;width:29%"></div></div>
            <span class="pie-pct">29%</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
