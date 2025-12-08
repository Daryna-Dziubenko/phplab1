<?php
require 'db_config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $page_name = $data['page_name'] ?? null;
    $block_id  = $data['block_id'] ?? null;
    $content   = $data['content'] ?? null;

    if ($page_name && $block_id && $content) {
        $sql = "INSERT INTO page_content (page_name, block_id, content) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE content = VALUES(content)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$page_name, $block_id, $content]);
            echo json_encode(['status' => 'success']);
        } catch (\PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing data']);
    }
}
?>