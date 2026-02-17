<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Verification - CredentiaTAU</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <style>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    background: linear-gradient(to bottom right, #8dd1a0ff, #f9f9f9);
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .verification-container {
    max-width: 700px;
    width: 100%;
    background: white;
    padding: 70px 60px;
    border-radius: 24px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.2);
    text-align: center;
  }

  .logo {
    width: 120px;
    height: 120px;
    display: block;
    margin: 0 auto 25px;
  }

  h2 {
    color: #1d6b37;
    margin-bottom: 15px;
    font-size: 42px;
    font-weight: 700;
  }

  .subtitle {
    color: #555;
    margin-bottom: 35px;
    font-size: 19px;
  }

  .info-box {
    background: #ecf8ed;
    border-left: 6px solid #1d6b37;
    padding: 20px;
    margin-bottom: 35px;
    text-align: left;
    border-radius: 8px;
  }

  .info-box p {
    margin: 0;
    color: #1d6b37;
    font-weight: 500;
  }

  .code-inputs {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 30px 0;
  }

  .code-input {
    width: 70px;
    height: 70px;
    font-size: 32px;
    text-align: center;
    border: 2px solid #ddd;
    border-radius: 12px;
    font-weight: bold;
    color: #1d6b37;
    transition: all 0.3s;
  }

  .code-input:focus {
    outline: none;
    border-color: #1d6b37;
    box-shadow: 0 0 0 4px rgba(29, 107, 55, 0.15);
  }

  button {
    width: 100%;
    padding: 22px;
    background: #1d6b37;
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 20px;
    cursor: pointer;
    transition: background 0.3s, transform 0.1s;
    margin-top: 15px;
  }

  button:hover {
    background: #155028;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(29, 107, 55, 0.3);
  }

  button:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
  }

  .resend-section {
    margin-top: 25px;
    padding-top: 25px;
    border-top: 2px solid #f0f0f0;
  }

  .resend-text {
    color: #666;
    margin-bottom: 10px;
  }

  .resend-btn {
    background: white;
    color: #1d6b37;
    border: 2px solid #1d6b37;
    padding: 15px 30px;
    width: auto;
    display: inline-block;
    margin-top: 10px;
  }

  .resend-btn:hover {
    background: #ecf8ed;
  }

  .resend-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .error, .success {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 25px;
    font-size: 16px;
    font-weight: 500;
  }

  .error {
    color: #dc3545;
    background: #f8d7da;
    border: 2px solid #f5c6cb;
  }

  .success {
    color: #155724;
    background: #d4edda;
    border: 2px solid #c3e6cb;
  }

  .timer {
    font-size: 18px;
    color: #1d6b37;
    font-weight: 600;
    margin: 15px 0;
  }

  @media (max-width: 768px) {
    .verification-container {
      padding: 50px 40px;
    }
    
    h2 {
      font-size: 36px;
    }
    
    .code-input {
      width: 50px;
      height: 50px;
      font-size: 24px;
    }
  }
</style>
</head>
<body>
  <div class="verification-container">
    <img src="<?= base_url('assets/img/TAU.png'); ?>" class="logo" alt="TAU Logo">
    <h2>Verification</h2>
    <p class="subtitle">Verify Your Identity</p>

    <div class="info-box">
      <p>📧 A 6-digit verification code has been sent to <strong><?= esc($email); ?></strong></p>
      <p style="margin-top: 10px;">⏱️ The code will expire in 10 minutes</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <p class="error"><?= session()->getFlashdata('error'); ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <p class="success"><?= session()->getFlashdata('success'); ?></p>
    <?php endif; ?>

    <form id="verifyForm" action="<?= base_url('auth/verify-code'); ?>" method="post">
      <div class="code-inputs">
        <input type="text" class="code-input" maxlength="1" id="code1" autocomplete="off" autofocus>
        <input type="text" class="code-input" maxlength="1" id="code2" autocomplete="off">
        <input type="text" class="code-input" maxlength="1" id="code3" autocomplete="off">
        <input type="text" class="code-input" maxlength="1" id="code4" autocomplete="off">
        <input type="text" class="code-input" maxlength="1" id="code5" autocomplete="off">
        <input type="text" class="code-input" maxlength="1" id="code6" autocomplete="off">
      </div>

      <input type="hidden" name="code" id="fullCode">
      
      <button type="submit" id="verifyBtn">Verify Code</button>
    </form>

    <div class="resend-section">
      <p class="resend-text">Didn't receive the code?</p>
      <button class="resend-btn" id="resendBtn" onclick="resendCode()">Resend Code</button>
      <div class="timer" id="timer" style="display: none;"></div>
    </div>

    <div style="margin-top: 30px;">
      <a href="<?= base_url('login'); ?>" style="color: #1d6b37; text-decoration: none; font-weight: 600;">← Back to Login</a>
    </div>
  </div>

  <script>
    // Code input handling
    const inputs = document.querySelectorAll('.code-input');
    const fullCodeInput = document.getElementById('fullCode');
    const verifyBtn = document.getElementById('verifyBtn');
    const resendBtn = document.getElementById('resendBtn');
    const timerDiv = document.getElementById('timer');

    // Auto-focus next input
    inputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        const value = e.target.value;
        
        // Only allow numbers
        e.target.value = value.replace(/[^0-9]/g, '');
        
        // Move to next input if current is filled
        if (e.target.value && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
        
        // Update hidden input
        updateFullCode();
      });

      // Handle backspace
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
          inputs[index - 1].focus();
        }
      });

      // Handle paste
      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
        
        pastedData.split('').forEach((char, i) => {
          if (inputs[i]) {
            inputs[i].value = char;
          }
        });
        
        updateFullCode();
        
        // Focus last filled input or next empty
        const lastIndex = Math.min(pastedData.length, inputs.length - 1);
        inputs[lastIndex].focus();
      });
    });

    function updateFullCode() {
      const code = Array.from(inputs).map(input => input.value).join('');
      fullCodeInput.value = code;
      
      // Enable/disable verify button
      if (code.length === 6) {
        verifyBtn.disabled = false;
      } else {
        verifyBtn.disabled = true;
      }
    }

    // Resend code functionality
    let resendCooldown = 0;
    let cooldownInterval;

    function resendCode() {
      if (resendCooldown > 0) return;

      resendBtn.disabled = true;
      resendBtn.textContent = 'Sending...';

      fetch('<?= base_url('auth/resend-code'); ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Start cooldown
          resendCooldown = 60;
          startCooldown();
          
          // Show success message
          alert('✓ Verification code resent successfully!');
          
          // Clear inputs
          inputs.forEach(input => input.value = '');
          inputs[0].focus();
          updateFullCode();
        } else {
          alert('✗ ' + data.message);
          resendBtn.disabled = false;
          resendBtn.textContent = 'Resend Code';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to resend code. Please try again.');
        resendBtn.disabled = false;
        resendBtn.textContent = 'Resend Code';
      });
    }

    function startCooldown() {
      resendBtn.style.display = 'none';
      timerDiv.style.display = 'block';
      
      cooldownInterval = setInterval(() => {
        resendCooldown--;
        timerDiv.textContent = `Resend available in ${resendCooldown} seconds`;
        
        if (resendCooldown <= 0) {
          clearInterval(cooldownInterval);
          timerDiv.style.display = 'none';
          resendBtn.style.display = 'inline-block';
          resendBtn.disabled = false;
          resendBtn.textContent = 'Resend Code';
        }
      }, 1000);
    }

    // Initialize
    updateFullCode();
  </script>
</body>
</html>