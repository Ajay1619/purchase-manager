<?php
require_once('../../config/sparrow.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/user_access_denied.css' ?>">
</head>

<body>
    <div class="container">
        <div class="error-content">
            <h1>🚫</h1>
            <h2>Oops! Access Denied</h2>
            <p>🚫 It seems like this page is off-limits for you. Maybe it’s a secret birdie meeting? 🐦</p>
            <a href="/" class="btn">🕊️ Fly Back to Home</a>

        </div>
        <div class="image-content">
            <img src="<?= GLOBAL_PATH . '/images/user access denied 1.png' ?>" alt="Funny Access Denied Image">
        </div>
    </div>
</body>

</html>