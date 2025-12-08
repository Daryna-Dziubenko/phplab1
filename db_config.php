<?php
$server_name = 'sql202.infinityfree.com';
$db_name = 'if0_39952370_db_lab';
$username = 'if0_39952370';
$password = 'rQODDXfAzZThDu';

// Налаштовую параметри драйвера PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host=$server_name;dbname=$db_name;charset=utf8mb4", $username, $password, $options);
} catch (\PDOException $e) {
    die("Помилка підключення до MySQL: " . $e->getMessage());
}
?>