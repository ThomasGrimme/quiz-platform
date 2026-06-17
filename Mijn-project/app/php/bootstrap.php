<?php
declare(strict_types=1);

// start de sessie als die nog niet actief is
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    // instellingen voor de sessiecookie
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    // Start de sessie
    session_start();
}

// controleert of de gebruiker is ingelogd
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

// geeft de naam van de ingelogde gebruiker terug
function current_user_name(): string
{
    return $_SESSION['user_name'] ?? $_SESSION['user_username'] ?? 'speler';
}

// maakt een CSRF token aan als die nog niet bestaat
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

// maakt een verborgen CSRF veld voor formulieren
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

// controleert of het CSRF token geldig is
function require_csrf_token(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');

    if ($token === '' || !hash_equals(csrf_token(), $token)) {
        header('Location: /index.php?error=invalid_input');
        exit;
    }
}