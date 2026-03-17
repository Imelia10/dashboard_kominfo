@extends('layouts.app')

@push('styles')
<style>
.hero{
  position:relative;
  width:100%;
  min-height:100vh; /* ubah ini */
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
  background:#1a3a6b;
}
.hero-bg{
  position:absolute;inset:0;
  background-image:url("{{ asset('images/gedung.jpg') }}");
  background-size:cover;
  background-position:center top;
}
.hero-overlay{
  position:absolute;inset:0;
  background:linear-gradient(
    160deg,
    rgba(10,25,60,0.18) 0%,
    rgba(5,15,40,0.08) 50%,
    rgba(10,25,60,0.16) 100%
  );
}
.hero-content{
  position:relative;
  z-index:2;
  text-align:center;
  padding:300px 24px;
  max-width:900px;
  margin:auto;
  transform:translateY(-20px); /* sedikit naik biar estetik */
}
@keyframes fadeUp{
  from{opacity:0;transform:translateY(24px)}
  to{opacity:1;transform:translateY(0)}
}
.hero-title{
  font-family:'Plus Jakarta Sans',sans-serif;
  font-size:clamp(26px,4.5vw,50px);
  font-weight:800;
  color:#fff;
  line-height:1.2; /* diperbaiki */
  letter-spacing:-1px;
  margin-bottom:6px; /* jarak ke subtitle */
  text-shadow:0 2px 16px rgba(0,0,0,0.55);
}

.hero-subtitle{
  font-family:'Plus Jakarta Sans',sans-serif;
  font-size:clamp(20px,3.5vw,38px);
  font-weight:700;
  color:#60a5fa;
  margin-top:0;
  margin-bottom:14px;
  text-shadow:0 2px 14px rgba(0,0,0,0.5);
}
.hero-divider{
  width:110px;height:3.5px;
  background:linear-gradient(90deg,#1565C0,#60a5fa);
  border-radius:2px;margin:0 auto;
}
</style>
@endpush

@section('content')
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1 class="hero-title">Dashboard Analisis Data Publik</h1>
    <h2 class="hero-subtitle">Provinsi Jawa Timur</h2>
    <div class="hero-divider"></div>
  </div>
</section>
@endsection
