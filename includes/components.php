<?php
// includes/components.php
// Plain functions that echo a chunk of HTML. Not fancy — just avoids
// retyping the same markup on every page.

function render_badge($label, $tone) {
    echo '<span class="badge tone-' . e($tone) . '">' . e($label) . '</span>';
}

function render_meter($joined, $max, $min) {
    $pct = $max > 0 ? min(100, ($joined / $max) * 100) : 0;
    $tickPct = $max > 0 ? ($min / $max) * 100 : 0;
    $st = match_status($joined, $max, $min);
    ?>
    <div class="meter-top">
        <div class="meter-count"><?= str_pad($joined, 2, '0', STR_PAD_LEFT) ?><span class="of">/ <?= str_pad($max, 2, '0', STR_PAD_LEFT) ?> players</span></div>
        <?php render_badge($st['label'], $st['tone']); ?>
    </div>
    <div class="meter-track">
        <div class="meter-fill" style="width: <?= $pct ?>%;"></div>
        <?php if ($min < $max): ?>
            <div class="meter-tick" style="left: <?= $tickPct ?>%;" title="Minimum to confirm"></div>
        <?php endif; ?>
    </div>
    <?php
}

function render_match_card($m) {
    $st = match_status($m['joined'], $m['max'], $m['min']);
    $full = $m['joined'] >= $m['max'];
    ?>
    <div class="match-card">
        <a href="match.php?id=<?= urlencode($m['id']) ?>">
            <div class="match-thumb">
                <img src="<?= e($m['img']) ?>" alt="">
                <span class="stripe"><span></span><span></span></span>
                <?php render_badge($st['label'], $st['tone']); ?>
            </div>
            <div class="match-body">
                <div class="name"><?= e($m['name']) ?></div>
                <div class="match-meta">📍 <?= e($m['field']) ?> · <?= $m['distance'] ?> km</div>
                <div class="match-meta">📅 <?= e($m['date']) ?> &nbsp; 🕗 <?= e(explode(' – ', $m['time'])[0]) ?></div>
            </div>
        </a>
        <div class="match-foot">
            <?php render_meter($m['joined'], $m['max'], $m['min']); ?>
            <div class="foot-row">
                <div class="price">$<?= money($m['price']) ?></div>
                <?php if (is_admin()): ?>
                    <span class="btn btn-subtle btn-sm">Owner view</span>
                <?php elseif ($m['joined_by_me']): ?>
                    <a href="match.php?id=<?= urlencode($m['id']) ?>" class="btn btn-subtle btn-sm">Joined ✓</a>
                <?php elseif ($full): ?>
                    <button class="btn btn-danger btn-sm" disabled>Match Full</button>
                <?php else: ?>
                    <a href="payment.php?id=<?= urlencode($m['id']) ?>" class="btn btn-primary btn-sm">Join Match</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

function render_field_card($f, $selected) {
    $cls = 'field-card' . ($selected ? ' selected' : '') . (!$f['available'] ? ' unavailable' : '');
    ?>
    <label class="<?= $cls ?>">
        <input type="radio" name="field_id" value="<?= e($f['id']) ?>" <?= $selected ? 'checked' : '' ?> <?= !$f['available'] ? 'disabled' : '' ?> style="display:none;" onchange="this.form.submit()">
        <img src="<?= e($f['img']) ?>" alt="">
        <div style="flex:1; min-width:0;">
            <div class="name"><?= e($f['name']) ?></div>
            <div class="area"><?= e($f['area']) ?></div>
            <div class="stars"><?= str_repeat('★', round($f['rating'])) ?> <span class="dim mono"><?= $f['rating'] ?></span></div>
            <div class="bottom">
                <span class="price mono"><?= '$' . $f['price'] ?>/hr</span>
                <span class="pill"><?= e($f['format']) ?></span>
            </div>
        </div>
    </label>
    <?php
}
