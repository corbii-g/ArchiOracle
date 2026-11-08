<?php
require_once __DIR__ . '/../config/database.php';

// Get one random phrase
$stmt = $pdo->query("SELECT phrase FROM phrases ORDER BY RAND() LIMIT 1");
$phrase = $stmt->fetchColumn();

echo $phrase;
?>