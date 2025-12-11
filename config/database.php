
<?php
$host = 'localhost';
$dbname = 'webtech_2025A_deubaybe_dounia';
$username = 'deubaybe.dounia';
$password = 'Dou81387';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
