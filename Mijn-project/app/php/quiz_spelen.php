<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_login();

$quizId = (int) ($_POST['quiz_id'] ?? $_GET['id'] ?? 0);
$result = null;
$totalQuestions = 0;
$correctCount = 0;
$quizJsonData = [];

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

    // Build JSON data for the game frontend
    if ($totalQuestions > 0) {
        foreach ($questions as $q) {
            $qId = (int) $q['id'];
            $answers = [];
            $correctId = null;
            foreach ($answersByQuestion[$qId] ?? [] as $a) {
                $aId = (int) $a['id'];
                $answers[] = ['id' => $aId, 'text' => $a['antwoord_tekst']];
                if ((int) $a['is_correct'] === 1) {
                    $correctId = $aId;
                }
            }
            $quizJsonData[] = [
                'id' => $qId,
                'text' => $q['vraag_tekst'],
                'answers' => $answers,
                'correct_id' => $correctId,
            ];
        }
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

<?php elseif ($result):
    $percentage = $totalQuestions > 0 ? round($result['correct'] / $result['total'] * 100) : 0;
    $wrong = $totalQuestions - $result['correct'];
?>
    <div class="result-hero">
        <div class="result-score-wrap">
            <span class="result-correct"><?= $result['correct'] ?></span>
            <span class="result-slash">/</span>
            <span class="result-total"><?= $result['total'] ?></span>
        </div>
        <div class="result-label"><?= $percentage ?>% goed</div>
    </div>

    <div class="result-stats">
        <div class="result-stat">
            <span class="result-stat-value" style="color:#16a34a;"><?= $result['correct'] ?></span>
            <span class="result-stat-label">Correct</span>
        </div>
        <div class="result-stat">
            <span class="result-stat-value" style="color:#dc2626;"><?= $wrong ?></span>
            <span class="result-stat-label">Fout</span>
        </div>
        <div class="result-stat">
            <span class="result-stat-value"><?= $result['total'] ?></span>
            <span class="result-stat-label">Totaal</span>
        </div>
    </div>

    <div class="result-actions">
        <a class="button" href="/quiz_spelen.php?id=<?= $quizId ?>">Opnieuw spelen</a>
        <a class="button button-ghost" href="/quiz_spelen.php">Andere quiz</a>
        <a class="button button-ghost" href="/dashboard.php">Dashboard</a>
    </div>

<?php else: ?>
    <?php if ($totalQuestions === 0): ?>
        <div class="card" style="padding:24px;text-align:center;">
            <div class="stack" style="align-items:center;">
                <h2>Geen vragen</h2>
                <p class="muted">Deze quiz heeft nog geen vragen.</p>
                <a class="button" href="/dashboard.php">Dashboard</a>
            </div>
        </div>
    <?php else: ?>
    <header class="card game-topbar">
        <div class="stack">
            <div class="eyebrow">Quiz spelen</div>
            <h1><?= htmlspecialchars($quiz['titel'], ENT_QUOTES, 'UTF-8') ?></h1>
        </div>
        <div class="game-stats">
            <div class="progress-track">
                <div class="progress-fill" id="progressFill" style="width:0%"></div>
            </div>
            <div class="stat-row">
                <span id="questionCounter">Vraag 1 / <?= $totalQuestions ?></span>
                <span id="scoreDisplay">Score: 0</span>
                <div class="timer" id="timerDisplay">15</div>
            </div>
        </div>
    </header>

    <form method="post" id="quizForm">
        <?= csrf_field() ?>
        <input type="hidden" name="quiz_id" value="<?= $quizId ?>">

        <?php foreach ($quizJsonData as $index => $qd): ?>
        <div class="question-step<?= $index === 0 ? ' active' : '' ?>" data-index="<?= $index ?>" data-question-id="<?= $qd['id'] ?>" data-correct-id="<?= $qd['correct_id'] ?>">
            <h2 class="question-text"><?= htmlspecialchars($qd['text'], ENT_QUOTES, 'UTF-8') ?></h2>
            <div class="answer-grid">
                <?php
                $colors = ['red', 'blue', 'yellow', 'green'];
                $ci = 0;
                ?>
                <?php foreach ($qd['answers'] as $a): ?>
                <button type="button" class="answer-btn answer-btn--<?= $colors[$ci % 4] ?>" data-answer-id="<?= $a['id'] ?>">
                    <?= htmlspecialchars($a['text'], ENT_QUOTES, 'UTF-8') ?>
                </button>
                <?php $ci++; endforeach; ?>
            </div>
            <input type="hidden" name="vraag_<?= $qd['id'] ?>" value="">
        </div>
        <?php endforeach; ?>

        <div class="game-nav">
            <button type="button" class="button" id="nextBtn" style="display:none">Volgende &rarr;</button>
            <button type="submit" class="button" id="finishBtn" style="display:none">Bekijk resultaat</button>
        </div>
    </form>
    <?php endif; ?>
<?php endif; ?>

</main>
<script src="/js/script.js"></script>
</body>
</html>
