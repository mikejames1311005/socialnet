<?php
require_once __DIR__ . '/common.php';

$user = require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description'] ?? '');
    $statement = db()->prepare('UPDATE account SET description = ? WHERE id = ?');
    $statement->execute([$description, $user['id']]);

    header('Location: /socialnet/setting.php?saved=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setting - SocialNet</title>
    <link rel="stylesheet" href="/socialnet/style.css">
</head>
<body>
    <main class="page">
        <?php require __DIR__ . '/menubar.php'; ?>

        <section class="panel">
            <h1>Setting</h1>

            <?php if (isset($_GET['saved'])): ?>
                <p class="message">Profile description saved.</p>
            <?php endif; ?>

            <form method="post">
                <label for="description">Profile Description</label>
                <textarea id="description" name="description"><?= h($user['description']) ?></textarea>

                <button type="submit">Save Profile</button>
            </form>
        </section>
    </main>
</body>
</html>
