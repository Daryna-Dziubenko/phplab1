<?php
require 'db_config.php';

// Отримуємо дані
$method = $_POST['method'] ?? 'stream';
$event_type = $_POST['event_type'] ?? 'unknown';
$client_time = $_POST['client_time'] ?? '';
$details = $_POST['details'] ?? '';

try {
    $stmt = $pdo->prepare("INSERT INTO lab5_events (method, event_type, client_time, details) VALUES (?, ?, ?, ?)");
    $stmt->execute([$method, $event_type, $client_time, $details]);
} catch (PDOException $e) {
}
?>