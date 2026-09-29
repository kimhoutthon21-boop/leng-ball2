<?php
// config.php — start the session and load a few tiny helpers.
// Every page includes this first.

session_start();

define('APP_NAME', 'Leng Ball');

// Make sure our session "storage" arrays exist before anything uses them.
if (!isset($_SESSION['joined']))    $_SESSION['joined'] = [];      // match ids the demo user joined
if (!isset($_SESSION['extra']))     $_SESSION['extra'] = [];       // [match_id => extra players added by demo user]
if (!isset($_SESSION['created']))   $_SESSION['created'] = [];     // matches the demo user published
if (!isset($_SESSION['draft']))     $_SESSION['draft'] = [];       // in-progress "create match" wizard answers
if (!isset($_SESSION['flash']))     $_SESSION['flash'] = '';       // one-shot success message
if (!isset($_SESSION['flash_type'])) $_SESSION['flash_type'] = 'success';
if (!isset($_SESSION['users']))     $_SESSION['users'] = [];       // session-only registered accounts

// Demo owner account so the owner experience is usable without a database.
$owner_email = 'owner@lengball.com';
$owner_exists = false;
foreach ($_SESSION['users'] as $user) {
    if (($user['email'] ?? '') === $owner_email) {
        $owner_exists = true;
        break;
    }
}
if (!$owner_exists) {
    $_SESSION['users'][] = [
        'email' => $owner_email,
        'name' => 'Phnom Penh Football Arena',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin',
        'field_id' => 'f1',
    ];
}

// Shortcut for safely printing text into HTML.
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function money($n) {
    return number_format((float)$n, 2);
}

// Pop the flash message so it only shows once.
function flash_take() {
    $msg = $_SESSION['flash'];
    $_SESSION['flash'] = '';
    return $msg;
}

function is_admin() {
    return !empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? 'user') === 'admin';
}
