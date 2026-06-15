<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

require_login();
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayeet | Quiz aanmaken</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/quiz_aanmaken.css">
</head>
<body>
<main class="page builder-shell">
    <section class="card builder-hero">
        <div class="eyebrow">Quiz bouwen</div>
        <h1>Maak een quiz die er strak uitziet.</h1>
        <p class="muted">Start met een titel, voeg later vragen toe en houd je flow overzichtelijk.</p>
    </section>

    <section class="builder-grid">
        <article class="card form-card builder-form">
            <div>
                <div class="eyebrow">Stap 1</div>
                <h2>Quizgegevens</h2>
            </div>

            <form class="builder-form" action="/php/quiz_opslaan.php" method="post">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="quiz_titel">Titel</label>
                    <input id="quiz_titel" name="titel" type="text" placeholder="Bijvoorbeeld: Algemene Kennis" required>
                </div>

                <div class="form-actions">
                    <a class="button button-ghost" href="/php/dashboard.php">Terug</a>
                    <button type="submit">Quiz opslaan</button>
                </div>
            </form>
        </article>

        <aside class="card panel-card">
            <div class="eyebrow">Slim opgebouwd</div>
            <h2>Handige checklist</h2>
            <div class="builder-steps">
                <div class="builder-step">
                    <span class="builder-step-index">1</span>
                    <div>
                        <strong>Titel kiezen</strong>
                        <p class="muted">Geef direct aan waar de quiz over gaat.</p>
                    </div>
                </div>
                <div class="builder-step">
                    <span class="builder-step-index">2</span>
                    <div>
                        <strong>Vragen toevoegen</strong>
                        <p class="muted">Bouw later de vragen netjes uit per onderdeel.</p>
                    </div>
                </div>
                <div class="builder-step">
                    <span class="builder-step-index">3</span>
                    <div>
                        <strong>Spel testen</strong>
                        <p class="muted">Controleer het spel voordat je hem deelt.</p>
                    </div>
                </div>
            </div>
        </aside>
    </section>
</main>
</body>
</html>
