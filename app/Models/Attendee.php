<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class Attendee extends BaseModel
{

    // Get the count of registered attendees for an event
    public function getRegisteredAttendeesCount(int $eventId): int
    {
        return $this->count('attendees', 'event_id = :event_id', [':event_id' => $eventId]);
    }

    // Check if a user is registered for an event
    public function isUserRegistered(int $eventId, int $userId): bool
    {
        $query = 'SELECT COUNT(*) FROM attendees WHERE event_id = :event_id AND user_id = :user_id';

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    // Get registration details for an event by user, along with event information
    public function getRegistrationDetails(int $eventId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.*, e.* 
         FROM attendees a
         LEFT JOIN events e ON a.event_id = e.id
         WHERE a.event_id = :event_id AND a.user_id = :user_id'
        );

        $stmt->execute([
            ':event_id' => $eventId,
            ':user_id' => $userId,
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }



    // Register an attendee
    public function registerAttendee(array $data): bool
    {
        return $this->insert('attendees', $data) !== null;
    }

    // Get an event by ID
    public function getEventById(int $id): ?array
    {
        return $this->fetchOne(
            "SELECT e.*, u.fullname AS created_by_name 
              FROM events e 
              JOIN users u ON e.created_by = u.id 
              WHERE e.id = :id",
            [':id' => $id]
        );
    }

    // Generate an event report
    public function generateEventReport(int $eventId): ?array
    {
        $event = $this->getEventById($eventId);
        if (!$event) {
            return null;
        }

        $attendees = $this->fetchAll(
            "SELECT a.id AS attendee_id, a.attendee_name, a.attendee_email, a.registration_type, a.registered_at, u.email AS registered_by_email
             FROM attendees a
             LEFT JOIN users u ON a.user_id = u.id
             WHERE a.event_id = :event_id
             ORDER BY a.registered_at DESC",
            [':event_id' => $eventId]
        );

        return [
            'event' => $event,
            'attendees' => $attendees,
        ];
    }

    // Get an attendee by ID
    public function getAttendeeById(int $id): ?array
    {
        return $this->fetchOne("SELECT * FROM attendees WHERE id = :id LIMIT 1", [':id' => $id]);
    }

    // Delete an attendee by ID
    public function deleteAttendeeById(int $id): bool
    {
        return $this->delete('attendees', 'id = :id', [':id' => $id]);
    }

    // counting attendees
    public function getAttendeeCount(): int
    {
        return $this->count('attendees');
    }
}