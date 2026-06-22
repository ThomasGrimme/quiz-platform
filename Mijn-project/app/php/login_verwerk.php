<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

require_csrf_token();


$identifier = strtolower(trim((string)($_POST['identifier'] ?? '')));
$password = (string)($_POST['password'] ?? '');


if ($identifier === '' || $password === '') {
    header('Location: /index.php?error=invalid_input');
    exit;
}


$statement = $pdo->prepare('SELECT id, name, gebruikersnaam, email, wachtwoord_hash FROM users WHERE gebruikersnaam = :id1 OR email = :id2 LIMIT 1');
$statement->execute(['id1' => $identifier, 'id2' => $identifier]);
$user = $statement->fetch();


if (!$user || !password_verify($password, $user['wachtwoord_hash'])) {
    header('Location: /index.php?error=invalid_login');
    exit;
}


session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_username'] = $user['gebruikersnaam'];
$_SESSION['user_email'] = $user['email'];


$touch = $pdo->prepare('UPDATE users SET laatst_ingelogd_op = CURRENT_TIMESTAMP WHERE id = :id');
$touch->execute(['id' => (int) $user['id']]);

header('Location: /dashboard.php');
exit;