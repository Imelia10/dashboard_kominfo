{{-- ── DATA DETAIL PER WILAYAH TABLE ─────────────────────────── --}}
{{-- Taruh ini langsung di dalam @section('content'), setelah gap table --}}

<style>
/* ─── FILTER & SEARCH BAR ─────────────────────────────────── */
.table-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 14px;
}
.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f5f7fb;
  border: 1.5px solid #e4e8f0;
  border-radius: 10px;
  padding: 7px 14px;
  font-size: 13px;
  color: #374151;
  transition: border-color .2s;
  min-width: 220px;
}
.search-box:focus-within {
  border-color: #1565C0;
  background: #fff;
}
.search-box input {
  border: none;
  outline: none;
  background: transparent;
  font-size: 13px;
  color: #374151;
  width: 100%;
  font-family: inherit;
}
.search-box input::placeholder { color: #b0bac9; }

.toolbar-right {
  display: flex;
  align-items: center;
  gap: 8px;
}
.btn-export {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #1565C0;
  color: #fff;
  border: none;
  border-radius: 9px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: background .2s, transform .1s;
  font-family: inherit;
}
.btn-export:hover { background: #0d47a1; transform: translateY(-1px); }
.btn-export:active { transform: translateY(0); }

.rows-select {
  display: flex;
  align-items: center;
  gap: 6px;
  background: #f5f7fb;
  border: 1.5px solid #e4e8f0;
  border-radius: 9px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
}
.rows-select select {
  border: none;
  outline: none;
  background: transparent;
  font-size: 12px;
  font-weight: 700;
  color: #1565C0;
  cursor: pointer;
  font-family: inherit;
}

/* ─── TABLE ───────────────────────────────────────────────── */
.detail-table-wrap { overflow-x: auto; }
.detail-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
}
.detail-table th {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: .6px;
  text-transform: uppercase;
  color: #9aacca;
  padding: 10px 14px;
  text-align: left;
  border-bottom: 2px solid #e4eaf5;
  white-space: nowrap;
  cursor: pointer;
  user-select: none;
  transition: color .15s;
}
.detail-table th:hover { color: #1565C0; }
.detail-table th .sort-icon {
  display: inline-block;
  margin-left: 4px;
  opacity: .35;
  font-size: 9px;
  transition: opacity .15s, transform .15s;
}
.detail-table th.sorted .sort-icon { opacity: 1; color: #1565C0; }
.detail-table th.sorted-desc .sort-icon { transform: rotate(180deg); }

.detail-table td {
  padding: 10px 14px;
  color: #374151;
  border-bottom: 1px solid #f0f4fa;
  font-weight: 500;
  white-space: nowrap;
}
.detail-table tbody tr:hover td { background: #f7f9fd; }
.detail-table tbody tr:last-child td { border-bottom: none; }

.td-nama {
  font-weight: 700;
  color: #0f1f3c !important;
  min-width: 160px;
}
.td-rank {
  font-size: 11px;
  font-weight: 800;
  color: #9aacca;
  text-align: center;
  width: 36px;
}

.td-kpm-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 130px;
}
.td-kpm-progress {
  flex: 1;
  height: 6px;
  background: #e4eaf5;
  border-radius: 3px;
  overflow: hidden;
  min-width: 60px;
}
.td-kpm-progress-fill {
  height: 100%;
  border-radius: 3px;
  background: linear-gradient(90deg, #42a5f5, #1565C0);
  transition: width .6s ease;
}
.td-kpm-pct {
  font-size: 11.5px;
  font-weight: 800;
  min-width: 38px;
  text-align: right;
}

.badge-ang {
  display: inline-flex;
  align-items: center;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 20px;
  white-space: nowrap;
}
.badge-ang.optimal  { background: #e3f2fd; color: #1565C0; }
.badge-ang.baik     { background: #e8f5e9; color: #2e7d32; }
.badge-ang.cukup    { background: #fff3e0; color: #e65100; }
.badge-ang.rendah   { background: #ffebee; color: #c62828; }

.td-gap-neg { color: #c62828 !important; font-weight: 700 !important; }
.td-gap-pos { color: #2e7d32 !important; font-weight: 700 !important; }

.row-kritis td  { background: #fff9f9; }
.row-waspada td { background: #fffcf6; }
.row-stabil td  { background: #f9fff9; }
.row-kritis:hover td  { background: #fff0f0 !important; }
.row-waspada:hover td { background: #fffbee !important; }
.row-stabil:hover td  { background: #f0fff0 !important; }

.status-dot {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  font-weight: 700;
}
.status-dot::before {
  content: '';
  width: 7px; height: 7px;
  border-radius: 50%;
  flex-shrink: 0;
}
.status-dot.stabil::before  { background: #43a047; }
.status-dot.waspada::before { background: #fb8c00; }
.status-dot.kritis::before  { background: #ef5350; }
.status-dot.stabil  { color: #2e7d32; }
.status-dot.waspada { color: #e65100; }
.status-dot.kritis  { color: #c62828; }

/* ─── PAGINATION ──────────────────────────────────────────── */
.pagination-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 16px;
  padding-top: 14px;
  border-top: 1px solid #f0f4fa;
}
.pagination-info { font-size: 12px; color: #9aacca; font-weight: 500; }
.pagination-info b { color: #374151; }
.pagination-btns { display: flex; gap: 4px; }
.pg-btn {
  min-width: 30px; height: 30px; padding: 0 8px;
  border-radius: 7px; border: 1.5px solid #e4e8f0;
  background: #fff; font-size: 12px; font-weight: 700;
  color: #6b7280; cursor: pointer;
  transition: all .15s;
  display: flex; align-items: center; justify-content: center;
  font-family: inherit;
}
.pg-btn:hover:not(:disabled) { border-color: #1565C0; color: #1565C0; background: #e3f2fd; }
.pg-btn.active { background: #1565C0; border-color: #1565C0; color: #fff; }
.pg-btn:disabled { opacity: .35; cursor: not-allowed; }

.table-empty {
  text-align: center; padding: 40px 20px;
  color: #b0bac9; font-size: 13px;
}
.table-empty svg { margin: 0 auto 10px; display: block; opacity: .3; }
</style>

<div class="chart-card" id="detail-table-section">
  <div class="flex items-start justify-between flex-wrap gap-3 mb-1">
    <div>
      <div class="section-title">Data Detail Per Wilayah</div>
      <div class="section-subtitle">Rencana &amp; Realisasi KPM + Anggaran seluruh Kabupaten/Kota Jawa Timur</div>
    </div>
  </div>

  <div class="table-toolbar">
    <div class="search-box">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#b0bac9" stroke-width="2.5">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <input type="text" id="searchWilayah" placeholder="Cari kabupaten/kota...">
    </div>
    <div class="toolbar-right">
      <div class="rows-select">
        Tampilkan
        <select id="rowsPerPage">
          <option value="6">6</option>
          <option value="10" selected>10</option>
          <option value="20">20</option>
          <option value="999">Semua</option>
        </select>
        baris
      </div>
      <button class="btn-export" id="btnExportCsv">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
          <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        Export CSV
      </button>
    </div>
  </div>

  <div class="detail-table-wrap">
    <table class="detail-table" id="detailTable">
      <thead>
        <tr>
          <th class="th-rank">#</th>
          <th data-col="nama">Kabupaten / Kota <span class="sort-icon">▲</span></th>
          <th data-col="rencana_kpm">Rencana KPM <span class="sort-icon">▲</span></th>
          <th data-col="realisasi_kpm">Realisasi KPM <span class="sort-icon">▲</span></th>
          <th data-col="gap_kpm">Gap KPM <span class="sort-icon">▲</span></th>
          <th data-col="pct_kpm">% Real. KPM <span class="sort-icon">▲</span></th>
          <th data-col="rencana_ang">Rencana Anggaran <span class="sort-icon">▲</span></th>
          <th data-col="realisasi_ang">Realisasi Anggaran <span class="sort-icon">▲</span></th>
          <th data-col="gap_ang">Gap Anggaran <span class="sort-icon">▲</span></th>
          <th data-col="pct_ang">% Serapan <span class="sort-icon">▲</span></th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody id="detailTableBody">
        @foreach($kabAnalytics as $item)
          @php
            $gapKpm = $item['rencana_kpm'] - $item['realisasi_kpm'];
            $gapAng = $item['rencana_ang'] - $item['realisasi_ang'];

            if ($item['pct_kpm'] >= 95)     $rowClass = 'row-stabil';
            elseif ($item['pct_kpm'] >= 70) $rowClass = 'row-waspada';
            else                             $rowClass = 'row-kritis';

            if ($item['pct_kpm'] >= 95)     $statusLabel = 'stabil';
            elseif ($item['pct_kpm'] >= 70) $statusLabel = 'waspada';
            else                             $statusLabel = 'kritis';

            if ($item['pct_ang'] >= 95)     $angBadge = 'optimal';
            elseif ($item['pct_ang'] >= 85) $angBadge = 'baik';
            elseif ($item['pct_ang'] >= 70) $angBadge = 'cukup';
            else                             $angBadge = 'rendah';

            if ($item['pct_ang'] >= 95)     $angLabel = 'Optimal';
            elseif ($item['pct_ang'] >= 85) $angLabel = 'Baik';
            elseif ($item['pct_ang'] >= 70) $angLabel = 'Cukup';
            else                             $angLabel = 'Rendah';

            $pctKpmBar = min($item['pct_kpm'], 100);
          @endphp
          <tr class="{{ $rowClass }} detail-row"
              data-nama="{{ strtolower($item['nama']) }}"
              data-rencana_kpm="{{ $item['rencana_kpm'] }}"
              data-realisasi_kpm="{{ $item['realisasi_kpm'] }}"
              data-gap_kpm="{{ $gapKpm }}"
              data-pct_kpm="{{ $item['pct_kpm'] }}"
              data-rencana_ang="{{ $item['rencana_ang'] }}"
              data-realisasi_ang="{{ $item['realisasi_ang'] }}"
              data-gap_ang="{{ $gapAng }}"
              data-pct_ang="{{ $item['pct_ang'] }}">
            <td class="td-rank">–</td>
            <td class="td-nama">{{ $item['nama'] }}</td>
            <td>{{ number_format($item['rencana_kpm']) }}</td>
            <td>{{ number_format($item['realisasi_kpm']) }}</td>
            <td class="{{ $gapKpm > 0 ? 'td-gap-neg' : 'td-gap-pos' }}">
              {{ $gapKpm > 0 ? '–'.number_format($gapKpm) : '+'.number_format(abs($gapKpm)) }}
            </td>
            <td>
              <div class="td-kpm-bar">
                <div class="td-kpm-progress">
                  <div class="td-kpm-progress-fill" style="width:{{ $pctKpmBar }}%"></div>
                </div>
                <span class="td-kpm-pct" style="color:{{ $item['pct_kpm']>=95?'#1565C0':($item['pct_kpm']>=70?'#e65100':'#c62828') }}">
                  {{ $item['pct_kpm'] }}%
                </span>
              </div>
            </td>
            <td>Rp {{ number_format($item['rencana_ang']) }}</td>
            <td>Rp {{ number_format($item['realisasi_ang']) }}</td>
            <td class="{{ $gapAng > 0 ? 'td-gap-neg' : 'td-gap-pos' }}">
              {{ $gapAng > 0 ? '–Rp '.number_format($gapAng) : '+Rp '.number_format(abs($gapAng)) }}
            </td>
            <td>
              <span class="badge-ang {{ $angBadge }}">{{ $item['pct_ang'] }}% · {{ $angLabel }}</span>
            </td>
            <td>
              <span class="status-dot {{ $statusLabel }}">{{ ucfirst($statusLabel) }}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="table-empty" id="tableEmpty" style="display:none">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      Tidak ada wilayah yang cocok dengan pencarian.
    </div>
  </div>

  <div class="pagination-wrap" id="paginationWrap">
    <div class="pagination-info" id="paginationInfo"></div>
    <div class="pagination-btns" id="paginationBtns"></div>
  </div>
</div>

<script>
(function () {
  const allRows   = Array.from(document.querySelectorAll('#detailTableBody .detail-row'));
  const tbody     = document.getElementById('detailTableBody');
  const searchEl  = document.getElementById('searchWilayah');
  const rowsEl    = document.getElementById('rowsPerPage');
  const exportBtn = document.getElementById('btnExportCsv');
  const emptyEl   = document.getElementById('tableEmpty');
  const pgInfo    = document.getElementById('paginationInfo');
  const pgBtns    = document.getElementById('paginationBtns');

  let currentPage = 1;
  let sortCol     = 'rencana_kpm';
  let sortAsc     = false;
  let searchVal   = '';
  let rowsPerPage = 10;

  function getVal(row, col) {
    const v = row.dataset[col];
    if (col === 'nama') return v || '';
    return parseFloat(v) || 0;
  }

  function filtered() {
    if (!searchVal) return allRows;
    return allRows.filter(r => r.dataset.nama.includes(searchVal));
  }

  function sorted(rows) {
    return [...rows].sort((a, b) => {
      const va = getVal(a, sortCol), vb = getVal(b, sortCol);
      if (typeof va === 'string') return sortAsc ? va.localeCompare(vb) : vb.localeCompare(va);
      return sortAsc ? va - vb : vb - va;
    });
  }

  function render() {
    const rows    = sorted(filtered());
    const total   = rows.length;
    const perPage = rowsPerPage >= 999 ? total : rowsPerPage;
    const pages   = Math.max(1, Math.ceil(total / perPage));
    if (currentPage > pages) currentPage = pages;

    const start = (currentPage - 1) * perPage;
    const end   = Math.min(start + perPage, total);

    allRows.forEach(r => { r.style.display = 'none'; r.cells[0].textContent = '–'; });
    emptyEl.style.display = total === 0 ? 'block' : 'none';

    rows.slice(start, end).forEach((r, i) => {
      r.style.display = '';
      r.cells[0].textContent = start + i + 1;
      tbody.appendChild(r);
    });

    pgInfo.innerHTML = total === 0
      ? 'Tidak ada hasil'
      : `Menampilkan <b>${start + 1}–${end}</b> dari <b>${total}</b> wilayah`;

    pgBtns.innerHTML = '';
    const mkBtn = (label, page, disabled, active) => {
      const b = document.createElement('button');
      b.className = 'pg-btn' + (active ? ' active' : '');
      b.disabled  = disabled;
      b.innerHTML = label;
      b.addEventListener('click', () => { currentPage = page; render(); });
      return b;
    };

    pgBtns.appendChild(mkBtn('‹', currentPage - 1, currentPage <= 1, false));
    const range = [];
    for (let p = 1; p <= pages; p++) {
      if (p === 1 || p === pages || Math.abs(p - currentPage) <= 1) range.push(p);
      else if (range[range.length - 1] !== '…') range.push('…');
    }
    range.forEach(p => {
      if (p === '…') {
        const s = document.createElement('button');
        s.className = 'pg-btn'; s.disabled = true; s.textContent = '…';
        pgBtns.appendChild(s);
      } else {
        pgBtns.appendChild(mkBtn(p, p, false, p === currentPage));
      }
    });
    pgBtns.appendChild(mkBtn('›', currentPage + 1, currentPage >= pages, false));
  }

  document.querySelectorAll('#detailTable thead th[data-col]').forEach(th => {
    th.addEventListener('click', () => {
      const col = th.dataset.col;
      if (sortCol === col) { sortAsc = !sortAsc; }
      else { sortCol = col; sortAsc = col === 'nama'; }
      currentPage = 1;
      document.querySelectorAll('#detailTable thead th').forEach(t => t.classList.remove('sorted','sorted-desc'));
      th.classList.add('sorted');
      if (!sortAsc) th.classList.add('sorted-desc');
      render();
    });
  });

  searchEl.addEventListener('input', () => {
    searchVal   = searchEl.value.toLowerCase().trim();
    currentPage = 1;
    render();
  });

  rowsEl.addEventListener('change', () => {
    rowsPerPage = parseInt(rowsEl.value);
    currentPage = 1;
    render();
  });

  exportBtn.addEventListener('click', () => {
    const headers = ['No','Kabupaten/Kota','Rencana KPM','Realisasi KPM','Gap KPM',
                     '% Real KPM','Rencana Anggaran','Realisasi Anggaran','Gap Anggaran','% Serapan'];
    const rows = sorted(filtered()).map((r, i) => {
      const d = r.dataset;
      const gk = parseInt(d.rencana_kpm) - parseInt(d.realisasi_kpm);
      const ga = parseFloat(d.rencana_ang) - parseFloat(d.realisasi_ang);
      return [i+1, r.querySelector('.td-nama').textContent.trim(),
              d.rencana_kpm, d.realisasi_kpm, gk, d.pct_kpm+'%',
              d.rencana_ang, d.realisasi_ang, ga, d.pct_ang+'%'].join(',');
    });
    const csv  = [headers.join(','), ...rows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = `bansos_jatim_{{ $selectedYear }}.csv`; a.click();
    URL.revokeObjectURL(url);
  });

  // Animate progress bars
  document.querySelectorAll('.td-kpm-progress-fill').forEach(el => {
    const w = el.style.width;
    el.style.width = '0';
    setTimeout(() => { el.style.width = w; }, 80);
  });

  render();
})();
</script>