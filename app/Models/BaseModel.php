<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use PDOException;
use App\Utils\Logger;

abstract class BaseModel
{
    protected PDO $pdo;
    protected Logger $logger;

    public function __construct(PDO $pdo, ?Logger $logger = null)
    {
        $this->pdo = $pdo;
        $this->logger = $logger ?? new Logger(__DIR__ . '/../../logs/debug.log');
    }

    // Execute a query and return success/failure
    protected function executeQuery(string $query, array $params = []): bool
    {
        try {
            $stmt = $this->pdo->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            $this->logger->error("Database Error: " . $e->getMessage());
            return false;
        }
    }

    // Fetch a single record
    protected function fetchOne(string $query, array $params = []): ?array
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            $this->logger->error("Database Error: " . $e->getMessage());
            return null;
        }
    }

    // Fetch a single record by ID
    public function findById(string $table, int $id): ?array
    {
        // Validate the table name to prevent SQL injection
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            $this->logger->error("Invalid table name: {$table}");
            return null;
        }

        $query = "SELECT * FROM {$table} WHERE id = :id LIMIT 1";

        try {
            return $this->fetchOne($query, ['id' => $id]);
        } catch (PDOException $e) {
            $this->logger->error("Database Error: " . $e->getMessage());
            return null;
        }
    }

    // Fetch multiple records
    protected function fetchAll(string $query, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->logger->error("Database Error: " . $e->getMessage());
            return [];
        }
    }

    // Insert a record and return the last insert ID
    protected function insert(string $table, array $data): ?int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        return $this->executeQuery($query, $data) ? (int) $this->pdo->lastInsertId() : null;
    }

    // Update a record
    protected function update(string $table, array $data, string $condition, array $conditionParams): bool
    {
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $query = "UPDATE $table SET $setClause WHERE $condition";
        return $this->executeQuery($query, array_merge($data, $conditionParams));
    }

    // Delete a record
    protected function delete(string $table, string $condition, array $conditionParams): bool
    {
        $query = "DELETE FROM $table WHERE $condition";
        return $this->executeQuery($query, $conditionParams);
    }

    // Count records in a table
    protected function count(string $table, string $condition = '', array $params = []): int
    {
        $query = "SELECT COUNT(*) FROM $table";
        if ($condition) {
            $query .= " WHERE $condition";
        }
        return (int) $this->fetchOne($query, $params)['COUNT(*)'] ?? 0;
    }

    // Generic search with pagination and sorting
    protected function search(
        string $table,
        array $searchColumns,
        string $searchTerm = '',
        int $page = 1,
        int $limit = 10,
        string $sort = 'id',
        string $order = 'desc',
        array $filters = []
    ): array {
        $offset = ($page - 1) * $limit;

        // Build the search condition
        $searchCondition = '';
        if (!empty($searchTerm)) {
            $searchCondition = implode(' OR ', array_map(fn($col) => "$col LIKE :search", $searchColumns));
        }

        // Build the base query
        $query = "SELECT * FROM $table";
        if ($searchCondition) {
            $query .= " WHERE ($searchCondition)";
        }

        // Add filters
        foreach ($filters as $column => $value) {
            $query .= $searchCondition ? " AND $column = :$column" : " WHERE $column = :$column";
        }

        // Add sorting and pagination
        $query .= " ORDER BY $sort $order LIMIT :limit OFFSET :offset";

        // Prepare parameters
        $params = [];
        if ($searchCondition) {
            $params[':search'] = "%$searchTerm%";
        }
        foreach ($filters as $column => $value) {
            $params[":$column"] = $value;
        }
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        // Fetch paginated results
        $results = $this->fetchAll($query, $params);

        // Fetch total results count
        $countQuery = "SELECT COUNT(*) FROM $table";
        if ($searchCondition) {
            $countQuery .= " WHERE ($searchCondition)";
        }
        foreach ($filters as $column => $value) {
            $countQuery .= $searchCondition ? " AND $column = :$column" : " WHERE $column = :$column";
        }
        $totalResults = (int) $this->fetchOne($countQuery, $params)['COUNT(*)'];

        return [
            'results' => $results,
            'totalPages' => ceil($totalResults / $limit),
            'totalResults' => $totalResults,
            'showingResults' => count($results),
        ];
    }

    // Send a JSON response.
    protected function sendJsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }




    /**
     * Generate a unique slug for any entity.
     *
     * @param string $name The base name to generate the slug from.
     * @param string $table The table to check for existing slugs.
     * @param string $slugColumn The column name that holds the slug in the table.
     * @return string The unique slug.
     */
    public function generateUniqueSlug(string $name, string $table, string $slugColumn = 'slug'): string
    {
        $slug = $this->generateSlugFromName($name);
        $uniqueSlug = $slug;
        $counter = 1;

        while ($this->isSlugExists($uniqueSlug, $table, $slugColumn)) {
            $uniqueSlug = $slug . '_' . $counter++;
        }

        return $uniqueSlug;
    }

    /**
     * Check if the slug exists in the specified table.
     *
     * @param string $slug The slug to check.
     * @param string $table The table to check the slug in.
     * @param string $slugColumn The column name that holds the slug in the table.
     * @return bool Whether the slug exists.
     */
    public function isSlugExists(string $slug, string $table, string $slugColumn): bool
    {
        $query = "SELECT COUNT(*) FROM {$table} WHERE {$slugColumn} = :slug";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Generate a slug from a given name.
     *
     * @param string $name The name to generate the slug from.
     * @return string The generated slug.
     */
    protected function generateSlugFromName(string $name): string
    {
        // Replace spaces with dashes, convert to lowercase, and remove special characters
        return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    }


}