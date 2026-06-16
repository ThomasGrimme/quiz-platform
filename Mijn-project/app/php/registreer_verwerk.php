<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$username = strtolower(trim((string)($_POST['gebruikersnaam'] ?? '')));
$email = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');
$passwordConfirm = (string)($_POST['password_confirm'] ?? '');

require_csrf_token();

if ($name === '' || $username === '' || $email === '' || $password === '' || $passwordConfirm === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password !== $passwordConfirm || strlen($password) < 8 || strlen($username) < 3) {
    header('Location: /index.php?error=invalid_input');
    exit;
}

$emailCheck = $pdo->prepare('SELECT id FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1');
$emailCheck->execute(['email' => $email]);

if ($emailCheck->fetch()) {
    header('Location: /index.php?error=duplicate_email');
    exit;
}

$usernameCheck = $pdo->prepare('SELECT id FROM users WHERE LOWER(gebruikersnaam) = LOWER(:gebruikersnaam) LIMIT 1');
$usernameCheck->execute(['gebruikersnaam' => $username]);

if ($usernameCheck->fetch()) {
    header('Location: /index.php?error=duplicate_username');
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$statement = $pdo->prepare('INSERT INTO users (name, gebruikersnaam, email, wachtwoord_hash) VALUES (:name, :gebruikersnaam, :email, :wachtwoord_hash)');

try {
    $statement->execute([
        'name' => $name,
        'gebruikersnaam' => $username,
        'email' => $email,
        'wachtwoord_hash' => $passwordHash,
    ]);
} catch (PDOException $exception) {
    header('Location: /index.php?error=duplicate_username');
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $pdo->lastInsertId();
$_SESSION['user_name'] = $name;
$_SESSION['user_username'] = $username;
$_SESSION['user_email'] = $email;

header('Location: /dashboard.php');
exit;
