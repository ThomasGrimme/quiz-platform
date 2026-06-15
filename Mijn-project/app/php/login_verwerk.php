<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
<<<<<<< HEAD
    header('Location: /index.php');
    exit;
}

$identifier = strtolower(trim((string)($_POST['identifier'] ?? '')));
$password = (string)($_POST['password'] ?? '');

require_csrf_token();

if ($identifier === '' || $password === '') {
    header('Location: /index.php?error=invalid_input');
    exit;
}

$statement = $pdo->prepare('SELECT id, name, gebruikersnaam, email, wachtwoord_hash FROM users WHERE LOWER(gebruikersnaam) = LOWER(:id1) OR LOWER(email) = LOWER(:id2) LIMIT 1');
$statement->execute(['id1' => $identifier, 'id2' => $identifier]);
$user = $statement->fetch();

if (!$user || !password_verify($password, $user['wachtwoord_hash'])) {
    header('Location: /index.php?error=invalid_login');
=======
    header('Location: index.php');
    exit;
}

$email = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');

if ($email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?error=invalid_input');
    exit;
}

$statement = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
$statement->execute(['email' => $email]);
$user = $statement->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    header('Location: index.php?error=invalid_login');
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['user_name'] = $user['name'];
<<<<<<< HEAD
$_SESSION['user_username'] = $user['gebruikersnaam'];
$_SESSION['user_email'] = $user['email'];

$touch = $pdo->prepare('UPDATE users SET laatst_ingelogd_op = CURRENT_TIMESTAMP WHERE id = :id');
$touch->execute(['id' => (int) $user['id']]);

header('Location: /dashboard.php');
=======
$_SESSION['user_email'] = $user['email'];

header('Location: dashboard.php');
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010
exit;
