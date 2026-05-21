<?php
require_once __DIR__ . '/../socialnet/common.php';

$user = require_login();
if ($user['username'] !== 'admin') {
    http_response_code(403);
    echo 'Not authorized.';
    exit;
}

$message = '';
$error = '';
$username = '';
$fullname = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $password = $_POST['password'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if ($username === '' || $fullname === '' || $password === '') {
        $error = 'Username, full name, and password are required.';
    } else {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $statement = db()->prepare(
                'INSERT INTO account (username, fullname, password, description) VALUES (?, ?, ?, ?)'
            );
            $statement->execute([$username, $fullname, $hashedPassword, $description]);

            $message = 'User created successfully.';
            $username = '';
            $fullname = '';
            $description = '';
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $error = 'That username already exists.';
            } else {
                $error = 'Could not create user. Please check the database.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New User - SocialNet</title>
    <link rel="stylesheet" href="/socialnet/style.css">
</head>
<body>
    <main class="page">
        <section class="panel">
            <h1>Create New User</h1>

            <?php if ($message !== ''): ?>
                <p class="message"><?= h($message) ?> <a href="/socialnet/signin.php">Go to Sign In</a></p>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <p class="error"><?= h($error) ?></p>
            <?php endif; ?>

            <form method="post">
                <label for="username">Username</label>
                <input id="username" name="username" value="<?= h($username) ?>" required>

                <label for="fullname">Full Name</label>
                <input id="fullname" name="fullname" value="<?= h($fullname) ?>" required>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>

                <label for="description">Profile Description</label>
                <textarea id="description" name="description"><?= h($description) ?></textarea>

                <button type="submit">Create User</button>
            </form>
        </section>
    </main>
</body>
</html>
