<?php
require_once '../config.php';

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            // Validate redirect is a safe relative URL (prevent open redirect)
            if (!$redirect || preg_match('/^https?:\/\//i', $redirect) || str_starts_with($redirect, '//')) {
                $redirect = 'index.php';
            }
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Mohon isi semua field.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — Index Computer</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="../../css/style.css?v=2.0.8">
  <script>
    (function(){
      var saved = localStorage.getItem('theme');
      var prefer = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
      var theme = saved || prefer;
      if(theme === 'light') document.documentElement.setAttribute('data-theme','light');
    })();
  </script>
</head>
<body>
<div class="admin-login-wrap" style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--bg);padding:20px;position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(59,130,246,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,0.04) 1px,transparent 1px);background-size:60px 60px;z-index:0;"></div>

  <div class="admin-login-card" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:48px 40px;width:100%;max-width:420px;position:relative;z-index:1;">
    <div style="text-align:center;margin-bottom:40px;">
      <div style="font-family:var(--font-display);font-size:28px;font-weight:800;margin-bottom:8px;">
        <span style="color:var(--accent);">Index</span> Admin
      </div>
      <div style="font-size:14px;color:var(--text-muted);">Masuk ke panel manajemen toko</div>
    </div>

    <?php if ($error): ?>
    <div class="alert error" style="display:block;margin-bottom:20px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-input" placeholder="admin" required autofocus>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn-submit" style="margin-top:8px;">Masuk →</button>
    </form>

    <div style="text-align:center;margin-top:24px;">
      <a href="../../index.php" style="font-size:13px;color:var(--text-muted);">← Kembali ke Website</a>
    </div>
  </div>
</div>
</body>
</html>