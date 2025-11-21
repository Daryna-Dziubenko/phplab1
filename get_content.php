<?php
require 'db_config.php';
header('Content-Type: application/json');

$page_name = $_GET['page_name'] ?? '';
$block_id = $_GET['block_id'] ?? '';

if ($page_name && $block_id) {
    try {
        $sql = "SELECT [content] FROM [page_content] WHERE [page_name] = ? AND [block_id] = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$page_name, $block_id]);
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