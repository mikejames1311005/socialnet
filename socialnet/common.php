<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function db()
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $config = require __DIR__ . '/../config.php';
    $dsn = 'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8mb4';

    try {
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $error) {
        die('Database connection failed. Please check config.php and MySQL.');
    }

    return $pdo;
}

function h($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function current_user()
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $statement = db()->prepare('SELECT id, username, fullname, description FROM account WHERE id = ?');
    $statement->execute([$_SESSION['user_id']]);
    $user = $statement->fetch();

    if (!$user) {
        $_SESSION = [];
        session_destroy();
        return null;
    }

    return $user;
}

function require_login()
{
    if (current_user() === null) {
        header('Location: /socialnet/signin.php');
        exit;
    }
}
