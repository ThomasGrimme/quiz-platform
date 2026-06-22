<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

require_login();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /quiz_aanmaken.php');
    exit;
}


require_csrf_token();


$titel = trim((string) ($_POST['titel'] ?? ''));

d
if ($titel === '') {
    header('Location: /quiz_aanmaken.php?error=empty_title');
    exit;
}


$statement = $pdo->prepare('INSERT INTO quizzes (titel, omschrijving, user_id) VALUES (:titel, NULL, :user_id)');
$statement->execute([
    'titel' => $titel,
    'user_id' => (int) $_SESSION['user_id'],
]);


header('Location: /quiz_bewerken.php?id=' . $pdo->lastInsertId());
exit;