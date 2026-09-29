<?php
require 'config.php';

unset($_SESSION['user']);
$_SESSION['flash'] = 'You have been logged out.';
$_SESSION['flash_type'] = 'error';
header('Location: login.php?mode=signup');
exit;
