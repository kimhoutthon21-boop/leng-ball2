<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'playbal');
define('DB_USER', 'root');
define('DB_PASS', 'Sn2!#2023KS');

function db()
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            die('<div style="font-family: sans-serif; padding: 40px; max-width: 600px; margin: 0 auto;">' .
                '<h2>Database connection failed</h2>' .
                '<p>Make sure MySQL is running, that you have imported <code>db.sql</code>, ' .
                'and that the credentials in <code>db.php</code> are correct.</p>' .
                '<p style="color:#888; font-size: 13px;">' . htmlspecialchars($e->getMessage()) . '</p>' .
                '</div>');
        }
    }

    return $pdo;
}
