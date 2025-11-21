<?php
require 'db_config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $page_name = $data['page_name'] ?? null;
    $block_id  = $data['block_id'] ?? null;
    $content   = $data['content'] ?? null; // Це буде JSON рядок об'єкта Collapse

    if ($page_name && $block_id && $content) {
        $sql = "
            MERGE INTO [page_content] AS [target]
            USING (VALUES (?, ?, ?)) AS [source] ([page_name], [block_id], [content])
            ON ([target].[page_name] = [source].[page_name] AND [target].[block_id] = [source].[block_id])
            WHEN MATCHED THEN
                UPDATE SET [target].[content] = [source].[content]
            WHEN NOT MATCHED THEN
                INSERT ([page_name], [block_id], [content]) 
                VALUES ([source].[page_name], [source].[block_id], [source].[content]);
        ";
        
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