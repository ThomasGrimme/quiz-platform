<?php
declare(strict_types=1);
require_once __DIR__ . '/php/auth.php';

require_login();
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayeet | Quiz spelen</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/quiz_spelen.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/js/script.js" defer></script>
</head>
<body>
<main class="page game-shell">
    <header class="card game-topbar">
        <div class="stack">
            <div class="eyebrow">Quiz spelen</div>
            <h1>Klaar om te starten</h1>
            <p class="muted">Hier komt later de jQuery-loop met vragen en antwoorden.</p>
        </div>

        <div class="game-progress">
            <span class="pill">Vraag 1 van 10</span>
            <span class="pill">Tijd 00:30</span>
        </div>
    </header>

    <section class="game-grid">
        <article class="card game-card question-card">
            <div class="eyebrow">Vraag</div>
            <h2>Vragen worden hier geladen.</h2>
            <div id="quiz-content" class="answers">
                <button class="answer-card" type="button" disabled>Antwoordopties verschijnen hier</button>
                <button class="answer-card" type="button" disabled>Antwoordopties verschijnen hier</button>
                <button class="answer-card" type="button" disabled>Antwoordopties verschijnen hier</button>
                <button class="answer-card" type="button" disabled>Antwoordopties verschijnen hier</button>
            </div>
        </article>

        <aside class="card game-card scoreboard">
            <div>
                <div class="eyebrow">Status</div>
                <h2>Speloverzicht</h2>
            </div>

            <div class="scoreboard-list">
                <div class="scoreboard-item">
                    <span>Beantwoord</span>
                    <strong>0</strong>
                </div>
                <div class="scoreboard-item">
                    <span>Goed</span>
                    <strong>0</strong>
                </div>
                <div class="scoreboard-item">
                    <span>Fout</span>
                    <strong>0</strong>
                </div>
            </div>
        </aside>
    </section>
</main>
</body>
</html>
