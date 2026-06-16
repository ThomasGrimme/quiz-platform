<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/db.php';

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /index.php?error=login_required');
        exit;
    }
}
