<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'aquilate_trex');
define('DB_USER', 'aquilate_trex');
define('DB_PASSWORD', '@dminTrex');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
} catch (mysqli_sql_exception $exception) {
    die("Failed to connect to MySQL: " . $exception->getMessage());
}

// Connection established successfully, proceed with further actions