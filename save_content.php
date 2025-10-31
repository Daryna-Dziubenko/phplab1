<?php
require 'db_config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $page_name = $_POST['page_name'] ?? null;
    $block_id = $_POST['block_id'] ?? null;
    $content_json = $_POST['content'] ?? null;

    if ($page_name && $block_id && $content_json) {
        
        $sql = "
            MERGE INTO [page_content] AS [target]
            USING (VALUES (?, ?, ?)) AS [source] ([page_name], [block_id], [content])
            ON ([target].[page_name] = [source].[page_name] AND [target].[block_id] = [source].[block_id])
            
            /* Якщо запис знайдено (MATCHED) - оновлюємо його */
            WHEN MATCHED THEN
                UPDATE SET [target].[content] = [source].[content]
            
            /* Якщо запис не знайдено (NOT MATCHED) - вставляємо новий */
            WHEN NOT MATCHED THEN
                INSERT ([page_name], [block_id], [content]) 
                VALUES ([source].[page_name], [source].[block_id], [source].[content]);
        ";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$page_name, $block_id, $content_json]);

            echo json_encode(['status' => 'success']);

        } catch (\PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Недостатньо даних']);
    }
} else {
     echo json_encode(['status' => 'error', 'message' => 'Неприпустимий метод запиту']);
}
?>