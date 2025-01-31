<?php

namespace App\Controllers;

use App\Models\Event;
use PDO;
use Exception;


class EventController extends BaseController
{
    private Event $eventModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->eventModel = new Event($this->pdo);
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
        $successMessage = $_SESSION['successMessage'] ?? '';
        unset($_SESSION['successMessage']);

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
            }

            if (empty($max_capacity) || $max_capacity <= 0) {
                $errors['max_capacity'] = "Maximum capacity must be a positive integer.";
            }

            if (empty($start_datetime) || !strtotime($start_datetime)) {
                $errors['start_datetime'] = "Please enter a valid start date and time.";
            }
            if (!strtotime($end_datetime)) {
                $errors['end_datetime'] = "Please enter a valid end date and time.";
            }
            if (strtotime($end_datetime) <= strtotime($start_datetime)) {
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
                        'created_by' => $_SESSION['user_id'],
                    ];

                    $eventId = $this->eventModel->createEvent($eventData);

                    if ($eventId) {
                        $_SESSION['successMessage'] = "Event created successfully.";

                        if ($this->isAjaxRequest()) {
                            echo json_encode(['success' => true]);
                            return;
                        } else {
                            $this->redirect(BASE_URL . 'events');
                            return;
                        }
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

            if ($this->isAjaxRequest()) {
                echo json_encode(['success' => false, 'errors' => $errors]);
                return;
            }
        }

        $this->renderView('events/create', [
            'title' => 'Create Event',
            'successMessage' => $successMessage,
            'errors' => $errors,
            'styles' => [BASE_URL . 'public/assets/css/event.css'],
            'scripts' => [],
        ], 'layouts/dashboard_layout');
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
            // Sanitize and validate inputs
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $max_capacity = filter_input(INPUT_POST, 'max_capacity', FILTER_VALIDATE_INT);
            $start_datetime = filter_input(INPUT_POST, 'start_datetime', FILTER_SANITIZE_STRING);
            $end_datetime = filter_input(INPUT_POST, 'end_datetime', FILTER_SANITIZE_STRING);

            // Validate name
            if (empty($name)) {
                $errors['name'] = "Event name is required.";
            } elseif (strlen($name) < 5 || strlen($name) > 250) {
                $errors['name'] = "Event name must be between 5 and 250 characters.";
            }

            // Validate description
            if (empty($description)) {
                $errors['description'] = "Event description is required.";
            }

            // Validate max capacity
            if (empty($max_capacity) || $max_capacity <= 0) {
                $errors['max_capacity'] = "Maximum capacity must be a positive integer.";
            }

            // Validate start datetime
            if (empty($start_datetime) || !strtotime($start_datetime)) {
                $errors['start_datetime'] = "Please enter a valid start date and time.";
            }

            // Validate end datetime
            if (empty($end_datetime) || !strtotime($end_datetime)) {
                $errors['end_datetime'] = "Please enter a valid end date and time.";
            } elseif (strtotime($end_datetime) <= strtotime($start_datetime)) {
                $errors['end_datetime'] = "End date and time must be after the start date and time.";
            }

            // If no errors, proceed to update the event
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

                    // Update the event
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

        // Fetch the event data to pre-fill the form
        $event = $this->eventModel->getEventById($id);

        if (!$event) {
            // If the event is not found, show a 404 error
            $this->renderView('errors/404', [
                'title' => 'Event Not Found',
                'styles' => [BASE_URL . 'public/assets/css/error.css'],
                'scripts' => [],
            ], 'layouts/dashboard_layout');
            return;
        }

        // Render the edit form with errors and event data
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






    public function registerAttendee(): void
    {
        $errors = [];
        $successMessage = '';
        $availableEvents = [];
        $registrationType = filter_input(INPUT_POST, 'registration_type', FILTER_SANITIZE_STRING);
        $attendeeName = '';
        $attendeeEmail = '';

        // Check if the request is a POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and validate inputs
            $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
            $userId = $_SESSION['user_id'] ?? null;

            // Validate event ID and user ID
            if (!$eventId || !$userId) {
                $errors['general'] = "Invalid event or user.";
            } else {
                try {
                    // Fetch the event by ID
                    $event = $this->eventModel->getEventById($eventId);

                    // Validate event and registration conditions
                    if (!$event) {
                        $errors['general'] = "Event not found.";
                    } elseif ($this->eventModel->getRegisteredAttendeesCount($eventId) >= $event['max_capacity']) {
                        $errors['general'] = "Event is already at full capacity.";
                    } elseif ($this->eventModel->isUserRegistered($eventId, $userId, $registrationType)) {
                        $errors['general'] = "You have already registered for this event.";
                    }

                    // Handle attendee details based on registration type
                    if ($registrationType === 'self') {
                        if (isset($_SESSION['user_name'], $_SESSION['user_email'])) {
                            $attendeeName = $_SESSION['user_name'];
                            $attendeeEmail = $_SESSION['user_email'];
                        } else {
                            $errors['general'] = "User details are missing from the session.";
                        }
                    } else {
                        // For "other" registration, sanitize input values
                        $attendeeName = filter_input(INPUT_POST, 'attendee_name', FILTER_SANITIZE_STRING);
                        $attendeeEmail = filter_input(INPUT_POST, 'attendee_email', FILTER_VALIDATE_EMAIL);

                        // Validate attendee name and email for "other" registration
                        if (empty($attendeeName)) {
                            $errors['attendee_name'] = "Attendee name is required.";
                        }
                        if (empty($attendeeEmail) || !$attendeeEmail) {
                            $errors['attendee_email'] = "A valid attendee email is required.";
                        }
                    }

                    // Proceed with registration if no errors
                    if (empty($errors)) {
                        // Prepare registration data
                        $registrationData = [
                            'event_id' => $eventId,
                            'user_id' => $userId,
                            'attendee_name' => $attendeeName,
                            'attendee_email' => $attendeeEmail,
                            'registered_at' => date('Y-m-d H:i:s'),
                            'registration_type' => $registrationType
                        ];

                        if ($this->eventModel->registerAttendee($registrationData)) {
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
                    // Log database errors
                    $errors['general'] = "A database error occurred. Please try again later.";
                    $this->logger->error("Database Error in Attendee Registration: " . $e->getMessage());
                }
            }
        }

        // Fetch available events
        try {
            $allEvents = $this->eventModel->getAllEvents();
            foreach ($allEvents as $event) {
                $registeredCount = $this->eventModel->getRegisteredAttendeesCount($event['id']);
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

        // Handle AJAX response for errors
        if ($this->isAjaxRequest()) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            return;
        }

        // Render the view with available events and errors
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
            // Validate event ID
            if ($id <= 0) {
                $this->renderError('Invalid Event ID', 'The provided event ID is invalid.');
                return;
            }

            // Fetch event details
            if (!$event = $this->eventModel->getEventById($id)) {
                $this->renderError('Event Not Found', 'The event you are trying to generate a report for does not exist.');
                return;
            }

            // Check user permissions
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





    // delete attendee by admin
    public function deleteAttendee(int $id): void
    {
        $userRole = $_SESSION['user_role'] ?? null;

        if ($userRole !== 'admin') {
            $this->sendJsonResponse([
                'status' => 'error',
                'message' => 'You do not have permission to delete this attendee.'
            ], 403);
            return;
        }

        try {
            $attendee = $this->eventModel->getAttendeeById($id);

            if (!$attendee) {
                $this->sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Attendee not found.'
                ], 404);
                return;
            }

            $success = $this->eventModel->deleteAttendee($id);

            if ($success) {
                $this->sendJsonResponse([
                    'status' => 'success',
                    'message' => 'Attendee deleted successfully.'
                ], 200);
            } else {
                $this->sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Attendee could not be deleted.'
                ], 500);
            }

        } catch (\Exception $e) {
            $this->logger->error("Error deleting attendee with ID: " . $id . ' - ' . $e->getMessage());

            $this->sendJsonResponse([
                'status' => 'error',
                'message' => 'An unexpected error occurred while deleting the attendee.'
            ], 500);
        }
    }


}
