<?php

namespace App\Models;

use PDO;
use Exception;

class User
{
    private PDO $pdo;

    public int $id;
    public string $fullname;
    public string $email;
    public string $password;
    public string $role;
    public string $created_at;
    public string $updated_at;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Helper function to handle query execution and exception handling
    private function executeQuery(string $query, array $params = []): bool
    {
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            return $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Helper function to fetch a single record
    private function fetchSingleRecord(string $query, array $params = []): ?array
    {
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // Register a new user
    public function registerUser(string $fullname, string $email, string $password): bool
    {
        if ($this->emailExists($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (fullname, email, password, created_at) VALUES (:fullname, :email, :password, NOW())";
        $params = [
            ':fullname' => $fullname,
            ':email' => $email,
            ':password' => $hashedPassword,
        ];

        return $this->executeQuery($query, $params);
    }

    // Check if email exists
    public function emailExists(string $email): bool
    {
        $query = "SELECT 1 FROM users WHERE email = :email LIMIT 1";
        $params = [':email' => $email];
        return (bool) $this->fetchSingleRecord($query, $params);
    }

    // Get user by email
    public function getUserByEmail(string $email): ?array
    {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $params = [':email' => $email];
        return $this->fetchSingleRecord($query, $params);
    }

    // Get user by ID
    public function getUserById(int $id): ?array
    {
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $params = [':id' => $id];
        return $this->fetchSingleRecord($query, $params);
    }

    // Update user information
    public function updateUser(int $id, string $fullname, string $email, ?string $password = null): bool
    {
        $query = "UPDATE users SET fullname = :fullname, email = :email, updated_at = NOW()";
        $params = [
            ':id' => $id,
            ':fullname' => $fullname,
            ':email' => $email,
        ];

        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query .= ", password = :password";
            $params[':password'] = $hashedPassword;
        }

        $query .= " WHERE id = :id";

        return $this->executeQuery($query, $params);
    }

    // Delete user by ID
    public function deleteUser(int $id): bool
    {
        $query = "DELETE FROM users WHERE id = :id";
        $params = [':id' => $id];
        return $this->executeQuery($query, $params);
    }

    // Verify password during login
    public function verifyPassword(string $inputPassword, string $storedPasswordHash): bool
    {
        return password_verify($inputPassword, $storedPasswordHash); // Compare hashed passwords securely
    }

    // Get all users with optional pagination
    public function getAllUsers(int $limit = 10, int $offset = 0): array
    {
        $query = "SELECT * FROM users LIMIT :limit OFFSET :offset";
        $params = [
            ':limit' => $limit,
            ':offset' => $offset,
        ];

        return $this->fetchAllRecords($query, $params);
    }

    // Fetch multiple records
    private function fetchAllRecords(string $query, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // In your model or appropriate class
    public function getUserCount()
    {
        $query = "SELECT COUNT(*) AS user_count FROM users";
        $result = $this->fetchAllRecords($query);

        return isset($result[0]['user_count']) ? (int) $result[0]['user_count'] : 0;
    }

    // Create a User object from the database row
    private function createUserObject(array $userData): self
    {
        $user = new self($this->pdo);
        $user->id = $userData['id'];
        $user->fullname = $userData['fullname'];
        $user->email = $userData['email'];
        $user->role = $userData['role'];
        $user->created_at = $userData['created_at'];
        $user->updated_at = $userData['updated_at'];

        return $user;
    }

    // Static method to find a user by ID
    public static function find(PDO $pdo, int $id): ?self
    {
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $params = [':id' => $id];
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? (new self($pdo))->createUserObject($userData) : null;
    }
}
