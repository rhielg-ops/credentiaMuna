<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter PIN - CredentiaTAU</title>
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
      <h2 class="text-3xl font-bold text-gray-800">Enter PIN</h2>
      <p class="mt-2 text-gray-600">Welcome back, <strong><?= esc($full_name) ?></strong></p>
      <p class="text-sm text-gray-500 mt-1">Enter your 4-digit PIN to continue</p>
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
          4-Digit PIN
        </label>
        <div class="relative">
          <input type="password"
                 id="mpinInput"
                 name="mpin"
                 maxlength="4"
                 autocomplete="off"
                 inputmode="numeric"
                 class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 pr-12
                        border-2 border-gray-300 rounded-lg focus:outline-none
                        focus:border-green-500 font-mono"
                 placeholder="••••"
                 oninput="this.value=this.value.replace(/[^0-9]/g,'')">
          <button type="button" id="mpinEyeBtn" title="Show"
                  class="absolute right-3 top-1/2 -translate-y-1/2
                         bg-transparent border-none cursor-pointer
                         text-gray-400 hover:text-gray-600 p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                       9.542 7-1.274 4.057-5.064 7-9.542 7
                       -4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>
        </div>
        <p class="text-xs text-gray-400 text-center mt-2">Digits only</p>
      </div>


      <button type="submit"
              class="w-full py-3 bg-green-700 text-white rounded-lg hover:bg-green-800
                     font-semibold text-lg">
        Verify MPIN
      </button>

      <div class="mt-5 text-center space-y-3">
        <div>
          <a href="<?= base_url('auth/forgot-mpin'); ?>"
             class="text-green-700 text-sm font-semibold hover:underline">
            Forgot PIN?
          </a>
        </div>
        <p class="text-xs text-gray-500">
          Need to use email OTP instead?
          <a href="<?= base_url('auth/logout'); ?>"
             
      </div>

    </form>

  </div>
</div>

<script>
  (function () {
    var PATH_OPEN   = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
                    + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    var PATH_CLOSED = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';

    var btn = document.getElementById('mpinEyeBtn');
    var inp = document.getElementById('mpinInput');
    if (!btn || !inp) return;

    btn.addEventListener('click', function () {
      var svg = btn.querySelector('svg');
      if (inp.type === 'password') {
        inp.type = 'text';
        if (svg) svg.innerHTML = PATH_CLOSED;
        btn.title = 'Hide';
      } else {
        inp.type = 'password';
        if (svg) svg.innerHTML = PATH_OPEN;
        btn.title = 'Show';
      }
    });
  })();
  </script>


</body>
</html>