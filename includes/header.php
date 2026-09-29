<?php
// includes/header.php
// Every page sets $page_title before including this file.
// Set $hide_topbar = true on a page (before including this file) to
// let a full-bleed hero image sit flush at the top, like match.php does.
if (!isset($page_title)) $page_title = APP_NAME;
if (!isset($hide_topbar)) $hide_topbar = false;
if (!isset($app_class)) $app_class = '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title) ?> · <?= APP_NAME ?></title>
  <link rel="stylesheet" href="css/style.css?v=<?= filemtime(__DIR__ . '/../css/style.css') ?>">
</head>
<body>
<div class="app <?= e($app_class) ?>">

  <?php if (!$hide_topbar): ?>
  <div class="topbar">
    <a href="index.php" class="brand">
      <img src="assets/playbal-logo.png?v=<?= filemtime(__DIR__ . '/../assets/playbal-logo.png') ?>" class="brand-logo" alt="Leng Ball">
    </a>
    <div class="topnav-links">
      <?php if (empty($_SESSION['user'])): ?>
        <a href="login.php">Log in</a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php $flash_type = $_SESSION['flash_type'] ?? 'success'; $msg = flash_take(); $_SESSION['flash_type'] = 'success'; if ($msg): ?>
    <div style="padding: 16px 20px 0;"><div class="flash <?= $flash_type === 'error' ? 'flash-error' : '' ?>"><?= e($msg) ?></div></div>
    <script>
      window.setTimeout(function () {
        var notice = document.querySelector('.flash');
        if (notice) notice.classList.add('is-hidden');
      }, 3000);
    </script>
  <?php endif; ?>
