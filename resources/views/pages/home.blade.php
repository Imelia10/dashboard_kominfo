@extends('layouts.app')

@push('styles')
<style>
/* ═══════════════════════════════════════════
   HERO
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

/* ── overlay lebih terang dari sebelumnya ── */
.hero-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(
    180deg,
    rgba(5,15,45,0.52) 0%,
    rgba(8,22,60,0.30) 35%,
    rgba(5,15,45,0.68) 100%
  );
}

/* ─── Hero text block ─── */
.hero-content {
  position: relative;
  z-index: 4;
  text-align: center;
  padding: 52px 24px 32px;
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
  font-size: clamp(22px, 3.8vw, 48px);
  font-weight: 800;
  color: #fff;
  line-height: 1.18;
  letter-spacing: -1.5px;
  margin-bottom: 8px;
  text-shadow: 0 2px 24px rgba(0,0,0,0.45);
}

.hero-subtitle {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: clamp(16px, 2.6vw, 28px);
  font-weight: 700;
  color: #93c5fd;
  margin-bottom: 18px;
  text-shadow: 0 2px 14px rgba(0,0,0,0.35);
}

.hero-divider {
  width: 100px; height: 3px;
  background: linear-gradient(90deg, #1565C0, #60a5fa, #93c5fd);
  border-radius: 2px;
  margin: 0 auto 36px;
}

/* ─── Stat pills ─── */
.hero-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 40px;
  flex-wrap: wrap;
}
.hero-stat-pill {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(255,255,255,0.13);
  border: 1px solid rgba(255,255,255,0.22);
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
  color: rgba(255,255,255,0.72);
  font-weight: 500;
}
.hero-stat-sep {
  width: 1px; height: 28px;
  background: rgba(255,255,255,0.22);
}

/* ═══════════════════════════════════════════
   CARDS WRAPPER — 4 + 4 grid
═══════════════════════════════════════════ */
.cards-wrapper {
  position: relative;
  z-index: 4;
  width: 100%;
  max-width: 1100px;
  padding: 0 28px 56px;
  margin: 0 auto;
  animation: fadeUp .9s .15s ease both;
}

.cards-section-label {
  text-align: center;
  margin-bottom: 20px;
}
.cards-section-label span {
  display: inline-block;
  font-size: 10px;
  font-weight: 700;
  letter-spacing: 1.4px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.60);
  border: 1px solid rgba(255,255,255,0.22);
  border-radius: 100px;
  padding: 4px 16px;
}

/* ── Row label (Sosial & Sektoral) ── */
.cards-row-label {
  font-size: 9.5px;
  font-weight: 700;
  letter-spacing: 1.1px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.45);
  margin-bottom: 10px;
  padding-left: 2px;
}

.cards-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 14px;
  margin-bottom: 14px;
}

/* ═══════════════════════════════════════════
   DASH CARD — solid color, no glassmorphism
═══════════════════════════════════════════ */
.dash-card {
  position: relative;
  border-radius: 18px;
  padding: 22px 20px 20px;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  gap: 14px;
  transition: transform .30s cubic-bezier(.34,1.56,.64,1),
              box-shadow .25s ease,
              filter .25s ease;
  overflow: hidden;
  cursor: pointer;
  animation: cardIn .55s ease both;
  /* shimmer top border */
  border-top: 3px solid var(--card-top, rgba(255,255,255,0.25));
}

.dash-card:nth-child(1){ animation-delay:.08s }
.dash-card:nth-child(2){ animation-delay:.14s }
.dash-card:nth-child(3){ animation-delay:.20s }
.dash-card:nth-child(4){ animation-delay:.26s }

@keyframes cardIn {
  from { opacity:0; transform:translateY(18px) scale(.97); }
  to   { opacity:1; transform:translateY(0) scale(1); }
}

.dash-card:hover {
  transform: translateY(-8px) scale(1.025);
  filter: brightness(1.08);
  box-shadow: 0 28px 56px rgba(0,0,0,0.36), 0 0 0 1.5px rgba(255,255,255,0.15);
}

/* Decorative background blob */
.dash-card::after {
  content:'';
  position:absolute;
  right: -24px; bottom: -24px;
  width: 100px; height: 100px;
  border-radius: 50%;
  background: rgba(255,255,255,0.08);
  transition: transform .35s ease;
}
.dash-card:hover::after { transform: scale(1.5); }

/* Icon wrapper */
.card-icon-wrap {
  width: 46px; height: 46px;
  border-radius: 13px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  background: rgba(255,255,255,0.18);
  transition: transform .28s cubic-bezier(.34,1.56,.64,1), background .2s;
}
.dash-card:hover .card-icon-wrap {
  transform: scale(1.14) rotate(-5deg);
  background: rgba(255,255,255,0.26);
}
.card-icon-wrap svg {
  width: 22px; height: 22px;
  stroke: #fff;
  fill: none;
  stroke-width: 2;
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
  background: rgba(255,255,255,0.18);
  color: rgba(255,255,255,0.75);
}

.card-name {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14.5px;
  font-weight: 700;
  color: #fff;
  line-height: 1.3;
  margin-bottom: 4px;
}

.card-desc {
  font-size: 11.5px;
  color: rgba(255,255,255,0.72);
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
  margin-top: auto;
  transition: gap .2s, color .2s;
}
.dash-card:hover .card-cta { gap: 10px; color:#fff; }
.card-cta svg {
  width: 13px; height: 13px;
  stroke: currentColor; fill:none; stroke-width:2.2;
}

/* ─── Solid color per card ─── */
/* Row 1 — Sosial & Ekonomi */
.card--bansos     { background: linear-gradient(145deg, #1d4ed8, #2563eb); --card-top: #60a5fa; box-shadow: 0 8px 28px rgba(29,78,216,0.40); }
.card--kemiskinan { background: linear-gradient(145deg, #b45309, #d97706); --card-top: #fbbf24; box-shadow: 0 8px 28px rgba(180,83,9,0.40); }
.card--demographics { background: linear-gradient(145deg, #6d28d9, #7c3aed); --card-top: #a78bfa; box-shadow: 0 8px 28px rgba(109,40,217,0.40); }
.card--bencana    { background: linear-gradient(145deg, #b91c1c, #dc2626); --card-top: #f87171; box-shadow: 0 8px 28px rgba(185,28,28,0.40); }

/* Row 2 — Sektoral */
.card--perikanan  { background: linear-gradient(145deg, #0e7490, #0891b2); --card-top: #22d3ee; box-shadow: 0 8px 28px rgba(14,116,144,0.40); }
.card--agri       { background: linear-gradient(145deg, #3d7a20, #4e9628); --card-top: #86efac; box-shadow: 0 8px 28px rgba(61,122,32,0.40); }
.card--environment{ background: linear-gradient(145deg, #065f46, #047857); --card-top: #34d399; box-shadow: 0 8px 28px rgba(6,95,70,0.40); }
.card--kepadatan  { background: linear-gradient(145deg, #9d174d, #be185d); --card-top: #f9a8d4; box-shadow: 0 8px 28px rgba(157,23,77,0.40); }

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
@media (max-width: 900px) {
  .cards-row { grid-template-columns: repeat(2, 1fr); gap: 12px; }
  .cards-wrapper { padding: 0 16px 44px; }
  .hero-content { padding: 44px 16px 24px; }
}
@media (max-width: 480px) {
  .cards-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
}
</style>
@endpush

@section('content')

<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>

  {{-- ── Judul ── --}}
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
        <div class="hero-stat-num">8</div>
        <div class="hero-stat-sep"></div>
        <div class="hero-stat-label">Modul Data Aktif</div>
      </div>
    </div>
  </div>

  {{-- ── Cards 4 + 4 ── --}}
  <div class="cards-wrapper">
    <div class="cards-section-label">
      <span>Navigasi Modul Data</span>
    </div>

    {{-- ── ROW 1: Sosial & Ekonomi ── --}}
    <div class="cards-row-label">Sosial &amp; Ekonomi</div>
    <div class="cards-row">

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

      {{-- Kemiskinan --}}
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

      {{-- Bencana Alam --}}
      <a href="{{ route('bencana') }}" class="dash-card card--bencana">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/>
            <line x1="12" y1="17" x2="12.01" y2="17"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sosial &amp; Ekonomi</div>
          <div class="card-name">Bencana Alam</div>
          <div class="card-desc">Data kejadian bencana, dampak, dan persebaran risiko per wilayah.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

    </div>{{-- end row 1 --}}

    {{-- ── ROW 2: Sektoral ── --}}
    <div class="cards-row-label" style="margin-top:8px;">Sektoral</div>
    <div class="cards-row">

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

      {{-- Agri & Pangan --}}
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

      {{-- Kepadatan Penduduk --}}
      <a href="{{ route('kepadatan') }}" class="dash-card card--kepadatan">
        <div class="card-icon-wrap">
          <svg viewBox="0 0 24 24">
            <rect x="3" y="3" width="7" height="7" rx="1"/>
            <rect x="14" y="3" width="7" height="7" rx="1"/>
            <rect x="3" y="14" width="7" height="7" rx="1"/>
            <rect x="14" y="14" width="7" height="7" rx="1"/>
          </svg>
        </div>
        <div>
          <div class="card-tag">Sektoral</div>
          <div class="card-name">Kepadatan Penduduk</div>
          <div class="card-desc">Peta kepadatan, tren pertumbuhan, dan distribusi usia per daerah.</div>
        </div>
        <div class="card-cta">
          Lihat Data
          <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </div>
      </a>

    </div>{{-- end row 2 --}}

  </div>{{-- end cards-wrapper --}}
</section>

<div class="footer-strip">
  <p>© {{ date('Y') }} Dinas Komunikasi dan Informatika Provinsi Jawa Timur</p>
  <p>Dashboard Analisis Data Publik — v2.0</p>
</div>

@endsection