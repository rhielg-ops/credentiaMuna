<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2">System Backup</h2>
  <p class="text-green-100">Backup and recovery management</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
  
  <!-- Last Backup Card -->
  <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800">Last Backup</h3>
    </div>
    <p class="text-3xl font-bold text-gray-900 mb-2"><?= date('M d, Y') ?></p>
    <p class="text-gray-500"><?= date('g:i A') ?></p>
  </div>

  <!-- Archived Academic Records Card -->
  <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800">Archived Academic Records</h3>
    </div>
    <p class="text-3xl font-bold text-gray-900 mb-2"><?= isset($archived_count) ? number_format($archived_count) : '0' ?></p>
    <p class="text-gray-500">Total archived records</p>
  </div>

</div>

<!-- Backup Actions -->
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
  <div class="flex items-center gap-3 mb-6">
    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
    </svg>
    <h3 class="text-xl font-bold text-gray-800">Backup Actions</h3>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Manual Backup Button -->
    <button onclick="performBackup()" class="flex items-center justify-center gap-3 bg-green-700 text-white px-6 py-4 rounded-lg hover:bg-green-800 font-semibold transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
      </svg>
      <span>Manual Backup</span>
    </button>

    <!-- Download Backup Button -->
    <button onclick="downloadBackup()" class="flex items-center justify-center gap-3 bg-blue-600 text-white px-6 py-4 rounded-lg hover:bg-blue-700 font-semibold transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
      </svg>
      <span>Download Backup</span>
    </button>
  </div>
</div>

<!-- Automatic Backup Schedule -->
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
  <div class="flex items-center gap-3 mb-6">
    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
    </svg>
    <h3 class="text-xl font-bold text-gray-800">Automatic Backup Schedule</h3>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Daily Backup Option -->
    <div class="border-2 border-green-700 bg-green-50 rounded-lg p-4 cursor-pointer hover:bg-green-100 transition-colors" onclick="setBackupSchedule('daily')">
      <div class="flex items-center justify-between mb-2">
        <h4 class="font-bold text-green-800">Daily</h4>
        <div class="w-6 h-6 bg-green-700 rounded-full flex items-center justify-center">
          <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
      </div>
      <p class="text-sm text-gray-700">Every day at 2:00 AM</p>
    </div>

    <!-- Weekly Backup Option -->
    <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors" onclick="setBackupSchedule('weekly')">
      <div class="flex items-center justify-between mb-2">
        <h4 class="font-bold text-gray-800">Weekly</h4>
        <div class="w-6 h-6 border-2 border-gray-300 rounded-full"></div>
      </div>
      <p class="text-sm text-gray-600">Every Sunday at 2:00 AM</p>
    </div>

    <!-- Monthly Backup Option -->
    <div class="border-2 border-gray-300 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors" onclick="setBackupSchedule('monthly')">
      <div class="flex items-center justify-between mb-2">
        <h4 class="font-bold text-gray-800">Monthly</h4>
        <div class="w-6 h-6 border-2 border-gray-300 rounded-full"></div>
      </div>
      <p class="text-sm text-gray-600">1st day of month at 2:00 AM</p>
    </div>
  </div>

  <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start gap-3">
      <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <div>
        <p class="text-sm font-semibold text-blue-800">Current Schedule: Daily at 2:00 AM</p>
        <p class="text-xs text-blue-700 mt-1">Automatic backups ensure your data is protected regularly</p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function performBackup() {
    if (confirm('Start manual backup now? This may take several minutes.')) {
      // Show loading state
      alert('Backup process initiated. You will be notified when complete.');
      
      // TODO: Implement actual backup logic
      // This would typically make an AJAX call to a backup endpoint
      console.log('Manual backup initiated');
    }
  }

  function downloadBackup() {
    if (confirm('Download the latest backup file? This may be a large download.')) {
      // TODO: Implement actual download logic
      alert('Download will begin shortly.');
      console.log('Backup download initiated');
    }
  }

  function setBackupSchedule(schedule) {
    // Remove active state from all options
    document.querySelectorAll('[onclick^="setBackupSchedule"]').forEach(el => {
      el.classList.remove('border-green-700', 'bg-green-50');
      el.classList.add('border-gray-300');
      const circle = el.querySelector('div > div');
      circle.classList.remove('bg-green-700');
      circle.classList.add('border-2', 'border-gray-300');
      circle.innerHTML = '';
    });

    // Add active state to selected option
    const selected = document.querySelector(`[onclick="setBackupSchedule('${schedule}')"]`);
    selected.classList.remove('border-gray-300');
    selected.classList.add('border-green-700', 'bg-green-50');
    const circle = selected.querySelector('div > div');
    circle.classList.remove('border-2', 'border-gray-300');
    circle.classList.add('bg-green-700');
    circle.innerHTML = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';

    // Update the info box
    const scheduleText = {
      'daily': 'Daily at 2:00 AM',
      'weekly': 'Weekly (Sunday) at 2:00 AM',
      'monthly': 'Monthly (1st day) at 2:00 AM'
    };

    const infoBox = document.querySelector('.bg-blue-50 p.text-sm');
    infoBox.textContent = `Current Schedule: ${scheduleText[schedule]}`;

    // TODO: Save schedule to backend
    console.log('Backup schedule set to:', schedule);
    
    // Show confirmation
    setTimeout(() => {
      alert(`Backup schedule updated to: ${scheduleText[schedule]}`);
    }, 100);
  }
</script>
<?= $this->endSection() ?>