<?php
require 'db_config.php';

// Отримуємо великий JSON з LocalStorage
$json = file_get_contents('php://input');
$events = json_decode($json, true);

if (is_array($events)) {
    try {
        $pdo->beginTransaction(); // Починаємо транзакцію для швидкості
        $stmt = $pdo->prepare("INSERT INTO lab5_events (method, event_type, client_time, details) VALUES (?, ?, ?, ?)");
        
        foreach ($events as $row) {
            $stmt->execute([
                'batch', 
                $row['event_type'], 
                $row['client_time'], 
                $row['details']
            ]);
        }
        $pdo->commit(); // Зберігаємо все разом
        echo json_encode(['status' => 'success', 'count' => count($events)]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>