@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-wrapper">
    <div class="page-header">
      <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a><span>›</span><span>Health</span>
      </div>
      <h1 class="page-title">Data <span>Kesehatan</span></h1>
      <p class="page-subtitle">Analisis data kesehatan Provinsi Jawa Timur · Update: Maret 2025</p>
    </div>
    <div class="kpi-grid">
      <div class="kpi-card blue"><div class="kpi-label">Fasilitas Kesehatan</div><div class="kpi-value">4.821</div><span class="kpi-change up">▲ 2.3%</span></div>
      <div class="kpi-card green"><div class="kpi-label">Angka Harapan Hidup</div><div class="kpi-value">72.1 Thn</div><span class="kpi-change up">▲ 0.4%</span></div>
      <div class="kpi-card orange"><div class="kpi-label">Angka Kematian Bayi</div><div class="kpi-value">18.2‰</div><span class="kpi-change down">▼ 1.1%</span></div>
      <div class="kpi-card purple"><div class="kpi-label">Tenaga Medis</div><div class="kpi-value">89.430</div><span class="kpi-change up">▲ 5.6%</span></div>
    </div>
    <div class="info-placeholder">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
      <p>Visualisasi data kesehatan lebih lengkap akan segera tersedia.</p>
    </div>
  </div>
</div>
@endsection
