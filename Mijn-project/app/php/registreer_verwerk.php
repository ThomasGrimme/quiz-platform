<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$name = trim((string)($_POST['naam'] ?? ''));
$email = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');
$passwordConfirm = (string)($_POST['password_confirm'] ?? '');

if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password !== $passwordConfirm || strlen($password) < 8) {
    header('Location: index.php?error=invalid_input');
    exit;
}

$check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$check->execute(['email' => $email]);

if ($check->fetch()) {
    header('Location: index.php?error=duplicate_email');
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$statement = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)');

try {
    $statement->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => $passwordHash,
    ]);
} catch (PDOException $exception) {
    header('Location: index.php?error=duplicate_email');
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $pdo->lastInsertId();
$_SESSION['user_name'] = $name;
$_SESSION['user_email'] = $email;

header('Location: dashboard.php');
exit;
