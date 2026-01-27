<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CredentiaTAU Login</title>
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

  .login-container {
    max-width: 700px;
    width: 100%;
    background: white;
    padding: 70px 60px;
    border-radius: 24px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.2);
  }

  .logo {
    width: 160px;
    height: 160px;
    display: block;
    margin: 0 auto 25px;
  }

  h2 {
    color: #1d6b37;
    text-align: center;
    margin-bottom: 15px;
    font-size: 42px;
    font-weight: 700;
    letter-spacing: -0.5px;
  }

  .subtitle {
    text-align: center;
    color: #555;
    margin-bottom: 45px;
    font-size: 19px;
  }

  label {
    display: block;
    color: #333;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 18px;
  }

  input[type="email"], input[type="password"] {
    width: 100%;
    padding: 20px;
    border: 2px solid #ddd;
    border-radius: 12px;
    margin-bottom: 28px;
    font-size: 18px;
    transition: border-color 0.3s, box-shadow 0.3s;
  }

  input[type="email"]:focus, input[type="password"]:focus {
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
    letter-spacing: 0.5px;
  }

  button:hover {
    background: #155028;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(29, 107, 55, 0.3);
  }

  button:active {
    transform: translateY(0);
  }

  .error {
    color: #dc3545;
    background: #f8d7da;
    border: 2px solid #f5c6cb;
    text-align: center;
    margin-bottom: 25px;
    padding: 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
  }

  .success {
    color: #155724;
    background: #d4edda;
    border: 2px solid #c3e6cb;
    text-align: center;
    margin-bottom: 25px;
    padding: 16px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
  }

  .demo {
    margin-top: 35px;
    background: #ecf8ed;
    border-left: 6px solid #1d6b37;
    padding: 24px;
    font-size: 16px;
    border-radius: 8px;
    line-height: 1.8;
  }

  .demo strong {
    display: block;
    margin-bottom: 12px;
    color: #1d6b37;
    font-size: 18px;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .login-container {
      padding: 50px 40px;
      max-width: 100%;
    }
    
    h2 {
      font-size: 36px;
    }
    
    .logo {
      width: 130px;
      height: 130px;
    }

    input[type="email"], input[type="password"] {
      padding: 18px;
      font-size: 16px;
    }

    button {
      padding: 20px;
      font-size: 18px;
    }
  }

  @media (max-width: 480px) {
    .login-container {
      padding: 40px 30px;
    }
    
    h2 {
      font-size: 30px;
    }
    
    .logo {
      width: 110px;
      height: 110px;
    }

    .subtitle {
      font-size: 16px;
    }
  }
</style>
</head>
<body>
  <div class="login-container">
    <img src="<?= base_url('assets/img/TAU.png'); ?>" class="logo" alt="TAU Logo">
    <h2>CredentiaTAU</h2>
    <p class="subtitle">Academic Records Management System</p>

    <?php if (session()->getFlashdata('error')): ?>
      <p class="error"><?= session()->getFlashdata('error'); ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
      <p class="success"><?= session()->getFlashdata('success'); ?></p>
    <?php endif; ?>

    <form action="<?= site_url('login'); ?>" method="post">
      <?= csrf_field() ?>
      
      <label>Email</label>
      <input type="email" name="email" placeholder="Enter your email" required value="<?= old('email') ?>">
      
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your password" required>
      
      <button type="submit">Sign In</button>
    </form>

    <div class="demo">
      <strong>Login Credentials:</strong>
      Super Admin: artryry6@gmail.com / superadmin123
    </div>
  </div>
</body>
</html>