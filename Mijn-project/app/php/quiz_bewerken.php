<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';

require_login();

$quizId = (int) ($_GET['id'] ?? 0);
if ($quizId < 1) {
    header('Location: /dashboard.php');
    exit;
}

$statement = $pdo->prepare('SELECT id, titel, user_id FROM quizzes WHERE id = :id LIMIT 1');
$statement->execute(['id' => $quizId]);
$quiz = $statement->fetch();

if (!$quiz || (int) $quiz['user_id'] !== (int) $_SESSION['user_id']) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf_token();

    $action = $_POST['action'] ?? '';

    if ($action === 'add_question') {
        $vraagTekst = trim((string) ($_POST['vraag_tekst'] ?? ''));
        $antwoorden = $_POST['antwoord'] ?? [];
        $correctIndex = (int) ($_POST['correct_antwoord'] ?? -1);

        if ($vraagTekst === '' || count($antwoorden) < 2) {
            $error = 'Voer een vraag en minimaal 2 antwoorden in.';
        } elseif ($correctIndex < 0 || $correctIndex >= count($antwoorden)) {
            $error = 'Selecteer het juiste antwoord.';
        } else {
            $maxOrder = $pdo->prepare('SELECT COALESCE(MAX(volgorde), 0) + 1 FROM questions WHERE quiz_id = :quiz_id');
            $maxOrder->execute(['quiz_id' => $quizId]);
            $nextOrder = (int) $maxOrder->fetchColumn();

            $qStmt = $pdo->prepare('INSERT INTO questions (vraag_tekst, quiz_id, volgorde) VALUES (:vraag_tekst, :quiz_id, :volgorde)');
            $qStmt->execute(['vraag_tekst' => $vraagTekst, 'quiz_id' => $quizId, 'volgorde' => $nextOrder]);
            $questionId = (int) $pdo->lastInsertId();

            $aStmt = $pdo->prepare('INSERT INTO answers (antwoord_tekst, is_correct, question_id, volgorde) VALUES (:antwoord_tekst, :is_correct, :question_id, :volgorde)');
            foreach ($antwoorden as $i => $antwoord) {
                $antwoord = trim((string) $antwoord);
                if ($antwoord === '') {
                    continue;
                }
                $aStmt->execute([
                    'antwoord_tekst' => $antwoord,
                    'is_correct' => $i === $correctIndex ? 1 : 0,
                    'question_id' => $questionId,
                    'volgorde' => $i + 1,
                ]);
            }

            $success = 'Vraag toegevoegd!';
        }
    } elseif ($action === 'delete_question') {
        $questionId = (int) ($_POST['question_id'] ?? 0);
        if ($questionId > 0) {
            $del = $pdo->prepare('DELETE q FROM questions q JOIN quizzes qz ON q.quiz_id = qz.id WHERE q.id = :id AND qz.user_id = :user_id');
            $del->execute(['id' => $questionId, 'user_id' => (int) $_SESSION['user_id']]);
            $success = 'Vraag verwijderd.';
        }
    }
}

$questionsStmt = $pdo->prepare('SELECT id, vraag_tekst, volgorde FROM questions WHERE quiz_id = :quiz_id ORDER BY volgorde ASC');
$questionsStmt->execute(['quiz_id' => $quizId]);
$questions = $questionsStmt->fetchAll();

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
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayeet | Quiz bewerken</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/quiz_bewerken.css?v=1">
</head>
<body>
<main class="page qb-shell">
    <header class="card" style="padding:24px;">
        <div class="stack">
            <div class="eyebrow">Quiz bewerken</div>
            <h1><?= htmlspecialchars($quiz['titel'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted">Voeg vragen en antwoorden toe aan deze quiz.</p>
        </div>
        <div class="form-actions" style="margin-top:16px;">
            <a class="button" href="/quiz_spelen.php?id=<?= $quizId ?>">Test spel</a>
            <a class="button button-ghost" href="/dashboard.php">Dashboard</a>
            <form method="post" class="inline-form" action="/quiz_verwijderen.php" onsubmit="return confirm('Weet je zeker dat je deze quiz wilt verwijderen?')">
                <?= csrf_field() ?>
                <input type="hidden" name="quiz_id" value="<?= $quizId ?>">
                <button class="button button-ghost" type="submit" style="color:#b91c1c;">Verwijder quiz</button>
            </form>
        </div>
    </header>

    <?php if ($error !== ''): ?>
        <div class="alert alert-error" style="margin-top:16px;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php elseif ($success !== ''): ?>
        <div class="alert alert-success" style="margin-top:16px;"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <section class="qb-two-col" style="margin-top:24px;">
        <article class="card" style="padding:24px;">
            <div class="stack">
                <div class="eyebrow">Nieuwe vraag</div>
                <h2>Vraag toevoegen</h2>
            </div>

            <form method="post" class="qb-form" style="margin-top:20px;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add_question">

                <div class="field">
                    <label for="vraag_tekst">Vraag</label>
                    <textarea id="vraag_tekst" name="vraag_tekst" rows="3" placeholder="Bijv: Wat is de hoofdstad van Frankrijk?" required></textarea>
                </div>

                <div class="qb-antwoorden" style="margin-top:16px;">
                    <label>Antwoorden</label>
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <div class="qb-antwoord-row" style="display:flex;align-items:center;gap:10px;margin-top:8px;">
                            <input type="radio" name="correct_antwoord" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?> required>
                            <input type="text" name="antwoord[]" placeholder="Antwoord <?= $i + 1 ?>" required style="flex:1;">
                        </div>
                    <?php endfor; ?>
                    <p class="muted" style="font-size:0.85rem;margin-top:6px;">Selecteer het juiste antwoord met de radio-knop.</p>
                </div>

                <div class="form-actions" style="margin-top:16px;">
                    <button class="button" type="submit">Vraag opslaan</button>
                </div>
            </form>
        </article>

        <aside class="card" style="padding:24px;">
            <div class="stack">
                <div class="eyebrow">Overzicht</div>
                <h2>Vragen (<?= count($questions) ?>)</h2>
            </div>

            <?php if (empty($questions)): ?>
                <div class="empty-state" style="margin-top:16px;">
                    <p class="muted">Nog geen vragen. Voeg je eerste vraag toe.</p>
                </div>
            <?php else: ?>
                <div class="list" style="margin-top:16px;">
                    <?php foreach ($questions as $q): ?>
                        <?php $qId = (int) $q['id']; ?>
                        <div class="qb-vraag-card" style="border:1px solid var(--border);border-radius:18px;padding:16px;">
                            <div style="display:flex;justify-content:space-between;align-items:start;gap:12px;">
                                <div style="flex:1;">
                                    <strong><?= htmlspecialchars($q['vraag_tekst'], ENT_QUOTES, 'UTF-8') ?></strong>
                                    <div class="chip-row" style="margin-top:8px;">
                                        <?php if (isset($answersByQuestion[$qId])): ?>
                                            <?php foreach ($answersByQuestion[$qId] as $a): ?>
                                                <span class="pill" style="<?= (int) $a['is_correct'] === 1 ? 'background:#d1fae5;color:#047857;' : '' ?>">
                                                    <?= (int) $a['is_correct'] === 1 ? '✓ ' : '' ?><?= htmlspecialchars($a['antwoord_tekst'], ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <form method="post" class="inline-form" onsubmit="return confirm('Vraag verwijderen?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete_question">
                                    <input type="hidden" name="question_id" value="<?= $qId ?>">
                                    <button class="button button-ghost" type="submit" style="min-height:36px;font-size:0.85rem;">Verwijder</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </aside>
    </section>
</main>
</body>
</html>
