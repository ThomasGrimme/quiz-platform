<?php
declare(strict_types=1);

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$password = $_POST['password'] ?? '';
password_hash($password, PASSWORD_DEFAULT);

header('Location: index.php?message=Account+aangemaakt');
exit;
