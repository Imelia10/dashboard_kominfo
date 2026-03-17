@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-wrapper">
    <div class="page-header">
      <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a><span>›</span><span>Environment</span>
      </div>
      <h1 class="page-title">Data <span>Lingkungan</span></h1>
      <p class="page-subtitle">Analisis data lingkungan Provinsi Jawa Timur · Update: Maret 2025</p>
    </div>
    <div class="kpi-grid">
      <div class="kpi-card blue"><div class="kpi-label">Indeks Kualitas Lingk.</div><div class="kpi-value">65.4</div><span class="kpi-change up">▲ 1.2%</span></div>
      <div class="kpi-card green"><div class="kpi-label">Tutupan Hutan</div><div class="kpi-value">23.8%</div><span class="kpi-change down">▼ 0.5%</span></div>
      <div class="kpi-card orange"><div class="kpi-label">Indeks Kualitas Air</div><div class="kpi-value">52.1</div><span class="kpi-change up">▲ 2.8%</span></div>
      <div class="kpi-card purple"><div class="kpi-label">Emisi CO₂</div><div class="kpi-value">42.3 Mt</div><span class="kpi-change down">▼ 3.1%</span></div>
    </div>
    <div class="info-placeholder">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 000 20 14.5 14.5 0 000-20"/><path d="M2 12h20"/></svg>
      <p>Visualisasi data lingkungan lebih lengkap akan segera tersedia.</p>
    </div>
  </div>
</div>
@endsection
