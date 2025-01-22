<?php
function dbConnect()
{
    $host = 'localhost';
    $dbname = 'ollyo_event_management';
    $user = 'root';
    $password = '';

    $dsn = "mysql:host=$host;dbname=$dbname";

    try {
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>