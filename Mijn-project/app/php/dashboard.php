<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

require_login();

function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayeet | Dashboard</title>
    <link rel="stylesheet" href="/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/css/components.css?v=<?= time() ?>">
    <link rel="stylesheet" href="/css/dashboard.css?v=<?= time() ?>">
</head>
<body>

<main class="db-shell">
    
    <header class="db-topbar">
        <div class="db-title-group">
            <span class="db-eyebrow">Dashboard</span>
            <h1 class="db-main-heading">Welkom, <?= e(current_user_name()) ?></h1>
            <p class="db-muted-text">Fijn dat je er bent. Kies een actie hieronder om direct aan de slag te gaan.</p>
        </div>
        <div class="db-actions-group">
            <form action="/logout.php" method="post" style="display: inline; margin: 0;">
                <?= csrf_field() ?>
                <button class="button button-ghost" type="submit">Uitloggen</button>
            </form>
        </div>
    </header>

    <section class="db-metrics-row" aria-label="Statistieken">
        <article class="db-metric-block">
            <span class="db-eyebrow">Mijn quizzen</span>
            <span class="db-metric-num">0</span>
            <p class="db-muted-text">Nog geen quizzen aangemaakt.</p>
        </article>
        <article class="db-metric-block">
            <span class="db-eyebrow">Actieve spelers</span>
            <span class="db-metric-num">0</span>
            <p class="db-muted-text">Wachten op de eerste speelronde.</p>
        </article>
        <article class="db-metric-block">
            <span class="db-eyebrow">Gepubliceerd</span>
            <span class="db-metric-num">0</span>
            <p class="db-muted-text">Niets live, alles is veilig.</p>
        </article>
        <article class="db-metric-block">
            <span class="db-eyebrow">Voltooid</span>
            <span class="db-metric-num">0</span>
            <p class="db-muted-text">Nog geen afgeronde sessies.</p>
        </article>
    </section>

    <section class="db-main-split">
        
        <article class="db-content-area" aria-labelledby="lib-heading">
            <div class="db-section-header">
                <div class="db-title-stack">
                    <span class="db-eyebrow">Jouw archief</span>
                    <h2 id="lib-heading" class="db-section-heading">Overzicht</h2>
                </div>
            </div>

            <div class="empty-state">
                <span class="pill"><span class="status-dot"></span> Nog geen items</span>
                <h3>Je hebt nog geen quizzen.</h3>
                <p class="muted">Maak hiernaast je eerste quiz aan om je overzicht te vullen.</p>
            </div>
        </article>

        <aside class="db-sidebar-area" aria-labelledby="actions-heading">
            <div class="db-section-header">
                <div class="db-title-stack">
                    <span class="db-eyebrow">Snel starten</span>
                    <h2 id="actions-heading" class="db-section-heading">Acties</h2>
                </div>
            </div>
            
            <nav class="db-actions-list">
                <a class="db-action-item db-action-primary" href="/quiz_aanmaken.php">
                    <strong>Quiz aanmaken</strong>
                    <span class="db-muted-text">Ontwerp een nieuwe quiz met op maat gemaakte vragen.</span>
                </a>
                <a class="db-action-item" href="/quiz_spelen.php">
                    <strong>Quiz spelen</strong>
                    <span class="db-muted-text">Test direct een live speelsessie uit.</span>
                </a>
                <a class="db-action-item" href="/index.php">
                    <strong>Terug naar start</strong>
                    <span class="db-muted-text">Ga naar de landingspagina van Kayeet.</span>
                </a>
            </nav>
        </aside>
        
    </section>
</main>

</body>
</html>