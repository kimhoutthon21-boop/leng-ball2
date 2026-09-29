<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

// Defaults for a fresh draft.
$defaults = [
    'date' => date('Y-m-d'), 'time_start' => '18:00', 'time_end' => '20:00',
    'field_id' => '', 'name' => '', 'format' => '7v7', 'players' => '14',
    'skill' => 'Casual', 'price' => '3.5',
];
$_SESSION['draft'] = array_merge($defaults, $_SESSION['draft']);

// If this is a form submission for the current step, merge it into the
// session draft and move on to the next step.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = isset($_POST['step']) ? (int)$_POST['step'] : 1;

    if ($step === 1) {
        $date = DateTime::createFromFormat('!Y-m-d', (string)($_POST['date'] ?? ''));
        $start = DateTime::createFromFormat('!H:i', (string)($_POST['time_start'] ?? ''));
        $end = DateTime::createFromFormat('!H:i', (string)($_POST['time_end'] ?? ''));
        $valid_date = $date && $date->format('Y-m-d') === $_POST['date'];
        $valid_start = $start && $start->format('H:i') === $_POST['time_start'];
        $valid_end = $end && $end->format('H:i') === $_POST['time_end'];

        if (!$valid_date || !$valid_start || !$valid_end || $end <= $start) {
            $_SESSION['flash'] = 'Choose a valid date and an end time after the start time.';
            header('Location: create.php?step=1');
            exit;
        }
    }

    foreach ($_POST as $key => $val) {
        if ($key !== 'step') $_SESSION['draft'][$key] = $val;
    }
    $next = min(4, $step + 1);
    header('Location: create.php?step=' . $next);
    exit;
}

$step = isset($_GET['step']) ? max(1, min(4, (int)$_GET['step'])) : 1;
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $step === 1) {
    unset($_SESSION['creator_paid']);
}
$d = $_SESSION['draft'];
$admin_field_id = $_SESSION['user']['field_id'] ?? '';
if (is_admin()) {
    $d['field_id'] = $admin_field_id;
    $_SESSION['draft']['field_id'] = $admin_field_id;
}

$page_title = 'Create a Match';
$active_nav = 'create';
$app_class = 'create-page';
require 'includes/header.php';

$step_labels = ['When', 'Where', 'Build', 'Publish'];
$selected_field = $d['field_id'] ? get_field($d['field_id']) : null;
$preview_date = DateTime::createFromFormat('!Y-m-d', (string)($d['date'] ?? ''));
$preview_start = DateTime::createFromFormat('!H:i', (string)($d['time_start'] ?? ''));
$preview_end = DateTime::createFromFormat('!H:i', (string)($d['time_end'] ?? ''));
$creator_paid = is_admin() || !empty($_SESSION['creator_paid']);
?>

<div class="create-shell" style="padding: 20px 20px 190px;">
    <h1 class="display">Create a Match</h1>

    <div class="wizard-steps">
        <?php foreach ($step_labels as $i => $label): $n = $i + 1; ?>
            <div class="wizard-step <?= $n <= $step ? 'done' : '' ?> <?= $n === $step ? 'current' : '' ?>">
                <div class="bar"></div>
                <div class="lbl"><?= $label ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($step === 1): ?>
        <form method="post" action="create.php">
            <input type="hidden" name="step" value="1">
            <h2 class="display" style="font-size:16px; margin-bottom:16px;">When are you playing?</h2>

            <div class="form-group">
                <label class="field-label">Date</label>
                <input type="date" name="date" class="input" value="<?= e($d['date']) ?>" required>
            </div>
            <div class="form-group">
                <div class="grid-2">
                    <div>
                        <label class="field-label" for="time-start">From</label>
                        <input id="time-start" type="time" name="time_start" class="input" value="<?= e($d['time_start'] ?? '18:00') ?>" required>
                    </div>
                    <div>
                        <label class="field-label" for="time-end">To</label>
                        <input id="time-end" type="time" name="time_end" class="input" value="<?= e($d['time_end'] ?? '20:00') ?>" required>
                    </div>
                </div>
            </div>

            <div class="sticky-cta with-nav"><div class="sticky-cta-inner">
                <button type="submit" class="btn btn-primary btn-block">Continue →</button>
            </div></div>
        </form>

    <?php elseif ($step === 2): ?>
        <form method="post" action="create.php">
            <input type="hidden" name="step" value="2">
            <h2 class="display" style="font-size:16px; margin-bottom:16px;">Where are you playing?</h2>
            <?php foreach (get_fields() as $f): ?>
                <?php if (is_admin() && $f['id'] !== $admin_field_id) continue; ?>
                <?php render_field_card($f, $d['field_id'] === $f['id']); ?>
            <?php endforeach; ?>
            <noscript><button type="submit" class="btn btn-subtle btn-block" style="margin-top:8px;">Confirm Field</button></noscript>

            <div class="sticky-cta with-nav"><div class="sticky-cta-inner">
                <a href="create.php?step=1" class="btn btn-ghost">‹ Back</a>
                <button type="submit" class="btn btn-primary" style="flex:1;" <?= !$d['field_id'] ? 'disabled' : '' ?>>Continue →</button>
            </div></div>
        </form>

    <?php elseif ($step === 3): ?>
        <form method="post" action="create.php">
            <input type="hidden" name="step" value="3">
            <h2 class="display" style="font-size:16px; margin-bottom:16px;">Build your match</h2>

            <div class="form-group">
                <label class="field-label">Match name</label>
                <input type="text" name="name" class="input" placeholder="e.g. Friday Night Football" value="<?= e($d['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="field-label">Format</label>
                <div class="choice-row">
                    <?php foreach (['5v5','7v7','11v11'] as $f): ?>
                        <input class="choice-input" type="radio" name="format" id="fmt-<?= $f ?>" value="<?= $f ?>" <?= $d['format'] === $f ? 'checked' : '' ?>>
                        <label class="choice-btn" for="fmt-<?= $f ?>"><?= $f ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="field-label">Players needed</label>
                <div class="choice-row">
                    <?php foreach ([10,12,14,16] as $n): ?>
                        <input class="choice-input" type="radio" name="players" id="pl-<?= $n ?>" value="<?= $n ?>" <?= (int)$d['players'] === $n ? 'checked' : '' ?>>
                        <label class="choice-btn" for="pl-<?= $n ?>"><?= $n ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="field-label">Skill level</label>
                <select name="skill" class="input">
                    <?php foreach (['Beginner','Casual','Intermediate','Advanced'] as $s): ?>
                        <option <?= $d['skill'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="field-label">Price per player (USD)</label>
                <input type="number" step="0.5" min="0" name="price" class="input" value="<?= e($d['price']) ?>">
            </div>

            <div class="sticky-cta with-nav"><div class="sticky-cta-inner">
                <a href="create.php?step=2" class="btn btn-ghost">‹ Back</a>
                <button type="submit" class="btn btn-primary" style="flex:1;">Continue →</button>
            </div></div>
        </form>

    <?php elseif ($step === 4): ?>
        <h2 class="display" style="font-size:16px; margin-bottom:16px;">Preview &amp; publish</h2>
        <div class="match-card">
            <img src="<?= e($selected_field['img'] ?? '') ?>" alt="" style="height:130px; width:100%; object-fit:cover;">
            <div class="match-body">
                <div class="name"><?= e($d['name'] ?: 'Untitled Match') ?></div>
                <div class="match-meta">📍 <?= e($selected_field['name'] ?? '') ?></div>
                <div class="match-meta">📅 <?= e($preview_date ? $preview_date->format('D, M j') : $d['date']) ?> &nbsp; 🕗 <?= e($preview_start && $preview_end ? $preview_start->format('g:i A') . ' – ' . $preview_end->format('g:i A') : '') ?></div>
                <div class="grid-3" style="margin: 12px 0;">
                    <div class="mini-stat"><div class="lbl">Format</div><div class="num"><?= e($d['format']) ?></div></div>
                    <div class="mini-stat"><div class="lbl">Players</div><div class="num"><?= e($d['players']) ?></div></div>
                    <div class="mini-stat"><div class="lbl">Skill</div><div class="num"><?= e($d['skill']) ?></div></div>
                </div>
                <div class="price" style="font-size:16px; padding-bottom: 8px;">$<?= money($d['price']) ?> per player</div>
            </div>
        </div>

        <form method="post" action="create_submit.php">
            <div class="sticky-cta with-nav"><div class="sticky-cta-inner">
                <a href="create.php?step=3" class="btn btn-ghost">‹ Back</a>
                <?php if ($creator_paid): ?>
                    <button type="submit" class="btn btn-primary" style="flex:1;">Publish Match<?= is_admin() ? ' — Free for owners' : '' ?></button>
                <?php else: ?>
                    <a href="create_payment.php" class="btn btn-primary" style="flex:1; text-align:center;">Pay to Publish</a>
                <?php endif; ?>
            </div></div>
        </form>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
