<?php
declare(strict_types=1);

// Start de sessie als die nog niet actief is
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    // Instellingen voor de sessiecookie
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

// Controleert of de gebruiker is ingelogd
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}


function current_user_name(): string
{
    return $_SESSION['user_name'] ?? $_SESSION['user_username'] ?? 'speler';
}

// Maakt een CSRF-token aan als die nog niet bestaat
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

// Maakt een verborgen CSRF veld voor formulieren
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

// Controleert of het CSRFtoken geldig is
function require_csrf_token(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');

    if ($token === '' || !hash_equals(csrf_token(), $token)) {
        header('Location: /index.php?error=invalid_input');
        exit;
    }
}