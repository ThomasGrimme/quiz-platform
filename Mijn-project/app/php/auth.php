<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/db.php';

//controleert of de gebruiker is ingelogd
function require_login(): void
{   // als de gebruiker niet is ingelogd word hij terug gestuurd
    if (!is_logged_in()) {
        header('Location: /index.php?error=login_required');
        exit;
    }
}
