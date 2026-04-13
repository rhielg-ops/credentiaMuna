<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'CredentiaTAU') ?></title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Poppins', sans-serif; }
    
    /* Floating Action Button Animations */
    .fab-button {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .fab-button:hover {
      transform: scale(1.1);
      box-shadow: 0 8px 25px rgba(22, 163, 74, 0.4);
    }
    
    .fab-menu {
      animation: slideUp 0.3s ease-out;
    }
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    <?= $this->renderSection('additional_styles') ?>
  </style>
</head>
<body class="bg-gray-50">
  <div class="flex h-screen">
    <!-- Green Sidebar -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto relative">
      <!-- Green Header -->
      <?= $this->include('partials/header') ?>

      <!-- Content Area -->
      <div class="p-8">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
          <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            <?= session()->getFlashdata('success') ?>
          </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
          <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>
        
        <?= $this->renderSection('content') ?>
      </div>
      
      <!-- Floating Action Button (if needed by child views) -->
      <?= $this->renderSection('fab') ?>
    </div>
  </div>

  <!-- Modal Section (if needed by child views) -->
  <?= $this->renderSection('modals') ?>

  <!-- Additional Scripts -->
  <?= $this->renderSection('scripts') ?>
<script>
  /**
   * setupEyeToggle(inputId, buttonId)
   * Wires an eye-icon button to show/hide any password or PIN input.
   * Call once per input after the DOM is ready.
   */
  function setupEyeToggle(inputId, buttonId) {
    var input  = document.getElementById(inputId);
    var button = document.getElementById(buttonId);
    if (!input || !button) return;

    var PATH_OPEN   = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
                    + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    var PATH_CLOSED = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';

    button.addEventListener('click', function () {
      var svg = button.querySelector('svg');
      if (input.type === 'password') {
        input.type = 'text';
        if (svg) svg.innerHTML = PATH_CLOSED;
        button.title = 'Hide';
      } else {
        input.type = 'password';
        if (svg) svg.innerHTML = PATH_OPEN;
        button.title = 'Show';
      }
    });
  }
  </script>
</body>
</html>
