<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

if (empty($_SESSION['user'])) {
    header('Location: login.php?mode=signup');
    exit;
}

$page_title = 'Home';
$active_nav = 'home';
require 'includes/header.php';

$matches = all_matches();
$today = array_filter($matches, fn($m) => $m['date'] === 'Fri, Aug 28');
?>

<div class="hero">
    <div class="hero-glow"></div>
    <span class="stripe"><span></span><span></span></span>
    <h1 class="display"><?= is_admin() ? 'Keep your pitch busy.<br><span style="color: var(--lime);">Grow your game.</span>' : 'Find your game.<br><span style="color: var(--lime);">Find your team.</span>' ?></h1>
    <p><?= is_admin() ? 'Track field performance, manage bookings, and create matches for your football community.' : 'Join a football match near you, or create one and find the players you need.' ?></p>
    <div class="cta-row">
        <a href="discover.php" class="btn btn-primary">⚲ Find a Match</a>
        <a href="create.php" class="btn btn-ghost">+ Create a Match</a>
    </div>
</div>

<div style="padding: 0 20px;">
    <div class="stat-grid">
        <div class="stat-box"><div class="num">1,200+</div><div class="lbl">Players</div></div>
        <div class="stat-box"><div class="num">85+</div><div class="lbl">Matches</div></div>
        <div class="stat-box"><div class="num">12</div><div class="lbl">Fields</div></div>
    </div>
</div>

<div style="padding: 32px 20px 0;">
    <h2 class="display" style="display:flex; align-items:center; gap:8px;">🔥 Matches happening today</h2>
    <p class="dim" style="font-size:12px; margin: 4px 0 16px;">Friday, August 28 · Phnom Penh</p>
    <div class="match-list">
        <?php foreach ($today as $m): render_match_card($m); endforeach; ?>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
