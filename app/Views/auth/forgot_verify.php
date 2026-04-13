<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enter Recovery Code — CredentiaTAU</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-100 to-gray-50 flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-xl p-8">
      <div class="text-center mb-6">
        <img src="<?= base_url('assets/img/TAU.png'); ?>" alt="TAU" class="w-16 h-16 mx-auto mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Enter Recovery Code</h2>
        <p class="text-gray-500 text-sm mt-1">
          A 6-digit code was sent to <strong><?= esc($email) ?></strong>
        </p>
        <p class="text-yellow-600 text-xs mt-1 font-semibold">
          ⏱ Code expires in:
          <span id="recoveryCountdown"
                style="font-family:'Courier New',monospace;font-weight:700;color:#b45309;">
            05:00
          </span>
        </p>

      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <?php
        // Route the form action based on purpose
        $action = ($purpose === 'mpin_reset')
          ? base_url('auth/forgot-mpin-verify')
          : base_url('auth/forgot-verify');
      ?>

      <form action="<?= $action ?>" method="post">
        <div class="mb-5">
          <label class="block text-sm font-medium text-gray-700 mb-3 text-center">6-Digit Code</label>
          <div id="codeInputs" class="flex justify-center gap-2">
            <?php for ($i = 1; $i <= 6; $i++): ?>
              <input type="text" maxlength="1" id="c<?= $i ?>"
                     class="w-12 h-12 text-center text-xl font-bold border-2 border-gray-300
                            rounded-lg focus:outline-none focus:border-green-500 text-green-700"
                     autocomplete="off">
            <?php endfor; ?>
          </div>
          <input type="hidden" name="otp" id="otpHidden">
        </div>
        <button type="submit" id="verifyBtn" disabled
                class="w-full py-3 bg-green-700 hover:bg-green-800 text-white font-semibold
                       rounded-xl transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
          Verify Code
        </button>
      </form>

      <div class="text-center mt-5 space-y-2">
        <p class="text-xs text-gray-500">Maximum 3 attempts allowed before account lockout.</p>
        <a href="<?= base_url('login'); ?>" class="text-sm text-green-700 hover:underline block">
          ← Back to Login
        </a>
      </div>
    </div>
  </div>
  <script>
    var inputs = [document.getElementById('c1'),document.getElementById('c2'),
                  document.getElementById('c3'),document.getElementById('c4'),
                  document.getElementById('c5'),document.getElementById('c6')];
    var hidden = document.getElementById('otpHidden');
    var btn    = document.getElementById('verifyBtn');
    inputs.forEach(function(inp, i) {
      inp.addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g,'');
        if (e.target.value && i < 5) inputs[i+1].focus();
        hidden.value = inputs.map(function(x){return x.value;}).join('');
        btn.disabled  = hidden.value.length !== 6;
      });
      inp.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && !e.target.value && i > 0) inputs[i-1].focus();
      });
      inp.addEventListener('paste', function(e) {
        e.preventDefault();
        var d = (e.clipboardData||window.clipboardData).getData('text').replace(/[^0-9]/g,'');
        d.split('').forEach(function(ch, j){ if(inputs[j]) inputs[j].value = ch; });
        hidden.value = inputs.map(function(x){return x.value;}).join('');
        btn.disabled = hidden.value.length !== 6;
        if(d.length >= 6) inputs[5].focus();
      });
    });
    inputs[0].focus();

    // ── Recovery OTP Countdown Timer ────────────────────────────────────────
    (function () {
      var remaining = 5 * 60; // 300 seconds = 5 minutes
      var el = document.getElementById('recoveryCountdown');
      if (!el) return;

      function fmt(s) {
        var m   = Math.floor(s / 60);
        var sec = s % 60;
        return (m < 10 ? '0' : '') + m + ':' + (sec < 10 ? '0' : '') + sec;
      }

      el.textContent = fmt(remaining);

      var timer = setInterval(function () {
        remaining -= 1;

        if (remaining <= 0) {
          clearInterval(timer);
          el.textContent = '00:00';
          el.style.color = '#dc2626';
          var btn = document.getElementById('verifyBtn');
          if (btn) {
            btn.disabled      = true;
            btn.textContent   = 'Code Expired';
            btn.style.opacity = '0.5';
          }
          return;
        }

        el.textContent = fmt(remaining);

        if (remaining <= 60)  { el.style.color = '#dc2626'; } // red under 1 min
        else if (remaining <= 120) { el.style.color = '#d97706'; } // orange under 2 min
        else                        { el.style.color = '#b45309'; } // yellow-brown default
      }, 1000);
    })();


  </script>
</body>
</html>