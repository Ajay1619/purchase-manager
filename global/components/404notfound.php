<?php
require_once('../../config/sparrow.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link rel="stylesheet" href="<?= GLOBAL_PATH . '/css/404notfound.css' ?>">
</head>

<body>
    <div class="container">
        <div class="error-content">
            <h1>Oops!</h1>
            <h2>404 - Page Not Found</h2>
            <p>🐦 Looks like our little sparrow friend has flown away with this page!</p>
            <p>🕊️ Don’t worry, we’ll help you find your way back to the nest.</p>
            <a href="/" class="btn">🏠 Fly Back to Home</a>

        </div>
        <div class="image-content">
            <img src="<?= GLOBAL_PATH . '/images/404 not found 5.png' ?>" alt="Sparrow Lost" />
        </div>
    </div>
</body>

</html>