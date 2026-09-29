<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';
$m = find_match($id);

if (is_admin()) {
    header('Location: match.php?id=' . urlencode($id));
    exit;
}
$show_qr = $_SERVER['REQUEST_METHOD'] === 'POST';
$qr_path = 'assets/payment-qr.png';
$has_qr = file_exists(__DIR__ . '/' . $qr_path);

if (!$m || $m['joined'] >= $m['max']) {
    header('Location: match.php?id=' . urlencode($id));
    exit;
}

$page_title = 'Reserve Spot';
$show_nav = false;
require 'includes/header.php';
?>

<div style="padding: 20px 20px 40px;">
    <a href="match.php?id=<?= urlencode($m['id']) ?>" class="back-link">‹ Back</a>
    <h1 class="display">Reserve Your Spot</h1>

    <div class="panel" style="margin-top: 20px;">
        <div style="font-weight:700;"><?= e($m['name']) ?></div>
        <div style="margin-top:12px;">
            <div class="info-row"><span>Date</span><span class="v"><?= e($m['date']) ?></span></div>
            <div class="info-row"><span>Time</span><span class="v"><?= e(explode(' – ', $m['time'])[0]) ?></span></div>
            <div class="info-row"><span>Location</span><span class="v"><?= e($m['field']) ?></span></div>
            <div class="hairline"></div>
            <div class="info-row" style="font-size:16px;"><span style="color:var(--ink); font-weight:700;">Price</span><span class="v price"><?= '$' . money($m['price']) ?></span></div>
        </div>
    </div>

    <p class="dim" style="font-size:12px; line-height:1.6; margin-top:16px; background: var(--bg-panel-2); border:1px solid var(--line); border-radius:12px; padding:12px;">
        Your payment reserves your spot and helps prevent no-shows.
    </p>

    <?php if ($show_qr): ?>
        <?php
        $qr_size = 21;
        $finder_starts = [[0, 0], [$qr_size - 7, 0], [0, $qr_size - 7]];
        ?>
        <div class="qr-payment panel" style="margin-top:24px; text-align:center;">
            <div class="section-title" style="margin-top:0;">Scan to Pay</div>
            <p class="dim" style="font-size:12px; margin:8px 0 16px;">Scan this code with your banking app.</p>
            <?php if ($has_qr): ?>
                <img class="fake-qr" src="<?= e($qr_path) ?>?v=<?= filemtime(__DIR__ . '/' . $qr_path) ?>" alt="Payment QR code">
            <?php else: ?>
            <svg class="fake-qr" viewBox="0 <?= $qr_size ?> <?= $qr_size ?>" role="img" aria-label="Demo QR payment code">
                <rect width="<?= $qr_size ?>" height="<?= $qr_size ?>" fill="#fff"/>
                <?php for ($y = 0; $y < $qr_size; $y++): ?>
                    <?php for ($x = 0; $x < $qr_size; $x++):
                        $finder = false;
                        foreach ($finder_starts as $start) {
                            $distance_x = $x - $start[0];
                            $distance_y = $y - $start[1];
                            if ($distance_x >= 0 && $distance_x < 7 && $distance_y >= 0 && $distance_y < 7) {
                                $finder = true;
                                $finder_pixel = $distance_x === 0 || $distance_x === 6 || $distance_y === 0 || $distance_y === 6 || ($distance_x >= 2 && $distance_x <= 4 && $distance_y >= 2 && $distance_y <= 4);
                                break;
                            }
                        }
                        $dark = $finder ? $finder_pixel : (($x * 7 + $y * 11 + $x * $y) % 5 < 2);
                        if ($dark): ?>
                            <rect x="<?= $x ?>" y="<?= $y ?>" width="1" height="1" fill="#0A0E14"/>
                        <?php endif; ?>
                    <?php endfor; ?>
                <?php endfor; ?>
            </svg>
            <p class="qr-demo-label">DEMO PAYMENT CODE</p>
            <?php endif; ?>
        </div>

        <form method="post" action="join.php">
            <input type="hidden" name="match_id" value="<?= e($m['id']) ?>">
            <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top:16px;">I've Completed Payment</button>
        </form>
    <?php else: ?>
    <form method="post" action="payment.php?id=<?= urlencode($m['id']) ?>">
        <input type="hidden" name="match_id" value="<?= e($m['id']) ?>">

        <div class="section-title" style="margin-top:24px;">Payment Method</div>
        <div style="display:flex; flex-direction:column; gap:8px;">
            <?php
            $methods = [
                'aba' => 'ABA Pay',
                'card' => 'Credit / Debit Card',
                'wallet' => 'Wallet Balance',
            ];
            foreach ($methods as $key => $label): ?>
                <label class="field-card" style="cursor:pointer; align-items:center;">
                    <input type="radio" name="method" value="<?= $key ?>" <?= $key === 'aba' ? 'checked' : '' ?> style="margin-right:4px;">
                    <span style="font-weight:700; font-size:14px; flex:1;"><?= $label ?></span>
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 24px;">
            Pay &amp; Reserve Spot — $<?= money($m['price']) ?>
        </button>
    </form>
    <?php endif; ?>
    <p class="dim center" style="font-size:10px; margin-top:10px;">Demo mode — no real payment is processed.</p>
</div>

<?php require 'includes/footer.php'; ?>
