<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';

$errorMessage = match ($error) {
    'login_required' => 'Log eerst in om verder te gaan.',
    'invalid_login' => 'E-mail of wachtwoord klopt niet.',
    'duplicate_email' => 'Dit e-mailadres is al geregistreerd.',
    'invalid_input' => 'Controleer de ingevulde gegevens.',
    default => '',
};
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayeet | Inloggen en registreren</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="script.js" defer></script>
</head>
<body>
<main class="page auth-shell">
    <section class="card brand-panel">
        <div class="eyebrow">Kayeet</div>
        <h1>Speel, bouw en beheer quizzes zonder rommel.</h1>
        <p class="muted">Een helder startscherm voor jouw quizplatform. Inloggen, registreren en direct aan de slag.</p>

        <div class="feature-grid">
            <article class="feature-card">
                <strong>Snelle toegang</strong>
                <p class="muted">Log in of maak meteen een account aan.</p>
            </article>
            <article class="feature-card">
                <strong>Quizbeheer</strong>
                <p class="muted">Ga direct door naar je dashboard en quizschermen.</p>
            </article>
        </div>
    </section>

    <section class="card auth-panel auth-stack">
        <div>
            <div class="eyebrow">Inloggen</div>
            <h2>Welkom terug</h2>
        </div>
        <?php if ($message !== ''): ?>
            <p class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if ($errorMessage !== ''): ?>
            <p class="alert alert-error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <form class="auth-form" action="login_verwerk.php" method="post">
            <div class="field">
                <label for="login_email">E-mail</label>
                <input id="login_email" name="email" type="email" data-autofocus required>
            </div>
            <div class="field">
                <label for="login_password">Wachtwoord</label>
                <input id="login_password" name="password" type="password" required>
            </div>
            <button type="submit">Inloggen</button>
        </form>

        <div>
            <div class="eyebrow">Registreren</div>
            <h2>Nieuw account</h2>
        </div>
        <form class="auth-form" action="registreer_verwerk.php" method="post">
            <div class="field">
                <label for="register_name">Naam</label>
                <input id="register_name" name="naam" type="text" required>
            </div>
            <div class="field">
                <label for="register_email">E-mail</label>
                <input id="register_email" name="email" type="email" required>
            </div>
            <div class="field">
                <label for="register_password">Wachtwoord</label>
                <input id="register_password" name="password" type="password" required>
            </div>
            <div class="field">
                <label for="register_password_confirm">Bevestig wachtwoord</label>
                <input id="register_password_confirm" name="password_confirm" type="password" required>
            </div>
            <button type="submit">Account maken</button>
        </form>
    </section>
</main>
</body>
</html>
