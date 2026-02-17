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
</body>
</html>