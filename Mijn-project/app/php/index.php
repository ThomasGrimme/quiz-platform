<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if (is_logged_in()) {
    header('Location: /dashboard.php');
    exit;
}

$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';

$errorMessage = match ($error) {
    'login_required' => 'Log eerst in om verder te gaan.',
    'invalid_login' => 'Gebruikersnaam, e-mail of wachtwoord klopt niet.',
    'duplicate_email' => 'Dit e-mailadres is al geregistreerd.',
    'duplicate_username' => 'Deze gebruikersnaam is al in gebruik.',
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/index.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<main class="auth-shell">
    <section class="brand-panel">
        <h1>Kayeet!</h1>
        <p class="muted">Speel, bouw en beheer quizzes zonder rommel. Inloggen, registreren en direct aan de slag.</p>

        <div class="feature-grid">
            <article class="feature-card"><strong>▲ Snelle start</strong></article>
            <article class="feature-card"><strong>◆ Quizbeheer</strong></article>
            <article class="feature-card"><strong>■ Live statistieken</strong></article>
            <article class="feature-card"><strong>● Volledig gratis</strong></article>
        </div>
    </section>

    <section class="auth-panel">
        <div class="auth-stack">
            <?php if ($message !== ''): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if ($errorMessage !== ''): ?>
                <div class="alert alert-error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="auth-tabs">
                <button type="button" class="tab-btn active" data-target="login-section">Inloggen</button>
                <button type="button" class="tab-btn" data-target="register-section">Registreren</button>
            </div>

            <div id="login-section" class="auth-tab-content active">
                <form class="auth-form" action="/login_verwerk.php" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="login_identifier">Gebruikersnaam of e-mailadres</label>
                        <input id="login_identifier" name="identifier" type="text" placeholder="jouwnaam of jouw@email.nl" required autofocus data-autofocus>
                    </div>

                    <div class="form-group">
                        <label for="login_password">Wachtwoord</label>
                        <input id="login_password" name="password" type="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit">Inloggen</button>
                </form>
            </div>

            <div id="register-section" class="auth-tab-content">
                <form class="auth-form" action="/registreer_verwerk.php" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="register_name">Naam</label>
                        <input id="register_name" name="name" type="text" placeholder="Bijv. Mark" required>
                    </div>

                    <div class="form-group">
                        <label for="register_username">Gebruikersnaam</label>
                        <input id="register_username" name="gebruikersnaam" type="text" placeholder="bijv. mark123" required>
                    </div>

                    <div class="form-group">
                        <label for="register_email">E-mailadres</label>
                        <input id="register_email" name="email" type="email" placeholder="jouw@email.nl" required>
                    </div>

                    <div class="form-group">
                        <label for="register_password">Wachtwoord</label>
                        <input id="register_password" name="password" type="password" placeholder="Minimaal 8 tekens" required>
                    </div>

                    <div class="form-group">
                        <label for="register_password_confirm">Bevestig wachtwoord</label>
                        <input id="register_password_confirm" name="password_confirm" type="password" placeholder="Herhaal je wachtwoord" required>
                    </div>

                    <button type="submit">Account maken</button>
                </form>
            </div>

            <p class="auth-note">Kayeet is een educatief open-source project.</p>
        </div>
    </section>
</main>

<script>
$(function () {
    $('.tab-btn').on('click', function () {
        const target = $(this).data('target');

        $('.tab-btn').removeClass('active');
        $(this).addClass('active');

        $('.auth-tab-content').removeClass('active');
        $('#' + target).addClass('active');

        $('#' + target + ' input:first').trigger('focus');
    });
});
</script>
</body>
</html>
