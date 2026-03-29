<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter MPIN - CredentiaTAU</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
  <div class="max-w-md w-full space-y-8">

    <div class="text-center">
      <div class="w-16 h-16 bg-green-700 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                   00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
      </div>
      <h2 class="text-3xl font-bold text-gray-800">Enter MPIN</h2>
      <p class="mt-2 text-gray-600">Welcome back, <strong><?= esc($full_name) ?></strong></p>
      <p class="text-sm text-gray-500 mt-1">Enter your 4-digit MPIN to continue</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        <?= session()->getFlashdata('error') ?>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/mpin-entry') ?>" method="post"
          class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-3 text-center">
          4-Digit MPIN
        </label>
        <input type="password"
               name="mpin"
               maxlength="4"
               autocomplete="off"
               inputmode="numeric"
               class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3
                      border-2 border-gray-300 rounded-lg focus:outline-none
                      focus:border-green-500 font-mono"
               placeholder="••••"
               oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        <p class="text-xs text-gray-400 text-center mt-2">Digits only</p>
      </div>

      <button type="submit"
              class="w-full py-3 bg-green-700 text-white rounded-lg hover:bg-green-800
                     font-semibold text-lg">
        Verify MPIN
      </button>

      <div class="mt-5 text-center">
        <p class="text-xs text-gray-500">
          Forgot your MPIN or need to use email OTP?<br>
          <a href="<?= base_url('auth/logout') ?>"
             class="text-green-700 hover:underline font-medium">
            Start over
          </a>
          and contact your administrator to reset your MPIN.
        </p>
      </div>

    </form>

  </div>
</div>

</body>
</html>