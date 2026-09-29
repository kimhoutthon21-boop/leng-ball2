<?php
require 'config.php';
require 'data.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create.php?step=1');
    exit;
}

if (!is_admin() && empty($_SESSION['creator_paid'])) {
    header('Location: create_payment.php');
    exit;
}

$d = $_SESSION['draft'];
$field = $d['field_id'] ? get_field($d['field_id']) : null;

$date = DateTime::createFromFormat('!Y-m-d', (string)($d['date'] ?? ''));
$start = DateTime::createFromFormat('!H:i', (string)($d['time_start'] ?? ''));
$end = DateTime::createFromFormat('!H:i', (string)($d['time_end'] ?? ''));
if (!$date || !$start || !$end || $end <= $start) {
    $_SESSION['flash'] = 'Please choose a valid date and time range.';
    header('Location: create.php?step=1');
    exit;
}

$duration_minutes = (int)(($end->getTimestamp() - $start->getTimestamp()) / 60);
$duration_hours = $duration_minutes / 60;
$duration = fmod($duration_hours, 1.0) === 0.0
    ? (int)$duration_hours . ' ' . ((int)$duration_hours === 1 ? 'hour' : 'hours')
    : $duration_hours . ' hours';

$players = (int)$d['players'];
$id = 'c' . time();

$match = [
    'id' => $id,
    'field_id' => $d['field_id'],
    'owner_email' => is_admin() ? ($_SESSION['user']['email'] ?? '') : '',
    'name' => $d['name'] !== '' ? $d['name'] : 'Untitled Match',
    'field' => $field ? $field['name'] : 'TBD',
    'area' => $field ? $field['area'] : '',
    'date' => $date->format('D, M j'),
    'time' => $start->format('g:i A') . ' – ' . $end->format('g:i A'),
    'duration' => $duration,
    'distance' => 0.5,
    'price' => (float)$d['price'],
    'max' => $players,
    'min' => (int)round($players * 0.7),
    'skill' => $d['skill'],
    'format' => $d['format'],
    'about' => 'Newly created match — be one of the first to join!',
    'img' => $field ? $field['img'] : 'https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?q=80&w=800&auto=format&fit=crop',
];

$_SESSION['created'][] = $match;
$_SESSION['draft'] = []; // clear the wizard for next time
unset($_SESSION['creator_paid']);
$_SESSION['flash'] = 'Match published! Share it to start getting players.';

header('Location: created.php?id=' . urlencode($id));
exit;
