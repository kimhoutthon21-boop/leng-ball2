<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

$page_title = 'Discover';
$active_nav = 'discover';
require 'includes/header.php';

$date_filter  = isset($_GET['date']) ? $_GET['date'] : 'All';
$skill_filter = isset($_GET['skill']) ? $_GET['skill'] : 'All';

$matches = all_matches();
$filtered = array_filter($matches, function ($m) use ($date_filter, $skill_filter) {
    $date_ok = $date_filter === 'All'
        || ($date_filter === 'Today' && $m['date'] === 'Fri, Aug 28')
        || ($date_filter === 'Tomorrow' && $m['date'] === 'Sat, Aug 29')
        || ($date_filter === 'This Week');
    $skill_ok = $skill_filter === 'All' || $m['skill'] === $skill_filter;
    return $date_ok && $skill_ok;
});

function chip_url($date, $skill) {
    return '?date=' . urlencode($date) . '&skill=' . urlencode($skill);
}
?>

<div style="padding: 20px 20px 0;">
    <h1 class="display">Find a Match</h1>
    <p class="dim" style="font-size:12px; margin-top:4px;"><?= count($filtered) ?> matches open near Phnom Penh</p>

    <div class="chip-row" style="margin-top:16px;">
        <?php foreach (['All','Today','Tomorrow','This Week'] as $d): ?>
            <a class="chip <?= $date_filter === $d ? 'active' : '' ?>" href="<?= chip_url($d, $skill_filter) ?>"><?= e($d) ?></a>
        <?php endforeach; ?>
    </div>
    <div class="chip-row">
        <?php foreach (['All','Beginner','Casual','Intermediate','Advanced'] as $s): ?>
            <a class="chip <?= $skill_filter === $s ? 'active' : '' ?>" href="<?= chip_url($date_filter, $s) ?>"><?= e($s) ?></a>
        <?php endforeach; ?>
        <span class="chip">Distance ▾</span>
        <span class="chip">Price ▾</span>
        <span class="chip">Players ▾</span>
    </div>
</div>

<div style="padding: 16px 20px 32px;">
    <?php if (empty($filtered)): ?>
        <div class="empty-row">No matches match those filters — try widening your search.</div>
    <?php else: ?>
        <div class="match-list">
            <?php foreach ($filtered as $m): render_match_card($m); endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
