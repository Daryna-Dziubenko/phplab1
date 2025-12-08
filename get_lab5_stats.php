<?php
require 'db_config.php';

// Отримуємо останні 20 записів для порівняння
$sql = "SELECT * FROM lab5_events ORDER BY id DESC LIMIT 20";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();

echo "<table><tr><th>ID</th><th>Метод</th><th>Подія</th><th>Час клієнта</th><th>Час сервера</th></tr>";
foreach ($rows as $r) {
    echo "<tr>";
    echo "<td>{$r['id']}</td>";
    echo "<td>{$r['method']}</td>";
    echo "<td>{$r['event_type']}</td>";
    echo "<td>{$r['client_time']}</td>";
    echo "<td>{$r['server_time']}</td>";
    echo "</tr>";
}
echo "</table>";
?>