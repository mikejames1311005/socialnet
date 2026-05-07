<?php
require_once __DIR__ . '/common.php';
require_login();

$user = current_user();
$statement = db()->prepare('SELECT username, fullname FROM account WHERE id <> ? ORDER BY username');
$statement->execute([$user['id']]);
$otherUsers = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - SocialNet</title>
    <link rel="stylesheet" href="/socialnet/style.css">
</head>
<body>
    <main class="page">
        <?php require __DIR__ . '/menubar.php'; ?>

        <section class="panel">
            <h1>Home</h1>
            <p><strong>Username:</strong> <?= h($user['username']) ?></p>
            <p><strong>Full Name:</strong> <?= h($user['fullname']) ?></p>

            <h2>Other Users</h2>
            <?php if (count($otherUsers) === 0): ?>
                <p>No other users found.</p>
            <?php else: ?>
                <ul class="user-list">
                    <?php foreach ($otherUsers as $otherUser): ?>
                        <li>
                            <a href="/socialnet/profile.php?owner=<?= urlencode($otherUser['username']) ?>">
                                <?= h($otherUser['fullname']) ?> (<?= h($otherUser['username']) ?>)
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
