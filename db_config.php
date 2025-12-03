// db_config.php - **ЗМІНИТИ**
<?php
$server_name = 'localhost\MSSQL_DEV_SERVER'; // Використовуйте повне ім'я сервера
$db_name = 'lab_db';
// **Для Windows Authentication встановлюємо порожні значення**
$username = '';
$password = '';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    // **IntegratedSecurity=SSPI** для Windows Authentication
    // **TrustServerCertificate=true** для довіри сертифікату, як на скріншоті
    $pdo = new PDO("sqlsrv:Server=$server_name;Database=$db_name;IntegratedSecurity=SSPI;TrustServerCertificate=true", $username, $password, $options);
} catch (\PDOException $e) {
    die("Помилка підключення до MS SQL Server: " . $e->getMessage());
}
?>