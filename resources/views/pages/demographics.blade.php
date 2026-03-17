@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-wrapper">
    <div class="page-header">
      <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span>›</span>
        <span>Demographics</span>
      </div>
      <h1 class="page-title">Data <span>Kependudukan</span></h1>
      <p class="page-subtitle">Analisis data kependudukan Provinsi Jawa Timur · Update: Maret 2025</p>
    </div>
    <div class="kpi-grid">
      <div class="kpi-card blue"><div class="kpi-label">Total Penduduk</div><div class="kpi-value">41.2 Juta</div><span class="kpi-change up">▲ 0.8%</span></div>
      <div class="kpi-card green"><div class="kpi-label">Kepadatan</div><div class="kpi-value">862/km²</div><span class="kpi-change up">▲ 0.6%</span></div>
      <div class="kpi-card orange"><div class="kpi-label">Laju Pertumbuhan</div><div class="kpi-value">0.82%</div><span class="kpi-change down">▼ 0.1%</span></div>
      <div class="kpi-card purple"><div class="kpi-label">Rasio Ketergantungan</div><div class="kpi-value">44.2%</div><span class="kpi-change down">▼ 1.2%</span></div>
    </div>
    <div class="info-placeholder">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
      </svg>
      <p>Visualisasi data kependudukan lebih lengkap akan segera tersedia.</p>
    </div>
  </div>
</div>
@endsection
