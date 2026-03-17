@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="page-wrapper">
    <div class="page-header">
      <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a><span>›</span><span>Education</span>
      </div>
      <h1 class="page-title">Data <span>Pendidikan</span></h1>
      <p class="page-subtitle">Analisis data pendidikan Provinsi Jawa Timur · Update: Maret 2025</p>
    </div>
    <div class="kpi-grid">
      <div class="kpi-card blue"><div class="kpi-label">Angka Melek Huruf</div><div class="kpi-value">94.7%</div><span class="kpi-change up">▲ 0.3%</span></div>
      <div class="kpi-card green"><div class="kpi-label">Rata-rata Lama Sekolah</div><div class="kpi-value">8.4 Thn</div><span class="kpi-change up">▲ 0.2%</span></div>
      <div class="kpi-card orange"><div class="kpi-label">Jumlah Sekolah</div><div class="kpi-value">37.820</div><span class="kpi-change up">▲ 1.5%</span></div>
      <div class="kpi-card purple"><div class="kpi-label">APK Perguruan Tinggi</div><div class="kpi-value">32.6%</div><span class="kpi-change up">▲ 2.1%</span></div>
    </div>
    <div class="info-placeholder">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
      <p>Visualisasi data pendidikan lebih lengkap akan segera tersedia.</p>
    </div>
  </div>
</div>
@endsection
