<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CredentiaTAU Login</title>
  <link rel="icon" href="<?= base_url('assets/img/TAU.png'); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* === Original CSS kept intact === */
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; overflow-x: hidden; }
    .container { display: flex; width: 100%; min-height: 100vh; }

    /* Left Panel */
    .left-panel { flex: 1; background: linear-gradient(135deg, rgba(26,143,74,0.95) 0%, rgba(13,94,47,0.95) 100%), url('<?= base_url('assets/TAU bg.jpg'); ?>') center/cover; background-blend-mode: multiply; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 40px; position: relative; overflow: hidden; }
    .left-panel::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.08) 1px, transparent 1px); background-size: 30px 30px; animation: backgroundMove 20s linear infinite; }
    @keyframes backgroundMove { 0% { transform: translate(0,0); } 100% { transform: translate(30px,30px); } }

    .logo-section { position: relative; z-index: 1; text-align: center; }
    .logo { width: 200px; height: 200px; margin-bottom: 40px; animation: fadeInScale 0.8s ease-out; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3)); }
    @keyframes fadeInScale { from { opacity:0; transform: scale(0.8); } to { opacity:1; transform: scale(1); } }
    .brand-title { color: white; font-size: 56px; font-weight: 700; margin-bottom: 16px; letter-spacing: -1px; animation: slideInLeft 0.8s ease-out 0.2s both; }
    .brand-subtitle { color: rgba(255,255,255,0.95); font-size: 22px; font-weight: 400; margin-bottom: 8px; animation: slideInLeft 0.8s ease-out 0.3s both; }
    .university-name { color: rgba(255,255,255,0.9); font-size: 18px; font-weight: 300; animation: slideInLeft 0.8s ease-out 0.4s both; }
    @keyframes slideInLeft { from { opacity:0; transform: translateX(-30px); } to { opacity:1; transform: translateX(0); } }

    /* Right Panel */
    .right-panel { flex:1; background:#f5f5f5; display:flex; align-items:center; justify-content:center; padding:60px 40px; }
    .login-form-container { width:100%; max-width:750px; background:white; padding:80px 70px; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); animation: slideInRight 0.8s ease-out; }
    @keyframes slideInRight { from { opacity:0; transform: translateX(30px); } to { opacity:1; transform: translateX(0); } }
    .form-header { margin-bottom: 45px; }
    .form-title { color:#1a1a1a; font-size:48px; font-weight:700; margin-bottom:12px; }
    .form-description { color:#666; font-size:18px; font-weight:400; }
    .form-group { margin-bottom:32px; }
    label { display:block; color:#333; font-weight:500; margin-bottom:12px; font-size:17px; }
    .input-wrapper { position:relative; }
    .input-icon { position:absolute; left:20px; top:50%; transform:translateY(-50%); color:#999; font-size:20px; }
    .toggle-password { position:absolute; right:20px; top:50%; transform:translateY(-50%); cursor:pointer; transition:all 0.3s ease; user-select:none; width:24px; height:24px; display:flex; align-items:center; justify-content:center; }
    .toggle-password svg { width:22px; height:22px; stroke:#999; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; transition: stroke 0.3s ease; }
    .toggle-password:hover svg { stroke:#1a8f4a; }
    input[type="email"], input[type="password"], input[type="text"] { width:100%; padding:20px 55px 20px 55px; border:1px solid #ddd; border-radius:8px; font-size:17px; font-family:'Poppins',sans-serif; transition: all 0.3s ease; background:#fafafa; }
    input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus { outline:none; border-color:#1a8f4a; background:white; box-shadow:0 0 0 3px rgba(26,143,74,0.1); }
    input::placeholder { color:#aaa; }
    #password { font-family:'Poppins',sans-serif; letter-spacing:normal; }
    #password[type="password"] { letter-spacing:0.2em; }

    .submit-btn { width:100%; padding:22px; background:#1a8f4a; color:white; border:none; border-radius:8px; font-weight:600; font-size:19px; cursor:pointer; transition: all 0.3s ease; margin-top:16px; text-transform:uppercase; letter-spacing:0.5px; }
    .submit-btn:hover { background:#0d5e2f; transform:translateY(-2px); box-shadow:0 6px 20px rgba(26,143,74,0.3); }
    .submit-btn:active { transform:translateY(0); }

    .error { color:#dc3545; background:#f8d7da; border-left:4px solid #dc3545; padding:14px 16px; border-radius:8px; font-size:14px; font-weight:500; margin-bottom:20px; }
    .success { color:#155724; background:#d4edda; border-left:4px solid #28a745; padding:14px 16px; border-radius:8px; font-size:14px; font-weight:500; margin-bottom:20px; }

    /* suspended notices and buttons kept as original */

    /* Responsive adjustments kept as original */
    @media (max-width:1024px){.container{flex-direction:column;} .left-panel{min-height:350px;padding:40px 30px;} .logo{width:150px;height:150px;margin-bottom:30px;} .brand-title{font-size:42px;} .brand-subtitle{font-size:18px;} .university-name{font-size:16px;} .right-panel{padding:40px 30px;} .login-form-container{padding:60px 50px;}}
    @media (max-width:768px){.left-panel{min-height:300px;padding:30px 20px;} .logo{width:120px;height:120px;margin-bottom:20px;} .brand-title{font-size:36px;} .brand-subtitle{font-size:16px;} .university-name{font-size:14px;} .login-form-container{padding:35px 30px;} .form-title{font-size:36px;}}
    @media (max-width:480px){.left-panel{min-height:250px;} .logo{width:100px;height:100px;} .form-title{font-size:24px;} .login-form-container{padding:30px 25px;}}
  </style>
</head>
<body>
  <div class="container">
    <!-- Left Panel -->
    <div class="left-panel">
      <div class="logo-section">
        <img src="<?= base_url('assets/img/TAU.png'); ?>" class="logo" alt="TAU Logo">
        <h1 class="brand-title">CredentiaTAU</h1>
        <p class="brand-subtitle">Academic Records Archiving System</p>
        <p class="university-name">Tarlac Agricultural University</p>
      </div>
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <div class="login-form-container">
        <div class="form-header">
          <h2 class="form-title">Sign In</h2>
          <p class="form-description">Enter your credentials to access your account</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
          <p class="error"><?= session()->getFlashdata('error'); ?></p>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
          <p class="success"><?= session()->getFlashdata('success'); ?></p>
        <?php endif; ?>

        <?php if (isset($suspended_email) && $suspended_email): ?>
          <div class="suspended-notice">
            <p><strong>⚠️ Account Suspended</strong></p>
            <p>Your account (<?= esc($suspended_email); ?>) is currently suspended.</p>
            <p>Please request reactivation below.</p>
          </div>
          <div id="requestSection">
            <?php if (isset($has_pending_request) && $has_pending_request): ?>
              <p class="warning">Your reactivation request is pending review by the administrator.</p>
            <?php else: ?>
              <button type="button" class="request-btn" id="sendRequestBtn" onclick="sendApprovalRequest()">📧 Send Reactivation Request</button>
            <?php endif; ?>
          </div>
          <div class="back-link"><a href="<?= base_url('login'); ?>">← Back to Login</a></div>
          <input type="hidden" id="suspended_user_id" value="<?= esc($suspended_user_id ?? ''); ?>">
          <input type="hidden" id="suspended_email" value="<?= esc($suspended_email ?? ''); ?>">
          <input type="hidden" id="suspended_name" value="<?= esc($suspended_name ?? ''); ?>">
        <?php else: ?>
          <form action="<?= site_url('login'); ?>" method="post">
            <?= csrf_field() ?>

            <!-- Updated Email/Username input -->
            <div class="form-group">
              <label>Email Address or Username</label>
              <div class="input-wrapper">
                <span class="input-icon"></span>
                <input type="text" name="email" placeholder="Enter your email or username" required value="<?= old('email') ?>">
              </div>
            </div>

            <div class="form-group">
              <label>Password</label>
              <div class="input-wrapper">
                <span class="input-icon"></span>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <span class="toggle-password" onclick="togglePassword()">
                  <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                </span>
              </div>
            </div>

            <button type="submit" class="submit-btn">Sign In</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eye-icon');
      if(passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;
      } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
      }
    }

    function sendApprovalRequest() {
      const userId = document.getElementById('suspended_user_id').value;
      const email = document.getElementById('suspended_email').value;
      const fullName = document.getElementById('suspended_name').value;
      const btn = document.getElementById('sendRequestBtn');
      btn.disabled = true; btn.textContent = 'Sending...';
      fetch('<?= base_url('auth/send-approval-request'); ?>', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: new URLSearchParams({'user_id':userId,'email':email,'full_name':fullName})
      })
      .then(res=>res.json()).then(data=>{
        if(data.success){ document.getElementById('requestSection').innerHTML = `<p class="success">${data.message}</p><p class="warning" style="margin-top: 15px;">Your reactivation request is pending review by the administrator.</p>`; }
        else { alert('Error: '+data.message); btn.disabled=false; btn.textContent='📧 Send Reactivation Request'; }
      })
      .catch(err=>{ console.error(err); alert('Failed to send request. Please try again.'); btn.disabled=false; btn.textContent='📧 Send Reactivation Request'; });
    }
  </script>
</body>
</html>
