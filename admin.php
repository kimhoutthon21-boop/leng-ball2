<?php
require 'config.php';
require 'data.php';

if (!is_admin()) {
    $_SESSION['login_redirect'] = 'admin.php';
    header('Location: login.php?mode=admin');
    exit;
}

$owner = $_SESSION['user'];
$field = get_field($owner['field_id'] ?? '');
$owned_matches = array_values(array_filter(all_matches(), function ($match) use ($field, $owner) {
    return ($match['field_id'] ?? '') === ($field['id'] ?? '')
        || (($match['owner_email'] ?? '') !== '' && $match['owner_email'] === $owner['email']);
}));

$duration_hours = function ($match) {
    if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s+hours?/', $match['duration'] ?? '', $parts)) return (float)$parts[1];
    if (preg_match('/([0-9]+)\s+hour/', $match['duration'] ?? '', $parts)) return (float)$parts[1];
    return 0;
};
$booked_hours = array_sum(array_map($duration_hours, $owned_matches));
$occupancy = count($owned_matches) ? array_sum(array_map(fn($match) => $match['max'] ? $match['joined'] / $match['max'] : 0, $owned_matches)) / count($owned_matches) : 0;
$estimated_revenue = $booked_hours * (float)($field['price'] ?? 0);
$player_bookings = array_sum(array_map(fn($match) => $match['joined'], $owned_matches));
$daily = [
    'Mon' => ['bookings' => 8, 'matches' => 1, 'hours' => 2, 'revenue' => 40, 'occupancy' => 57, 'rows' => [['name' => 'Monday Kickoff', 'players' => '8/14', 'status' => 'Waiting for Players', 'tone' => 'warn', 'revenue' => 40]]],
    'Tue' => ['bookings' => 14, 'matches' => 2, 'hours' => 4, 'revenue' => 80, 'occupancy' => 75, 'rows' => [['name' => 'Tuesday 5v5', 'players' => '10/12', 'status' => 'Confirmed', 'tone' => 'ok', 'revenue' => 40], ['name' => 'Tuesday Night League', 'players' => '4/14', 'status' => 'Waiting for Players', 'tone' => 'warn', 'revenue' => 40]]],
    'Wed' => ['bookings' => 11, 'matches' => 1, 'hours' => 2, 'revenue' => 40, 'occupancy' => 79, 'rows' => [['name' => 'Wednesday Social', 'players' => '11/14', 'status' => 'Confirmed', 'tone' => 'ok', 'revenue' => 40]]],
    'Thu' => ['bookings' => 18, 'matches' => 2, 'hours' => 4, 'revenue' => 80, 'occupancy' => 86, 'rows' => [['name' => 'Thursday After Work', 'players' => '12/14', 'status' => 'Confirmed', 'tone' => 'ok', 'revenue' => 40], ['name' => 'Thursday 7s', 'players' => '6/14', 'status' => 'Waiting for Players', 'tone' => 'warn', 'revenue' => 40]]],
    'Fri' => ['bookings' => 24, 'matches' => 2, 'hours' => 4, 'revenue' => 80, 'occupancy' => 86, 'rows' => [['name' => 'Friday Night Football', 'players' => '10/14', 'status' => 'Confirmed', 'tone' => 'ok', 'revenue' => 40], ['name' => 'Full House Friday', 'players' => '14/14', 'status' => 'Match Full', 'tone' => 'ok', 'revenue' => 40]]],
    'Sat' => ['bookings' => 21, 'matches' => 2, 'hours' => 4, 'revenue' => 80, 'occupancy' => 81, 'rows' => [['name' => 'Saturday Morning Kickoff', 'players' => '12/14', 'status' => 'Confirmed', 'tone' => 'ok', 'revenue' => 40], ['name' => 'Saturday Sunset Game', 'players' => '9/14', 'status' => 'Waiting for Players', 'tone' => 'warn', 'revenue' => 40]]],
    'Sun' => ['bookings' => 16, 'matches' => 1, 'hours' => 2, 'revenue' => 40, 'occupancy' => 71, 'rows' => [['name' => 'Sunday League Warm-up', 'players' => '16/22', 'status' => 'Confirmed', 'tone' => 'ok', 'revenue' => 40]]],
];
$weekly = array_map(fn($day) => $day['bookings'], $daily);
$max_w = max(1, max($weekly));

$page_title = 'Field Dashboard';
$show_nav = true;
$active_nav = 'profile';
$app_class = 'admin-page';
require 'includes/header.php';
?>

<div style="padding: 20px 20px 40px;">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px;">
        <div>
            <h1 class="display">Field Dashboard</h1>
            <p class="dim" style="font-size:12px; margin-top:2px;"><?= e($field['name'] ?? 'Your field') ?> · <?= e($field['area'] ?? '') ?></p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
            <a href="create.php" class="btn btn-primary btn-sm">+ New Match</a>
            <button type="button" class="btn btn-ghost btn-sm" id="logout-button">Log out</button>
        </div>
    </div>

    <div class="grid-2" style="margin-top:16px;">
        <?php foreach ([
            ['Estimated Revenue', '$' . number_format($estimated_revenue, 0), 'metric-revenue'],
            ['Booked Hours', number_format($booked_hours, 1), 'metric-hours'],
            ['Average Occupancy', number_format($occupancy * 100, 0) . '%', 'metric-occupancy'],
            ['Player Bookings', (string)$player_bookings, 'metric-bookings'],
        ] as [$label, $val, $id]): ?>
            <div class="panel">
                <div class="mono" id="<?= e($id) ?>" style="color:var(--lime); font-weight:700; font-size:22px;"><?= e($val) ?></div>
                <div class="dim" style="font-size:12px; margin-top:2px;"><?= e($label) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="panel" style="margin-top:14px;">
        <div class="section-title">When your field is busy</div>
        <div class="bar-chart" style="margin-top:12px;">
            <?php foreach ($weekly as $day => $v): ?>
                <div class="bar-col day-bar" role="button" tabindex="0" data-day="<?= e($day) ?>" aria-label="View <?= e($day) ?> performance" aria-pressed="false">
                    <div class="bar-track"><div class="bar-fill" style="height: <?= ($v / $max_w) * 100 ?>%;"></div></div>
                    <div class="bar-day"><?= $day ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="admin-daily-summary" style="margin-top:14px;">
            <div class="section-title" id="daily-title">Select a day to see performance</div>
            <div class="grid-3" style="margin-top:10px;">
                <div><div class="num" id="daily-bookings">—</div><div class="lbl">Bookings</div></div>
                <div><div class="num" id="daily-matches">—</div><div class="lbl">Matches</div></div>
                <div><div class="num" id="daily-hours">—</div><div class="lbl">Hours</div></div>
            </div>
        </div>
    </div>

    <div style="margin-top:16px;">
        <div class="section-title">Matches</div>
        <div class="panel" style="padding:0; overflow:hidden;">
            <table class="admin-table">
                <thead><tr><th>Match</th><th>Field</th><th>Players</th><th>Status</th><th style="text-align:right;">Revenue</th></tr></thead>
                <tbody id="matches-body"><tr><td colspan="5" class="dim">Select a day to see its matches.</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

<div id="logout-modal" hidden style="position:fixed; inset:0; background:rgba(10,14,20,0.74); align-items:center; justify-content:center; padding:20px; z-index:120;">
    <div class="panel" style="width:min(100%, 360px); padding:20px; border-radius:16px;">
        <div class="display" style="font-size:20px; margin-bottom:8px;">Log out?</div>
        <p class="dim" style="font-size:13px; line-height:1.5;">Are you sure you want to leave your field dashboard?</p>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:18px;">
            <button type="button" class="btn btn-ghost" data-close-logout>Cancel</button>
            <a href="logout.php" class="btn btn-primary">Log out</a>
        </div>
    </div>
</div>

<script>
    (function () {
        const daily = <?= json_encode($daily, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
        const bars = document.querySelectorAll('.day-bar');
        const title = document.getElementById('daily-title');
        const bookings = document.getElementById('daily-bookings');
        const matchesCount = document.getElementById('daily-matches');
        const hours = document.getElementById('daily-hours');
        const tableBody = document.getElementById('matches-body');

        function addCell(row, text, className) {
            const cell = document.createElement('td');
            cell.textContent = text;
            if (className) cell.className = className;
            row.appendChild(cell);
        }

        function selectDay(day) {
            const stats = daily[day];
            if (!stats) return;
            bars.forEach(function (bar) {
                const selected = bar.dataset.day === day;
                bar.classList.toggle('is-selected', selected);
                bar.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
            title.textContent = day + ' performance';
            bookings.textContent = stats.bookings;
            matchesCount.textContent = stats.matches;
            hours.textContent = Number(stats.hours).toFixed(1);
            document.getElementById('metric-revenue').textContent = '$' + Number(stats.revenue).toFixed(0);
            document.getElementById('metric-hours').textContent = Number(stats.hours).toFixed(1);
            document.getElementById('metric-occupancy').textContent = stats.occupancy + '%';
            document.getElementById('metric-bookings').textContent = stats.bookings;
            tableBody.replaceChildren();
            stats.rows.forEach(function (match) {
                const row = document.createElement('tr');
                addCell(row, match.name);
                addCell(row, '<?= e($field['area'] ?? '') ?>', 'dim');
                addCell(row, match.players, 'mono dim');
                addCell(row, match.status, 'status-' + match.tone);
                addCell(row, '$' + Number(match.revenue).toFixed(0), 'mono price');
                row.lastChild.style.textAlign = 'right';
                tableBody.appendChild(row);
            });
        }

        bars.forEach(function (bar) {
            bar.addEventListener('click', function () { selectDay(bar.dataset.day); });
            bar.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    selectDay(bar.dataset.day);
                }
            });
        });

        const modal = document.getElementById('logout-modal');
        const logoutButton = document.getElementById('logout-button');
        if (logoutButton && modal) {
            logoutButton.addEventListener('click', function () {
                modal.hidden = false;
                modal.style.display = 'flex';
            });
            modal.querySelector('[data-close-logout]').addEventListener('click', function () {
                modal.hidden = true;
                modal.style.display = 'none';
            });
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.hidden = true;
                    modal.style.display = 'none';
                }
            });
        }
    }());
</script>

<?php require 'includes/footer.php'; ?>
