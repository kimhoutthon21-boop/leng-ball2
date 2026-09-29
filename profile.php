<?php
require 'config.php';
require 'data.php';
require 'includes/components.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_confirm'])) {
  unset($_SESSION['user']);
  $_SESSION['flash'] = 'You have been logged out.';
  $_SESSION['flash_type'] = 'error';
  header('Location: login.php');
    exit;
}

$page_title = 'Profile';
$active_nav = 'profile';
require 'includes/header.php';
?>

<div style="padding: 20px 20px 32px;">
    <h1 class="display">Profile</h1>

  <?php if (empty($_SESSION['user'])): ?>
    <div class="panel" style="margin-top:20px; text-align:center;">
      <div class="display" style="font-size:18px;">Your profile is waiting</div>
      <p class="dim" style="font-size:13px; margin:8px 0 18px;">Log in or create your Leng Ball account to manage your profile.</p>
      <a href="login.php" class="btn btn-primary btn-block">Log In</a>
    </div>
  <?php endif; ?>

    <div class="profile-head">
        <div class="profile-avatar">AN</div>
        <div>
            <div class="display" style="font-size:20px;">Andrew</div>
            <div style="color: var(--gold); font-size:14px; margin-top:2px;">★ 4.9 rating</div>
        </div>
    </div>

    <div class="grid-2 profile-stats">
        <div class="panel"><div class="mono" style="color:var(--lime); font-weight:700; font-size:22px;">42</div><div class="dim" style="font-size:12px; margin-top:2px;">Matches Played</div></div>
        <div class="panel"><div class="mono" style="color:var(--lime); font-weight:700; font-size:22px;">6</div><div class="dim" style="font-size:12px; margin-top:2px;">Matches Created</div></div>
        <div class="panel"><div class="mono" style="color:var(--lime); font-weight:700; font-size:22px;">95%</div><div class="dim" style="font-size:12px; margin-top:2px;">Attendance Rate</div></div>
        <div class="panel"><div class="mono" style="color:var(--lime); font-weight:700; font-size:22px;">4.9</div><div class="dim" style="font-size:12px; margin-top:2px;">Rating</div></div>
    </div>

    <div class="panel" style="margin-top:12px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div class="section-title" style="margin:0; display:flex; align-items:center; gap:6px;">🛡️ Reliability Score</div>
            <span class="mono" style="color:var(--ok); font-weight:700; font-size:14px;">95%</span>
        </div>
        <div class="meter-track"><div class="meter-fill" style="width:95%; background: var(--ok);"></div></div>
        <p class="dim" style="font-size:12px; margin-top:10px; line-height:1.6;">40 of 42 reserved matches attended. High-reliability players get priority access to popular matches.</p>
    </div>

    <div style="margin-top:16px;">
        <button id="logout-button" type="button" class="btn btn-ghost btn-block">Log out</button>
    </div>
</div>

<div id="logout-modal" hidden style="position:fixed; inset:0; background:rgba(10,14,20,0.74); align-items:center; justify-content:center; padding:20px; z-index:120;">
    <div class="panel" style="width:min(100%, 360px); padding:20px; border-radius:16px;">
        <div class="display" style="font-size:20px; margin-bottom:8px;">Log out?</div>

        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:18px;">
            <button type="button" class="btn btn-ghost" data-close-logout>Cancel</button>
            <form method="post" action="profile.php" style="margin:0;">
                <input type="hidden" name="logout_confirm" value="1">
                <button type="submit" class="btn btn-primary">Log out</button>
            </form>
        </div>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('logout-modal');
    const btn = document.getElementById('logout-button');
    const closeButtons = document.querySelectorAll('[data-close-logout]');

    if (btn && modal) {
      btn.addEventListener('click', function () {
        modal.hidden = false;
        modal.style.display = 'flex';
      });
    }

    closeButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        modal.hidden = true;
        modal.style.display = 'none';
      });
    });

    if (modal) {
      modal.addEventListener('click', function (event) {
        if (event.target === modal) {
          modal.hidden = true;
          modal.style.display = 'none';
        }
      });
    }
  });
</script>

<?php require 'includes/footer.php'; ?>
