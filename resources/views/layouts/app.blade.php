<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Analisis Data Publik - Provinsi Jawa Timur</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --blue-primary:#1565C0;
  --blue-accent:#2196F3;
  --blue-light:#E3F2FD;
  --sidebar-w:220px;
  --header-h:64px;
  --transition:0.35s cubic-bezier(0.4,0,0.2,1);
}
html,body{height:100%;overflow:hidden}
body{font-family:'DM Sans',sans-serif;background:#F4F6FA;color:#1a2236}

/* HEADER */
.header{
  position:fixed;top:0;left:0;right:0;height:var(--header-h);
  background:#fff;border-bottom:1px solid #e4e8f0;
  display:flex;align-items:center;justify-content:space-between;
  padding:0 20px;z-index:1000;
  box-shadow:0 1px 8px rgba(21,101,192,0.07);
}
.header-left{display:flex;align-items:center;gap:14px}
.brand{
  display:flex;align-items:center;gap:12px;
  cursor:pointer;transition:opacity 0.2s;user-select:none;
}
.brand:hover{opacity:0.8}
.brand-icon-img{
  width:40px;height:40px;border-radius:10px;
  object-fit:contain;flex-shrink:0;
  background:linear-gradient(135deg,#0f2d5e,#1565C0);padding:5px;
}
.brand-text{line-height:1.25}
.brand-name{font-family:'Plus Jakarta Sans',sans-serif;font-size:13px;font-weight:700;color:#111827}
.brand-sub{font-size:11.5px;color:#6b7280;font-weight:400}
.header-logos{display:flex;align-items:center}
.header-logo-img{height:46px;width:auto;object-fit:contain;background:transparent}

/* SIDEBAR */
.sidebar{
  position:fixed;top:var(--header-h);left:0;
  width:var(--sidebar-w);height:calc(100vh - var(--header-h));
  background:#fff;border-right:1px solid #e4e8f0;
  padding:20px 0 24px;
  transform:translateX(calc(-1 * var(--sidebar-w)));
  transition:transform var(--transition),box-shadow var(--transition);
  z-index:900;overflow-y:auto;
}
body.sidebar-open .sidebar{
  transform:translateX(0);
  box-shadow:6px 0 24px rgba(21,101,192,0.09);
}
.sidebar-overlay{
  position:fixed;inset:0;
  background:rgba(10,30,70,0.15);backdrop-filter:blur(1.5px);
  z-index:800;opacity:0;pointer-events:none;
  transition:opacity var(--transition);
}
body.sidebar-open .sidebar-overlay{opacity:1;pointer-events:auto}
.sidebar-section{padding:0 10px}
.nav-item{
  display:flex;align-items:center;gap:12px;padding:10px 14px;
  border-radius:10px;color:#374151;font-size:13.5px;font-weight:500;
  transition:all 0.2s;margin-bottom:3px;text-decoration:none;
  border:none;background:none;width:100%;text-align:left;cursor:pointer;
}
.nav-item:hover{background:#EFF6FF;color:var(--blue-primary)}
.nav-item.active{background:var(--blue-light);color:var(--blue-primary);font-weight:600}
.nav-icon{width:18px;height:18px;flex-shrink:0;stroke:currentColor}

/* MAIN */
.main-content{
  margin-top:var(--header-h);
  height:calc(100vh - var(--header-h));
  overflow:hidden;
}

/* Inner page scrollable */
.page-inner{
  height:100%;
  overflow-y:auto;
}

/* KPI CARDS */
.page-wrapper{padding:36px 36px 60px;max-width:1200px;margin:0 auto}
.page-header{margin-bottom:30px}
.breadcrumb{font-size:12px;color:#9ca3af;margin-bottom:8px;display:flex;align-items:center;gap:5px}
.breadcrumb a{color:#1565C0;text-decoration:none}
.breadcrumb a:hover{text-decoration:underline}
.page-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:27px;font-weight:800;color:#0d2a5e;letter-spacing:-0.4px}
.page-title span{color:#1565C0}
.page-subtitle{font-size:13.5px;color:#9ca3af;margin-top:5px}

.kpi-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:16px;margin-bottom:28px}
.kpi-card{
  background:#fff;border-radius:15px;padding:20px 20px 16px;
  border:1px solid #e9edf4;box-shadow:0 2px 6px rgba(21,101,192,0.05);
  position:relative;overflow:hidden;transition:box-shadow 0.2s,transform 0.2s;
}
.kpi-card::before{content:"";position:absolute;top:0;left:0;right:0;height:3px}
.kpi-card.blue::before{background:linear-gradient(90deg,#1565C0,#42A5F5)}
.kpi-card.green::before{background:linear-gradient(90deg,#2E7D32,#66BB6A)}
.kpi-card.orange::before{background:linear-gradient(90deg,#E65100,#FFA726)}
.kpi-card.purple::before{background:linear-gradient(90deg,#6A1B9A,#AB47BC)}
.kpi-card:hover{box-shadow:0 8px 22px rgba(21,101,192,0.11);transform:translateY(-2px)}
.kpi-label{font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:9px}
.kpi-value{font-family:'Plus Jakarta Sans',sans-serif;font-size:24px;font-weight:800;color:#0d2a5e;line-height:1;margin-bottom:7px}
.kpi-change{font-size:11.5px;font-weight:600;display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px}
.kpi-change.up{background:#e8f5e9;color:#2E7D32}
.kpi-change.down{background:#fdecea;color:#c62828}

.charts-grid{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:20px}
.chart-card{background:#fff;border-radius:15px;padding:22px;border:1px solid #e9edf4;box-shadow:0 2px 6px rgba(21,101,192,0.05)}
.card-title{font-family:'Plus Jakarta Sans',sans-serif;font-size:14.5px;font-weight:700;color:#0d2a5e;margin-bottom:3px}
.card-subtitle{font-size:11.5px;color:#9ca3af;margin-bottom:18px}
.bar-chart{width:100%}
.pie-visual{display:flex;flex-direction:column;gap:11px;margin-top:6px}
.pie-item{display:flex;align-items:center;gap:9px;font-size:12.5px}
.pie-dot{width:9px;height:9px;border-radius:50%;flex-shrink:0}
.pie-bar-wrap{flex:1;height:5px;background:#f3f4f8;border-radius:3px;overflow:hidden}
.pie-bar-fill{height:100%;border-radius:3px}
.pie-pct{font-weight:700;color:#0d2a5e;font-size:12.5px;min-width:32px;text-align:right}

.info-placeholder{
  background:#fff;border-radius:15px;padding:56px 28px;
  border:1px solid #e9edf4;text-align:center;color:#9ca3af;
}
.info-placeholder svg{width:56px;height:56px;margin:0 auto 14px;display:block;opacity:0.25}
.info-placeholder p{font-size:14px}

@media(max-width:800px){
  .charts-grid{grid-template-columns:1fr}
  .page-wrapper{padding:20px 16px 40px}
  .brand-name{font-size:11px}
}

@stack('styles')
</style>
</head>
<body>

<div class="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- HEADER -->
<header class="header">
  <div class="header-left">
    <div class="brand" onclick="toggleSidebar()">
      <img class="brand-icon-img" src="{{ asset('images/icon-clean.png') }}" alt="Dastik Icon">
      <div class="brand-text">
        <div class="brand-name">Dastik Dinas Komunikasi dan Informatika</div>
        <div class="brand-sub">Provinsi Jawa Timur</div>
      </div>
    </div>
  </div>
  <div class="header-logos">
    <img class="header-logo-img" src="{{ asset('images/logo-kominfo.png') }}" alt="KOMINFO JATIM">
  </div>
</header>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-section">
    <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        <polyline points="9,22 9,12 15,12 15,22"/>
      </svg>Home
    </a>
    <a href="{{ route('economy') }}" class="nav-item {{ request()->routeIs('economy') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
        <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
        <polyline points="17 6 23 6 23 12"/>
      </svg>Economy
    </a>
    <a href="{{ route('demographics') }}" class="nav-item {{ request()->routeIs('demographics') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
        <path d="M16 3.13a4 4 0 010 7.75"/>
      </svg>Demographics
    </a>
    <a href="{{ route('health') }}" class="nav-item {{ request()->routeIs('health') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
      </svg>Health
    </a>
    <a href="{{ route('environment') }}" class="nav-item {{ request()->routeIs('environment') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <path d="M12 2a14.5 14.5 0 000 20 14.5 14.5 0 000-20"/>
        <path d="M2 12h20"/>
      </svg>Environment
    </a>
    <a href="{{ route('education') }}" class="nav-item {{ request()->routeIs('education') ? 'active' : '' }}">
      <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke-width="2">
        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
      </svg>Education
    </a>
  </div>
</aside>

<!-- MAIN CONTENT -->
<main class="main-content">
  @yield('content')
</main>

<script>
function toggleSidebar(){document.body.classList.toggle('sidebar-open')}
function closeSidebar(){document.body.classList.remove('sidebar-open')}
document.addEventListener('keydown', e => { if(e.key === 'Escape') closeSidebar() })
</script>

@stack('scripts')
</body>
</html>
