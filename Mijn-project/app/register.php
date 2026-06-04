<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayeet Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <div class="card">

        <h1>Registreren</h1>
        <p>Maak een account aan om quizzen te maken en te spelen.</p>

        <form action="" method="POST">

            <label for="username">Gebruikersnaam</label>
            <input type="text" id="username" name="username" required>

            <label for="email">E-mailadres</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Wachtwoord</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Bevestig wachtwoord</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" class="btn">
                Account aanmaken
            </button>

        </form>

        <p class="login-link">
            Heb je al een account?
            <a href="login.php">Log hier in</a>
        </p>

    </div>
</div>

</body>
</html>