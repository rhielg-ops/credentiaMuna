<?php
// KPI values ($total_files, $total_folders, $file_types, $folder_distribution, $monthly_data)
// are computed in App\Controllers\Dashboard::index() and passed here via $data.

// Guard against null (e.g. directory missing or user has no assigned folders)
$total_files         = $total_files         ?? 0;
$total_folders       = $total_folders       ?? 0;
$file_types          = $file_types          ?? [];
$folder_distribution = $folder_distribution ?? [];
$monthly_data        = $monthly_data        ?? array_fill(0, 12, 0);

// Group file types for chart
$pdf_count   = $file_types['pdf']  ?? 0;
$docx_count  = ($file_types['docx'] ?? 0) + ($file_types['doc'] ?? 0);
$png_count   = $file_types['png']  ?? 0;
$jpeg_count  = ($file_types['jpg'] ?? 0) + ($file_types['jpeg'] ?? 0);
$other_count = $total_files - ($pdf_count + $docx_count + $png_count + $jpeg_count);

// Get top 5 folders by file count
$folder_distribution = $folder_distribution ?? [];
arsort($folder_distribution);
$top_folders   = array_slice($folder_distribution, 0, 5, true);
$folder_labels = array_keys($top_folders);
$folder_values = array_values($top_folders);
?>
<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2">Dashboard Overview</h2>
  <p class="text-green-100">Academic records and enrollment statistics</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
  <!-- Total Records (Files) -->
  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Total Records (Files)</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= number_format($total_files) ?></p>
    <p class="text-sm text-green-200">Files you can access</p>
  </div>

  <!-- Total Folders -->
  <div class="bg-green-700 rounded-xl p-6 shadow-md border border-green-800 text-white transition-all duration-300 hover:bg-green-600 hover:shadow-lg cursor-default">
    <div class="flex items-center justify-between mb-4">
      <span class="text-green-100 font-medium">Total Folders</span>
      <div class="w-12 h-12 bg-green-800/50 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
        </svg>
      </div>
    </div>
    <p class="text-3xl font-bold mb-1"><?= number_format($total_folders) ?></p>
    <p class="text-sm text-green-200">Folders you can access</p>
  </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
  
  <!-- Files by Type Chart -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-800">Files by Type</h3>
      <button onclick="exportChart('fileTypeChart')" class="text-sm text-green-700 hover:text-green-800 font-medium">
        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
        Export
      </button>
    </div>
    <canvas id="fileTypeChart" class="w-full" style="max-height: 300px;"></canvas>
  </div>

  <!-- Folder Distribution Chart -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-800">Folder Distribution</h3>
      <button onclick="exportChart('folderDistChart')" class="text-sm text-green-700 hover:text-green-800 font-medium">
        <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
        Export
      </button>
    </div>
    <canvas id="folderDistChart" class="w-full" style="max-height: 300px;"></canvas>
  </div>

</div>

<!-- Access Timeline Chart -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h3 class="text-lg font-semibold text-gray-800">Files Added Over Time</h3>
      <p class="text-sm text-gray-500 mt-1">Track file uploads and additions</p>
    </div>
    <button onclick="exportChart('timelineChart')" class="text-sm text-green-700 hover:text-green-800 font-medium">
      <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
      </svg>
      Export
    </button>
  </div>
  <canvas id="timelineChart" class="w-full" style="max-height: 350px;"></canvas>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart colors
const colors = {
  primary: '#15803d',
  secondary: '#16a34a',
  tertiary: '#22c55e',
  quaternary: '#86efac',
  light: '#dcfce7',
  border: '#bbf7d0'
};

// Files by Type - Pie Chart
const fileTypeCtx = document.getElementById('fileTypeChart').getContext('2d');
const fileTypeChart = new Chart(fileTypeCtx, {
  type: 'doughnut',
  data: {
    labels: ['PDF', 'Word Documents', 'PNG Images', 'JPEG Images', 'Others'],
    datasets: [{
      data: [<?= $pdf_count ?>, <?= $docx_count ?>, <?= $png_count ?>, <?= $jpeg_count ?>, <?= $other_count ?>],
      backgroundColor: [
        colors.primary,
        colors.secondary,
        colors.tertiary,
        colors.quaternary,
        colors.light
      ],
      borderColor: '#fff',
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          padding: 15,
          font: {
            size: 12
          }
        }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            return context.label + ': ' + context.parsed + ' files';
          }
        }
      }
    }
  }
});

// Folder Distribution - Bar Chart
const folderDistCtx = document.getElementById('folderDistChart').getContext('2d');
const folderDistChart = new Chart(folderDistCtx, {
  type: 'bar',
  data: {
    labels: [<?= !empty($folder_labels) ? "'" . implode("','", array_map('addslashes', $folder_labels)) . "'" : "'No Data'" ?>],
    datasets: [{
      label: 'Number of Files',
      data: [<?= !empty($folder_values) ? implode(',', $folder_values) : '0' ?>],
      backgroundColor: colors.secondary,
      borderColor: colors.primary,
      borderWidth: 1,
      borderRadius: 6
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        display: false
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            return context.parsed.y + ' files';
          }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
          callback: function(value) {
            if (Math.floor(value) === value) {
              return value;
            }
          }
        },
        grid: {
          color: '#f3f4f6'
        }
      },
      x: {
        grid: {
          display: false
        }
      }
    }
  }
});

// Files Added Over Time - Line Chart
const timelineCtx = document.getElementById('timelineChart').getContext('2d');
const timelineChart = new Chart(timelineCtx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    datasets: [{
      label: 'Files Added',
      data: [<?= implode(',', $monthly_data) ?>],
      borderColor: colors.secondary,
      backgroundColor: 'rgba(22, 163, 74, 0.1)',
      fill: true,
      tension: 0.4,
      pointBackgroundColor: colors.primary,
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 5,
      pointHoverRadius: 7
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        display: false
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            return context.parsed.y + ' files added';
          }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
          callback: function(value) {
            if (Math.floor(value) === value) {
              return value;
            }
          }
        },
        grid: {
          color: '#f3f4f6'
        }
      },
      x: {
        grid: {
          display: false
        }
      }
    }
  }
});

// Export chart function
function exportChart(chartId) {
  const chart = Chart.getChart(chartId);
  if (chart) {
    const url = chart.toBase64Image();
    const link = document.createElement('a');
    link.download = chartId + '_' + new Date().getTime() + '.png';
    link.href = url;
    link.click();
  }
}
</script>

<?= $this->endSection() ?>