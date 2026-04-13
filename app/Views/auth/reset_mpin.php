<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset PIN — CredentiaTAU</title>
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
        <h2 class="text-2xl font-bold text-gray-800">Set New PIN</h2>
        <p class="text-gray-500 text-sm mt-2">Enter your new 4-digit PIN</p>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <form action="<?= base_url('auth/reset-mpin'); ?>" method="post">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">New PIN</label>
          <div class="relative">
            <input type="password" id="newPinInput" name="new_mpin" maxlength="4"
                   inputmode="numeric" required autocomplete="off"
                   class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 pr-12
                          border-2 border-gray-300 rounded-xl focus:outline-none
                          focus:border-purple-500 font-mono"
                   placeholder="••••"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            <button type="button" id="newPinEyeBtn" title="Show"
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
        </div>
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New PIN</label>
          <div class="relative">
            <input type="password" id="confirmPinInput" name="confirm_mpin" maxlength="4"
                   inputmode="numeric" required autocomplete="off"
                   class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 pr-12
                          border-2 border-gray-300 rounded-xl focus:outline-none
                          focus:border-purple-500 font-mono"
                   placeholder="••••"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            <button type="button" id="confirmPinEyeBtn" title="Show"
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
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-5 text-xs text-blue-800">
          Your new PIN will be valid for <strong>30 days</strong>.
        </div>
        <button type="submit"
                class="w-full py-3 bg-purple-700 hover:bg-purple-800 text-white font-semibold
                       rounded-xl transition-colors">
          Save New PIN
        </button>
      </form>

      <div class="text-center mt-4">
        <a href="<?= base_url('login'); ?>" class="text-sm text-gray-500 hover:text-gray-700">
          ← Cancel and return to Login
        </a>
      </div>
    </div>
  </div>
<script>
  (function () {
    var PATH_OPEN   = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'
                    + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    var PATH_CLOSED = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';

    function wireEye(inputId, buttonId) {
      var inp = document.getElementById(inputId);
      var btn = document.getElementById(buttonId);
      if (!inp || !btn) return;
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
    }

    wireEye('newPinInput',     'newPinEyeBtn');
    wireEye('confirmPinInput', 'confirmPinEyeBtn');
  })();
  </script>



</body>
</html>
