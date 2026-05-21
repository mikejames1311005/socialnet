<?php
require_once __DIR__ . '/common.php';

$user = require_login();
$owner = trim($_GET['owner'] ?? '');

if ($owner === '') {
    $owner = $user['username'];
}

if ($owner !== $user['username']) {
    http_response_code(403);
    echo 'Not authorized to view this profile.';
    exit;
}

$statement = db()->prepare('SELECT username, fullname, description FROM account WHERE username = ?');
$statement->execute([$owner]);
$profile = $statement->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - SocialNet</title>
    <link rel="stylesheet" href="/socialnet/style.css">
</head>
<body>
    <main class="page">
        <?php require __DIR__ . '/menubar.php'; ?>

        <section class="panel">
            <h1>Profile</h1>

            <?php if (!$profile): ?>
                <p class="error">Profile not found.</p>
            <?php else: ?>
                <p><strong>Owner Username:</strong> <?= h($profile['username']) ?></p>
                <p><strong>Owner Full Name:</strong> <?= h($profile['fullname']) ?></p>
                <h2>Profile Description</h2>
                <p class="profile-text"><?= h($profile['description'] ?: 'No profile description yet.') ?></p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
