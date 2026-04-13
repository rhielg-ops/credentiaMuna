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
            <div class="relative">
              <input type="password" id="newPasswordInput" name="new_password" required minlength="8"
                     placeholder="At least 8 characters"
                     class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 text-sm">
              <button type="button" id="newPasswordEyeBtn" title="Show"
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

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <div class="relative">
              <input type="password" id="confirmPasswordInput" name="confirm_password" required minlength="8"
                     placeholder="Repeat new password"
                     class="w-full px-4 py-2.5 pr-12 border border-gray-300 rounded-xl focus:outline-none
                            focus:ring-2 focus:ring-green-500 text-sm">
              <button type="button" id="confirmPasswordEyeBtn" title="Show"
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
         <div id="passwordMismatchError" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-3">
            Passwords do not match. Please ensure both fields are identical.
          </div>
          <button type="submit" id="submitPasswordBtn"
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

    wireEye('newPasswordInput',     'newPasswordEyeBtn');
    wireEye('confirmPasswordInput', 'confirmPasswordEyeBtn');
  })();

  // Password match validation
  (function() {
    var form = document.getElementById('resetForm');
    var newPassInput = document.getElementById('newPasswordInput');
    var confirmPassInput = document.getElementById('confirmPasswordInput');
    var errorDiv = document.getElementById('passwordMismatchError');
    var submitBtn = document.getElementById('submitPasswordBtn');

    function validatePasswordMatch() {
      if (confirmPassInput.value && newPassInput.value !== confirmPassInput.value) {
        errorDiv.classList.remove('hidden');
        confirmPassInput.classList.add('border-red-500');
        confirmPassInput.classList.remove('border-gray-300');
        return false;
      } else {
        errorDiv.classList.add('hidden');
        confirmPassInput.classList.remove('border-red-500');
        confirmPassInput.classList.add('border-gray-300');
        return true;
      }
    }

    if (confirmPassInput) {
      confirmPassInput.addEventListener('input', validatePasswordMatch);
      confirmPassInput.addEventListener('blur', validatePasswordMatch);
    }

    if (form) {
      form.addEventListener('submit', function(e) {
        if (!validatePasswordMatch()) {
          e.preventDefault();
          confirmPassInput.focus();
        }
      });
    }
  })();
  </script>
</body>
</html>
