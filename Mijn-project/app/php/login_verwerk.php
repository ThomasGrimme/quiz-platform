<?php
declare(strict_types=1);

// Ensure the session is started before anything else runs
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

// 1. Verify CSRF token BEFORE processing inputs or database queries
require_csrf_token();

$identifier = strtolower(trim((string)($_POST['identifier'] ?? '')));
$password = (string)($_POST['password'] ?? '');

if ($identifier === '' || $password === '') {
    header('Location: /index.php?error=invalid_input');
    exit;
}

// 2. Optimized SQL: Removed redundant LOWER() calls since $identifier is already lowercased,
// and assumed your DB collation is already case-insensitive (standard for MySQL/PostgreSQL).
$statement = $pdo->prepare('SELECT id, name, gebruikersnaam, email, wachtwoord_hash FROM users WHERE gebruikersnaam = :id1 OR email = :id2 LIMIT 1');
$statement->execute(['id1' => $identifier, 'id2' => $identifier]);
$user = $statement->fetch();

if (!$user || !password_verify($password, $user['wachtwoord_hash'])) {
    header('Location: /index.php?error=invalid_login');
    exit;
}

// 3. Proper session handling: Regenerate ID before populating data
session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_username'] = $user['gebruikersnaam'];
$_SESSION['user_email'] = $user['email'];

$touch = $pdo->prepare('UPDATE users SET laatst_ingelogd_op = CURRENT_TIMESTAMP WHERE id = :id');
$touch->execute(['id' => (int) $user['id']]);

header('Location: /dashboard.php');
exit;