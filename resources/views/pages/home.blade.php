@extends('layouts.app')

@push('styles')
<style>
/* ═══════════════════════════════════════════
   HERO — full page with cards inside
═══════════════════════════════════════════ */
.hero {
  position: relative;
  width: 100%;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  background: #0f2d5e;
}

.hero-bg {
  position: absolute; inset: 0;
  background-image: url("{{ asset('images/gedung.jpg') }}");
  background-size: cover;
  background-position: center top;
  transform: scale(1.04);
  transition: transform 10s ease;
}
.hero:hover .hero-bg { transform: scale(1.0); }

.hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(
    180deg,
    rgba(5,15,45,0.72) 0%,
    rgba(8,22,60,0.52) 35%,
    rgba(5,15,45,0.84) 100%
  );
}

/* ─── Hero text block ─── */
.hero-content {
  position: relative;
  z-index: 4;
  text-align: center;
  padding: 52px 24px 36px;
  max-width: 860px;
  width: 100%;
  margin: 0 auto;
  animation: fadeUp .8s ease both;
}

@keyframes fadeUp {
  from { opacity:0; transform:translateY(24px); }
  to   { opacity:1; transform:translateY(0); }
}

.hero-title {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(24px, 4vw, 50px);
  font-weight: 800;
  color: #fff;
  line-height: 1.18;
  letter-spacing: -1.5px;
  margin-bottom: 8px;
  text-shadow: 0 2px 24px rgba(0,0,0,0.55);
}

.hero-subtitle {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(17px, 2.8vw, 30px);
  font-weight: 700;
  color: #60a5fa;
  margin-bottom: 18px;
  text-shadow: 0 2px 14px rgba(0,0,0,0.45);
}

.hero-divider {
  width: 100px; height: 3px;
  background: linear-gradient(90deg, #1565C0, #60a5fa, #93c5fd);
  border-radius: 2px;
  margin: 0 auto 40px;
}

/* ─── Stat pills ─── */
.hero-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 44px;
  flex-wrap: wrap;
}
.hero-stat-pill {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(255,255,255,0.10);
  border: 1px solid rgba(255,255,255,0.18);
  backdrop-filter: blur(12px);
  border-radius: 100px;
  padding: 8px 20px;
}
.hero-stat-num {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 18px;
  font-weight: 800;
  color: #fff;
  line-height: 1;
}
.hero-stat-label {
  font-size: 11.5px;
  color: rgba(255,255,255,0.70);
  font-weight: 500;
}
.hero-stat-sep {
  width: 1px; height: 28px;
  background: rgba(255,255,255,0.20);
}

/* ═══════════════════════════════════════════
   CARDS — di dalam hero di atas BG gambar
═══════════════════════════════════════════ */
.cards-wrapper {
  position: relative;
  z-index: 4;
  width: 100%;
  max-width: 1160px;
  padding: 0 28px 56px;
  margin: 0 auto;
  animation: fadeUp .9s .15s ease both;
}

.cards-section-label {
  text-align: center;
  margin-bottom: 22px;
}
.cards-section-label span {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.4px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.55);
  border: 1px solid rgba(255,255,255,0.18);
  border-radius: 100px;
  padding: 4px 14px;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(168px, 1fr));
  gap: 14px;
}

/* ── Individual Card — glassmorphism ── */
.dash-card {
  position: relative;
  background: rgba(255,255,255,0.11);
  border: 1px solid rgba(255,255,255,0.20);
  backdrop-filter: blur(18px) saturate(1.6);
  -webkit-backdrop-filter: blur(18px) saturate(1.6);
  border-radius: 18px;
  padding: 22px 18px 20px;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: transform .28s cubic-bezier(.34,1.56,.64,1),
              background .25s ease,
              box-shadow .25s ease,
              border-color .25s ease;
  overflow: hidden;
  cursor: pointer;
  animation: cardIn .55s ease both;
}
.dash-card:nth-child(1){ animation-delay:.10s }
.dash-card:nth-child(2){ animation-delay:.16s }
.dash-card:nth-child(3){ animation-delay:.22s }
.dash-card:nth-child(4){ animation-delay:.28s }
.dash-card:nth-child(5){ animation-delay:.34s }
.dash-card:nth-child(6){ animation-delay:.40s }
.dash-card:nth-child(7){ animation-delay:.46s }

@keyframes cardIn {
  from { opacity:0; transform:translateY(20px) scale(.97); }
  to   { opacity:1; transform:translateY(0) scale(1); }
}

.dash-card:hover {
  transform: translateY(-7px) scale(1.02);
  background: rgba(255,255,255,0.18);
  border-color: rgba(255,255,255,0.35);
  box-shadow: 0 24px 52px rgba(0,0,0,0.32), 0 0 0 1px rgba(255,255,255,0.12);
}

/* Colored top accent bar */
.dash-card::before {
  content:'';
  position:absolute; top:0; left:0; right:0;
  height:3px;
  border-radius:18px 18px 0 0;
  background: var(--card-accent, linear-gradient(90deg,#60a5fa,#93c5fd));
  opacity:0;
  transition: opacity .25s;
}
.dash-card:hover::before { opacity:1; }

/* Icon wrapper */
.card-icon-wrap {
  width: 44px; height: 44px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: transform .28s cubic-bezier(.34,1.56,.64,1);
}
.dash-card:hover .card-icon-wrap { transform: scale(1.14) rotate(-5deg); }

.card-icon-wrap svg {
  width: 22px; height: 22px;
  stroke: currentColor;
  fill: none;
  stroke-width: 1.9;
  stroke-linecap: round;
  stroke-linejoin: round;
}

/* Card tag */
.card-tag {
  display: inline-block;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: .7px;
  text-transform: uppercase;
  border-radius: 5px;
  padding: 2px 7px;
  margin-bottom: 2px;
  background: rgba(255,255,255,0.12);
  color: rgba(255,255,255,0.65);
}

.card-name {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  font-weight: 700;
  color: #fff;
  line-height: 1.3;
  margin-bottom: 4px;
  text-shadow: 0 1px 6px rgba(0,0,0,0.3);
}

.card-desc {
  font-size: 11.5px;
  color: rgba(255,255,255,0.60);
  line-height: 1.55;
  flex: 1;
}

/* Arrow CTA */
.card-cta {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  font-weight: 600;
  color: rgba(255,255,255,0.80);
  margin-top: 2px;
  transition: gap .2s, color .2s;
}
.dash-card:hover .card-cta { gap: 9px; color:#fff; }
.card-cta svg {
  width: 13px; height: 13px;
  stroke: currentColor; fill:none; stroke-width:2.2;
}

/* ─── Color accent per card ─── */
.card--bansos      { --card-accent: linear-gradient(90deg,#3b82f6,#93c5fd); }
.card--bansos      .card-icon-wrap { background:rgba(59,130,246,0.22); color:#93c5fd; }

.card--kemiskinan  { --card-accent: linear-gradient(90deg,#f59e0b,#fcd34d); }
.card--kemiskinan  .card-icon-wrap { background:rgba(245,158,11,0.22); color:#fcd34d; }

.card--demographics{ --card-accent: linear-gradient(90deg,#8b5cf6,#c4b5fd); }
.card--demographics .card-icon-wrap { background:rgba(139,92,246,0.22); color:#c4b5fd; }

.card--perikanan   { --card-accent: linear-gradient(90deg,#06b6d4,#67e8f9); }
.card--perikanan   .card-icon-wrap { background:rgba(6,182,212,0.22); color:#67e8f9; }

.card--environment { --card-accent: linear-gradient(90deg,#22c55e,#86efac); }
.card--environment .card-icon-wrap { background:rgba(34,197,94,0.22); color:#86efac; }

.card--education   { --card-accent: linear-gradient(90deg,#f43f5e,#fda4af); }
.card--education   .card-icon-wrap { background:rgba(244,63,94,0.22); color:#fda4af; }

/* ── BARU: Agri & Pangan ── */
.card--agri        { --card-accent: linear-gradient(90deg,#84cc16,#d9f99d); }
.card--agri        .card-icon-wrap { background:rgba(132,204,22,0.22); color:#d9f99d; }

/* ─── Footer ─── */
.footer-strip {
  background: #fff;
  border-top: 1px solid #E4E8F0;
  padding: 16px 28px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 8px;
}
.footer-strip p { font-size: 12px; color: #9CA3AF; }

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .cards-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .cards-wrapper { padding: 0 16px 44px; }
  .hero-content { padding: 44px 16px 28px; }
}
@media (max-width: 400px) {
  .cards-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>

  {{-- Judul --}}
  <div class="hero-content">
    <h1 class="hero-title">Dashboard Analisis Data Publik</h1>
    <h2 class="hero-subtitle">Provinsi Jawa Timur</h2>
    <div class="hero-divider"></div>

    <div class="hero-stats">
      <div class="hero-stat-pill">
        <div class="hero-stat-num">38</div>
        <div class="hero-stat-sep"></div>
        <div class="hero-stat-label">Kabupaten / Kota</div>
      </div>
      <div class="hero-stat-pill">
        <div class="hero-stat-num">7</div>
        <div class="hero-stat-sep"></div>
        <div class="hero-stat-label">Modul Data Aktif</div>
      </div>
    </div>
  </div>

  {{-- Cards di dalam hero, di atas BG gambar --}}
  <div class="cards-wrapper">
    <div class="cards-section-label">
      <span>Navigasi Modul Data</span>
    </div>

    <div class="cards-grid">

      {{-- Bansos --}}
      <a href="{{ route('economy') }}" class="dash-card card--bansos">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
            <polyline points="17 6 23 6 23 12"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sosial &amp; Ekonomi</div>
          <div class="card-name">Bantuan Sosial</div>
          <div class="card-desc">Distribusi dan penerima program bansos per kabupaten/kota.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

      {{-- Kemiskinan & Naker --}}
      <a href="{{ route('kemiskinan') }}" class="dash-card card--kemiskinan">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            <line x1="12" y1="14" x2="12" y2="18"/>
            <line x1="10" y1="16" x2="14" y2="16"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sosial &amp; Ekonomi</div>
          <div class="card-name">Kemiskinan &amp; Naker</div>
          <div class="card-desc">Tingkat kemiskinan, pengangguran, dan ketenagakerjaan daerah.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

      {{-- Demographics --}}
      <a href="{{ route('demographics') }}" class="dash-card card--demographics">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 00-3-3.87"/>
            <path d="M16 3.13a4 4 0 010 7.75"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sosial &amp; Ekonomi</div>
          <div class="card-name">Demografi</div>
          <div class="card-desc">Sebaran penduduk, struktur usia, dan dinamika kependudukan.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

      {{-- Perikanan --}}
      <a href="{{ route('perikanan') }}" class="dash-card card--perikanan">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <path d="M4 10h16l-2 3H6l-2-3z"/>
            <path d="M12 4v6"/>
            <path d="M12 4l3 2M12 4l-3 2"/>
            <path d="M2 16c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sektoral</div>
          <div class="card-name">Produksi Perikanan</div>
          <div class="card-desc">Data hasil tangkap, budidaya, dan sentra perikanan per wilayah.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

      {{-- Environment --}}
      <a href="{{ route('environment') }}" class="dash-card card--environment">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 2a14.5 14.5 0 000 20 14.5 14.5 0 000-20"/>
            <path d="M2 12h20"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sektoral</div>
          <div class="card-name">Lingkungan Hidup</div>
          <div class="card-desc">Indikator kualitas udara, air, dan lingkungan per kabupaten/kota.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

      {{-- Education --}}
      <a href="{{ route('education') }}" class="dash-card card--education">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sektoral</div>
          <div class="card-name">Pendidikan</div>
          <div class="card-desc">Angka partisipasi sekolah, fasilitas, dan capaian literasi daerah.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

      {{-- ★ BARU: Agri & Pangan ★ --}}
      <a href="{{ route('agri') }}" class="dash-card card--agri">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <path d="M12 2C6 8 4 13 12 22c8-9 6-14 0-20z"/>
            <path d="M12 22V8"/>
            <path d="M12 12l-3-3M12 16l3-3"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sektoral</div>
          <div class="card-name">Agri &amp; Pangan</div>
          <div class="card-desc">Luas panen, produksi padi, dan produktivitas lahan per daerah.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

    </div>
  </div>
</section>

<div class="footer-strip">
  <p>© {{ date('Y') }} Dinas Komunikasi dan Informatika Provinsi Jawa Timur</p>
  <p>Dashboard Analisis Data Publik — v2.0</p>
</div>

@endsection