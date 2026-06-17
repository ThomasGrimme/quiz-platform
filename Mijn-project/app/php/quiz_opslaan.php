<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';


require_login();

// controleert of het formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /quiz_aanmaken.php');
    exit;
}

// controleert het CSRF token
require_csrf_token();

// haalt de titel op
$titel = trim((string) ($_POST['titel'] ?? ''));

// controleert of er een titel is ingevuld
if ($titel === '') {
    header('Location: /quiz_aanmaken.php?error=empty_title');
    exit;
}

// slaat de nieuwe quiz op in de database
$statement = $pdo->prepare('INSERT INTO quizzes (titel, omschrijving, user_id) VALUES (:titel, NULL, :user_id)');
$statement->execute([
    'titel' => $titel,
    'user_id' => (int) $_SESSION['user_id'],
]);

// stuurt de gebruiker naar de pagina om de quiz te bewerken
header('Location: /quiz_bewerken.php?id=' . $pdo->lastInsertId());
exit;