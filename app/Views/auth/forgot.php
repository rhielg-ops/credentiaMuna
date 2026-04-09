<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Username or Password — CredentiaTAU</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-100 to-gray-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <div class="text-center mb-8">
        <img src="<?= base_url('assets/img/TAU.png'); ?>" alt="TAU" class="w-20 h-20 mx-auto mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Account Recovery</h2>
        <p class="text-gray-500 text-sm mt-2">Enter your email to receive a recovery code</p>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('auth/forgot'); ?>" method="post">
        <div class="mb-5">
          <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
          <input type="email" name="email" required
                 placeholder="Enter your registered email"
                 class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none
                        focus:ring-2 focus:ring-green-500 text-sm">
        </div>
        <button type="submit"
                class="w-full py-3 bg-green-700 hover:bg-green-800 text-white font-semibold
                       rounded-xl transition-colors">
          Send Recovery Code
        </button>
      </form>

      <div class="text-center mt-5">
        <a href="<?= base_url('login'); ?>" class="text-sm text-green-700 hover:underline">
          ← Back to Login
        </a>
      </div>
    </div>
  </div>
</body>
</html>
