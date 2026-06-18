<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_login();

function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

$userId = (int) $_SESSION['user_id'];

$myQuizzes = $pdo->prepare('SELECT id, titel, aangemaakt_op FROM quizzes WHERE user_id = :user_id ORDER BY aangemaakt_op DESC');
$myQuizzes->execute(['user_id' => $userId]);
$quizzes = $myQuizzes->fetchAll();

$quizCount = count($quizzes);
$totalQuestions = 0;

if ($quizCount > 0) {
    $ids = array_column($quizzes, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $qStmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE quiz_id IN ($placeholders)");
    $qStmt->execute($ids);
    $totalQuestions = (int) $qStmt->fetchColumn();
}

$scoresStmt = $pdo->prepare('SELECT COUNT(*) as plays, COALESCE(ROUND(AVG(score)), 0) as avg_score FROM scores WHERE user_id = :user_id');
$scoresStmt->execute(['user_id' => $userId]);
$scoresData = $scoresStmt->fetch();
if (!$scoresData) {
    $scoresData = ['plays' => 0, 'avg_score' => 0];
}
$totalPlays = (int) $scoresData['plays'];
$avgScore = (int) $scoresData['avg_score'];
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
<main class="dash">
    <header class="dash-top card">
        <div class="dash-greet">
            <span class="dash-eyebrow">Dashboard</span>
            <h1 class="dash-heading">Welkom, <?= e(current_user_name()) ?></h1>
            <p class="dash-sub">Beheer je quizzen en speel ze.</p>
        </div>
        <form action="/logout.php" method="post" style="margin:0;">
            <?= csrf_field() ?>
            <button class="button button-ghost" type="submit">Uitloggen</button>
        </form>
    </header>

    <section class="dash-stats">
        <article class="dash-stat">
            <span class="dash-stat-num"><?= $quizCount ?></span>
            <span class="dash-stat-label">Quizzen</span>
        </article>
        <article class="dash-stat">
            <span class="dash-stat-num"><?= $totalQuestions ?></span>
            <span class="dash-stat-label">Vragen</span>
        </article>
        <article class="dash-stat">
            <span class="dash-stat-num"><?= $totalPlays ?></span>
            <span class="dash-stat-label">Keer gespeeld</span>
        </article>
        <article class="dash-stat">
            <span class="dash-stat-num"><?= $avgScore ?></span>
            <span class="dash-stat-label">Gemiddelde score</span>
        </article>
    </section>

    <div class="dash-split">
        <section>
            <div class="dash-section-header">
                <div class="dash-eyebrow">Jouw quizzen</div>
                <h2>Overzicht</h2>
            </div>

            <?php if (empty($quizzes)): ?>
                <div class="card" style="padding:40px 24px;text-align:center;">
                    <h3 style="margin-bottom:8px;">Nog geen quizzen</h3>
                    <p class="muted" style="margin-bottom:20px;">Maak je eerste quiz om te beginnen.</p>
                    <a class="button" href="/quiz_aanmaken.php">Quiz maken</a>
                </div>
            <?php else: ?>
                <div class="dash-list">
                    <?php foreach ($quizzes as $qz): ?>
                        <div class="dash-item card">
                            <div class="dash-item-body">
                                <strong class="dash-item-title"><?= e($qz['titel']) ?></strong>
                                <span class="dash-item-date"><?= e($qz['aangemaakt_op']) ?></span>
                            </div>
                            <div class="dash-item-actions">
                                <a class="button" href="/quiz_spelen.php?id=<?= (int) $qz['id'] ?>" style="min-height:36px;font-size:0.85rem;">Speel</a>
                                <a class="button button-ghost" href="/quiz_bewerken.php?id=<?= (int) $qz['id'] ?>" style="min-height:36px;font-size:0.85rem;">Bewerk</a>
                                <form method="post" action="/quiz_verwijderen.php" style="display:inline;margin:0;" onsubmit="return confirm('Weet je zeker dat je deze quiz wilt verwijderen?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="quiz_id" value="<?= (int) $qz['id'] ?>">
                                    <button class="button button-ghost" type="submit" style="min-height:36px;font-size:0.85rem;color:#dc2626;border-color:#fecaca;">Verwijder</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside>
            <div class="dash-section-header">
                <div class="dash-eyebrow">Snel starten</div>
                <h2>Acties</h2>
            </div>
            <nav class="dash-nav">
                <a class="dash-nav-item" href="/quiz_aanmaken.php">
                    <strong>Quiz aanmaken</strong>
                    <span class="muted" style="font-size:0.85rem;">Nieuwe quiz ontwerpen</span>
                </a>
                <a class="dash-nav-item" href="/quiz_spelen.php">
                    <strong>Quiz spelen</strong>
                    <span class="muted" style="font-size:0.85rem;">Speel een bestaande quiz</span>
                </a>
                <a class="dash-nav-item" href="/index.php">
                    <strong>Home</strong>
                    <span class="muted" style="font-size:0.85rem;">Terug naar de startpagina</span>
                </a>
            </nav>
        </aside>
    </div>
</main>
</body>
</html>