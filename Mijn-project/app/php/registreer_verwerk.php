<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
<<<<<<< HEAD
    header('Location: /index.php');
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$username = strtolower(trim((string)($_POST['gebruikersnaam'] ?? '')));
=======
    header('Location: index.php');
    exit;
}

$name = trim((string)($_POST['naam'] ?? ''));
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010
$email = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');
$passwordConfirm = (string)($_POST['password_confirm'] ?? '');

<<<<<<< HEAD
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
=======
if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password !== $passwordConfirm || strlen($password) < 8) {
    header('Location: index.php?error=invalid_input');
    exit;
}

$check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$check->execute(['email' => $email]);

if ($check->fetch()) {
    header('Location: index.php?error=duplicate_email');
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
<<<<<<< HEAD
$statement = $pdo->prepare('INSERT INTO users (name, gebruikersnaam, email, wachtwoord_hash) VALUES (:name, :gebruikersnaam, :email, :wachtwoord_hash)');
=======
$statement = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)');
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010

try {
    $statement->execute([
        'name' => $name,
<<<<<<< HEAD
        'gebruikersnaam' => $username,
        'email' => $email,
        'wachtwoord_hash' => $passwordHash,
    ]);
} catch (PDOException $exception) {
    header('Location: /index.php?error=duplicate_username');
=======
        'email' => $email,
        'password_hash' => $passwordHash,
    ]);
} catch (PDOException $exception) {
    header('Location: index.php?error=duplicate_email');
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $pdo->lastInsertId();
$_SESSION['user_name'] = $name;
<<<<<<< HEAD
$_SESSION['user_username'] = $username;
$_SESSION['user_email'] = $email;

header('Location: /dashboard.php');
=======
$_SESSION['user_email'] = $email;

header('Location: dashboard.php');
>>>>>>> 1166b0a68129a69ad62e0cc4b40ba86c4e47c010
exit;
