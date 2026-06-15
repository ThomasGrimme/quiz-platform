<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: quiz_aanmaken.php');
    exit;
}

header('Location: dashboard.php');
exit;
