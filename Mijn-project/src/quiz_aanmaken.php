<?php
declare(strict_types=1);
session_start();
?>
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayeet | Quiz aanmaken</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="page stack">
    <section class="card stack">
        <h1>Quiz aanmaken</h1>
        <form class="stack" action="quiz_opslaan.php" method="post">
            <div>
                <label for="quiz_titel">Titel</label>
                <input id="quiz_titel" name="titel" type="text" required>
            </div>
            <button type="submit">Opslaan</button>
        </form>
    </section>
</main>
</body>
</html>
