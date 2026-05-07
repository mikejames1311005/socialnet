<?php
require_once __DIR__ . '/common.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About - SocialNet</title>
    <link rel="stylesheet" href="/socialnet/style.css">
</head>
<body>
    <main class="page">
        <?php require __DIR__ . '/menubar.php'; ?>

        <section class="panel">
            <h1>About</h1>
            <p><strong>Student Name:</strong> Tran Thanh Dat</p>
            <p><strong>Student Number:</strong> TROY ID 1695358</p>
            <p>SocialNet is a simple PHP and MySQL web application for the university web application assignment.</p>
        </section>
    </main>
</body>
</html>
