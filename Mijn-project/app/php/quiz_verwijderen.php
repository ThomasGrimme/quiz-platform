<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /dashboard.php');
    exit;
}

require_csrf_token();

$quizId = (int) ($_POST['quiz_id'] ?? 0);

if ($quizId < 1) {
    header('Location: /dashboard.php');
    exit;
}


$statement = $pdo->prepare('DELETE FROM quizzes WHERE id = :id AND user_id = :user_id');
$statement->execute([
    'id' => $quizId,
    'user_id' => (int) $_SESSION['user_id'],
]);

header('Location: /dashboard.php');
exit;