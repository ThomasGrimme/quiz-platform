<?php
declare(strict_types=1);

<<<<<<< HEAD
require_once __DIR__ . '/php/auth.php';
=======
require_once __DIR__ . '/auth.php';
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
<<<<<<< HEAD
    header('Location: /php/quiz_aanmaken.php');
    exit;
}

require_csrf_token();

header('Location: /php/dashboard.php');
=======
    header('Location: quiz_aanmaken.php');
    exit;
}

header('Location: dashboard.php');
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010
exit;
