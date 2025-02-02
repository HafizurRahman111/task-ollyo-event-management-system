<?php

namespace App\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use PDO;
use Exception;
use PDOException;

class AttendeeController extends BaseController
{
    private Event $eventModel;
    private Attendee $attendeeModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->eventModel = new Event($this->pdo);
        $this->attendeeModel = new Attendee($this->pdo);

    }


    public function showRegistrationForm(): void
    {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $successMessage = $_SESSION['successMessage'] ?? '';
        unset($_SESSION['successMessage']);

        $availableEvents = [];
        $isRegistered = false;

        try {
            $allEvents = $this->eventModel->getAllEvents();
            foreach ($allEvents as $event) {
                $registeredCount = $this->attendeeModel->getRegisteredAttendeesCount($event['id']);
                if ($event['max_capacity'] > $registeredCount) {
                    $availableEvents[] = [
                        'id' => $event['id'],
                        'name' => $event['name'],
                        'description' => $event['description'],
                        'start_datetime' => $event['start_datetime'],
                        'end_datetime' => $event['end_datetime'],
                        'max_capacity' => $event['max_capacity'],
                        'registered_count' => $registeredCount,
                        'available_count' => $event['max_capacity'] - $registeredCount,
                    ];
                }
            }
        } catch (PDOException $e) {
            $errors['general'] = "Failed to fetch available events. Please try again later.";
            $this->logger->error("Database Error in Fetching Events: " . $e->getMessage());
        }

        $this->renderView('events/register_attendee', [
            'title' => 'Register for Event',
            'errors' => $errors,
            'successMessage' => $successMessage,
            'availableEvents' => $availableEvents,
            'isRegistered' => $isRegistered,
        ], 'layouts/dashboard_layout');
    }

    public function checkRegistration(int $id): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            $this->sendJsonResponse([
                'status' => 'error',
                'message' => 'You must be logged in to check registration.',
            ], 401);
            return;
        }

        $eventId = $id;

        try {
            $isRegistered = $this->attendeeModel->isUserRegistered($eventId, $userId);

            if ($isRegistered) {
                $registrationInfo = $this->attendeeModel->getRegistrationDetails($eventId, $userId);
                $this->sendJsonResponse([
                    'status' => 'success',
                    'message' => 'User is already registered for the event.',
                    'alreadyRegistered' => true,
                    'registrationInfo' => $registrationInfo
                ]);
            } else {
                $this->sendJsonResponse([
                    'status' => 'success',
                    'message' => 'User is not registered for the event.',
                    'alreadyRegistered' => false,
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error("Error checking registration: " . $e->getMessage());

            $this->sendJsonResponse([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again later.',
                'errorDetails' => $e->getMessage()
            ], 500);

        }
    }


    public function registerAttendee(): void
    {
        $errors = [];
        $successMessage = '';
        $availableEvents = [];
        $registrationType = filter_input(INPUT_POST, 'registration_type', FILTER_SANITIZE_STRING);
        $attendeeName = '';
        $attendeeEmail = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
            $userId = $_SESSION['user_id'] ?? null;

            if (!$eventId || !$userId) {
                $errors['general'] = "Invalid event or user.";
            } else {
                try {
                    $event = $this->eventModel->getEventById($eventId);

                    if (!$event) {
                        $errors['general'] = "Event not found.";
                    } elseif ($this->attendeeModel->getRegisteredAttendeesCount($eventId) >= $event['max_capacity']) {
                        $errors['general'] = "Event is already at full capacity.";
                    } elseif ($this->attendeeModel->isUserRegistered($eventId, $userId)) {
                        $errors['general'] = "You have already registered for this event.";
                    }

                    if ($registrationType === 'self') {
                        if (isset($_SESSION['user_name'], $_SESSION['user_email'])) {
                            $attendeeName = $_SESSION['user_name'];
                            $attendeeEmail = $_SESSION['user_email'];
                        } else {
                            $errors['general'] = "User details are missing from the session.";
                        }
                    } else {
                        $attendeeName = filter_input(INPUT_POST, 'attendee_name', FILTER_SANITIZE_STRING);
                        $attendeeEmail = filter_input(INPUT_POST, 'attendee_email', FILTER_VALIDATE_EMAIL);

                        if (empty($attendeeName)) {
                            $errors['attendee_name'] = "Attendee name is required.";
                        }
                        if (empty($attendeeEmail) || !$attendeeEmail) {
                            $errors['attendee_email'] = "A valid attendee email is required.";
                        }
                    }

                    if (empty($errors)) {
                        $registrationData = [
                            'event_id' => $eventId,
                            'user_id' => $userId,
                            'attendee_name' => $attendeeName,
                            'attendee_email' => $attendeeEmail,
                            'registered_at' => date('Y-m-d H:i:s'),
                            'registration_type' => $registrationType
                        ];

                        if ($this->attendeeModel->registerAttendee($registrationData)) {
                            $successMessage = "Successfully registered for the event.";

                            if ($this->isAjaxRequest()) {
                                echo json_encode(['success' => true, 'message' => $successMessage, 'redirect_url' => BASE_URL . 'events/view/' . $eventId]);

                            } else {
                                $_SESSION['successMessage'] = $successMessage;
                                $this->redirect(BASE_URL . 'events');
                            }
                            return;
                        } else {
                            $errors['general'] = "Failed to register. Please try again.";
                        }
                    }
                } catch (PDOException $e) {
                    $errors['general'] = "A database error occurred. Please try again later.";
                    $this->logger->error("Database Error in Attendee Registration: " . $e->getMessage());
                }
            }
        }

        try {
            $allEvents = $this->eventModel->getAllEvents();
            foreach ($allEvents as $event) {
                $registeredCount = $this->attendeeModel->getRegisteredAttendeesCount($event['id']);
                if ($event['max_capacity'] > $registeredCount) {
                    $availableEvents[] = [
                        'id' => $event['id'],
                        'name' => $event['name'],
                        'description' => $event['description'],
                        'start_datetime' => $event['start_datetime'],
                        'end_datetime' => $event['end_datetime'],
                        'max_capacity' => $event['max_capacity'],
                        'registered_count' => $registeredCount,
                        'available_count' => $event['max_capacity'] - $registeredCount,
                    ];
                }
            }
        } catch (PDOException $e) {
            $errors['general'] = "Failed to fetch available events. Please try again later.";
            $this->logger->error("Database Error in Fetching Events: " . $e->getMessage());
        }

        if ($this->isAjaxRequest()) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $this->renderView('events/register_attendee', [
            'title' => 'Register for Event',
            'errors' => $errors,
            'successMessage' => $successMessage,
            'availableEvents' => $availableEvents,
        ], 'layouts/dashboard_layout');
    }


    public function generateReport(int $id): void
    {
        try {
            if ($id <= 0) {
                $this->renderError('Invalid Event ID', 'The provided event ID is invalid.');
                return;
            }

            if (!$event = $this->eventModel->getEventById($id)) {
                $this->renderError('Event Not Found', 'The event you are trying to generate a report for does not exist.');
                return;
            }

            if (($_SESSION['user_role'] ?? '') !== 'admin') {
                $this->renderError('Forbidden', 'You do not have permission to generate this report.');
                return;
            }

            if ($reportData = $this->eventModel->generateEventReport($id)) {
                $this->renderView('attendees/report', [
                    'title' => "Event Report: {$event['name']}",
                    'event' => $event,
                    'reportData' => $reportData
                ], 'layouts/dashboard_layout');
            } else {
                $this->renderError('Report Generation Failed', 'There was an error generating the report. Please try again later.');
            }
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error("Error generating report for event ID: $id - " . $e->getMessage());
            $this->renderError('Server Error', 'An unexpected error occurred while generating the report.');
        }
    }


    public function deleteAttendee(int $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse(['status' => 'error', 'message' => 'Invalid request method.'], 405);
            return;
        }

        if (($_SESSION['user_role'] ?? null) !== 'admin') {
            $this->sendJsonResponse(['status' => 'error', 'message' => 'You do not have permission to delete this attendee.'], 403);
            return;
        }

        try {
            if (!$this->attendeeModel->getAttendeeById($id)) {
                $this->sendJsonResponse(['status' => 'error', 'message' => 'Attendee not found.'], 404);
                return;
            }

            if (!$this->attendeeModel->deleteAttendeeById($id)) {
                throw new Exception('Failed to delete attendee. Please try again.');
            }

            $this->sendJsonResponse(['status' => 'success', 'message' => 'Attendee deleted successfully.'], 200);

        } catch (\Exception $e) {
            $this->logger->error("Error deleting attendee with ID: $id - " . $e->getMessage());
            $this->sendJsonResponse(['status' => 'error', 'message' => 'An unexpected error occurred while deleting the attendee.'], 500);
        }
    }

}
