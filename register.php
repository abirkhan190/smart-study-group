<?php
require 'config.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = trim($_POST['name']); $email = trim($_POST['email']); $pw = $_POST['password'];
  if (strlen($pw) < 6) $err = "Password must be at least 6 characters";
  else {
    $c = $pdo->prepare("SELECT id FROM users WHERE email=?"); $c->execute([$email]);
    if ($c->fetch()) $err = "Email already registered";
    else {
      $pdo->prepare("INSERT INTO users(name,email,password,security_answer) VALUES(?,?,?,?)")
          ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT), password_hash(strtolower(trim($_POST['answer'])), PASSWORD_DEFAULT)]);
      header("Location: index.php"); exit;
    }
  }
}
?>
<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Register</title>
<link rel="stylesheet" href="style.css"></head>
<body class="authbg">
<form class="authcard" method="post">
  <div class="logo">📚</div><h1>Create Account</h1><p>Join Smart Study Group</p>
  <?php if($err): ?><div class="error"><?= e($err) ?></div><?php endif; ?>
  <input type="text" name="name" placeholder="Name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password (min 6)" required>
  <input type="text" name="answer" placeholder="Recovery answer: your best friend's name" required>
  <button class="btn green full">Register</button>
  <p>Already have an account? <a href="index.php">Login</a></p>
</form></body></html>
