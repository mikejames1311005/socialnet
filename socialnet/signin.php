<?php
require_once __DIR__ . '/common.php';

if (current_user() !== null) {
    header('Location: /socialnet/index.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $statement = db()->prepare('SELECT id, username, password FROM account WHERE username = ?');
    $statement->execute([$username]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        header('Location: /socialnet/index.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In - SocialNet</title>
    <link rel="stylesheet" href="/socialnet/style.css">
</head>
<body>
    <main class="page">
        <section class="panel">
            <h1>Sign In</h1>

            <?php if ($error !== ''): ?>
                <p class="error"><?= h($error) ?></p>
            <?php endif; ?>

            <form method="post">
                <label for="username">Username</label>
                <input id="username" name="username" value="<?= h($username) ?>" required>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>

                <button type="submit">Sign In</button>
            </form>
        </section>
    </main>
</body>
</html>
