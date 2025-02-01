<?php
declare(strict_types=1);

namespace App\Models;

use PDO;


class User extends BaseModel
{
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
        return (bool) $this->fetchOne($query, $params);
    }

    // Get user by email
    public function getUserByEmail(string $email): ?array
    {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $params = [':email' => $email];
        return $this->fetchOne($query, $params);
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

    // counting users
    public function getUserCount()
    {
        return $this->count('users');
    }

    // get a user by id 
    public function getUserById(int $id): ?array
    {
        return $this->findById('users', $id);
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


    public function getUsers(int $page = 1, int $limit = 5, string $search = '', string $sort = 'id', string $order = 'asc'): array
    {
        try {
            $validSortColumns = ['id', 'fullname', 'email', 'role', 'created_at'];
            $sort = in_array($sort, $validSortColumns) ? $sort : 'id';
            $order = in_array(strtolower($order), ['asc', 'desc']) ? $order : 'asc';

            $offset = ($page - 1) * $limit;

            $sql = "SELECT * FROM users WHERE fullname LIKE :search ORDER BY $sort $order LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countSql = "SELECT COUNT(*) FROM users WHERE fullname LIKE :search";
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $countStmt->execute();
            $totalResults = $countStmt->fetchColumn();

            $totalPages = ceil($totalResults / $limit);

            return [
                'users' => $users,
                'totalPages' => $totalPages,
                'totalResults' => $totalResults,
            ];
        } catch (\Exception $e) {
            error_log("Error in UserModel: " . $e->getMessage());
            throw $e;
        }
    }
}
