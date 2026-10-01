<?php
require 'config.php';
require 'db.php';

if (!empty($_SESSION['user'])) {
    header('Location: ' . (is_admin() ? 'admin.php' : 'profile.php'));
    exit;
}

$error = '';
$mode = in_array($_GET['mode'] ?? 'login', ['login', 'signup', 'admin'], true) ? ($_GET['mode'] ?? 'login') : 'login';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
    $mode = $action === 'signup' ? 'signup' : ($action === 'admin_login' ? 'admin' : 'login');
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif ($action === 'signup') {
        $name = trim((string)($_POST['name'] ?? ''));

        if ($name === '' || strlen($name) < 2) {
            $error = 'Enter your name.';
        } elseif (strlen($password) < 6) {
            $error = 'Your password must be at least 6 characters.';
        } else {
            try {
                $pdo = db();
                $pdo->beginTransaction();
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare(
                    "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'player')"
                );
                $stmt->execute([$name, $email, $password_hash]);
                $user_id = (int)$pdo->lastInsertId();

                $profile = $pdo->prepare('INSERT INTO player_profiles (user_id, full_name) VALUES (?, ?)');
                $profile->execute([$user_id, $name]);
                $pdo->commit();

                $_SESSION['users'][] = [
                    'email' => $email,
                    'name' => $name,
                    'password' => $password_hash,
                    'role' => 'player',
                ];
                $_SESSION['user'] = [
                    'id' => $user_id,
                    'email' => $email,
                    'name' => $name,
                    'role' => 'player',
                ];
                $_SESSION['flash'] = 'Your account is ready. Welcome to Leng Ball!';
                header('Location: index.php');
                exit;
            } catch (PDOException $exception) {
                if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
                $error = $exception->getCode() === '23000'
                    ? 'An account with that email already exists.'
                    : 'Unable to create your account right now.';
            }
        }
    } else {
        $authenticated = false;

        // Field owners are authenticated against the real database first.
        if ($action === 'admin_login') {
            try {
                $stmt = db()->prepare(
                    "SELECT id, name, email, password_hash FROM users WHERE email = ? AND role = 'admin' LIMIT 1"
                );
                $stmt->execute([$email]);
                $db_user = $stmt->fetch();

                if ($db_user && password_verify($password, $db_user['password_hash'])) {
                    $_SESSION['user'] = [
                        'id' => (int)$db_user['id'],
                        'email' => $db_user['email'],
                        'name' => $db_user['name'],
                        'role' => 'admin',
                        'field_id' => 'f1',
                    ];
                    $authenticated = true;
                }
            } catch (PDOException $e) {
                // Database not reachable/imported yet — fall back to the session demo account below.
            }
        }

        // Session-only demo accounts (regular players, and the demo owner account).
        if (!$authenticated) {
            foreach ($_SESSION['users'] as $user) {
                $correct_role = $action !== 'admin_login' || ($user['role'] ?? 'user') === 'admin';
                if ($user['email'] === $email && $correct_role && password_verify($password, $user['password'])) {
                    $_SESSION['user'] = [
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'role' => $user['role'] ?? 'user',
                        'field_id' => $user['field_id'] ?? '',
                    ];
                    $authenticated = true;
                    break;
                }
            }
        } else {
            try {
                $stmt = db()->prepare(
                    "SELECT id, name, email, password_hash FROM users WHERE email = ? AND role = 'player' LIMIT 1"
                );
                $stmt->execute([$email]);
                $db_user = $stmt->fetch();

                if ($db_user && password_verify($password, $db_user['password_hash'])) {
                    $_SESSION['user'] = [
                        'id' => (int)$db_user['id'],
                        'email' => $db_user['email'],
                        'name' => $db_user['name'],
                        'role' => 'player',
                    ];
                    $authenticated = true;
                }
            } catch (PDOException $exception) {
                // Keep the session demo account available when the database is offline.
            }
        }

        if ($authenticated) {
            $_SESSION['flash'] = 'Welcome back!';
            $redirect = $action === 'admin_login' ? 'admin.php' : ($_SESSION['login_redirect'] ?? 'index.php');
            unset($_SESSION['login_redirect']);
            header('Location: ' . $redirect);
            exit;
        }

        $error = 'That email or password is not correct.';
    }
}

$page_title = $mode === 'signup' ? 'Sign Up' : ($mode === 'admin' ? 'Owner Login' : 'Log In');
$show_nav = false;
$hide_topbar = true;
require 'includes/header.php';
?>

<div style="max-width:460px; margin:0 auto; padding:48px 20px 40px;">
    <div class="center">
        <img src="assets/playbal-logo.png?v=<?= filemtime(__DIR__ . '/assets/playbal-logo.png') ?>" class="auth-logo" alt="Leng Ball">
        <h1 class="display" style="font-size:32px;">Leng Ball</h1>
        <p class="dim" style="font-size:14px; margin-top:8px;">Find your people. Fill the pitch.</p>
    </div>

    <?php if ($mode === 'login' || $mode === 'admin'): ?>
        <form method="post" action="login.php?mode=<?= $mode === 'admin' ? 'admin' : 'login' ?>" class="panel">
            <input type="hidden" name="action" value="<?= $mode === 'admin' ? 'admin_login' : 'login' ?>">
            <h2 class="display" style="font-size:20px; margin-bottom:6px;"><?= $mode === 'admin' ? 'Field Owner Login' : 'Log In' ?></h2>
            <p class="dim" style="font-size:12px; margin-bottom:20px;"><?= $mode === 'admin' ? 'Manage your pitch, bookings, and revenue.' : 'Already part of the squad?' ?></p>
            <?php if ($error): ?>
                <div class="flash" style="background:rgba(248,113,113,0.1); border-color:rgba(248,113,113,0.3); color:var(--bad);">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label class="field-label" for="email">Email</label>
                <input id="email" type="email" name="email" class="input" autocomplete="email" required value="<?= e(($_POST['action'] ?? '') === 'login' ? ($_POST['email'] ?? '') : '') ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="field-label" for="password">Password</label>
                <input id="password" type="password" name="password" class="input" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top:24px;"><?= $mode === 'admin' ? 'Open Dashboard' : 'Log In' ?></button>
            <?php if ($mode === 'admin'): ?>
                <p class="center dim" style="font-size:11px; margin:18px 0 0;">Demo owner: owner@lengball.com · admin123</p>
                <p class="center dim" style="font-size:12px; margin:12px 0 0;"><a href="login.php" style="color:var(--lime); font-weight:700;">Player login</a></p>
            <?php else: ?>
                <p class="center dim" style="font-size:12px; margin:18px 0 0;">New to Leng Ball? <a href="login.php?mode=signup" style="color:var(--lime); font-weight:700;">Sign up</a></p>
                <p class="center dim" style="font-size:12px; margin:12px 0 0;">Own a field? <a href="login.php?mode=admin" style="color:var(--lime); font-weight:700;">Owner login</a></p>
            <?php endif; ?>
        </form>
    <?php else: ?>
        <form method="post" action="login.php" class="panel">
            <input type="hidden" name="action" value="signup">
            <h2 class="display" style="font-size:20px; margin-bottom:6px;">Sign Up</h2>
            <p class="dim" style="font-size:12px; margin-bottom:20px;">New here? Make an account.</p>
            <?php if ($error): ?>
                <div class="flash" style="background:rgba(248,113,113,0.1); border-color:rgba(248,113,113,0.3); color:var(--bad);">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label class="field-label" for="name">Name</label>
                <input id="name" type="text" name="name" class="input" autocomplete="name" required>
            </div>
            <div class="form-group">
                <label class="field-label" for="signup-email">Email</label>
                <input id="signup-email" type="email" name="email" class="input" autocomplete="email" required>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="field-label" for="signup-password">Password</label>
                <input id="signup-password" type="password" name="password" class="input" minlength="6" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-ghost btn-lg btn-block" style="margin-top:24px;">Create Account</button>
            <p class="center dim" style="font-size:12px; margin:18px 0 0;">Already have an account? <a href="login.php" style="color:var(--lime); font-weight:700;">Log in</a></p>
            <p class="center dim" style="font-size:12px; margin:12px 0 0;">Own a field? <a href="login.php?mode=admin" style="color:var(--lime); font-weight:700;">Owner login</a></p>
        </form>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
