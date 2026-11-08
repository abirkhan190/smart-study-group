<?php
require 'config.php';
$msg = ''; $ok = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $s = $pdo->prepare("SELECT id,security_answer FROM users WHERE email=?");
  $s->execute([trim($_POST['email'])]); $u = $s->fetch();
  if (strlen($_POST['password']) < 6) $msg = "Password must be at least 6 characters";
  elseif ($u && $u['security_answer'] && password_verify(strtolower(trim($_POST['answer'])), $u['security_answer'])) {
    $pdo->prepare("UPDATE users SET password=? WHERE id=?")
        ->execute([password_hash($_POST['password'], PASSWORD_DEFAULT), $u['id']]);
    $ok = true; $msg = "Password changed. You can login now.";
  } else $msg = "Email or recovery answer is incorrect";
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Forgot Password</title>
<link rel="stylesheet" href="style.css"></head>
<body class="authbg">
<form class="authcard" method="post">
  <div class="logo">🔑</div><h1>Reset Password</h1><p>Answer your recovery question</p>
  <?php if($msg): ?><div class="<?= $ok?'success':'error' ?>"><?= e($msg) ?></div><?php endif; ?>
  <input type="email" name="email" placeholder="Email" required>
  <input type="text" name="answer" placeholder="Recovery answer: your best friend's name" required>
  <input type="password" name="password" placeholder="New password (min 6)" required>
  <button class="btn blue full">Change Password</button>
  <p><a href="index.php">Back to Login</a></p>
</form></body></html>
