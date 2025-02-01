<?php

class Database
{
    private static ?self $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $dsn = 'mysql:host=127.0.0.1;dbname=ems_db';
        $username = 'root';
        $password = '';

        try {
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}