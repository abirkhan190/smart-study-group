<?php
require 'config.php'; auth(); $page = 'groups'; $uid = $_SESSION['uid']; $gid = (int)($_GET['gid'] ?? 0);
$g = $pdo->prepare("SELECT * FROM study_groups WHERE id=?"); $g->execute([$gid]); $g = $g->fetch();
if (!$g) { header("Location: groups.php"); exit; }
$leader = $g['leader_id'] == $uid;
if ($leader && isset($_POST['remove']) && (int)$_POST['remove'] != $uid) {
  $pdo->prepare("DELETE FROM group_members WHERE group_id=? AND user_id=?")->execute([$gid, (int)$_POST['remove']]);
  header("Location: members.php?gid=$gid"); exit;
}
$m = $pdo->prepare("SELECT u.id,u.name,u.email FROM group_members gm JOIN users u ON u.id=gm.user_id WHERE gm.group_id=?");
$m->execute([$gid]);
include 'header.php';
?>
<h1 class="title">Members — <?= e($g['name']) ?></h1>
<?php foreach ($m->fetchAll() as $u): ?>
  <form method="post" class="card group">
    <div><b><?= e($u['name']) ?></b> <?= $u['id']==$g['leader_id'] ? '👑' : '' ?>
      <span class="muted"><?= e($u['email']) ?></span></div>
    <?php if ($leader && $u['id'] != $uid): ?>
      <button name="remove" value="<?= $u['id'] ?>" class="btn red">Remove</button>
    <?php endif; ?>
  </form>
<?php endforeach; ?>
<a class="btn gray" href="groups.php">← Back</a>
<?php include 'footer.php'; ?>
