<?php

namespace App\Controllers;

use App\Models\Attendee;
use App\Models\Event;
use PDO;
use Exception;

class EventController extends BaseController
{
    private Event $eventModel;
    private Attendee $attendeeModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->eventModel = new Event($this->pdo);
        $this->attendeeModel = new Attendee($this->pdo);

    }

    public function index(): void
    {
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
        $sort = htmlspecialchars($_GET['sort'] ?? 'id', ENT_QUOTES, 'UTF-8');
        $order = strtolower($_GET['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $search = htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8');
        $limit = 5;

        $userRole = $_SESSION['user_role'] ?? null;
        $currentUserId = $_SESSION['user_id'] ?? null;

        try {
            $eventsData = $this->eventModel->getEvents($page, $limit, $sort, $order, $search);
            $events = $eventsData['events'];
            $totalPages = $eventsData['totalPages'];
            $totalResults = $eventsData['totalResults'];
            $showingResults = $eventsData['showingResults'];

            if ($this->isAjaxRequest()) {
                $this->sendJsonResponse([
                    'status' => 'success',
                    'data' => [
                        'events' => $events,
                        'totalPages' => $totalPages,
                        'totalResults' => $totalResults,
                        'showingResults' => $showingResults,
                    ],
                ]);
                return;
            }

            $this->renderView('events/list', [
                'title' => 'Events Manage',
                'styles' => [BASE_URL . 'public/assets/css/event.css'],
                'scripts' => [],
                'userRole' => $userRole,
                'currentUserId' => $currentUserId,
                'events' => $events,
                'showingResults' => $showingResults,
                'totalPages' => $totalPages,
                'totalResults' => $totalResults,
                'currentPage' => $page,
                'sort' => $sort,
                'order' => $order,
                'search' => $search,
            ], 'layouts/dashboard_layout');

        } catch (\Exception $e) {
            $this->logger->error("Error fetching events: " . $e->getMessage());

            if ($this->isAjaxRequest()) {
                $this->sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Failed to fetch events. Please try again later.',
                ], 500);
                return;
            }

            $this->renderView('errors/error', [
                'title' => 'Error',
                'styles' => [BASE_URL . 'public/assets/css/error.css'],
                'scripts' => [],
                'message' => 'An error occurred while fetching events. Please try again later.',
            ], 'layouts/dashboard_layout');
        }
    }


    public function create(): void
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $max_capacity = filter_input(INPUT_POST, 'max_capacity', FILTER_VALIDATE_INT);
            $start_datetime = filter_input(INPUT_POST, 'start_datetime', FILTER_SANITIZE_STRING);
            $end_datetime = filter_input(INPUT_POST, 'end_datetime', FILTER_SANITIZE_STRING);

            if (empty($name)) {
                $errors['name'] = "Event name is required.";
            } elseif (strlen($name) < 5 || strlen($name) > 250) {
                $errors['name'] = "Event name must be between 5 and 250 characters.";
            }

            if (empty($description)) {
                $errors['description'] = "Event description is required.";
            } elseif (strlen($description) < 10 || strlen($description) > 1000) {
                $errors['description'] = "Event description must be between 10 and 1000 characters.";
            }

            if (empty($max_capacity) || $max_capacity <= 0) {
                $errors['max_capacity'] = "Maximum capacity must be a positive integer.";
            }

            if (empty($start_datetime) || !strtotime($start_datetime)) {
                $errors['start_datetime'] = "Please enter a valid start date and time.";
            }

            if (empty($end_datetime) || !strtotime($end_datetime)) {
                $errors['end_datetime'] = "Please enter a valid end date and time.";
            } elseif (strtotime($end_datetime) <= strtotime($start_datetime)) {
                $errors['end_datetime'] = "End date and time must be after the start date and time.";
            }

            if (empty($errors)) {
                try {
                    $slug = $this->generateSlugFromName($name);

                    $existingEvent = $this->eventModel->getEventBySlug($slug);

                    if ($existingEvent) {
                        $slug = $this->generateUniqueSlugFromName($name);
                    }

                    $eventData = [
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                        'max_capacity' => $max_capacity,
                        'start_datetime' => $start_datetime,
                        'end_datetime' => $end_datetime,
                        'created_by' => $_SESSION['user_id'],
                    ];

                    $eventId = $this->eventModel->createEvent($eventData);

                    if ($eventId) {
                        echo json_encode(['success' => true, 'eventId' => $eventId, 'slug' => $slug]);
                        return;
                    } else {
                        $errors['general'] = "Failed to create event. Please try again.";
                    }
                } catch (PDOException $e) {
                    $errors['general'] = "A database error occurred. Please try again later.";
                    $this->logger->error("Database Error in Event Creation: " . $e->getMessage());
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                    $this->logger->error("Event Create Error: " . $e->getMessage());
                }
            }

            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        $this->renderView('events/create', [
            'title' => 'Create Event',
            'styles' => [BASE_URL . 'public/assets/css/form.css'],
            'scripts' => [BASE_URL . ''],
        ], 'layouts/dashboard_layout');
    }

    private function generateUniqueSlugFromName(string $name): string
    {
        $slug = $this->generateSlugFromName($name);

        $existingEvent = $this->eventModel->getEventBySlug($slug);
        $counter = 1;

        while ($existingEvent) {
            $slug = $this->generateSlugFromName($name) . '-' . $counter;
            $existingEvent = $this->eventModel->getEventBySlug($slug);
            $counter++;
        }

        return $slug;
    }

    public function show(int $id): void
    {
        try {
            $event = $this->eventModel->getEventById($id);
            $userRole = $_SESSION['user_role'] ?? null;

            if (!$event) {
                $this->renderView('errors/404', [
                    'title' => 'Event Not Found',
                    'styles' => [BASE_URL . 'public/assets/css/error.css'],
                    'scripts' => [],
                ], 'layouts/dashboard_layout');
                return;
            }

            $this->renderView('events/show', [
                'title' => $event['name'],
                'styles' => [BASE_URL . 'public/assets/css/event.css'],
                'scripts' => [],
                'userRole' => $userRole,
                'event' => $event,
            ], 'layouts/dashboard_layout');
        } catch (PDOException $e) {
            // Log the error and show a 500 error page
            $this->logger->error("Error fetching event: " . $e->getMessage());
            $this->renderView('errors/500', [
                'title' => 'Server Error',
                'styles' => [BASE_URL . 'public/assets/css/error.css'],
                'scripts' => [],
            ], 'layouts/dashboard_layout');
        }
    }

    public function edit(int $id): void
    {
        $userRole = $_SESSION['user_role'] ?? null;
        $currentUserId = $_SESSION['user_id'] ?? null;

        try {
            $event = $this->eventModel->getEventById($id);

            if (!$event) {
                $this->renderView('errors/error', [
                    'title' => 'Event Not Found',
                    'message' => 'The event you are looking for does not exist.',
                ], 'layouts/dashboard_layout');
                return;
            }

            // Check if the user is allowed to edit the event
            if ($event['created_by'] !== $currentUserId && $userRole !== 'admin') {
                $this->renderView('errors/error', [
                    'title' => 'Forbidden',
                    'message' => 'You do not have permission to edit this event.',
                ], 'layouts/dashboard_layout');
                return;
            }

            $this->renderView('events/edit', [
                'title' => 'Edit Event: ' . $event['name'],
                'styles' => [BASE_URL . 'public/assets/css/event.css'],
                'scripts' => [],
                'event' => $event,
            ], 'layouts/dashboard_layout');
        } catch (PDOException $e) {
            // Log the error and show a 500 error page
            $this->logger->error("Error fetching event: " . $e->getMessage());
            $this->renderView('errors/error', [
                'title' => 'Server Error',
                'message' => 'An internal server error occurred. Please try again later.',
            ], 'layouts/dashboard_layout');
        }
    }



    public function update(int $id): void
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $max_capacity = filter_input(INPUT_POST, 'max_capacity', FILTER_VALIDATE_INT);
            $start_datetime = filter_input(INPUT_POST, 'start_datetime', FILTER_SANITIZE_STRING);
            $end_datetime = filter_input(INPUT_POST, 'end_datetime', FILTER_SANITIZE_STRING);

            if (empty($name)) {
                $errors['name'] = "Event name is required.";
            } elseif (strlen($name) < 5 || strlen($name) > 250) {
                $errors['name'] = "Event name must be between 5 and 250 characters.";
            }

            if (empty($description)) {
                $errors['description'] = "Event description is required.";
            } elseif (strlen($description) < 10 || strlen($description) > 1000) {
                $errors['description'] = "Event description must be between 10 and 1000 characters.";
            }

            if (empty($max_capacity) || $max_capacity <= 0) {
                $errors['max_capacity'] = "Maximum capacity must be a positive integer.";
            }

            if (empty($start_datetime) || !strtotime($start_datetime)) {
                $errors['start_datetime'] = "Please enter a valid start date and time.";
            }

            if (empty($end_datetime) || !strtotime($end_datetime)) {
                $errors['end_datetime'] = "Please enter a valid end date and time.";
            } elseif (strtotime($end_datetime) <= strtotime($start_datetime)) {
                $errors['end_datetime'] = "End date and time must be after the start date and time.";
            }

            if (empty($errors)) {
                try {
                    $slug = $this->generateSlugFromName($name);

                    $eventData = [
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                        'max_capacity' => $max_capacity,
                        'start_datetime' => $start_datetime,
                        'end_datetime' => $end_datetime,
                    ];

                    $updated = $this->eventModel->updateEvent($id, $eventData);

                    if ($updated) {
                        $_SESSION['successMessage'] = "Event updated successfully.";
                        $this->redirect(BASE_URL . 'events/view/' . $id);
                        return;
                    } else {
                        $errors['general'] = "Failed to update event. Please try again.";
                    }
                } catch (PDOException $e) {
                    $errors['general'] = "A database error occurred. Please try again later.";
                    $this->logger->error("Database Error in Event Update: " . $e->getMessage());
                }
            }
        }

        $event = $this->eventModel->getEventById($id);

        if (!$event) {
            $this->renderView('errors/404', [
                'title' => 'Event Not Found',
                'styles' => [BASE_URL . 'public/assets/css/error.css'],
                'scripts' => [],
            ], 'layouts/dashboard_layout');
            return;
        }

        $this->renderView('events/edit', [
            'title' => 'Edit Event',
            'styles' => [BASE_URL . 'public/assets/css/event.css'],
            'scripts' => [],
            'event' => $event,
            'errors' => $errors,
        ], 'layouts/dashboard_layout');
    }



    public function delete(int $id): void
    {
        $currentUserId = $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['user_role'] ?? null;

        try {

            $event = $this->eventModel->getEventById($id);

            if (!$event) {
                $this->sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Event not found'
                ], 404);
                return;
            }

            if ($event['created_by'] !== $currentUserId && $userRole !== 'admin') {
                $this->sendJsonResponse([
                    'status' => 'error',
                    'message' => 'You do not have permission to delete this event.'
                ], 403);
                return;
            }

            $success = $this->eventModel->deleteEvent($id);

            if ($success) {
                $this->sendJsonResponse([
                    'status' => 'success',
                    'message' => 'Event deleted successfully'
                ], 200);

            } else {
                $this->sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Event not found or could not be deleted'
                ], 404);
            }
        } catch (\Exception $e) {
            $this->logger->log("Error deleting event with ID: " . $id . ': ' . $e->getMessage());

            $this->sendJsonResponse([
                'status' => 'error',
                'message' => 'An unexpected error occurred while deleting the event.'
            ], 500);
        }
    }

    public function search(): void
    {
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?? '';

        try {
            $events = $this->eventModel->searchEvents($search);
            $this->sendJsonResponse(['status' => 'success', 'data' => $events], 200);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['status' => 'error', 'message' => 'Failed to search events'], 500);
        }
    }


    /**
     * Fetch event details by ID and return as JSON.
     *
     * @param int $id The ID of the event.
     */
    public function getEventDetails(int $id): void
    {
        try {
            $event = $this->eventModel->getEventById($id);

            if (!$event) {
                $this->sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Event not found.',
                ], 404);
                return;
            }

            $this->sendJsonResponse([
                'status' => 'success',
                'data' => $event,
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Error in EventController::getEventDetails: " . $e->getMessage());

            $this->sendJsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while fetching event details.',
            ], 500);
        }
    }

}
