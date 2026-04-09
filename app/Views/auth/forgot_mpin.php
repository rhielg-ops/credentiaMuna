<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot MPIN — CredentiaTAU</title>
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
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-800">Forgot MPIN</h2>
        <p class="text-gray-500 text-sm mt-2">Enter your email to reset your MPIN</p>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('auth/forgot-mpin'); ?>" method="post">
        <div class="mb-5">
          <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
          <input type="email" name="email" required placeholder="Enter your registered email"
                 class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none
                        focus:ring-2 focus:ring-purple-500 text-sm">
        </div>
        <button type="submit"
                class="w-full py-3 bg-purple-700 hover:bg-purple-800 text-white font-semibold
                       rounded-xl transition-colors">
          Send MPIN Reset Code
        </button>
      </form>

      <div class="text-center mt-5">
        <a href="<?= base_url('auth/mpin-entry'); ?>" class="text-sm text-gray-500 hover:text-gray-700">
          ← Back to MPIN Entry
        </a>
      </div>
    </div>
  </div>
</body>
</html>
