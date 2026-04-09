<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset MPIN — CredentiaTAU</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-100 to-gray-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0
                     01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Set New MPIN</h2>
        <p class="text-gray-500 text-sm mt-2">Enter your new 4-digit MPIN</p>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('auth/reset-mpin'); ?>" method="post">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">New MPIN</label>
          <input type="password" name="new_mpin" maxlength="4" inputmode="numeric"
                 required autocomplete="off"
                 class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3
                        border-2 border-gray-300 rounded-xl focus:outline-none focus:border-purple-500
                        font-mono"
                 placeholder="••••"
                 oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New MPIN</label>
          <input type="password" name="confirm_mpin" maxlength="4" inputmode="numeric"
                 required autocomplete="off"
                 class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3
                        border-2 border-gray-300 rounded-xl focus:outline-none focus:border-purple-500
                        font-mono"
                 placeholder="••••"
                 oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-5 text-xs text-blue-800">
          Your new MPIN will be valid for <strong>30 days</strong>.
        </div>
        <button type="submit"
                class="w-full py-3 bg-purple-700 hover:bg-purple-800 text-white font-semibold
                       rounded-xl transition-colors">
          Save New MPIN
        </button>
      </form>

      <div class="text-center mt-4">
        <a href="<?= base_url('login'); ?>" class="text-sm text-gray-500 hover:text-gray-700">
          ← Cancel and return to Login
        </a>
      </div>
    </div>
  </div>
</body>
</html>
