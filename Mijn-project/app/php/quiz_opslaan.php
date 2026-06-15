<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /php/quiz_aanmaken.php');
    exit;
}

require_csrf_token();

header('Location: /php/dashboard.php');
exit;
