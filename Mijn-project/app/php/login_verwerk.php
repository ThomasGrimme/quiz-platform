<?php
declare(strict_types=1);

// Start de sessie als die nog niet actief is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';

// Controleert of het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}


require_csrf_token();

// Haalt de ingevulde gegevens op
$identifier = strtolower(trim((string)($_POST['identifier'] ?? '')));
$password = (string)($_POST['password'] ?? '');

// kijkt of de gegevens zijn ingevuld
if ($identifier === '' || $password === '') {
    header('Location: /index.php?error=invalid_input');
    exit;
}

// zoekt de gebruiker op in de database
$statement = $pdo->prepare('SELECT id, name, gebruikersnaam, email, wachtwoord_hash FROM users WHERE gebruikersnaam = :id1 OR email = :id2 LIMIT 1');
$statement->execute(['id1' => $identifier, 'id2' => $identifier]);
$user = $statement->fetch();

// kijkt of de inloggegevens kloppen
if (!$user || !password_verify($password, $user['wachtwoord_hash'])) {
    header('Location: /index.php?error=invalid_login');
    exit;
}

// slaat de gebruiker op in de sessie
session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_username'] = $user['gebruikersnaam'];
$_SESSION['user_email'] = $user['email'];

// werkt de laatste inlogtijd bij
$touch = $pdo->prepare('UPDATE users SET laatst_ingelogd_op = CURRENT_TIMESTAMP WHERE id = :id');
$touch->execute(['id' => (int) $user['id']]);


header('Location: /dashboard.php');
exit;