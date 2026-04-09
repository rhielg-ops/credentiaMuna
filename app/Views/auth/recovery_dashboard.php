<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Recovery — CredentiaTAU</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-100 to-gray-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <div class="text-center mb-6">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Hello, <?= esc($full_name) ?>!</h2>
        <p class="text-gray-500 text-sm mt-1">Your identity has been verified</p>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <!-- Account Info -->
      <div class="bg-gray-50 rounded-xl p-4 mb-6 space-y-3">
        <div>
          <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Username (read-only)</p>
          <p class="text-gray-800 font-mono font-semibold text-lg"><?= esc($username ?: '(not set)') ?></p>
        </div>
        <div>
          <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Email</p>
          <p class="text-gray-800"><?= esc($email) ?></p>
        </div>
      </div>

      <!-- Reset Password Form -->
      <div id="resetSection">
        <button onclick="document.getElementById('resetForm').classList.toggle('hidden')"
                class="w-full py-3 bg-green-700 hover:bg-green-800 text-white font-semibold
                       rounded-xl transition-colors mb-3">
          Reset Password
        </button>

        <form id="resetForm" action="<?= base_url('auth/reset-password'); ?>" method="post"
              class="hidden mt-4 space-y-4 border-t border-gray-200 pt-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="password" name="new_password" required minlength="8"
                   placeholder="At least 8 characters"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none
                          focus:ring-2 focus:ring-green-500 text-sm">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input type="password" name="confirm_password" required minlength="8"
                   placeholder="Repeat new password"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none
                          focus:ring-2 focus:ring-green-500 text-sm">
          </div>
          <button type="submit"
                  class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold
                         rounded-xl transition-colors text-sm">
            Save New Password
          </button>
        </form>
      </div>

      <div class="text-center mt-4">
        <a href="<?= base_url('login'); ?>"
           class="text-sm text-gray-500 hover:text-gray-700">
          ← Cancel and return to Login
        </a>
      </div>
    </div>
  </div>
</body>
</html>
