<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

$id = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (string)($_POST['match_id'] ?? '')
    : (string)($_GET['id'] ?? '');
$deleted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_SESSION['created'] as $index => $match) {
        if ($match['id'] === $id) {
            unset($_SESSION['created'][$index]);
            unset($_SESSION['extra'][$id], $_SESSION['joined'][$id]);
            $deleted = true;
            break;
        }
    }

    $_SESSION['created'] = array_values($_SESSION['created']);
    $_SESSION['flash'] = $deleted ? 'Match deleted.' : 'That match could not be deleted.';
    header('Location: matches.php');
    exit;
}

$m = find_match($id);
if (!$m || !$m['is_created']) {
    header('Location: matches.php');
    exit;
}

$page_title = 'Delete Match';
$show_nav = false;
require 'includes/header.php';
?>

<div style="padding: 20px 20px 40px;">
    <a href="match.php?id=<?= urlencode($m['id']) ?>" class="back-link">&lt; Back to match</a>
    <h1 class="display">Delete Match?</h1>
    <p class="dim" style="font-size:13px; line-height:1.6; margin-top:10px;">Review the details below before permanently removing this match.</p>

    <div class="panel" style="margin-top:20px; overflow:hidden; padding:0;">
        <img src="<?= e($m['img']) ?>" alt="" style="height:150px; width:100%; object-fit:cover;">
        <div style="padding:16px;">
            <div class="display" style="font-size:20px;"><?= e($m['name']) ?></div>
            <div class="hairline"></div>
            <div class="info-row"><span>Date</span><span class="v"><?= e($m['date']) ?></span></div>
            <div class="info-row"><span>Time</span><span class="v"><?= e($m['time']) ?></span></div>
            <div class="info-row"><span>Location</span><span class="v"><?= e($m['field']) ?>, <?= e($m['area']) ?></span></div>
            <div class="info-row"><span>Format</span><span class="v"><?= e($m['format']) ?></span></div>
            <div class="info-row"><span>Skill</span><span class="v"><?= e($m['skill']) ?></span></div>
            <div class="info-row"><span>Duration</span><span class="v"><?= e($m['duration']) ?></span></div>
            <div class="info-row"><span>Players</span><span class="v"><?= $m['joined'] ?> / <?= $m['max'] ?></span></div>
            <div class="info-row" style="margin-bottom:0;"><span>Price</span><span class="v price">$<?= money($m['price']) ?> per player</span></div>
        </div>
    </div>

    <div class="panel" style="margin-top:16px; border-color:rgba(248,113,113,0.4); color:var(--bad); font-size:13px; line-height:1.5;">
        This action cannot be undone. Players will no longer be able to join this match.
    </div>

    <div style="display:flex; gap:12px; margin-top:20px;">
        <a href="match.php?id=<?= urlencode($m['id']) ?>" class="btn btn-ghost" style="flex:1;">Keep Match</a>
        <form method="post" action="delete_match.php" style="flex:1;">
            <input type="hidden" name="match_id" value="<?= e($m['id']) ?>">
            <button type="submit" class="btn btn-danger btn-block">Delete Match</button>
        </form>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
