<?php
// data.php — all the "demo data" lives here in plain PHP arrays.
// Swap this file out for real database queries later and nothing
// else in the app has to change, as long as these functions still
// return arrays shaped the same way.

function get_fields() {
    return [
        ['id' => 'f1', 'name' => 'Phnom Penh Football Arena',   'area' => 'BKK1',            'rating' => 4.8, 'price' => 20, 'format' => '7v7',   'available' => true,  'owner_email' => 'owner@lengball.com', 'img' => 'https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'f2', 'name' => 'IZZI Sports Club',             'area' => 'Tuol Kork',        'rating' => 4.6, 'price' => 18, 'format' => '5v5',   'available' => true,  'owner_email' => '', 'img' => 'https://images.unsplash.com/photo-1489944440615-453fc2b6a9a9?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'f3', 'name' => 'Diamond Island Turf Park',     'area' => 'Koh Pich',         'rating' => 4.9, 'price' => 25, 'format' => '11v11', 'available' => true,  'owner_email' => '', 'img' => 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'f4', 'name' => 'Chroy Changvar Riverside Pitch','area' => 'Chroy Changvar',  'rating' => 4.5, 'price' => 16, 'format' => '7v7',   'available' => true,  'owner_email' => '', 'img' => 'https://images.unsplash.com/photo-1517927033932-b3d18e61fb3a?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'f5', 'name' => 'Camko City Sports Complex',    'area' => 'Sen Sok',          'rating' => 4.7, 'price' => 22, 'format' => '7v7',   'available' => false, 'owner_email' => '', 'img' => 'https://images.unsplash.com/photo-1543326727-cf6c39e8f84c?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'f6', 'name' => 'Old Stadium 7s Court',         'area' => 'Daun Penh',        'rating' => 4.4, 'price' => 15, 'format' => '5v5',   'available' => true,  'owner_email' => '', 'img' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=800&auto=format&fit=crop'],
    ];
}

function get_field($id) {
    foreach (get_fields() as $f) if ($f['id'] === $id) return $f;
    return null;
}

// A small pool of names used to generate "players already in the match".
function name_pool() {
    return ['Sokha','Dara','Pisach','Vichet','Sreymom','Ratanak','Bopha','Chanthy','Vibol','Sokun','Kunthea','Rithy','Sopheak','Chenda','Panha','Sovann','Reaksmey','Malis','Kosal','Sochea'];
}

function players_for($n, $seed) {
    $names = name_pool();
    // deterministic-ish shuffle per match so it looks the same on every page load
    mt_srand($seed);
    shuffle($names);
    mt_srand(); // reset
    return array_slice($names, 0, $n);
}

function get_base_matches() {
    return [
        ['id' => 'm1', 'field_id' => 'f1', 'name' => 'Friday Night Football',   'field' => 'Phnom Penh Football Arena',    'area' => 'BKK1',            'date' => 'Fri, Aug 28', 'time' => '8:00 PM – 10:00 PM', 'duration' => '2 hours',   'distance' => 1.2, 'price' => 3.5, 'max' => 14, 'min' => 10, 'base_joined' => 10, 'skill' => 'Intermediate', 'format' => '7v7',   'about' => 'Casual 7v7 game. All skill levels welcome. Come have fun and meet new players.', 'img' => 'https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'm2', 'name' => 'Saturday Morning Kickoff', 'field' => 'IZZI Sports Club',              'area' => 'Tuol Kork',        'date' => 'Sat, Aug 29', 'time' => '7:00 AM – 9:00 AM',  'duration' => '2 hours',   'distance' => 2.4, 'price' => 4.0, 'max' => 12, 'min' => 8,  'base_joined' => 8,  'skill' => 'Casual',       'format' => '5v5',   'about' => 'Early morning sweat before the heat kicks in. Friendly and relaxed pace.', 'img' => 'https://images.unsplash.com/photo-1489944440615-453fc2b6a9a9?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'm3', 'name' => 'Casual 5v5',               'field' => 'Old Stadium 7s Court',          'area' => 'Daun Penh',        'date' => 'Fri, Aug 28', 'time' => '6:00 PM – 7:00 PM',  'duration' => '1 hour',    'distance' => 0.8, 'price' => 3.0, 'max' => 10, 'min' => 6,  'base_joined' => 7,  'skill' => 'Beginner',     'format' => '5v5',   'about' => 'Quick after-work game. Great for beginners who just want to move the ball around.', 'img' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'm4', 'name' => 'Sunday League Warm-up',    'field' => 'Diamond Island Turf Park',      'area' => 'Koh Pich',         'date' => 'Sun, Aug 30', 'time' => '4:00 PM – 6:00 PM',  'duration' => '2 hours',   'distance' => 3.1, 'price' => 5.0, 'max' => 22, 'min' => 16, 'base_joined' => 14, 'skill' => 'Advanced',     'format' => '11v11', 'about' => 'Full-pitch 11v11 to shake off the rust before league season. Bring boots, not egos.', 'img' => 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'm5', 'name' => 'Riverside Sunset Kick',    'field' => 'Chroy Changvar Riverside Pitch','area' => 'Chroy Changvar',   'date' => 'Fri, Aug 28', 'time' => '5:30 PM – 7:00 PM',  'duration' => '1.5 hours', 'distance' => 4.0, 'price' => 3.5, 'max' => 14, 'min' => 10, 'base_joined' => 9,  'skill' => 'Intermediate', 'format' => '7v7',   'about' => 'Golden-hour football with a river breeze. Chill vibe, decent competition.', 'img' => 'https://images.unsplash.com/photo-1517927033932-b3d18e61fb3a?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'm6', 'field_id' => 'f1', 'name' => 'Full House Friday',        'field' => 'Phnom Penh Football Arena',     'area' => 'BKK1',             'date' => 'Fri, Aug 28', 'time' => '6:00 PM – 8:00 PM',  'duration' => '2 hours',   'distance' => 1.2, 'price' => 3.5, 'max' => 14, 'min' => 10, 'base_joined' => 14, 'skill' => 'Intermediate', 'format' => '7v7',   'about' => 'This one filled up fast — great sign for how the platform solves the empty-pitch problem.', 'img' => 'https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?q=80&w=800&auto=format&fit=crop'],
        ['id' => 'm7', 'name' => 'Ladies Night Football',    'field' => 'Camko City Sports Complex',     'area' => 'Sen Sok',          'date' => 'Sat, Aug 29', 'time' => '6:00 PM – 8:00 PM',  'duration' => '2 hours',   'distance' => 5.2, 'price' => 4.0, 'max' => 14, 'min' => 10, 'base_joined' => 6,  'skill' => 'Casual',       'format' => '7v7',   'about' => "Women's casual match — new players and returning footballers both welcome.", 'img' => 'https://images.unsplash.com/photo-1543326727-cf6c39e8f84c?q=80&w=800&auto=format&fit=crop'],
    ];
}

// Turns a joined/max/min triple into a label + tone ("ok" / "warn" / "bad")
function match_status($joined, $max, $min) {
    if ($joined >= $max) return ['label' => 'Match Full', 'tone' => 'ok'];
    if ($joined >= $min) return ['label' => 'Confirmed', 'tone' => 'ok'];
    return ['label' => 'Waiting for Players', 'tone' => 'warn'];
}

function ensure_database_match($match, $is_created = false) {
    require_once __DIR__ . '/db.php';

    static $match_ids = [];
    $title = $match['name'];
    if (isset($match_ids[$title])) return $match_ids[$title];

    $stmt = db()->prepare('SELECT id FROM matches WHERE title = ? ORDER BY id LIMIT 1');
    $stmt->execute([$title]);
    $database_id = $stmt->fetchColumn();
    if ($database_id) {
        return $match_ids[$title] = (int)$database_id;
    }

    $owner_id = $is_created ? ($_SESSION['user']['id'] ?? null) : null;
    if (!$owner_id) {
        $owner_id = db()->query("SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1")->fetchColumn();
    }
    if (!$owner_id) return null;

    $field_stmt = db()->prepare('SELECT id FROM fields WHERE name = ? LIMIT 1');
    $field_stmt->execute([$match['field']]);
    $field_id = $field_stmt->fetchColumn() ?: null;

    $date_value = preg_replace('/^[A-Za-z]{3},\\s*/', '', $match['date']);
    $date = DateTime::createFromFormat('!M j Y', $date_value . ' ' . date('Y'));
    $time_value = explode(' – ', $match['time'])[0];
    $time = DateTime::createFromFormat('!g:i A', $time_value);
    if (!$date || !$time) return null;

    $formation_stmt = db()->prepare('SELECT id FROM formations WHERE format = ? ORDER BY id LIMIT 1');
    $formation_stmt->execute([$match['format']]);
    $formation_id = $formation_stmt->fetchColumn() ?: null;

    $insert = db()->prepare(
        "INSERT INTO matches (admin_id, field_id, title, match_date, match_time, location, match_fee, max_players, min_players, skill_level, format, formation_id, status, about, image_url)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'open', ?, ?)"
    );
    $insert->execute([
        $owner_id,
        $field_id,
        $title,
        $date->format('Y-m-d'),
        $time->format('H:i:s'),
        $match['field'],
        $match['price'],
        $match['max'],
        $match['min'],
        $match['skill'],
        $match['format'],
        $formation_id,
        $match['about'],
        $match['img'],
    ]);

    return $match_ids[$title] = (int)db()->lastInsertId();
}

function match_participants($database_match_id) {
    if (!$database_match_id) return [];

    require_once __DIR__ . '/db.php';
    $stmt = db()->prepare(
        "SELECT users.id, users.name
         FROM match_requests
         JOIN users ON users.id = match_requests.player_id
         WHERE match_requests.match_id = ? AND match_requests.status = 'approved'
         ORDER BY match_requests.created_at"
    );
    $stmt->execute([$database_match_id]);
    return $stmt->fetchAll();
}

// Match counts and participant names come from persisted approved requests.
function all_matches() {
    $out = [];

    foreach (get_base_matches() as $m) {
        $m['db_id'] = ensure_database_match($m);
        $participants = match_participants($m['db_id']);
        $m['joined'] = count($participants);
        $m['players'] = array_column($participants, 'name');
        $participant_ids = array_map('intval', array_column($participants, 'id'));
        $m['joined_by_me'] = in_array((int)($_SESSION['user']['id'] ?? 0), $participant_ids, true);
        $m['is_created'] = false;
        $out[] = $m;
    }

    foreach ($_SESSION['created'] as $m) {
        $m['db_id'] = ensure_database_match($m, true);
        $participants = match_participants($m['db_id']);
        $m['joined'] = count($participants);
        $m['players'] = array_column($participants, 'name');
        $participant_ids = array_map('intval', array_column($participants, 'id'));
        $m['joined_by_me'] = in_array((int)($_SESSION['user']['id'] ?? 0), $participant_ids, true);
        $m['is_created'] = true;
        $out[] = $m;
    }

    return $out;
}

function find_match($id) {
    foreach (all_matches() as $m) {
        if ($m['id'] === $id) return $m;
    }
    return null;
}
