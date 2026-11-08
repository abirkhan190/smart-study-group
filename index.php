<?php
require 'config.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $s = $pdo->prepare("SELECT * FROM users WHERE email=?");
  $s->execute([trim($_POST['email'])]);
  $u = $s->fetch();
  if ($u && password_verify($_POST['password'], $u['password'])) {
    $_SESSION['uid'] = $u['id']; $_SESSION['name'] = $u['name'];
    header("Location: dashboard.php"); exit;
  }
  $err = "Invalid email or password";
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Login</title>
<link rel="stylesheet" href="style.css"></head>
<body class="authbg">
<form class="authcard" method="post">
  <div class="logo">📚</div><h1>Smart Study Group</h1><p>Login to your account</p>
  <?php if($err): ?><div class="error"><?= e($err) ?></div><?php endif; ?>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button class="btn blue full">Login</button>
  <p>No account? <a href="register.php">Register</a></p>
  <p><a href="forgot.php">Forgot password?</a></p>
</form></body></html>
