<?php
require 'config.php'; auth(); $page = 'groups'; $uid = $_SESSION['uid'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $a = $_POST['action'] ?? ''; $gid = (int)($_POST['gid'] ?? 0);
  if ($a == 'create' && trim($_POST['name']) && trim($_POST['subject'])) {
    $pdo->prepare("INSERT INTO study_groups(name,subject,leader_id) VALUES(?,?,?)")
        ->execute([trim($_POST['name']), trim($_POST['subject']), $uid]);
    $pdo->prepare("INSERT INTO group_members VALUES(?,?)")->execute([$pdo->lastInsertId(), $uid]);
  } elseif ($a == 'join') {
    $pdo->prepare("INSERT IGNORE INTO group_members VALUES(?,?)")->execute([$gid, $uid]);
  } elseif ($a == 'leave') {
    $pdo->prepare("DELETE FROM group_members WHERE group_id=? AND user_id=? 
      AND group_id NOT IN (SELECT id FROM study_groups WHERE leader_id=?)")->execute([$gid, $uid, $uid]);
  } elseif ($a == 'delete') {
    $pdo->prepare("DELETE FROM study_groups WHERE id=? AND leader_id=?")->execute([$gid, $uid]);
  }
  header("Location: groups.php"); exit;
}
$s = $pdo->prepare("SELECT g.*, u.name AS leader,
  (SELECT COUNT(*) FROM group_members WHERE group_id=g.id) AS members,
  (SELECT COUNT(*) FROM tasks WHERE group_id=g.id AND status='Pending') AS pending,
  (SELECT COUNT(*) FROM tasks WHERE group_id=g.id AND status='Completed') AS done,
  (SELECT COUNT(*) FROM group_members WHERE group_id=g.id AND user_id=?) AS joined
  FROM study_groups g JOIN users u ON u.id=g.leader_id WHERE g.name LIKE ? OR g.subject LIKE ? ORDER BY g.id DESC");
$q = trim($_GET['q'] ?? ''); $s->execute([$uid, "%$q%", "%$q%"]); $groups = $s->fetchAll();
include 'header.php';
?>
<h1 class="title">Study Groups</h1><p class="muted">Create or join study groups</p>
<form method="post" class="card row">
  <input type="hidden" name="action" value="create">
  <b>＋ Create New Group</b>
  <input name="name" placeholder="Group Name" required>
  <input name="subject" placeholder="Subject Name" required>
  <button class="btn blue">Create</button>
</form>
<form method="get" class="card row"><input name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="🔍 Search groups by name or subject..."><button class="btn blue">Search</button></form>
<?php foreach ($groups as $g): ?>
<div class="card group" data-text="<?= e(strtolower($g['name'].' '.$g['subject'])) ?>">
  <div>
    <h3><?= e($g['name']) ?></h3>
    <div class="sub">📚 <?= e($g['subject']) ?></div>
    <small>👑 Leader: <?= e($g['leader']) ?></small>
    <div class="badges"><span class="b bl">👥 <?= $g['members'] ?> Members</span>
      <span class="b yl">⏳ <?= $g['pending'] ?> Pending</span>
      <span class="b gr">✅ <?= $g['done'] ?> Done</span></div>
    <?php $tot = $g['pending'] + $g['done']; $p = $tot ? round($g['done'] * 100 / $tot) : 0; ?>
    <div class="bar"><div style="width:<?= $p ?>%"></div></div><small><?= $p ?>% complete</small>
  </div>
  <form method="post" class="actions">
    <input type="hidden" name="gid" value="<?= $g['id'] ?>">
    <a class="btn gray" href="members.php?gid=<?= $g['id'] ?>">View Members</a>
    <?php if ($g['joined']): ?><a class="btn blue" href="group.php?gid=<?= $g['id'] ?>">Open</a><?php endif; ?>
    <?php if ($g['leader_id'] == $uid): ?>
      <button name="action" value="delete" class="btn red">Delete Group</button>
    <?php elseif ($g['joined']): ?>
      <button name="action" value="leave" class="btn orange">Leave</button>
    <?php else: ?>
      <button name="action" value="join" class="btn green">Join</button>
    <?php endif; ?>
  </form>
</div>
<?php endforeach; ?>
<?php include 'footer.php'; ?>
