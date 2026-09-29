<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

$page_title = 'My Matches';
$active_nav = 'matches';
require 'includes/header.php';

$all = all_matches();
$upcoming = array_filter($all, fn($m) => $m['joined_by_me']);
$created = array_filter($all, fn($m) => $m['is_created']);
?>

<div style="padding: 20px 20px 32px;">
    <h1 class="display">My Matches</h1>

    <div class="section-title" style="margin-top:24px; color:var(--ink-dim);">Upcoming</div>
    <?php if (empty($upcoming)): ?>
        <div class="empty-row">No matches joined yet — go find one you like.</div>
    <?php endif; ?>
    <?php foreach ($upcoming as $m): $st = match_status($m['joined'], $m['max'], $m['min']); ?>
        <a href="match.php?id=<?= urlencode($m['id']) ?>" class="panel match-history-card" style="display:flex; align-items:center; gap:12px; margin-bottom:10px; text-decoration:none;">
            <img src="<?= e($m['img']) ?>" alt="" style="width:56px; height:56px; border-radius:10px; object-fit:cover; flex-shrink:0;">
            <div style="flex:1; min-width:0;">
                <div style="font-weight:700; font-size:14px;"><?= e($m['name']) ?></div>
                <div class="dim" style="font-size:12px;"><?= $m['joined'] ?>/<?= $m['max'] ?> players · <?= e($m['date']) ?>, <?= e(explode(' – ', $m['time'])[0]) ?></div>
            </div>
            <?php render_badge($st['label'], $st['tone']); ?>
        </a>
    <?php endforeach; ?>

    <div class="section-title" style="margin-top:28px; color:var(--ink-dim);">My Created Matches</div>
    <?php if (empty($created)): ?>
        <div class="empty-row">You haven't created a match yet.</div>
    <?php endif; ?>
    <?php foreach ($created as $m):
        $needed = max(0, $m['min'] - $m['joined']);
        $st = match_status($m['joined'], $m['max'], $m['min']);
    ?>
        <a href="match.php?id=<?= urlencode($m['id']) ?>" class="panel match-history-card" style="display:block; margin-bottom:10px; text-decoration:none;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div style="font-weight:700; font-size:14px;"><?= e($m['name']) ?></div>
                <?php render_badge($st['label'], $st['tone']); ?>
            </div>
            <div class="dim" style="font-size:12px; margin-top:4px;"><?= $m['joined'] ?> / <?= $m['max'] ?> players · Needs <?= $needed ?> more players</div>
        </a>
        <a href="delete_match.php?id=<?= urlencode($m['id']) ?>" class="btn btn-danger btn-sm btn-block" style="margin-top:-2px; margin-bottom:10px;">Delete Match</a>
    <?php endforeach; ?>
</div>

<?php require 'includes/footer.php'; ?>
