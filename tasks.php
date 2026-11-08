<?php
require 'config.php'; auth(); $page = 'tasks'; $uid = $_SESSION['uid'];
$gs = $pdo->prepare("SELECT g.* FROM study_groups g JOIN group_members m ON m.group_id=g.id WHERE m.user_id=?");
$gs->execute([$uid]); $my = $gs->fetchAll();
$gid = (int)($_GET['gid'] ?? ($my[0]['id'] ?? 0));
$cur = null; foreach ($my as $g) if ($g['id'] == $gid) $cur = $g;
$leader = $cur && $cur['leader_id'] == $uid;
if ($cur && $_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['add']) && $leader)
    $pdo->prepare("INSERT INTO tasks(group_id,title,deadline) VALUES(?,?,?)")
        ->execute([$gid, trim($_POST['title']), $_POST['deadline']]);
  if (isset($_POST['update']) && $leader)
    $pdo->prepare("UPDATE tasks SET title=?, deadline=? WHERE id=? AND group_id=?")
        ->execute([trim($_POST['title']), $_POST['deadline'], (int)$_POST['update'], $gid]);
  if (isset($_POST['delete']) && $leader)
    $pdo->prepare("DELETE FROM tasks WHERE id=? AND group_id=?")->execute([(int)$_POST['delete'], $gid]);
  if (isset($_POST['toggle']))
    $pdo->prepare("UPDATE tasks SET status=IF(status='Pending','Completed','Pending') WHERE id=? AND group_id=?")
        ->execute([(int)$_POST['toggle'], $gid]);
  header("Location: tasks.php?gid=$gid"); exit;
}
$edit = (int)($_GET['edit'] ?? 0);
$tasks = [];
if ($cur) { $t = $pdo->prepare("SELECT * FROM tasks WHERE group_id=? ORDER BY deadline"); $t->execute([$gid]); $tasks = $t->fetchAll(); }
include 'header.php';
?>
<h1 class="title">Tasks</h1>
<form method="get" class="card row"><b>Select Group</b>
  <select name="gid">
    <?php foreach ($my as $g): ?>
      <option value="<?= $g['id'] ?>" <?= $g['id']==$gid?'selected':'' ?>><?= e($g['name']) ?></option>
    <?php endforeach; ?>
  </select><button class="btn blue">Show</button></form>
<?php if ($leader): ?>
<form method="post" class="card row">
  <input name="title" placeholder="Task title" required>
  <input type="date" name="deadline" required>
  <button name="add" value="1" class="btn blue">Add Task</button>
</form>
<?php endif; ?>
<?php foreach ($tasks as $t): ?>
<?php if ($leader && $t['id'] == $edit): ?>
<form method="post" class="card row">
  <input name="title" value="<?= e($t['title']) ?>" required>
  <input type="date" name="deadline" value="<?= e($t['deadline']) ?>" required>
  <button name="update" value="<?= $t['id'] ?>" class="btn green">Save</button>
  <a class="btn gray" href="tasks.php?gid=<?= $gid ?>">Cancel</a>
</form>
<?php else: ?>
<form method="post" class="card group">
  <div><h3><?= e($t['title']) ?></h3><small>Deadline: <?= e($t['deadline']) ?></small><br>
    <span class="b <?= $t['status']=='Completed'?'gr':'yl' ?>"><?= $t['status'] ?></span></div>
  <div class="actions">
    <button name="toggle" value="<?= $t['id'] ?>" class="btn blue">Toggle Status</button>
    <?php if ($leader): ?>
      <a class="btn gray" href="tasks.php?gid=<?= $gid ?>&edit=<?= $t['id'] ?>">Edit</a>
      <button name="delete" value="<?= $t['id'] ?>" class="btn red">Delete</button>
    <?php endif; ?>
  </div>
</form>
<?php endif; ?>
<?php endforeach; ?>
<?php if (!$my): ?><p class="muted">Join a group first to see tasks.</p><?php endif; ?>
<?php include 'footer.php'; ?>
