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

$page_title = 'Success';
$show_nav = false;
require 'includes/header.php';
?>

<div style="padding: 50px 20px 40px;" class="center">
    <div class="check-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="#0A0E14" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
    </div>
    <h1 class="display" style="font-size:28px;">You're In ⚽</h1>
    <p class="dim" style="margin-top:6px; font-size:14px;">Your spot has been reserved.</p>

    <div class="panel" style="margin-top:28px; text-align:left;">
        <div class="display" style="font-size:17px;"><?= e($m['name']) ?></div>
        <div class="dim" style="font-size:13px; margin-top:10px; display:flex; flex-direction:column; gap:6px;">
            <span>📅 <?= e($m['date']) ?></span>
            <span>🕗 <?= e($m['time']) ?></span>
            <span>📍 <?= e($m['field']) ?></span>
        </div>
        <div class="hairline"></div>
        <?php render_meter($m['joined'], $m['max'], $m['min']); ?>
    </div>

    <div style="display:flex; gap:12px; margin-top:24px;">
        <a href="match.php?id=<?= urlencode($m['id']) ?>" class="btn btn-primary" style="flex:1;">View Match</a>
        <a href="index.php" class="btn btn-ghost" style="flex:1;">📅 Add to Calendar</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
