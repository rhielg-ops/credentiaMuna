<?= $this->extend('layouts/auth_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="bg-green-700 text-white p-8 rounded-xl mb-6">
  <h2 class="text-3xl font-bold mb-2">System Backup</h2>
  <p class="text-green-100">Backup and recovery management for academic records</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

  <!-- Last Backup Card -->
  <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800">Last Backup</h3>
    </div>
    <p id="statLastDate" class="text-2xl font-bold text-gray-900 mb-1">Loading…</p>
    <p id="statLastFile" class="text-xs text-gray-500 truncate">—</p>
  </div>

  <!-- Archived Records Card -->
  <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800">Total Records</h3>
    </div>
    <p id="statSourceCount" class="text-2xl font-bold text-gray-900 mb-1">—</p>
    <p class="text-xs text-gray-500">Files in academic_records</p>
  </div>

  <!-- Total Backups Card -->
  <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
    <div class="flex items-center gap-3 mb-4">
      <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800">Total Backups</h3>
    </div>
    <p id="statTotalBackups" class="text-2xl font-bold text-gray-900 mb-1">—</p>
    <p class="text-xs text-gray-500">Stored in File Server 2</p>
  </div>

</div>

<!-- Backup Actions -->
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
  <div class="flex items-center gap-3 mb-6">
    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
    </svg>
    <h3 class="text-xl font-bold text-gray-800">Backup Actions</h3>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Manual Backup Button -->
    <button id="btnManualBackup" onclick="performBackup()"
      class="flex items-center justify-center gap-3 bg-green-700 text-white px-6 py-4 rounded-lg hover:bg-green-800 font-semibold transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
      <svg id="backupIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
      </svg>
      <svg id="backupSpinner" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
      </svg>
      <span id="backupBtnText">Manual Backup Now</span>
    </button>

    <!-- Download Latest Backup Button -->
    <button onclick="downloadLatest()"
      class="flex items-center justify-center gap-3 bg-blue-600 text-white px-6 py-4 rounded-lg hover:bg-blue-700 font-semibold transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
      </svg>
      <span>Download Latest Backup</span>
    </button>
  </div>
</div>

<!-- Automatic Backup Schedule — Super Admin only -->
<?php
$isSuperAdmin = (session()->get('role') === 'admin' && session()->get('access_level') === 'full');
?>
<?php if ($isSuperAdmin): ?>
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">
  <div class="flex items-center gap-3 mb-6">
    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <h3 class="text-xl font-bold text-gray-800">Automatic Backup Schedule</h3>
  </div>

  <!-- Frequency Picker -->
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6" id="frequencyCards">
    <?php foreach ([
      ['daily',    'Daily',    'Every day'],
      ['weekly',   'Weekly',   'Once a week'],
      ['monthly',  'Monthly',  'Once a month'],
      ['disabled', 'Off',      'No auto-backup'],
    ] as [$val, $label, $desc]): ?>
    <div data-freq="<?= $val ?>"
      onclick="selectFrequency('<?= $val ?>')"
      class="freq-card border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition-colors text-center select-none">
      <h4 class="font-bold text-gray-700 text-sm"><?= $label ?></h4>
      <p class="text-xs text-gray-500 mt-1"><?= $desc ?></p>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Time Picker -->
  <div id="scheduleOptions" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Time -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Backup Time</label>
      <input type="time" id="scheduleTime" value="23:59"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"/>
      <p class="text-xs text-gray-500 mt-1">24-hour format</p>
    </div>

    <!-- Day (weekly only) -->
    <div id="weeklyDayWrap" class="hidden">
      <label class="block text-sm font-medium text-gray-700 mb-2">Day of Week</label>
      <select id="scheduleDay" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
        <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
          <option value="<?= strtolower($d) ?>"><?= $d ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Date (monthly only) -->
    <div id="monthlyDateWrap" class="hidden">
      <label class="block text-sm font-medium text-gray-700 mb-2">Day of Month</label>
      <select id="scheduleDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
        <?php for ($i = 1; $i <= 28; $i++): ?>
          <option value="<?= $i ?>"><?= $i ?><?= $i === 1 ? 'st' : ($i === 2 ? 'nd' : ($i === 3 ? 'rd' : 'th')) ?></option>
        <?php endfor; ?>
      </select>
    </div>
  </div>

  <!-- Save Schedule Button -->
  <button onclick="saveSchedule()"
    class="flex items-center gap-2 bg-green-700 text-white px-6 py-3 rounded-lg hover:bg-green-800 font-semibold transition-colors">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    Save Schedule
  </button>

  <!-- Current Schedule Info Box -->
  <div id="scheduleInfoBox" class="mt-5 bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
    <div class="flex items-start gap-3">
      <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <div>
        <p id="scheduleInfoText" class="text-sm font-semibold text-blue-800"></p>
        <p id="scheduleInfoSub" class="text-xs text-blue-700 mt-1"></p>
      </div>
    </div>
  </div>

  <!-- Cron Command Reference -->
 
</div>
<?php endif; ?>

<!-- Backup History -->
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
      <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <h3 class="text-xl font-bold text-gray-800">Backup Records</h3>
    </div>
    <button onclick="loadBackupList()" class="text-sm text-green-700 hover:text-green-900 font-medium flex items-center gap-1">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      Refresh
    </button>
  </div>

  <div id="backupListWrap">
    <div class="text-center py-8 text-gray-400">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"/>
      </svg>
      <p class="text-sm">Loading backup files…</p>
    </div>
  </div>
</div>

<!-- ── Modal Step 1: Ask whether to use a PIN ─────────────── -->
    <div id="modalBackupStep1"
         class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center gap-4 mb-5">
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">Create Manual Backup</h3>
            <p class="text-sm text-gray-500">Do you want to protect the backup with a 4-digit PIN?</p>
          </div>
        </div>
        
        <div class="flex gap-3 justify-end">
          <button onclick="closeModal('modalBackupStep1')"
            class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 text-sm transition-colors">
            Cancel
          </button>
          <button onclick="closeModal('modalBackupStep1'); runBackup('')"
            class="px-4 py-2.5 rounded-lg bg-gray-600 text-white font-semibold hover:bg-gray-700 text-sm transition-colors">
            No PIN — Backup Now
          </button>
          <button onclick="closeModal('modalBackupStep1'); openBackupPinModal()"
            class="px-4 py-2.5 rounded-lg bg-green-700 text-white font-semibold hover:bg-green-800 text-sm transition-colors">
            Yes — Set PIN
          </button>
        </div>
      </div>
    </div>

    <!-- ── Modal Step 2: Enter the 4-digit backup PIN ──────────── -->
    <div id="modalBackupPin"
         class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
        <div class="flex items-center gap-4 mb-5">
          <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                       00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">Set Backup PIN</h3>
            <p class="text-sm text-gray-500">Enter a 4-digit numeric PIN for this backup.</p>
          </div>
        </div>
        <div class="mb-5">
          <label class="block text-sm font-semibold text-gray-700 mb-2">4-Digit PIN</label>
          <div class="relative">
            <input type="password" id="backupPinInput" maxlength="4" inputmode="numeric"
                   class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 pr-12
                          border-2 border-gray-300 rounded-xl focus:outline-none focus:border-purple-500
                          font-mono"
                   placeholder="••••"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();confirmBackupPin();}">
            <button type="button" id="backupPinEyeBtn" title="Show"
                    class="absolute right-3 top-1/2 -translate-y-1/2
                           bg-transparent border-none cursor-pointer
                           text-gray-400 hover:text-gray-600 p-1">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                         9.542 7-1.274 4.057-5.064 7-9.542 7
                         -4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
            </button>
          </div>
          <p id="backupPinError" class="text-xs text-red-600 mt-1 hidden">
            Please enter exactly 4 digits.
          </p>
          <p class="text-xs text-gray-400 mt-2">
            ⚠️ Store this PIN safely — you will need it to open the backup file.
          </p>
        </div>
        <div class="flex gap-3">
          <button onclick="closeModal('modalBackupPin'); document.getElementById('modalBackupStep1').classList.remove('hidden')"
            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-xl text-gray-700
                   font-semibold hover:bg-gray-50 text-sm transition-colors">
            ← Back
          </button>
          <button onclick="confirmBackupPin()"
            class="flex-1 px-4 py-2.5 bg-purple-700 text-white rounded-xl
                   font-semibold hover:bg-purple-800 text-sm transition-colors">
            Start Backup
          </button>
        </div>
      </div>
    </div>


<!-- ── Modal: Confirm Delete Backup ────────────────────────── -->
<div id="modalDelete" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
    <div class="flex items-center gap-4 mb-4">
      <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
      </div>
      <div>
        <h3 class="text-lg font-bold text-gray-900">Delete Backup?</h3>
        <p class="text-sm text-gray-500">This action cannot be undone.</p>
      </div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-5">
      <p class="text-xs text-gray-500 mb-1">File to be deleted:</p>
      <p id="modalDeleteFilename" class="text-sm font-semibold text-gray-800 break-all"></p>
    </div>
    <div class="flex gap-3 justify-end">
      <button onclick="closeModal('modalDelete')"
        class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-colors text-sm">
        Cancel
      </button>
      <button id="modalDeleteConfirmBtn"
        class="px-5 py-2.5 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition-colors text-sm">
        Yes, Delete
      </button>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-6 right-6 z-50 hidden max-w-sm">
  <div id="toastInner" class="flex items-start gap-3 px-5 py-4 rounded-xl shadow-lg border text-sm font-medium">
    <span id="toastIcon" class="text-lg flex-shrink-0 mt-0.5"></span>
    <div>
      <p id="toastTitle" class="font-bold"></p>
      <p id="toastMsg" class="font-normal text-xs mt-0.5 opacity-80"></p>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  const BASE = '<?= base_url('super-admin/backup') ?>';
  const IS_SUPER = <?= $isSuperAdmin ? 'true' : 'false' ?>;

  let currentFrequency = 'daily';

  function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
  }

  // Close modals when clicking the dark backdrop
  document.addEventListener('click', e => {
    ['modalBackupStep1', 'modalBackupPin', 'modalDelete'].forEach(id => {
      const modal = document.getElementById(id);
      if (e.target === modal) closeModal(id);
    });
  });

  // ── Toast ────────────────────────────────────────────────────
  function showToast(type, title, msg = '') {
    const toast = document.getElementById('toast');
    const inner = document.getElementById('toastInner');
    const icon  = document.getElementById('toastIcon');
    document.getElementById('toastTitle').textContent = title;
    document.getElementById('toastMsg').textContent   = msg;

    const styles = {
      success: 'bg-green-50 border-green-300 text-green-900',
      error:   'bg-red-50 border-red-300 text-red-900',
      info:    'bg-blue-50 border-blue-300 text-blue-900',
      warning: 'bg-yellow-50 border-yellow-300 text-yellow-900',
    };
    const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };

    inner.className = 'flex items-start gap-3 px-5 py-4 rounded-xl shadow-lg border text-sm font-medium ' + (styles[type] || styles.info);
    icon.textContent = icons[type] || '💬';

    toast.classList.remove('hidden');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => toast.classList.add('hidden'), 5000);
  }

  // ── Manual Backup ────────────────────────────────────────────
  function performBackup() {
      document.getElementById('modalBackupStep1').classList.remove('hidden');
    }

    function openBackupPinModal() {
      // Clear previous value and error
      var inp = document.getElementById('backupPinInput');
      if (inp) { inp.value = ''; inp.type = 'password'; }
      document.getElementById('backupPinError').classList.add('hidden');
      document.getElementById('modalBackupPin').classList.remove('hidden');

      // Wire the eye-icon toggle (only once per modal open)
      var btn = document.getElementById('backupPinEyeBtn');
      if (btn && !btn._wired) {
        btn._wired = true;
        var PATH_OPEN   = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
                        + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        var PATH_CLOSED = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        btn.addEventListener('click', function () {
          var svg = btn.querySelector('svg');
          if (inp.type === 'password') { inp.type = 'text';     if (svg) svg.innerHTML = PATH_CLOSED; btn.title = 'Hide'; }
          else                          { inp.type = 'password'; if (svg) svg.innerHTML = PATH_OPEN;   btn.title = 'Show'; }
        });
      }
    }

    function confirmBackupPin() {
      var pin   = document.getElementById('backupPinInput').value.trim();
      var errEl = document.getElementById('backupPinError');
      if (!/^\d{4}$/.test(pin)) {
        errEl.classList.remove('hidden');
        return;
      }
      errEl.classList.add('hidden');
      closeModal('modalBackupPin');
      runBackup(pin);
    }



  async function runBackup(pin) {
      pin = pin || '';

    const btn    = document.getElementById('btnManualBackup');
    const icon   = document.getElementById('backupIcon');
    const spin   = document.getElementById('backupSpinner');
    const label  = document.getElementById('backupBtnText');

    btn.disabled = true;
    icon.classList.add('hidden');
    spin.classList.remove('hidden');
    label.textContent = 'Creating Backup…';

    try {
      const fetchBody = JSON.stringify(pin ? { pin: pin } : {});
      const res = await fetch(BASE + '/run', {
        method:  'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type':     'application/json',
        },
        body: fetchBody,
      });

      const data = await res.json();

       if (data.success) {
        var pinLabel = data.protected ? ' 🔒 PIN-protected' : '';
        showToast('success', 'Backup Complete!' + pinLabel,
          `${data.file_count} file(s) → ${data.zip_size} — ${data.filename}`);
        loadBackupList();
      } else {
        showToast('error', 'Backup Failed', data.message || 'Unknown error.');
      }
    } catch (e) {
      showToast('error', 'Request Failed', e.message);
    } finally {
      btn.disabled = false;
      icon.classList.remove('hidden');
      spin.classList.add('hidden');
      label.textContent = 'Manual Backup Now';
    }
  }

  // ── Download latest ──────────────────────────────────────────
  function downloadLatest() {
    window.location.href = BASE + '/download';
  }

  function downloadFile(filename) {
    window.location.href = BASE + '/download?file=' + encodeURIComponent(filename);
  }

  // ── Delete backup ────────────────────────────────────────────
  function deleteBackup(filename) {
    document.getElementById('modalDeleteFilename').textContent = filename;
    const btn = document.getElementById('modalDeleteConfirmBtn');
    btn.onclick = async () => {
      closeModal('modalDelete');
      try {
      const res  = await fetch(BASE + '/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ filename })
      });
      const data = await res.json();
      if (data.success) {
        showToast('success', 'Deleted', filename);
        loadBackupList();
      } else {
        showToast('error', 'Delete Failed', data.message);
      }
    } catch (e) {
        showToast('error', 'Request Failed', e.message);
      }
    };
    document.getElementById('modalDelete').classList.remove('hidden');
  }

  // ── Load Backup List ─────────────────────────────────────────
  async function loadBackupList() {
    const wrap = document.getElementById('backupListWrap');
    wrap.innerHTML = `<div class="text-center py-8 text-gray-400"><p class="text-sm">Refreshing…</p></div>`;

    try {
      const res  = await fetch(BASE + '/list', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();

      if (!data.success) throw new Error(data.message || 'Failed to load.');

      // Update stats
      const last = data.last_backup;
      document.getElementById('statLastDate').textContent     = last ? last.date : 'No backups yet';
      document.getElementById('statLastFile').textContent     = last ? last.file + ' (' + last.size + ')' : '—';
      document.getElementById('statSourceCount').textContent  = data.source_count.toLocaleString();
      document.getElementById('statTotalBackups').textContent = data.total;

      // Load schedule into UI if super admin
      if (IS_SUPER && data.schedule) {
        applyScheduleToUI(data.schedule);
      }

      // Render list
      if (data.backups.length === 0) {
        wrap.innerHTML = `
          <div class="text-center py-12 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"/>
            </svg>
            <p class="font-semibold text-gray-500">No backup files found</p>
            <p class="text-sm mt-1">Click "Manual Backup Now" to create your first backup.</p>
          </div>`;
        return;
      }

      let rows = '';
      data.backups.forEach((b, i) => {
        const isLatest = i === 0;
        rows += `
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 ${isLatest ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200'} rounded-lg">
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 ${isLatest ? 'bg-green-100' : 'bg-gray-200'} rounded-lg flex items-center justify-center flex-shrink-0">
              <svg class="w-5 h-5 ${isLatest ? 'text-green-700' : 'text-gray-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
            </div>
            <div>
              <p class="font-semibold text-sm text-gray-900 break-all">${b.filename} ${isLatest ? '<span class="ml-1 text-xs bg-green-600 text-white px-2 py-0.5 rounded-full">Latest</span>' : ''}</p>
              <p class="text-xs text-gray-500 mt-0.5">${b.created_at} · ${b.size}</p>
            </div>
          </div>
          <div class="flex gap-2 flex-shrink-0">
            <button onclick="downloadFile('${b.filename}')"
              class="flex items-center gap-1.5 text-xs bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 font-semibold transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
              </svg>
              Download
            </button>
            ${IS_SUPER ? `
            <button onclick="deleteBackup('${b.filename}')"
              class="flex items-center gap-1.5 text-xs bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200 font-semibold transition-colors">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              Delete
            </button>` : ''}
          </div>
        </div>`;
      });

      wrap.innerHTML = `<div class="space-y-3">${rows}</div>`;

    } catch (e) {
      wrap.innerHTML = `<div class="text-center py-8 text-red-400 text-sm">Error loading backups: ${e.message}</div>`;
    }
  }

  // ── Schedule UI ──────────────────────────────────────────────
  function selectFrequency(freq) {
    currentFrequency = freq;
    document.querySelectorAll('.freq-card').forEach(c => {
      const isActive = c.dataset.freq === freq;
      c.classList.toggle('border-green-700', isActive);
      c.classList.toggle('bg-green-50', isActive);
      c.classList.toggle('border-gray-200', !isActive);
    });

    document.getElementById('weeklyDayWrap').classList.toggle('hidden', freq !== 'weekly');
    document.getElementById('monthlyDateWrap').classList.toggle('hidden', freq !== 'monthly');
    document.getElementById('scheduleOptions').classList.toggle('hidden', freq === 'disabled');
  }

  function applyScheduleToUI(schedule) {
    const freq = schedule.frequency || 'daily';
    selectFrequency(freq);

    if (schedule.time) document.getElementById('scheduleTime').value = schedule.time;
    if (schedule.day)  document.getElementById('scheduleDay').value  = schedule.day;
    if (schedule.date) document.getElementById('scheduleDate').value = schedule.date;

    // Show info box
    const box  = document.getElementById('scheduleInfoBox');
    const text = document.getElementById('scheduleInfoText');
    const sub  = document.getElementById('scheduleInfoSub');

    if (schedule.enabled) {
      const freqLabel = { daily: 'Daily', weekly: 'Weekly', monthly: 'Monthly' }[freq] || freq;
      const timeStr   = schedule.time || '11:59';
      let detail      = '';
      if (freq === 'weekly')  detail = ` — every ${schedule.day || 'sunday'}`;
      if (freq === 'monthly') detail = ` — on the ${schedule.date || 1}st`;

      text.textContent = `Active Schedule: ${freqLabel} at ${timeStr}${detail}`;
      sub.textContent  = schedule.updated_at
        ? `Last updated: ${schedule.updated_at}${schedule.updated_by ? ' by ' + schedule.updated_by : ''}`
        : 'Automatic backups are enabled.';
      box.classList.remove('hidden');
    } else {
      text.textContent = 'Auto-backup is currently disabled.';
      sub.textContent  = 'Select a frequency above and save to enable.';
      box.classList.remove('hidden');
    }
  }

  async function saveSchedule() {
    const payload = {
      frequency: currentFrequency,
      time:      document.getElementById('scheduleTime').value,
      day:       document.getElementById('scheduleDay').value,
      date:      parseInt(document.getElementById('scheduleDate').value),
    };

    try {
      const res  = await fetch(BASE + '/schedule', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (data.success) {
        showToast('success', 'Schedule Saved', data.message || 'Auto-backup schedule updated.');
        applyScheduleToUI(data.schedule);
      } else {
        showToast('error', 'Save Failed', data.message || 'Could not save schedule.');
      }
    } catch (e) {
      showToast('error', 'Request Failed', e.message);
    }
  }

  // ── Init ─────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', () => {
    loadBackupList();
  });
</script>
<?= $this->endSection() ?>