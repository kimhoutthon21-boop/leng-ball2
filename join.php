<?php
require 'config.php';
require 'data.php';
require_once 'db.php';

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

if ($m && !empty($_SESSION['user']['email']) && !empty($m['db_id'])) {
    $user_id = (int)($_SESSION['user']['id'] ?? 0);
    if (!$user_id) {
        $user_stmt = db()->prepare("SELECT id FROM users WHERE email = ? AND role = 'player' LIMIT 1");
        $user_stmt->execute([$_SESSION['user']['email']]);
        $user_id = (int)$user_stmt->fetchColumn();
        if ($user_id) $_SESSION['user']['id'] = $user_id;
    }

    if ($user_id) {
        $pdo = db();
        try {
            $pdo->beginTransaction();
            $match_stmt = $pdo->prepare('SELECT max_players FROM matches WHERE id = ? FOR UPDATE');
            $match_stmt->execute([(int)$m['db_id']]);
            $max_players = (int)$match_stmt->fetchColumn();

            $existing_stmt = $pdo->prepare('SELECT id, status FROM match_requests WHERE match_id = ? AND player_id = ?');
            $existing_stmt->execute([(int)$m['db_id'], $user_id]);
            $existing = $existing_stmt->fetch();

            if ($max_players > 0 && (!$existing || $existing['status'] !== 'approved')) {
                $count_stmt = $pdo->prepare(
                    "SELECT COUNT(*) FROM match_requests WHERE match_id = ? AND status = 'approved'"
                );
                $count_stmt->execute([(int)$m['db_id']]);
                $joined_count = (int)$count_stmt->fetchColumn();

                if ($joined_count < $max_players) {
                    if ($existing) {
                        $update = $pdo->prepare(
                            "UPDATE match_requests SET status = 'approved', reviewed_by = NULL, reviewed_at = NOW() WHERE id = ?"
                        );
                        $update->execute([(int)$existing['id']]);
                    } else {
                        $insert = $pdo->prepare(
                            "INSERT INTO match_requests (match_id, player_id, status) VALUES (?, ?, 'approved')"
                        );
                        $insert->execute([(int)$m['db_id'], $user_id]);
                    }
                    $_SESSION['flash'] = 'Your spot is confirmed.';
                } else {
                    $_SESSION['flash'] = 'This match is full.';
                }
            }

            $pdo->commit();
        } catch (PDOException $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $_SESSION['flash'] = 'Unable to confirm your spot. Please try again.';
        }
    } else {
        $_SESSION['flash'] = 'Your player account could not be found. Please sign in again.';
    }
}

header('Location: success.php?id=' . urlencode($match_id));
exit;
