<?php
require 'config.php';
require 'data.php';

$d = $_SESSION['draft'] ?? [];
if (!$d) {
    header('Location: create.php?step=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['creator_paid'] = true;
    $_SESSION['flash'] = 'Payment confirmed. Your match is ready to publish.';
    header('Location: create.php?step=4');
    exit;
}

$page_title = 'Pay to Publish';
$show_nav = false;
require 'includes/header.php';

$qr_path = 'assets/payment-qr.png';
$has_qr = file_exists(__DIR__ . '/' . $qr_path);
$qr_size = 21;
$finder_starts = [[0, 0], [$qr_size - 7, 0], [0, $qr_size - 7]];
?>

<div style="padding: 20px 20px 40px;">
    <a href="create.php?step=4" class="back-link">&lt; Back to preview</a>
    <h1 class="display">Pay to Publish</h1>
    <p class="dim" style="font-size:13px; line-height:1.6; margin-top:10px;">
        Scan the QR code with your banking app to complete match creation.
    </p>

    <div class="panel" style="margin-top:20px;">
        <div style="font-weight:700;"><?= e($d['name'] ?: 'Untitled Match') ?></div>
        <div class="dim" style="font-size:12px; margin-top:6px;">
            <?= e($d['date'] ?? '') ?> · <?= e($d['time_start'] ?? '') ?> - <?= e($d['time_end'] ?? '') ?>
        </div>
    </div>

    <div class="qr-payment panel" style="margin-top:24px; text-align:center;">
        <div class="section-title" style="margin-top:0;">Scan to Pay</div>
        <p class="dim" style="font-size:12px; margin:8px 0 16px;">Scan this code with your banking app.</p>
        <?php if ($has_qr): ?>
            <img class="fake-qr" src="<?= e($qr_path) ?>?v=<?= filemtime(__DIR__ . '/' . $qr_path) ?>" alt="Payment QR code">
        <?php else: ?>
            <svg class="fake-qr" viewBox="0 0 <?= $qr_size ?> <?= $qr_size ?>" role="img" aria-label="Demo QR payment code">
                <rect width="<?= $qr_size ?>" height="<?= $qr_size ?>" fill="#fff"/>
                <?php for ($y = 0; $y < $qr_size; $y++): ?>
                    <?php for ($x = 0; $x < $qr_size; $x++):
                        $finder = false;
                        $finder_pixel = false;
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

    <form method="post" action="create_payment.php">
        <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top:16px;">I've Completed Payment</button>
    </form>
    <p class="dim center" style="font-size:10px; margin-top:10px;">Demo mode - no real payment is processed.</p>
</div>

<?php require 'includes/footer.php'; ?>
