<?php
require 'config.php'; auth(); $page = 'dashboard'; $uid = $_SESSION['uid'];
function one($pdo, $sql, $p){ $s = $pdo->prepare($sql); $s->execute($p); return $s->fetchColumn(); }
$total   = one($pdo, "SELECT COUNT(*) FROM study_groups", []);
$joined  = one($pdo, "SELECT COUNT(*) FROM group_members WHERE user_id=?", [$uid]);
$leading = one($pdo, "SELECT COUNT(*) FROM study_groups WHERE leader_id=?", [$uid]);
$q = "SELECT COUNT(*) FROM tasks t JOIN group_members m ON m.group_id=t.group_id
      WHERE m.user_id=? AND t.status=?";
$pending = one($pdo, $q, [$uid, 'Pending']);
$done    = one($pdo, $q, [$uid, 'Completed']);
$ds = $pdo->prepare("SELECT t.title,t.deadline,g.name AS gname FROM tasks t
  JOIN group_members m ON m.group_id=t.group_id JOIN study_groups g ON g.id=t.group_id
  WHERE m.user_id=? AND t.status='Pending' AND t.deadline <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)
  ORDER BY t.deadline");
$ds->execute([$uid]); $due = $ds->fetchAll();
include 'header.php';
?>
<h1 class="title">Welcome back, <?= e($_SESSION['name']) ?>! 👋</h1>
<p class="muted">Here's your study overview</p>
<div class="grid3">
  <div class="stat blue"><h2><?= $total ?></h2>Total Groups</div>
  <div class="stat green"><h2><?= $joined ?></h2>Joined Groups</div>
  <div class="stat purple"><h2><?= $leading ?></h2>Groups Leading</div>
</div>
<div class="grid2">
  <div class="card side yellow"><h2><?= $pending ?></h2>⏳ Pending Tasks</div>
  <div class="card side lime"><h2><?= $done ?></h2>✅ Completed Tasks</div>
</div>
<div class="card"><h3>⏰ Due Soon</h3>
<?php if (!$due): ?><p class="muted">No tasks due soon. 🎉</p><?php endif; ?>
<?php foreach ($due as $t): $late = $t['deadline'] < date('Y-m-d'); ?>
  <div class="due <?= $late ? 'overdue' : '' ?>"><b><?= e($t['title']) ?></b> — <?= e($t['gname']) ?>
    <?php $days = (int)((strtotime($t['deadline']) - strtotime(date('Y-m-d'))) / 86400); ?>
    <span><?= $late ? 'Overdue: ' . e($t['deadline']) : ($days == 0 ? 'Due today' : ($days == 1 ? 'Due tomorrow' : "Due in $days days")) ?></span></div>
<?php endforeach; ?></div>
<div class="card"><h3>📌 About This System</h3>
<p>Smart Study Group Management System is a web-based platform that helps students create study groups, collaborate with peers, and manage tasks in one place.</p></div>
<div class="card"><h3>🚀 Key Features</h3><div class="grid2">
  <div class="feat">👤 <b>User Authentication</b><br>Secure registration and login.</div>
  <div class="feat">👥 <b>Group Management</b><br>Create, join and leave groups.</div>
  <div class="feat">📝 <b>Task Management</b><br>Add tasks with deadlines.</div>
  <div class="feat">📊 <b>Dashboard Overview</b><br>Groups, tasks and progress.</div>
  <div class="feat">🔍 <b>Search Groups</b><br>Find groups by name or subject.</div>
  <div class="feat">👑 <b>Leader Controls</b><br>Leaders manage members and tasks.</div>
</div></div>
<?php include 'footer.php'; ?>
