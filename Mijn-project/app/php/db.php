<?php
declare(strict_types=1);

// databasegegevens ophalen
$host = getenv('DB_HOST') ?: 'mysql';
$dbname = getenv('DB_NAME') ?: 'database';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'root';

// verbinding maken met de database
$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // maakt de tabellen aan en voegt testgegevens toe
    ensureSchema($pdo);
    seedTestUser($pdo);
} catch (PDOException ) {
    http_response_code(500);
    exit('Database connection failed.');
}

// maakt alle benodigde tabellen aan
function ensureSchema(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            gebruikersnaam VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            wachtwoord_hash VARCHAR(255) NOT NULL,
            aangemaakt_op TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            laatst_ingelogd_op TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_users_gebruikersnaam (gebruikersnaam),
            UNIQUE KEY uniq_users_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    if (columnExists($pdo, 'users', 'password_hash') && !columnExists($pdo, 'users', 'wachtwoord_hash')) {
        $pdo->exec('ALTER TABLE users CHANGE password_hash wachtwoord_hash VARCHAR(255) NOT NULL');
    }

    if (columnExists($pdo, 'users', 'created_at') && !columnExists($pdo, 'users', 'aangemaakt_op')) {
        $pdo->exec('ALTER TABLE users CHANGE created_at aangemaakt_op TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    if (!columnExists($pdo, 'users', 'gebruikersnaam')) {
        $pdo->exec('ALTER TABLE users ADD COLUMN gebruikersnaam VARCHAR(100) NULL AFTER name');
    }

    if (!columnExists($pdo, 'users', 'name')) {
        $pdo->exec('ALTER TABLE users ADD COLUMN name VARCHAR(100) NOT NULL DEFAULT "" AFTER id');
    }

    if (!columnExists($pdo, 'users', 'aangemaakt_op')) {
        $pdo->exec('ALTER TABLE users ADD COLUMN aangemaakt_op TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    if (!columnExists($pdo, 'users', 'laatst_ingelogd_op')) {
        $pdo->exec('ALTER TABLE users ADD COLUMN laatst_ingelogd_op TIMESTAMP NULL DEFAULT NULL');
    }

    backfillUsernames($pdo);
    ensureIndex($pdo, 'users', 'uniq_users_gebruikersnaam', 'UNIQUE KEY uniq_users_gebruikersnaam (gebruikersnaam)');
    ensureIndex($pdo, 'users', 'uniq_users_email', 'UNIQUE KEY uniq_users_email (email)');

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS quizzes (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            titel VARCHAR(150) NOT NULL,
            omschrijving TEXT NULL,
            user_id INT UNSIGNED NOT NULL,
            aangemaakt_op TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_quizzes_user_id (user_id),
            CONSTRAINT fk_quizzes_user
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS questions (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            vraag_tekst TEXT NOT NULL,
            quiz_id INT UNSIGNED NOT NULL,
            volgorde INT UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            KEY idx_questions_quiz_id (quiz_id),
            CONSTRAINT fk_questions_quiz
                FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS answers (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            antwoord_tekst TEXT NOT NULL,
            is_correct TINYINT(1) NOT NULL DEFAULT 0,
            question_id INT UNSIGNED NOT NULL,
            volgorde INT UNSIGNED NOT NULL DEFAULT 1,
            PRIMARY KEY (id),
            KEY idx_answers_question_id (question_id),
            CONSTRAINT fk_answers_question
                FOREIGN KEY (question_id) REFERENCES questions(id)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS scores (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            score INT NOT NULL DEFAULT 0,
            gespeeld_op TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            quiz_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NULL,
            PRIMARY KEY (id),
            KEY idx_scores_quiz_id (quiz_id),
            KEY idx_scores_user_id (user_id),
            CONSTRAINT fk_scores_quiz
                FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
                ON DELETE CASCADE,
            CONSTRAINT fk_scores_user
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

// bekijkt of een kolom bestaat
function columnExists(PDO $pdo, string $table, string $column): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND COLUMN_NAME = :column_name'
    );
    $statement->execute([
        'table_name' => $table,
        'column_name' => $column,
    ]);

    return (int) $statement->fetchColumn() > 0;
}

// controleert of een index bestaat
function indexExists(PDO $pdo, string $table, string $indexName): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM information_schema.STATISTICS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND INDEX_NAME = :index_name'
    );
    $statement->execute([
        'table_name' => $table,
        'index_name' => $indexName,
    ]);

    return (int) $statement->fetchColumn() > 0;
}

// voegt een index toe als deze nog niet bestaat
function ensureIndex(PDO $pdo, string $table, string $indexName, string $definition): void
{
    if (!indexExists($pdo, $table, $indexName)) {
        $pdo->exec('ALTER TABLE ' . $table . ' ADD ' . $definition);
    }
}

// vult ontbrekende gebruikersnamen automatisch in
function backfillUsernames(PDO $pdo): void
{
    $statement = $pdo->query('SELECT id, name, gebruikersnaam, email FROM users ORDER BY id ASC');
    $rows = $statement->fetchAll();
    $used = [];

    foreach ($rows as $row) {
        $username = strtolower(trim((string) ($row['gebruikersnaam'] ?? '')));
        if ($username !== '') {
            $used[$username] = true;
        }
    }

    foreach ($rows as $row) {
        $currentUsername = strtolower(trim((string) ($row['gebruikersnaam'] ?? '')));
        if ($currentUsername !== '') {
            continue;
        }

        $source = (string) ($row['name'] ?? '');
        if ($source === '') {
            $source = (string) strstr((string) ($row['email'] ?? ''), '@', true);
        }

        $base = normalizeUsername($source);
        $candidate = $base;
        $suffix = 1;

        // maakt een unieke gebruikersnaam
        while (isset($used[$candidate])) {
            $candidate = $base . $suffix;
            $suffix++;
        }

        $update = $pdo->prepare('UPDATE users SET gebruikersnaam = :gebruikersnaam WHERE id = :id');
        $update->execute([
            'gebruikersnaam' => $candidate,
            'id' => (int) $row['id'],
        ]);

        $used[$candidate] = true;
    }

    $pdo->exec("UPDATE users SET gebruikersnaam = COALESCE(gebruikersnaam, '') WHERE gebruikersnaam IS NULL OR gebruikersnaam = ''");
    $pdo->exec('ALTER TABLE users MODIFY gebruikersnaam VARCHAR(100) NOT NULL');
}

// zet een naam om naar een goedgekeurde gebruikersnaam
function normalizeUsername(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '', $value) ?? '';

    return $value !== '' ? $value : 'user';
}

// maakt een testgebruiker aan als die nog niet bestaat
function seedTestUser(PDO $pdo): void
{
    $email = 'test@test.com';
    $username = 'testuser';

    $statement = $pdo->prepare('SELECT id FROM users WHERE LOWER(email) = LOWER(:email) OR LOWER(gebruikersnaam) = LOWER(:gebruikersnaam) LIMIT 1');
    $statement->execute([
        'email' => $email,
        'gebruikersnaam' => $username,
    ]);

    if (!$statement->fetch()) {
        $insert = $pdo->prepare('INSERT INTO users (name, gebruikersnaam, email, wachtwoord_hash) VALUES (:name, :gebruikersnaam, :email, :wachtwoord_hash)');
        $insert->execute([
            'name' => 'Test User',
            'gebruikersnaam' => $username,
            'email' => $email,
            'wachtwoord_hash' => password_hash('Test1234!', PASSWORD_DEFAULT),
        ]);
    }
}