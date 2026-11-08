<?php
// Change these values to match your local setup
$host     = 'localhost';
$dbname   = 'vague_8ball';
$username = 'root';
$password = '';          // empty on XAMPP/MAMP by default

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}