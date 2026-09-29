<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$m = find_match($id);

if (!$m) {
    header('Location: matches.php');
    exit;
}

$page_title = 'Match Published';
$show_nav = false;
require 'includes/header.php';

$link = 'lengball.app/m/' . strtolower(str_replace(' ', '-', $m['name']));
?>

<div style="padding: 40px 20px 40px;" class="center">
    <div style="font-size:48px;">⚽</div>
    <h1 class="display" style="margin-top:8px;">Your match is live!</h1>
    <p class="dim" style="margin-top:4px; font-size:14px;">Now you just need players.</p>

    <div class="panel" style="margin-top:24px; text-align:left;">
        <div style="font-weight:700;"><?= e($m['name']) ?></div>
        <div class="dim" style="font-size:12px; margin-top:4px;"><?= e($m['date']) ?> · <?= e($m['time']) ?> · <?= e($m['field']) ?></div>
        <div style="margin-top:12px;">
            <?php render_meter(0, $m['max'], $m['min']); ?>
        </div>
    </div>

    <p class="dim" style="font-size:12px; line-height:1.6; margin-top:20px; background: var(--bg-panel-2); border:1px solid var(--line); border-radius:12px; padding:12px;">
        Need players? Share your match with friends or the community.
    </p>

    <div class="share-grid" style="margin-top:16px;">
        <button class="share-btn" onclick="copyLink('Copy Link')">📋<span>Copy Link</span></button>
        <button class="share-btn" onclick="copyLink('Telegram')">✈️<span>Telegram</span></button>
        <button class="share-btn" onclick="copyLink('Facebook')">📘<span>Facebook</span></button>
        <button class="share-btn" onclick="copyLink('WhatsApp')">💬<span>WhatsApp</span></button>
    </div>
    <div id="share-msg" class="dim" style="font-size:12px; margin-top:8px; min-height:16px;"></div>

    <div style="text-align:left; margin-top:20px;">
        <label class="field-label">Match Link</label>
        <div style="display:flex; align-items:center; gap:8px; background: var(--bg-panel); border:1px solid var(--line); border-radius:12px; padding:12px 14px;">
            <span class="mono dim" style="font-size:12px; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= e($link) ?></span>
            <span>📋</span>
        </div>
    </div>

    <a href="matches.php" class="btn btn-primary btn-lg btn-block" style="margin-top:24px;">Go to My Matches</a>
</div>

<script>
// Tiny bit of progressive-enhancement JS just for the "copy" feedback text.
// The page works fine without it — this only affects the little message below the buttons.
function copyLink(label) {
    var msg = document.getElementById('share-msg');
    msg.textContent = (label === 'Copy Link' ? 'Link copied' : label + ' share') + ' ✓';
    if (navigator.clipboard) {
        navigator.clipboard.writeText('<?= e($link) ?>').catch(function () {});
    }
}
</script>

<?php require 'includes/footer.php'; ?>
