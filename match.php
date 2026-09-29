<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$m = find_match($id);

if (!$m) {
    header('Location: discover.php');
    exit;
}

$page_title = $m['name'];
$hide_topbar = true;
$active_nav = 'discover';
require 'includes/header.php';

$st = match_status($m['joined'], $m['max'], $m['min']);
$full = $m['joined'] >= $m['max'];
$spots_left = max(0, $m['max'] - $m['joined']);
$colors = ['#C6F135','#E8A93A','#34D399','#60A5FA','#F472B6','#FB923C','#A78BFA'];
?>

<div class="detail-hero">
    <img src="<?= e($m['img']) ?>" alt="">
    <button class="back-btn" onclick="history.back()">‹</button>
    <div class="hero-title">
        <span class="stripe"><span></span><span></span></span>
        <h1 class="display" style="font-size:24px;"><?= e($m['name']) ?></h1>
    </div>
</div>

<div style="padding: 18px 20px 180px;">
    <div class="match-meta" style="font-size:14px; color: var(--ink-soft); flex-wrap:wrap; gap: 14px; display:flex;">
        <span>📍 <?= e($m['field']) ?></span>
        <span>📅 <?= e($m['date']) ?></span>
        <span>🕗 <?= e($m['time']) ?></span>
    </div>
    <div class="price" style="font-size:18px; margin-top:8px;">$<?= money($m['price']) ?> per player</div>

    <div class="panel" style="margin-top: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
            <div class="section-title" style="margin:0;">Players</div>
            <span class="mono" style="font-size:12px; color: <?= $st['tone'] === 'bad' ? 'var(--bad)' : ($spots_left > 0 ? 'var(--warn)' : 'var(--ok)') ?>; font-weight:700;">
                <?= $spots_left > 0 ? $spots_left . ' spots remaining' : 'Match Full' ?>
            </span>
        </div>
        <div class="avatars">
            <?php foreach ($m['players'] as $i => $name): ?>
                <div class="avatar" style="background: <?= $colors[$i % count($colors)] ?>;"><?= e(strtoupper(substr($name, 0, 2))) ?></div>
            <?php endforeach; ?>
            <?php if ($m['joined'] > count($m['players'])): ?>
                <div class="avatar more">+<?= $m['joined'] - count($m['players']) ?></div>
            <?php endif; ?>
        </div>
        <?php render_meter($m['joined'], $m['max'], $m['min']); ?>
    </div>

    <div style="margin-top: 20px;">
        <div class="section-title">About this match</div>
        <p class="dim" style="font-size:14px; line-height:1.6;"><?= e($m['about']) ?></p>
    </div>

    <div class="grid-3" style="margin-top: 20px;">
        <div class="mini-stat"><div class="lbl">Skill Level</div><div class="num"><?= e($m['skill']) ?></div></div>
        <div class="mini-stat"><div class="lbl">Format</div><div class="num"><?= e($m['format']) ?></div></div>
        <div class="mini-stat"><div class="lbl">Duration</div><div class="num"><?= e($m['duration']) ?></div></div>
    </div>

    <?php if ($m['is_created'] && !is_admin()): ?>
        <a href="delete_match.php?id=<?= urlencode($m['id']) ?>" class="btn btn-danger btn-block" style="margin-top:24px;">Delete My Match</a>
    <?php endif; ?>
</div>

<div class="sticky-cta with-nav">
    <div class="sticky-cta-inner">
        <?php if (is_admin()): ?>
            <div class="btn btn-subtle btn-lg btn-block" style="text-align:center;">Owner view — joining disabled</div>
        <?php elseif ($m['joined_by_me']): ?>
            <a href="success.php?id=<?= urlencode($m['id']) ?>" class="btn btn-primary btn-lg btn-block">You're In — View Receipt</a>
        <?php elseif ($full): ?>
            <button class="btn btn-danger btn-lg btn-block" disabled>Match Full</button>
        <?php else: ?>
            <a href="payment.php?id=<?= urlencode($m['id']) ?>" class="btn btn-primary btn-lg btn-block">Join Match — $<?= money($m['price']) ?></a>
        <?php endif; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
