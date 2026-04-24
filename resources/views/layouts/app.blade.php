<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Analisis Data Publik - Provinsi Jawa Timur</title>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

{{-- Tailwind CDN --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: {
          'jakarta': ['"Plus Jakarta Sans"', 'sans-serif'],
          'dm': ['"DM Sans"', 'sans-serif'],
        }
      }
    }
  }
</script>

<style>
  /* Hard reset */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  /* html/body fill viewport, no scroll here */
  html, body {
    height: 100%;
    overflow: hidden;
    font-family: 'DM Sans', sans-serif;
    background: #F4F6FA;
    color: #1a2236;
  }

  :root {
    --header-h: 64px;
    --sidebar-w: 220px;
    --ease: cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* ─── HEADER ─── */
  .app-header {
    position: fixed;
    inset: 0 0 auto 0;
    height: var(--header-h);
    background: #fff;
    border-bottom: 1px solid #e4e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 24px;
    z-index: 100;
    box-shadow: 0 1px 8px rgba(21,101,192,0.07);
  }

  .brand {
    display: flex; align-items: center; gap: 12px;
    cursor: pointer; user-select: none;
    transition: opacity .2s;
  }
  .brand:hover { opacity: .8; }

  .brand-icon {
    width: 40px; height: 40px; border-radius: 10px;
    background: linear-gradient(135deg, #0f2d5e, #1565C0);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    padding: 6px;
  }
  .brand-icon img { width: 100%; height: 100%; object-fit: contain; }

  /* ─── SIDEBAR ─── */
  .app-sidebar {
    position: fixed;
    top: var(--header-h);
    left: 0;
    width: var(--sidebar-w);
    height: calc(100vh - var(--header-h));
    background: #fff;
    border-right: 1px solid #e4e8f0;
    padding: 18px 0 24px;
    transform: translateX(calc(-1 * var(--sidebar-w)));
    transition: transform .35s var(--ease), box-shadow .35s var(--ease);
    z-index: 90;
    overflow-y: auto;
  }
  body.sidebar-open .app-sidebar {
    transform: translateX(0);
    box-shadow: 6px 0 24px rgba(21,101,192,0.10);
  }

  /* Overlay */
  .sidebar-overlay {
    position: fixed; inset: 0;
    background: rgba(10,30,70,0.15);
    backdrop-filter: blur(2px);
    z-index: 80;
    opacity: 0; pointer-events: none;
    transition: opacity .35s var(--ease);
  }
  body.sidebar-open .sidebar-overlay { opacity: 1; pointer-events: auto; }

  /* Nav items */
  .nav-item {
    display: flex; align-items: center; gap: 11px;
    padding: 9px 14px;
    margin: 0 10px 2px;
    border-radius: 10px;
    font-size: 13.5px; font-weight: 500; color: #374151;
    text-decoration: none;
    transition: background .15s, color .15s;
    white-space: nowrap;
  }
  .nav-item:hover  { background: #EFF6FF; color: #1565C0; }
  .nav-item.active { background: #E3F2FD; color: #1565C0; font-weight: 600; }
  .nav-icon { width: 18px; height: 18px; flex-shrink: 0; stroke: currentColor; fill: none; }

  /* ─── MAIN CONTENT ─── */
  .app-main {
    position: fixed;
    top: var(--header-h);
    left: 0;
    right: 0;
    bottom: 0;
    overflow-y: auto;   /* ← FIX: was "hidden", now scrollable */
    overflow-x: hidden;
  }
  body.sidebar-open .app-main {
    left: var(--sidebar-w);
    transition: left .35s var(--ease);
  }
</style>

@stack('styles')
</head>
<body>

<div class="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ════ HEADER ════ --}}
<header class="app-header">
  <div class="flex items-center gap-3">
    <div class="brand" onclick="toggleSidebar()">
      <div class="brand-icon">
        <img src="{{ asset('images/icon-clean.png') }}" alt="Dastik">
      </div>
      <div>
        <div style="font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:700;color:#111827;line-height:1.25">
          Dastik Dinas Komunikasi dan Informatika
        </div>
        <div style="font-size:11.5px;color:#6b7280">Provinsi Jawa Timur</div>
      </div>
    </div>
  </div>
  <img src="{{ asset('images/logo-kominfo.png') }}" alt="KOMINFO" style="height:46px;width:auto;object-fit:contain">
</header>

{{-- ════ SIDEBAR ════ --}}
<aside class="app-sidebar">

  <a href="{{ route('home') }}"
    class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
    <svg class="nav-icon" viewBox="0 0 24 24" stroke-width="2">
      <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
      <polyline points="9,22 9,12 15,12 15,22"/>
    </svg>
    Home
  </a>

  <a href="{{ route('economy') }}"
    class="nav-item {{ request()->routeIs('economy') ? 'active' : '' }}">
    <svg class="nav-icon" viewBox="0 0 24 24" stroke-width="2">
      <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
      <polyline points="17 6 23 6 23 12"/>
    </svg>
    Economy
  </a>

  <a href="{{ route('demographics') }}"
    class="nav-item {{ request()->routeIs('demographics') ? 'active' : '' }}">
    <svg class="nav-icon" viewBox="0 0 24 24" stroke-width="2">
      <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
      <circle cx="9" cy="7" r="4"/>
      <path d="M23 21v-2a4 4 0 00-3-3.87"/>
      <path d="M16 3.13a4 4 0 010 7.75"/>
    </svg>
    Demographics
  </a>

  <a href="{{ route('perikanan') }}"
    class="nav-item {{ request()->routeIs('perikanan') ? 'active' : '' }}">
    <svg class="nav-icon" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
      <path d="M4 10h16l-2 3H6l-2-3z"/>
      <path d="M12 4v6"/>
      <path d="M12 4l3 2M12 4l-3 2"/>
      <path d="M2 16c2-2 4-2 6 0s4 2 6 0 4-2 6 0"/>
    </svg>
    Produksi Perikanan
  </a>

  <a href="{{ route('environment') }}"
    class="nav-item {{ request()->routeIs('environment') ? 'active' : '' }}">
    <svg class="nav-icon" viewBox="0 0 24 24" stroke-width="2">
      <circle cx="12" cy="12" r="10"/>
      <path d="M12 2a14.5 14.5 0 000 20 14.5 14.5 0 000-20"/>
      <path d="M2 12h20"/>
    </svg>
    Environment
  </a>

  <a href="{{ route('education') }}"
    class="nav-item {{ request()->routeIs('education') ? 'active' : '' }}">
    <svg class="nav-icon" viewBox="0 0 24 24" stroke-width="2">
      <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
      <path d="M6 12v5c3 3 9 3 12 0v-5"/>
    </svg>
    Education
  </a>

</aside>

{{-- ════ MAIN ════ --}}
<main class="app-main">
  @yield('content')
</main>

<script>
  function toggleSidebar(){ document.body.classList.toggle('sidebar-open'); }
  function closeSidebar(){  document.body.classList.remove('sidebar-open'); }
  document.addEventListener('keydown', e => { if(e.key==='Escape') closeSidebar(); });
</script>

@stack('scripts')
</body>
</html>