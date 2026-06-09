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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
    <link rel="stylesheet" href="index.css">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        /* Schone tab verwerking zonder inline JS hacks */
        .auth-tabs {
            display: flex;
            background: #f1f5f9;
            padding: 6px;
            border-radius: 14px;
            margin-bottom: 8px;
        }
        .tab-btn {
            flex: 1;
            padding: 12px;
            border: none;
            background: transparent;
            font-weight: 800;
            font-size: 0.95rem;
            color: #64748b;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .tab-btn.active {
            background: #ffffff;
            color: var(--text-dark, #1e293b);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .auth-tab-content {
            display: none;
        }
        .auth-tab-content.active {
            display: block;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .form-group label {
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--text-dark, #1e293b);
        }
        /* Alerts */
        .alert {
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .alert-success { background: #ecfdf5; color: #065f46; border: 2px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 2px solid #fca5a5; }
    </style>
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
                <div class="alert alert-success">
                    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            
            <?php if ($errorMessage !== ''): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <div class="auth-tabs">
                <button type="button" class="tab-btn active" data-target="login-section">Inloggen</button>
                <button type="button" class="tab-btn" data-target="register-section">Registreren</button>
            </div>

            <div id="login-section" class="auth-tab-content active">
                <form class="auth-form" action="login_verwerk.php" method="post">
                    <div class="form-group">
                        <label for="login_email">E-mailadres</label>
                        <input id="login_email" name="email" type="email" placeholder="jouw@email.nl" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_password">Wachtwoord</label>
                        <input id="login_password" name="password" type="password" placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit">Inloggen</button>
                </form>
            </div>

            <div id="register-section" class="auth-tab-content">
                <form class="auth-form" action="registreer_verwerk.php" method="post">
                    <div class="form-group">
                        <label for="register_name">Naam</label>
                        <input id="register_name" name="naam" type="text" placeholder="Bijv. Mark" required>
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
$(document).ready(function() {
    $('.tab-btn').on('click', function() {
        const target = $(this).data('target');

        // Wissel actieve tab knop klasse
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        
        // Wissel formulieren via CSS klassen
        $('.auth-tab-content').removeClass('active');
        $('#' + target).addClass('active');
        
        // Focus op eerste invoerveld
        $('#' + target + ' input:first').focus();
    });
});
</script>

</body>
</html>