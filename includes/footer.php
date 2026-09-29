<?php
// includes/footer.php
// Set $active_nav to one of: home, discover, create, matches, profile, dashboard
// Set $show_nav = false before including this to hide the bottom nav
// entirely (used on payment/success/created/partners/admin pages).
if (!isset($active_nav)) $active_nav = '';
if (!isset($show_nav)) $show_nav = true;

$nav_items = [
    'home'     => ['label' => 'Home',       'icon' => '⌂', 'href' => 'index.php'],
    'discover' => ['label' => 'Discover',   'icon' => '⚲', 'href' => 'discover.php'],
    'create'   => ['label' => 'Create',     'icon' => '+', 'href' => 'create.php'],
    'matches'  => ['label' => 'My Matches', 'icon' => '▤', 'href' => 'matches.php'],
    'profile'  => ['label' => 'Profile',    'icon' => '☺', 'href' => 'profile.php'],
    'dashboard' => ['label' => 'Dashboard', 'icon' => '▥', 'href' => 'admin.php'],
];
  if (is_admin()) {
    $nav_items['profile'] = $nav_items['dashboard'];
    unset($nav_items['dashboard']);
  } else {
    unset($nav_items['dashboard']);
  }
?>

  <?php if ($show_nav): ?>
  <div class="bottom-nav">
    <div class="bottom-nav-inner">
      <?php foreach ($nav_items as $key => $item): ?>
        <a href="<?= $item['href'] ?>" class="nav-item <?= $active_nav === $key ? 'active' : '' ?>">
          <span class="ic"><?= $item['icon'] ?></span>
          <span class="lbl"><?= $item['label'] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</div>
</body>
</html>
