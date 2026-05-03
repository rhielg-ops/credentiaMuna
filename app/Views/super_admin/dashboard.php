<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>

<!-- ── Chart-card styles ── -->
<style>
  @keyframes chartRise {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .chart-card-styled {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e5ede8;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 6px 20px rgba(0,0,0,0.05);
    padding: 24px 26px 22px;
    animation: chartRise 0.4s ease both;
    transition: box-shadow 0.2s ease;
  }
  .chart-card-styled:hover {
    box-shadow: 0 4px 24px rgba(21,128,61,0.11);
  }

  .chart-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 4px;
  }
  .chart-card-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 3px;
    letter-spacing: -0.1px;
  }
  .chart-card-sub {
    font-size: 12px;
    color: #9ca3af;
    margin: 0;
  }

  .chart-export-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    font-weight: 600;
    color: #15803d;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    padding: 5px 14px;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.15s, border-color 0.15s;
    line-height: 1.4;
  }
  .chart-export-btn:hover {
    background: #dcfce7;
    border-color: #86efac;
  }
  .chart-export-btn svg {
    width: 12px; height: 12px;
    stroke: #15803d; flex-shrink: 0;
  }

  .chart-divider {
    height: 1px;
    background: linear-gradient(to right, #e5ede8, transparent);
    margin: 14px 0 18px;
  }

  .chart-card-styled:nth-child(2) { animation-delay: 0.08s; }
  .chart-card-full-styled         { animation-delay: 0.16s; }

  .chart-card-full-styled {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e5ede8;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 6px 20px rgba(0,0,0,0.05);
    padding: 24px 26px 22px;
    margin-bottom: 8px;
    animation: chartRise 0.4s ease both;
    transition: box-shadow 0.2s ease;
  }
  .chart-card-full-styled:hover {
    box-shadow: 0 4px 24px rgba(21,128,61,0.11);
  }

  /* ── Export Modal ── */
  @keyframes modalBackdropIn {
    from { opacity: 0; }
    to   { opacity: 1; }
  }
  @keyframes modalPanelIn {
    from { opacity: 0; transform: translateY(12px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }

  .export-modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.38);
    backdrop-filter: blur(3px);
    z-index: 9998;
    align-items: center;
    justify-content: center;
    animation: modalBackdropIn 0.2s ease both;
  }
  .export-modal-backdrop.active {
    display: flex;
  }

  .export-modal {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.18), 0 4px 16px rgba(0,0,0,0.08);
    padding: 28px 28px 24px;
    width: 340px;
    max-width: calc(100vw - 32px);
    position: relative;
    animation: modalPanelIn 0.25s ease both;
    z-index: 9999;
  }

  .export-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
  }
  .export-modal-title {
    font-size: 15.5px;
    font-weight: 700;
    color: #111827;
    margin: 0;
  }
  .export-modal-close {
    width: 30px; height: 30px;
    border-radius: 8px;
    border: none;
    background: #f3f4f6;
    color: #6b7280;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background 0.15s, color 0.15s;
    flex-shrink: 0;
  }
  .export-modal-close:hover { background: #fee2e2; color: #dc2626; }
  .export-modal-close svg { width: 14px; height: 14px; stroke: currentColor; }

  .export-modal-subtitle {
    font-size: 12px;
    color: #9ca3af;
    margin: 0 0 20px;
  }
  .export-modal-subtitle span {
    font-weight: 600;
    color: #374151;
  }

  .export-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .export-option-btn {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1.5px solid #e5ede8;
    background: #fafafa;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, transform 0.12s;
    text-align: left;
    width: 100%;
  }
  .export-option-btn:hover {
    background: #f0fdf4;
    border-color: #86efac;
    transform: translateX(3px);
  }
  .export-option-btn:active { transform: translateX(1px); }

  .export-option-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }
  .export-option-icon.csv  { background: #dcfce7; }
  .export-option-icon.pdf  { background: #fee2e2; }
  .export-option-icon.png  { background: #dbeafe; }

  .export-option-label {
    font-size: 13.5px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 2px;
  }
  .export-option-desc {
    font-size: 11.5px;
    color: #9ca3af;
    margin: 0;
  }

  .export-option-arrow {
    margin-left: auto;
    color: #d1d5db;
    flex-shrink: 0;
    transition: color 0.15s;
  }
  .export-option-btn:hover .export-option-arrow { color: #15803d; }
  .export-option-arrow svg { width: 15px; height: 15px; stroke: currentColor; }
</style>

<!-- Page Header -->
<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2">Admin Dashboard</h2>
  <p class="text-green-100">System management and oversight</p>
</div>



<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  
  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Total Folders</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= number_format($stats['total_folders']) ?></p>
    <p class="text-sm text-green-200">Academic folders</p>
  </div>

  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Total Records</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= number_format($stats['total_records']) ?></p>
    <p class="text-sm text-green-200">Academic documents</p>
  </div>

  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Total Users</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= $stats['total_admins'] ?></p>
    <p class="text-sm text-green-200"><?= $stats['active_admins'] ?> active</p>
  </div>

  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Pending Staff</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= $stats['pending_staff'] ?></p>
    <p class="text-sm text-green-200">awaiting approval</p>
  </div>

</div>

<!-- ── Charts Section ── -->
<?php
// Guard against null values
$total_files         = $total_files         ?? 0;
$total_folders       = $total_folders       ?? 0;
$file_types          = $file_types          ?? [];
$folder_distribution = $folder_distribution ?? [];
$monthly_data        = $monthly_data        ?? array_fill(0, 12, 0);

// Group file types for chart
$pdf_count   = $file_types['pdf']  ?? 0;
$docx_count  = ($file_types['docx'] ?? 0) + ($file_types['doc'] ?? 0);
$image_count = ($file_types['png'] ?? 0) + ($file_types['jpg'] ?? 0) + ($file_types['jpeg'] ?? 0);
$other_count = $total_files - ($pdf_count + $docx_count + $image_count);

//folder_distribution already contains plain integer file counts (direct files only,
// valid extensions only) as computed by Dashboard::countAccessibleItems().
// Sort descending and take the top 5.
$filtered_folder_distribution = array_map('intval', $folder_distribution);
arsort($filtered_folder_distribution);
$top_folders   = array_slice($filtered_folder_distribution, 0, 5, true);

$folder_labels = array_keys($top_folders);
$folder_values = array_values($top_folders);
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

  <!-- Records by Type -->
  <div class="chart-card-styled">
    <div class="chart-card-header">
      <div>
        <p class="chart-card-title">Records by Type</p>
        <p class="chart-card-sub">Breakdown of all accessible records</p>
      </div>
      <button class="chart-export-btn" onclick="openExportModal('fileTypeChart', 'Records by Type')">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export
      </button>
    </div>
    <div class="chart-divider"></div>
    <canvas id="fileTypeChart" class="w-full" style="max-height: 300px;"></canvas>
  </div>

  <!-- Folder Distribution -->
  <div class="chart-card-styled">
    <div class="chart-card-header">
      <div>
        <p class="chart-card-title">Folder Distribution</p>
        <p class="chart-card-sub">Top 5 folders by folder count</p>
      </div>
      <button class="chart-export-btn" onclick="openExportModal('folderDistChart', 'Folder Distribution')">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Export
      </button>
    </div>
    <div class="chart-divider"></div>
    <canvas id="folderDistChart" class="w-full" style="max-height: 300px;"></canvas>
  </div>

</div>

<!-- Records Added Over Time — full width -->
<div class="chart-card-full-styled mb-8">
  <div class="chart-card-header">
    <div>
      <p class="chart-card-title">Records Added Over Time</p>
      <p class="chart-card-sub">Monthly uploads (bars) with cumulative total (line)</p>
    </div>
    <button class="chart-export-btn" onclick="openExportModal('timelineChart', 'Records Added Over Time')">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
      </svg>
      Export
    </button>
  </div>
  <div class="chart-divider"></div>
  <canvas id="timelineChart" class="w-full" style="max-height: 350px;"></canvas>
</div>

<!-- ── Export Modal ── -->
<div class="export-modal-backdrop" id="exportModalBackdrop">
  <div class="export-modal" role="dialog" aria-modal="true" aria-labelledby="exportModalTitle">

    <div class="export-modal-header">
      <p class="export-modal-title" id="exportModalTitle">Export Chart</p>
      <button class="export-modal-close" onclick="closeExportModal()" aria-label="Close">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <p class="export-modal-subtitle">Chart: <span id="exportModalChartName">—</span></p>

    <div class="export-options">

      <!-- CSV -->
      <button class="export-option-btn" onclick="doExport('csv')">
        <div class="export-option-icon csv">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/><path d="M7 13l2 4m0 0l2-4m-2 4v0"/>
          </svg>
        </div>
        <div>
          <p class="export-option-label">CSV Spreadsheet</p>
          <p class="export-option-desc">Download chart data as .csv</p>
        </div>
        <div class="export-option-arrow">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
      </button>

      <!-- PDF -->
      <button class="export-option-btn" onclick="doExport('pdf')">
        <div class="export-option-icon pdf">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="12" y2="17"/>
          </svg>
        </div>
        <div>
          <p class="export-option-label">PDF Document</p>
          <p class="export-option-desc">Download chart as a .pdf file</p>
        </div>
        <div class="export-option-arrow">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
      </button>

      <!-- PNG -->
      <button class="export-option-btn" onclick="doExport('png')">
        <div class="export-option-icon png">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
          </svg>
        </div>
        <div>
          <p class="export-option-label">PNG Image</p>
          <p class="export-option-desc">Download chart as a .png image</p>
        </div>
        <div class="export-option-arrow">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>
      </button>

    </div>
  </div>
</div>

<!-- Recent System Activity -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
  <div class="flex items-center gap-2 mb-6">
    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
    </svg>
    <h3 class="text-xl font-bold text-gray-800">Recent System Activity</h3>
  </div>

  <div class="space-y-4">
    <?php foreach ($recent_activity as $activity): ?>
    <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
      <div class="w-12 h-12 bg-<?= $activity['icon_bg'] ?>-100 rounded-lg flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-<?= $activity['icon_bg'] ?>-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
        </svg>
      </div>
      <div class="flex-1">
        <p class="font-semibold text-gray-800"><?= esc($activity['title']) ?></p>
        <p class="text-sm text-gray-500">by <?= esc($activity['user']) ?></p>
      </div>
      <div class="text-right text-sm text-gray-500">
        <p><?= esc($activity['time']) ?></p>
        <p><?= esc($activity['timestamp']) ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const colors = {
  primary:    '#15803d',
  secondary:  '#16a34a',
  tertiary:   '#22c55e',
  quaternary: '#86efac',
  light:      '#dcfce7',
  border:     '#bbf7d0'
};

const tip = {
  backgroundColor: '#1f2937',
  titleColor: '#f9fafb',
  bodyColor: '#d1fae5',
  borderColor: '#374151',
  borderWidth: 1,
  padding: 12,
  cornerRadius: 10,
  displayColors: true,
  boxPadding: 5
};

// ─── 1. Records by Type ────────────────────────────────────────────────────────
const fileTypeCtx = document.getElementById('fileTypeChart').getContext('2d');
const fileTypeChart = new Chart(fileTypeCtx, {
  type: 'bar',
  data: {
    labels: ['PDF', 'Word Documents', 'Images', 'Others'],
    datasets: [{
      label: 'Number of Folders',
      data: [<?= $pdf_count ?>, <?= $docx_count ?>, <?= $image_count ?>, <?= $other_count ?>],
      backgroundColor: [
        'rgba(21,128,61,0.85)',
        'rgba(22,163,74,0.70)',
        'rgba(34,197,94,0.58)',
        'rgba(134,239,172,0.70)'
      ],
      borderColor: [colors.primary, colors.secondary, colors.tertiary, colors.quaternary],
      borderWidth: 1.5,
      borderRadius: 8,
      borderSkipped: false
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        ...tip,
        callbacks: {
          label: function(ctx) {
            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
            const pct   = total > 0 ? ((ctx.parsed.x / total) * 100).toFixed(1) : 0;
            return '  ' + ctx.parsed.x + ' records  ·  ' + pct + '%';
          }
        }
      }
    },
    scales: {
      x: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
          callback: function(value) { return Number.isInteger(value) ? value : null; },
          color: '#9ca3af'
        },
        grid: { color: '#f3f4f6' },
        border: { dash: [4, 4] }
      },
      y: {
        grid: { display: false },
        ticks: { font: { weight: '600', size: 12 }, color: '#374151' }
      }
    }
  }
});

// ─── 2. Folder Distribution ──────────────────────────────────────────────────
const folderDistCtx = document.getElementById('folderDistChart').getContext('2d');
const folderDistChart = new Chart(folderDistCtx, {
  type: 'bar',
  data: {
    labels: [<?= !empty($folder_labels) ? "'" . implode("','", array_map('addslashes', $folder_labels)) . "'" : "'No Data'" ?>],
    datasets: [{
      label: 'Number of Files',
      data: [<?= !empty($folder_values) ? implode(',', $folder_values) : '0' ?>],
      backgroundColor: 'rgba(22,163,74,0.15)',
      borderColor: colors.secondary,
      borderWidth: 2,
      borderRadius: 8,
      borderSkipped: false
    }]

  },
  options: {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        ...tip,
        callbacks: {
          label: function(ctx) { return '  ' + ctx.parsed.x + ' folders'; }
        }
      }
    },
    scales: {
      x: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
          callback: function(value) { return Number.isInteger(value) ? value : null; },
          color: '#9ca3af'
        },
        grid: { color: '#f3f4f6' },
        border: { dash: [4, 4] }
      },
      y: {
        grid: { display: false },
        ticks: { font: { weight: '600', size: 12 }, color: '#374151' }
      }
    }
  }
});

// ─── 3. Records Added Over Time ────────────────────────────────────────────────
const monthlyRaw = [<?= implode(',', $monthly_data) ?>];

const cumulativeData = monthlyRaw.reduce((acc, val) => {
  acc.push((acc[acc.length - 1] || 0) + val);
  return acc;
}, []);

const timelineCtx = document.getElementById('timelineChart').getContext('2d');
const timelineChart = new Chart(timelineCtx, {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
             'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [
      {
        type: 'bar',
        label: 'Records Added',
        data: monthlyRaw,
        backgroundColor: 'rgba(22,163,74,0.18)',
        borderColor: colors.secondary,
        borderWidth: 2,
        borderRadius: 7,
        borderSkipped: false,
        yAxisID: 'yMonthly',
        order: 2
      },
      {
        type: 'line',
        label: 'Cumulative Total',
        data: cumulativeData,
        borderColor: colors.primary,
        backgroundColor: 'rgba(21,128,61,0.07)',
        fill: true,
        borderWidth: 2.5,
        pointBackgroundColor: '#fff',
        pointBorderColor: colors.primary,
        pointBorderWidth: 2.5,
        pointRadius: 5,
        pointHoverRadius: 7,
        tension: 0.4,
        yAxisID: 'yCumulative',
        order: 1
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: {
        display: true,
        position: 'top',
        align: 'end',
        labels: {
          usePointStyle: true,
          pointStyleWidth: 10,
          padding: 20,
          font: { size: 12, weight: '500' },
          color: '#374151'
        }
      },
      tooltip: {
        ...tip,
        callbacks: {
          label: function(ctx) {
            return ctx.dataset.label === 'Records Added'
              ? '  Added this month: ' + ctx.parsed.y
              : '  Cumulative total: ' + ctx.parsed.y;
          }
        }
      }
    },
    scales: {
      yMonthly: {
        type: 'linear',
        position: 'left',
        beginAtZero: true,
        title: {
          display: true,
          text: 'Monthly Uploads',
          color: colors.secondary,
          font: { size: 11, weight: '600' }
        },
        ticks: {
          stepSize: 1,
          color: '#9ca3af',
          callback: function(value) { return Number.isInteger(value) ? value : null; }
        },
        grid: { color: '#f3f4f6' },
        border: { dash: [4, 4] }
      },
      yCumulative: {
        type: 'linear',
        position: 'right',
        beginAtZero: true,
        title: {
          display: true,
          text: 'Cumulative Total',
          color: colors.primary,
          font: { size: 11, weight: '600' }
        },
        ticks: {
          stepSize: 1,
          color: '#9ca3af',
          callback: function(value) { return Number.isInteger(value) ? value : null; }
        },
        grid: { drawOnChartArea: false }
      },
      x: {
        grid: { display: false },
        ticks: { font: { weight: '500' }, color: '#374151' }
      }
    }
  }
});

// ── Export Modal Logic ────────────────────────────────────────────────────────
let _exportChartId   = null;
let _exportChartName = '';

function openExportModal(chartId, chartName) {
  _exportChartId   = chartId;
  _exportChartName = chartName;
  document.getElementById('exportModalChartName').textContent = chartName;
  document.getElementById('exportModalBackdrop').classList.add('active');
}

function closeExportModal() {
  document.getElementById('exportModalBackdrop').classList.remove('active');
}

document.getElementById('exportModalBackdrop').addEventListener('click', function(e) {
  if (e.target === this) closeExportModal();
});

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeExportModal();
});

function doExport(format) {
  const chart = Chart.getChart(_exportChartId);
  const ts    = new Date().getTime();

  if (!chart) { closeExportModal(); return; }

  if (format === 'png') {
    // ── PNG ──────────────────────────────────────────────────────────────────
    const link    = document.createElement('a');
    link.download = 'records_' + ts + '.png';
    link.href     = chart.toBase64Image('image/png', 1.0);
    link.click();
    closeExportModal();

  } else if (format === 'pdf') {
    // ── PDF (via jsPDF) ──────────────────────────────────────────────────────
    const script  = document.createElement('script');
    script.src    = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
    script.onload = function() {
      const { jsPDF } = window.jspdf;
      const imgData   = chart.toBase64Image('image/png', 1.0);
      const pdf       = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
      const pageW     = pdf.internal.pageSize.getWidth();
      const pageH     = pdf.internal.pageSize.getHeight();
      const margin    = 40;
      const imgW      = pageW - margin * 2;
      const imgH      = imgW * (chart.height / chart.width);

      pdf.setFillColor(21, 128, 61);
      pdf.rect(0, 0, pageW, 36, 'F');
      pdf.setTextColor(255, 255, 255);
      pdf.setFontSize(13);
      pdf.setFont('helvetica', 'bold');
      pdf.text(_exportChartName, margin, 24);

      pdf.setFontSize(9);
      pdf.setFont('helvetica', 'normal');
      const dateStr = new Date().toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
      pdf.text(dateStr, pageW - margin, 24, { align: 'right' });

      const topY = 36 + 20;
      pdf.addImage(imgData, 'PNG', margin, topY, imgW, Math.min(imgH, pageH - topY - margin));
      pdf.save('records_' + ts + '.pdf');
      closeExportModal();
    };
    document.head.appendChild(script);

   } else if (format === 'csv') {
    // ── CSV — chart-aware headers and filename ───────────────────────────────

    // Map each chart ID to its correct first-column label and export filename
    const chartMeta = {
      fileTypeChart:   { rowLabel: 'Record Type', filename: 'records_by_type_'         },
      folderDistChart: { rowLabel: 'Folder Name',  filename: 'folder_distribution_', valueLabel: 'Number of Files' },
      timelineChart:   { rowLabel: 'Month',        filename: 'records_over_time_'      }
    };

    const meta     = chartMeta[_exportChartId] || { rowLabel: 'Label', filename: 'chart_export_' };
    const cfg      = chart.config;
    const labels   = cfg.data.labels || [];
    const headers  = [meta.rowLabel, ...cfg.data.datasets.map(ds => ds.label || 'Value')];
    const rows     = [headers];

    labels.forEach(function(label, i) {
      const row = [label];
      cfg.data.datasets.forEach(function(ds) {
        row.push(ds.data[i] !== undefined ? ds.data[i] : '');
      });
      rows.push(row);
    });

    const csvContent = rows.map(function(row) {
      return row.map(function(cell) {
        const str = String(cell);
        return /[,"\n]/.test(str) ? '"' + str.replace(/"/g, '""') + '"' : str;
      }).join(',');
    }).join('\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.download = meta.filename + ts + '.csv';
    link.href = URL.createObjectURL(blob);
    link.click();
    URL.revokeObjectURL(link.href);
    closeExportModal();
  }

}
</script>

<?= $this->endSection() ?>