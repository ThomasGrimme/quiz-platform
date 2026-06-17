<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

// controleert of de gebruiker is ingelogd
require_login();

// zet speciale tekens om naar veilige HTML
function e(string $val): string
{
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

$userId = (int) $_SESSION['user_id'];
$myQuizzes = $pdo->prepare('SELECT id, titel, aangemaakt_op FROM quizzes WHERE user_id = :user_id ORDER BY aangemaakt_op DESC');
$myQuizzes->execute(['user_id' => $userId]);
$quizzes = $myQuizzes->fetchAll();

// telt het aantal quizzen en vragen
$quizCount = count($quizzes);
$totalQuestions = 0;

if ($quizCount > 0) {
    $ids = array_column($quizzes, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $qStmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id IN ($placeholders)");
    $qStmt->execute($ids);
    $totalQuestions = (int) $qStmt->fetchColumn();
}
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayeet | Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/dashboard.css">
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
            <form action="/logout.php" method="post" class="logout-form">
                <?= csrf_field() ?>
                <button class="button button-ghost" type="submit">Uitloggen</button>
            </form>
        </div>
    </header>

    <section class="db-metrics-row" aria-label="Statistieken">
        <article class="db-metric-block">
            <span class="db-eyebrow">Mijn quizzen</span>
            <span class="db-metric-num"><?= $quizCount ?></span>
            <p class="db-muted-text"><?= $quizCount === 0 ? 'Nog geen quizzen aangemaakt.' : 'Totaal aantal quizzen.' ?></p>
        </article>

        <article class="db-metric-block">
            <span class="db-eyebrow">Vragen</span>
            <span class="db-metric-num"><?= $totalQuestions ?></span>
            <p class="db-muted-text">Totaal aantal vragen.</p>
        </article>

        <article class="db-metric-block">
            <span class="db-eyebrow">Gepubliceerd</span>
            <span class="db-metric-num"><?= $quizCount ?></span>
            <p class="db-muted-text">Klaar om te spelen.</p>
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

            <?php if (empty($quizzes)): ?>
                <div class="empty-state">
                    <span class="pill"><span class="status-dot"></span> Nog geen items</span>
                    <h3>Je hebt nog geen quizzen.</h3>
                    <p class="muted">Maak hiernaast je eerste quiz aan om je overzicht te vullen.</p>
                    <a class="button" href="/quiz_aanmaken.php">Quiz maken</a>
                </div>
            <?php else: ?>
                <div class="list">
                    <?php foreach ($quizzes as $qz): ?>
                        <div class="list-item">
                            <div>
                                <strong><?= e($qz['titel']) ?></strong>
                                <span class="muted quiz-date"><?= e($qz['aangemaakt_op']) ?></span>
                            </div>

                            <div class="form-actions quiz-actions">
                                <a class="button button-ghost quiz-button" href="/quiz_bewerken.php?id=<?= (int) $qz['id'] ?>">Bewerk</a>

                                <a class="button quiz-button" href="/quiz_spelen.php?id=<?= (int) $qz['id'] ?>">Speel</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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