<?php
// Підключаю мій файл конфігурації БД, щоб мати доступ до змінної $pdo
require 'db_config.php';
header('Content-Type: application/json');

$page_name = $_GET['page_name'] ?? '';
$block_id = $_GET['block_id'] ?? '';

// Перевіряю, чи передані необхідні дані, перш ніж робити запит до бд
if ($page_name && $block_id) {
    try {
        $sql = "SELECT content FROM page_content WHERE page_name = ? AND block_id = ?";
        // Готую запит до виконання
        $stmt = $pdo->prepare($sql);
        // Виконую запит, передаючи реальні значення змінних
        $stmt->execute([$page_name, $block_id]);
        // Отримую один рядок результату
        $row = $stmt->fetch();

        if ($row) {
            echo json_encode(['status' => 'success', 'data' => $row['content']]);
        } else {
            echo json_encode(['status' => 'empty']);
        }
    } catch (\PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>