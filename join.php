<?php
require 'config.php';
require 'data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: discover.php');
    exit;
}

if (is_admin()) {
    header('Location: discover.php');
    exit;
}

$match_id = isset($_POST['match_id']) ? $_POST['match_id'] : '';
$m = find_match($match_id);

if ($m && $m['joined'] < $m['max']) {
    // Bump this match's joined count by one for this session.
    if (!isset($_SESSION['extra'][$match_id])) $_SESSION['extra'][$match_id] = 0;
    $_SESSION['extra'][$match_id]++;

    // Remember that the demo user joined this match.
    if (!in_array($match_id, $_SESSION['joined'])) {
        $_SESSION['joined'][] = $match_id;
    }
}

header('Location: success.php?id=' . urlencode($match_id));
exit;
