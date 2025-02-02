<?php

declare(strict_types=1);

namespace App\Models;
use PDO;

class Event extends BaseModel
{

    // // Execute queries safely
    // private function executeQuery(string $query, array $params = []): bool
    // {
    //     try {
    //         $stmt = $this->pdo->prepare($query);
    //         return $stmt->execute($params);
    //     } catch (PDOException $e) {
    //         error_log("Database Error: " . $e->getMessage());
    //         return false;
    //     }
    // }

    // Fetch single record safely
    private function fetchSingleRecord(string $query, array $params = []): ?array
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }

    // Fetch multiple records
    private function fetchRecords(string $query, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return [];
        }
    }

    // Get all events
    public function getAllEvents(int $limit = 0): array
    {
        $query = "SELECT * FROM events ORDER BY start_datetime DESC";

        if ($limit > 0) {
            $query .= " LIMIT :limit";
        }

        $stmt = $this->pdo->prepare($query);

        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvents(int $page, int $limit, string $sort, string $order, string $search): array
    {
        $offset = ($page - 1) * $limit;

        $allowedSortColumns = ['id', 'name', 'max_capacity', 'start_datetime', 'end_datetime', 'created_at'];
        $sort = in_array($sort, $allowedSortColumns) ? $sort : 'id';
        $order = strtolower($order) === 'desc' ? 'DESC' : 'ASC';

        try {
            $query = "SELECT e.*, u.fullname AS created_by_name 
            FROM events e
            JOIN users u ON e.created_by = u.id";
            $countQuery = "SELECT COUNT(*) as total FROM events";

            if (!empty($search)) {
                $searchTerm = "%$search%";
                $query .= " WHERE name LIKE :search OR description LIKE :search";
                $countQuery .= " WHERE name LIKE :search OR description LIKE :search";
            }

            $query .= " ORDER BY $sort $order LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($countQuery);
            if (!empty($search)) {
                $stmt->bindValue(':search', $searchTerm, PDO::PARAM_STR);
            }
            $stmt->execute();
            $totalResults = $stmt->fetchColumn();

            $stmt = $this->pdo->prepare($query);
            if (!empty($search)) {
                $stmt->bindValue(':search', $searchTerm, PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalPages = ceil($totalResults / $limit);

            return [
                'events' => $events,
                'totalPages' => $totalPages,
                'totalResults' => $totalResults,
                'showingResults' => count($events),
            ];
        } catch (PDOException $e) {
            throw new PDOException("Database error: " . $e->getMessage());
        }
    }

    private function getTotalCount(string $search): int
    {
        $query = "SELECT COUNT(*) FROM events WHERE name LIKE :search OR description LIKE :search";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }


    // counting events
    public function getEventCount(): int
    {
        return $this->count('events');
    }


    // Create a new event
    public function createEvent(array $eventData): ?int
    {
        return $this->insert('events', $eventData);
    }
    // public function createEvent(array $eventData)
    // {
    //     try {
    //         $query = "INSERT INTO events (name, slug, description, max_capacity, start_datetime, end_datetime, created_by)
    //               VALUES (:name, :slug, :description, :max_capacity, :start_datetime, :end_datetime, :created_by)";
    //         $stmt = $this->pdo->prepare($query);

    //         $stmt->bindValue(':name', $eventData['name'], PDO::PARAM_STR);
    //         $stmt->bindValue(':slug', $eventData['slug'], PDO::PARAM_STR);
    //         $stmt->bindValue(':description', $eventData['description'], PDO::PARAM_STR);
    //         $stmt->bindValue(':max_capacity', $eventData['max_capacity'], PDO::PARAM_INT);
    //         $stmt->bindValue(':start_datetime', $eventData['start_datetime'], PDO::PARAM_STR);
    //         $stmt->bindValue(':end_datetime', $eventData['end_datetime'], PDO::PARAM_STR);
    //         $stmt->bindValue(':created_by', $eventData['created_by'], PDO::PARAM_INT);

    //         $stmt->execute();

    //         return $this->pdo->lastInsertId();
    //     } catch (PDOException $e) {
    //         throw new PDOException("Failed to create event: " . $e->getMessage());
    //     }
    // }

    // Get an event by ID
    public function getEventById(int $id): ?array
    {
        try {
            $query = "SELECT e.*, u.fullname AS created_by_name 
                  FROM events e 
                  JOIN users u ON e.created_by = u.id 
                  WHERE e.id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            return $event ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Failed to fetch event: " . $e->getMessage());
        }
    }


    public function updateEvent(int $id, array $eventData): bool
    {
        try {
            $query = "UPDATE events 
                  SET name = :name, 
                      slug = :slug, 
                      description = :description, 
                      max_capacity = :max_capacity, 
                      start_datetime = :start_datetime, 
                      end_datetime = :end_datetime 
                  WHERE id = :id";
            $stmt = $this->pdo->prepare($query);

            // Bind parameters
            $stmt->bindValue(':name', $eventData['name'], PDO::PARAM_STR);
            $stmt->bindValue(':slug', $eventData['slug'], PDO::PARAM_STR);
            $stmt->bindValue(':description', $eventData['description'], PDO::PARAM_STR);
            $stmt->bindValue(':max_capacity', $eventData['max_capacity'], PDO::PARAM_INT);
            $stmt->bindValue(':start_datetime', $eventData['start_datetime'], PDO::PARAM_STR);
            $stmt->bindValue(':end_datetime', $eventData['end_datetime'], PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            // Execute the query
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException("Failed to update event: " . $e->getMessage());
        }
    }


    // Delete an event
    public function deleteEvent(int $id): bool
    {
        return $this->executeQuery("DELETE FROM events WHERE id = :id", ['id' => $id]);
    }

    // Search events by name or description
    public function searchEvents(string $search): array
    {
        return $this->fetchRecords(
            "SELECT * FROM events WHERE name LIKE :search OR description LIKE :search ORDER BY start_datetime DESC",
            ['search' => "%$search%"]
        );
    }


    public function generateEventReport(int $eventId): array|false
    {
        if ($eventId <= 0) {
            return false;
        }

        try {
            $eventQuery = '
                SELECT 
                    e.id, 
                    e.name, 
                    e.description, 
                    e.max_capacity, 
                    e.start_datetime, 
                    e.end_datetime, 
                    e.created_at, 
                    e.updated_at, 
                    u.fullname AS created_by_name
                FROM 
                    events e
                LEFT JOIN 
                    users u ON e.created_by = u.id
                WHERE 
                    e.id = :eventId
            ';
            $eventStmt = $this->pdo->prepare($eventQuery);
            $eventStmt->execute(['eventId' => $eventId]);
            $event = $eventStmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                return false;
            }

            $attendeesQuery = '
                SELECT 
                    a.id AS attendee_id, 
                    a.attendee_name, 
                    a.attendee_email, 
                    a.registration_type, 
                    a.registered_at, 
                    u.email AS registered_by_email
                FROM 
                    attendees a
                LEFT JOIN 
                    users u ON a.user_id = u.id
                WHERE 
                    a.event_id = :eventId
                ORDER BY 
                    a.registered_at DESC
            ';
            $attendeesStmt = $this->pdo->prepare($attendeesQuery);
            $attendeesStmt->execute(['eventId' => $eventId]);
            $attendees = $attendeesStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'event' => $event,
                'attendees' => $attendees,
            ];

        } catch (\PDOException $e) {
            $this->logger->error('PDOException in generateEventReport: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * Update the event slug.
     */
    public function updateEventSlug(int $eventId, string $slug): bool
    {
        $query = "UPDATE events SET slug = :slug WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['slug' => $slug, 'id' => $eventId]);
    }

    public function getEventBySlug(string $slug): ?array
    {
        try {
            $query = "SELECT * FROM events WHERE slug = :slug LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute();

            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            return $event ? $event : null;
        } catch (PDOException $e) {

            $this->logger->error("Database error while fetching event by slug: " . $e->getMessage());
            return null;
        }
    }
}
