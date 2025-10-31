<?php
$server_name = 'localhost'; 
$db_name = 'lab_db';
$username = 'sa';      
$password = '4861';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("sqlsrv:Server=$server_name;Database=$db_name", $username, $password, $options);
} catch (\PDOException $e) {
     die("Помилка підключення до MS SQL Server: " . $e->getMessage());
}
?>