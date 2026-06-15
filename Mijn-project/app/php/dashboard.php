<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

require_login();

$userName = current_user_name();
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayeet | Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="components.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
<main class="page dashboard-shell">
    <header class="card dashboard-topbar">
        <div class="stack">
            <div class="eyebrow">Dashboard</div>
            <h1>Welkom, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted">Kies een quiz, start er één, of ga meteen bouwen.</p>
        </div>

        <div class="dashboard-actions">
            <a class="button button-secondary" href="quiz_spelen.php">Spelen</a>
            <a class="button" href="quiz_aanmaken.php">Nieuwe quiz</a>
            <a class="button button-ghost" href="logout.php">Uitloggen</a>
        </div>
    </header>

    <section class="dashboard-metrics">
        <article class="card metric-card">
            <div class="eyebrow">Mijn quizzes</div>
            <div class="metric-value">0</div>
            <p class="muted">Nog geen quizzen aangemaakt.</p>
        </article>
        <article class="card metric-card">
            <div class="eyebrow">Actieve spelers</div>
            <div class="metric-value">0</div>
            <p class="muted">Wachten op de eerste speelronde.</p>
        </article>
        <article class="card metric-card">
            <div class="eyebrow">Gepubliceerd</div>
            <div class="metric-value">0</div>
            <p class="muted">Niets live, dus alles nog veilig.</p>
        </article>
        <article class="card metric-card">
            <div class="eyebrow">Voltooid</div>
            <div class="metric-value">0</div>
            <p class="muted">Nog geen afgeronde sessies.</p>
        </article>
    </section>

    <section class="dashboard-grid">
        <article class="card quiz-library">
            <div class="section-title">
                <div>
                    <div class="eyebrow">Jouw quizzen</div>
                    <h2>Overzicht</h2>
                </div>
                <a class="button button-ghost" href="quiz_aanmaken.php">Quiz maken</a>
            </div>

            <div class="empty-state">
                <span class="pill"><span class="status-dot"></span> Nog geen items</span>
                <h3>Je hebt nog geen quizzen.</h3>
                <p class="muted">Maak je eerste quiz aan om hier meteen een mooi overzicht te krijgen.</p>
                <a class="button" href="quiz_aanmaken.php">Start met bouwen</a>
            </div>
        </article>

        <aside class="card quick-actions">
            <div>
                <div class="eyebrow">Snel starten</div>
                <h2>Acties</h2>
            </div>

            <a class="quick-action" href="quiz_aanmaken.php">
                <strong>Quiz aanmaken</strong>
                <span class="muted">Nieuwe quiz, vragen en instellingen.</span>
            </a>
            <a class="quick-action" href="quiz_spelen.php">
                <strong>Quiz spelen</strong>
                <span class="muted">Test de speelervaring meteen uit.</span>
            </a>
            <a class="quick-action" href="index.php">
                <strong>Terug naar start</strong>
                <span class="muted">Meld je opnieuw aan of registreer een gebruiker.</span>
            </a>
        </aside>
    </section>
</main>
</body>
</html>
