<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_login();

$error = $_GET['error'] ?? '';
$message = $_GET['message'] ?? '';
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

<main class="qb-shell">

    <?php if ($message === 'quiz_aangemaakt'): ?>
        <div class="alert alert-success qb-alert">Quiz opgeslagen! Voeg nu vragen toe.</div>
    <?php elseif ($error === 'empty_title'): ?>
        <div class="alert alert-error qb-alert">Vul een titel in.</div>
    <?php endif; ?>

    <section class="qb-hero">
        <span class="qb-eyebrow">Quiz bouwen</span>
        <h1 class="qb-main-heading">Maak een quiz die er strak uitziet.</h1>
        <p class="qb-muted-text">Start met een titel, voeg later vragen toe en houd je flow overzichtelijk.</p>
    </section>

    <section class="qb-workspace-split">

        <article class="qb-form-box">
            <div class="qb-section-header">
                <span class="qb-eyebrow">Stap 1</span>
                <h2 class="qb-section-heading">Quizgegevens</h2>
            </div>

            <form action="/quiz_opslaan.php" method="post" class="qb-form">
                <?= csrf_field() ?>

                <div class="qb-input-field">
                    <label for="quiz_titel">Titel van je quiz</label>
                    <input id="quiz_titel" name="titel" type="text" placeholder="Bijvoorbeeld: Algemene Kennis" required autocomplete="off">
                </div>

                <div class="qb-form-actions">
                    <a class="button button-ghost" href="/dashboard.php">Terug naar dashboard</a>
                    <button class="button" type="submit">Quiz opslaan</button>
                </div>
            </form>
        </article>

        <aside class="qb-sidebar-box">
            <div class="qb-section-header">
                <span class="qb-eyebrow">Slim opgebouwd</span>
                <h2 class="qb-section-heading">Handige checklist</h2>
            </div>

            <div class="qb-steps-stack">
                <div class="qb-step-item qb-step-active">
                    <span class="qb-step-num">1</span>
                    <div class="qb-step-content">
                        <strong>Titel kiezen</strong>
                        <p class="qb-muted-text">Geef direct aan waar de quiz over gaat.</p>
                    </div>
                </div>

                <div class="qb-step-item">
                    <span class="qb-step-num">2</span>
                    <div class="qb-step-content">
                        <strong>Vragen toevoegen</strong>
                        <p class="qb-muted-text">Bouw later de vragen netjes uit per onderdeel.</p>
                    </div>
                </div>

                <div class="qb-step-item">
                    <span class="qb-step-num">3</span>
                    <div class="qb-step-content">
                        <strong>Spel testen</strong>
                        <p class="qb-muted-text">Controleer het spel voordat je hem deelt.</p>
                    </div>
                </div>
            </div>
        </aside>

    </section>

</main>

</body>
</html>