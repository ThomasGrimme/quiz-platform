<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';


require_login();

// haalt het ID van de gekozen quiz op
$quizId = (int) ($_GET['id'] ?? 0);
$result = null;
$totalQuestions = 0;
$correctCount = 0;

if ($quizId > 0) {

    // haalt de quizgegevens op
    $qStmt = $pdo->prepare('SELECT id, titel, user_id FROM quizzes WHERE id = :id');
    $qStmt->execute(['id' => $quizId]);
    $quiz = $qStmt->fetch();

    if (!$quiz) {
        header('Location: /quiz_spelen.php');
        exit;
    }

    // haalt alle vragen van de quiz op
    $questionsStmt = $pdo->prepare('SELECT id, vraag_tekst FROM questions WHERE quiz_id = :quiz_id ORDER BY volgorde ASC');
    $questionsStmt->execute(['quiz_id' => $quizId]);
    $questions = $questionsStmt->fetchAll();

    // haalt alle antwoorden van de vragen op
    $answersByQuestion = [];
    if (!empty($questions)) {
        $ids = array_column($questions, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $aStmt = $pdo->prepare("SELECT id, antwoord_tekst, is_correct, question_id FROM answers WHERE question_id IN ($placeholders) ORDER BY volgorde ASC");
        $aStmt->execute($ids);

        foreach ($aStmt->fetchAll() as $row) {
            $answersByQuestion[(int) $row['question_id']][] = $row;
        }
    }

    $totalQuestions = count($questions);

    // verwerkt de ingevulde antwoorden
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $totalQuestions > 0) {
        require_csrf_token();

        $correctCount = 0;

        foreach ($questions as $q) {
            $qId = (int) $q['id'];
            $selected = (int) ($_POST['vraag_' . $qId] ?? -1);

            if (isset($answersByQuestion[$qId])) {
                foreach ($answersByQuestion[$qId] as $a) {
                    if ((int) $a['id'] === $selected && (int) $a['is_correct'] === 1) {
                        $correctCount++;
                        break;
                    }
                }
            }
        }

        // slaat de score op in de database
        $scoreStmt = $pdo->prepare('INSERT INTO scores (score, quiz_id, user_id) VALUES (:score, :quiz_id, :user_id)');
        $scoreStmt->execute([
            'score' => $correctCount,
            'quiz_id' => $quizId,
            'user_id' => (int) $_SESSION['user_id'],
        ]);

        $result = [
            'correct' => $correctCount,
            'total' => $totalQuestions
        ];
    }

} else {

    // haalt alle beschikbare quizzen op
    $quizzesStmt = $pdo->query('SELECT id, titel, user_id FROM quizzes ORDER BY aangemaakt_op DESC');
    $allQuizzes = $quizzesStmt->fetchAll();
}
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
</head>
<body>
<main class="page game-shell">

<?php if ($quizId < 1): ?>
    <header class="card game-topbar">
        <div class="stack">
            <div class="eyebrow">Quiz spelen</div>
            <h1>Kies een quiz</h1>
            <p class="muted">Selecteer een quiz om te spelen.</p>
        </div>
        <a class="button button-ghost" href="/dashboard.php">Dashboard</a>
    </header>

    <?php if (empty($allQuizzes)): ?>
        <div class="card" style="padding:24px;">
            <div class="empty-state">
                <h3>Nog geen quizzes</h3>
                <p class="muted">Er zijn nog geen quizzes beschikbaar.</p>
                <a class="button" href="/quiz_aanmaken.php">Quiz maken</a>
            </div>
        </div>
    <?php else: ?>
        <div class="list">
            <?php foreach ($allQuizzes as $qz): ?>
                <div class="list-item">
                    <div>
                        <strong><?= htmlspecialchars($qz['titel'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <span class="muted" style="font-size:0.85rem;">door <?= (int) $qz['user_id'] === (int) $_SESSION['user_id'] ? 'jou' : '' ?></span>
                    </div>
                    <a class="button" href="/quiz_spelen.php?id=<?= (int) $qz['id'] ?>">Spelen</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php elseif ($result): ?>
    <header class="card game-topbar">
        <div class="stack">
            <div class="eyebrow">Resultaat</div>
            <h1><?= htmlspecialchars($quiz['titel'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted">Je hebt de quiz voltooid!</p>
        </div>
        <div class="game-progress">
            <span class="pill" style="background:#d1fae5;color:#047857;"><?= $result['correct'] ?>/<?= $result['total'] ?> goed</span>
        </div>
    </header>

    <div class="card" style="padding:24px;text-align:center;">
        <div style="font-size:3rem;font-weight:900;letter-spacing:-0.04em;"><?= $result['correct'] ?>/<?= $result['total'] ?></div>
        <p class="muted" style="margin-top:8px;"><?= $result['correct'] === $result['total'] ? 'Perfect score!' : 'Probeer het nog eens.' ?></p>
        <div class="form-actions" style="justify-content:center;margin-top:16px;">
            <a class="button" href="/quiz_spelen.php?id=<?= $quizId ?>">Opnieuw spelen</a>
            <a class="button button-ghost" href="/quiz_spelen.php">Andere quiz</a>
            <a class="button button-ghost" href="/dashboard.php">Dashboard</a>
        </div>
    </div>

<?php else: ?>
    <form method="post">
        <?= csrf_field() ?>
        <header class="card game-topbar">
            <div class="stack">
                <div class="eyebrow">Quiz spelen</div>
                <h1><?= htmlspecialchars($quiz['titel'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="muted">Beantwoord alle vragen.</p>
            </div>
            <div class="game-progress">
                <span class="pill"><?= $totalQuestions ?> vragen</span>
            </div>
        </header>

        <section class="game-grid">
            <div class="card" style="padding:24px;display:grid;gap:24px;">
                <?php foreach ($questions as $q): ?>
                    <?php $qId = (int) $q['id']; ?>
                    <div>
                        <h3 style="margin-bottom:12px;"><?= htmlspecialchars($q['vraag_tekst'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <div class="answers">
                            <?php if (isset($answersByQuestion[$qId])): ?>
                                <?php foreach ($answersByQuestion[$qId] as $a): ?>
                                    <label class="answer-card" style="display:flex;align-items:center;gap:12px;cursor:pointer;">
                                        <input type="radio" name="vraag_<?= $qId ?>" value="<?= (int) $a['id'] ?>" required style="width:auto;min-height:auto;accent-color:var(--primary);">
                                        <?= htmlspecialchars($a['antwoord_tekst'], ENT_QUOTES, 'UTF-8') ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="card game-card scoreboard" style="padding:24px;">
                <div>
                    <div class="eyebrow">Status</div>
                    <h2>Speloverzicht</h2>
                </div>
                <div class="scoreboard-list">
                    <div class="scoreboard-item"><span>Vragen</span><strong><?= $totalQuestions ?></strong></div>
                </div>
                <button class="button" type="submit" style="width:100%;margin-top:12px;">Controleren</button>
                <a class="button button-ghost" href="/quiz_spelen.php" style="width:100%;margin-top:8px;">Annuleren</a>
            </aside>
        </section>
    </form>
<?php endif; ?>

</main>
</body>
</html>
