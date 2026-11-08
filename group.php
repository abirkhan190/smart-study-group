<?php
require 'config.php'; auth(); $page = 'groups'; $uid = $_SESSION['uid']; $gid = (int)($_GET['gid'] ?? 0);
$g = $pdo->prepare("SELECT g.*, u.name AS leader FROM study_groups g
  JOIN users u ON u.id=g.leader_id JOIN group_members m ON m.group_id=g.id AND m.user_id=? WHERE g.id=?");
$g->execute([$uid, $gid]); $g = $g->fetch();
if (!$g) { header("Location: groups.php"); exit; }
$leader = $g['leader_id'] == $uid;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $text = trim($_POST['text'] ?? '');
  if (isset($_POST['notice']) && $leader && $text !== '')
    $pdo->prepare("INSERT INTO notices(group_id,user_id,message) VALUES(?,?,?)")->execute([$gid, $uid, $text]);
  if (isset($_POST['delnotice']) && $leader)
    $pdo->prepare("DELETE FROM notices WHERE id=? AND group_id=?")->execute([(int)$_POST['delnotice'], $gid]);
  if (isset($_POST['chat']) && $text !== '')
    $pdo->prepare("INSERT INTO messages(group_id,user_id,message) VALUES(?,?,?)")->execute([$gid, $uid, mb_substr($text, 0, 500)]);
  header("Location: group.php?gid=$gid"); exit;
}
$c = $pdo->prepare("SELECT SUM(status='Completed') d, COUNT(*) t FROM tasks WHERE group_id=?");
$c->execute([$gid]); $c = $c->fetch();
$pct = $c['t'] ? round($c['d'] * 100 / $c['t']) : 0;
$n = $pdo->prepare("SELECT n.*, u.name FROM notices n JOIN users u ON u.id=n.user_id WHERE n.group_id=? ORDER BY n.id DESC");
$n->execute([$gid]); $notices = $n->fetchAll();
$m = $pdo->prepare("SELECT * FROM (SELECT m.id, m.user_id, m.message, m.created_at, u.name
  FROM messages m JOIN users u ON u.id=m.user_id WHERE m.group_id=? ORDER BY m.id DESC LIMIT 50) x ORDER BY id");
$m->execute([$gid]); $msgs = $m->fetchAll();
include 'header.php';
?>
<h1 class="title"><?= e($g['name']) ?></h1>
<p class="muted">📚 <?= e($g['subject']) ?> · 👑 Leader: <?= e($g['leader']) ?></p>

<div class="card"><h3>📊 Progress</h3>
  <div class="bar" style="width:100%"><div style="width:<?= $pct ?>%"></div></div>
  <small><?= (int)$c['d'] ?> of <?= (int)$c['t'] ?> tasks completed (<?= $pct ?>%)</small>
  <p><a class="btn blue" href="tasks.php?gid=<?= $gid ?>">Go to Tasks</a>
     <a class="btn gray" href="members.php?gid=<?= $gid ?>">Members</a></p></div>

<div class="card"><h3>📢 Notice Board</h3>
  <?php if ($leader): ?>
  <form method="post" class="row"><input name="text" placeholder="Write a notice for the group..." required>
    <button name="notice" value="1" class="btn blue">Post</button></form>
  <?php endif; ?>
  <?php if (!$notices): ?><p class="muted">No notices yet.</p><?php endif; ?>
  <?php foreach ($notices as $x): ?>
  <form method="post" class="notice">
    <div><?= e($x['message']) ?><br><small class="muted"><?= e($x['name']) ?> · <?= e($x['created_at']) ?></small></div>
    <?php if ($leader): ?><button name="delnotice" value="<?= $x['id'] ?>" class="btn red">Delete</button><?php endif; ?>
  </form>
  <?php endforeach; ?>
</div>

<div class="card"><h3>💬 Group Chat</h3>
  <div class="chat">
    <?php if (!$msgs): ?><p class="muted">No messages yet. Say hi!</p><?php endif; ?>
    <?php foreach ($msgs as $x): ?>
      <div class="msg <?= $x['user_id'] == $uid ? 'me' : '' ?>"><small><b><?= e($x['name']) ?></b> · <?= e($x['created_at']) ?></small><br><?= e($x['message']) ?></div>
    <?php endforeach; ?>
  </div>
  <form method="post" class="row" style="margin-top:12px"><input name="text" placeholder="Type a message..." required maxlength="500">
    <button name="chat" value="1" class="btn green">Send</button>
    <a class="btn gray" href="group.php?gid=<?= $gid ?>">Refresh</a></form>
</div>
<?php include 'footer.php'; ?>
