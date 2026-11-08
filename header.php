<?php auth(); ?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Smart Study Group</title>
<link rel="stylesheet" href="style.css"></head>
<body>
<nav class="nav">
  <div class="brand">📚 Smart Study Group</div>
  <div class="links">
    <a href="dashboard.php" class="<?= $page=='dashboard'?'active':'' ?>">Dashboard</a>
    <a href="groups.php" class="<?= $page=='groups'?'active':'' ?>">Groups</a>
    <a href="tasks.php" class="<?= $page=='tasks'?'active':'' ?>">Tasks</a>
    <span class="user"><b><?= e(strtoupper($_SESSION['name'][0])) ?></b> <?= e($_SESSION['name']) ?></span>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</nav>
<main class="container">
