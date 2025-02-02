<?php

use App\Controllers\AttendeeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\EventController;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;

/**
 * Helper function to define routes
 */
function route(
    string $method,
    string $path,
    string $controller,
    string $action,
    array $middlewares = [],
    array $parameters = [],
    string $name = null
): array {
    return compact('method', 'path', 'controller', 'action', 'middlewares', 'parameters', 'name');
}

// Middleware Instances
$roleMiddleware = new RoleMiddleware(['admin', 'user']);
$adminMiddleware = new RoleMiddleware(['admin']);

// Parameter Constraints
$idConstraint = ['id' => '[0-9]+'];

return [
    // Home Route
    route('GET', '/ems', HomeController::class, 'index', [], [], 'home'),

    // Authentication Routes
    route('GET', '/ems/login', AuthController::class, 'login', [], [], 'login'),
    route('GET', '/ems/register', AuthController::class, 'register', [], [], 'register'),
    route('POST', '/ems/logout', AuthController::class, 'logout', [AuthMiddleware::class], [], 'logout'),

    // Dashboard Routes
    route('GET', '/ems/dashboard', DashboardController::class, 'index', [AuthMiddleware::class, $roleMiddleware], [], 'dashboard.index'),
    route('GET', '/ems/profile', UserController::class, 'profile', [AuthMiddleware::class, $roleMiddleware], [], 'users.profile'),

    // User Routes
    route('GET', '/ems/users', UserController::class, 'index', [AuthMiddleware::class, $adminMiddleware], [], 'users.index'),

    // Event Routes
    route('GET', '/ems/events', EventController::class, 'index', [AuthMiddleware::class], [], 'events.index'),
    route('POST', '/ems/events/create', EventController::class, 'create', [AuthMiddleware::class, $roleMiddleware], [], 'events.create'),
    route('GET', '/ems/events/view/{id}', EventController::class, 'show', [AuthMiddleware::class, $roleMiddleware], $idConstraint, 'events.show'),
    route('GET', '/ems/events/edit/{id}', EventController::class, 'edit', [AuthMiddleware::class, $roleMiddleware], $idConstraint, 'events.edit'),
    route('POST', '/ems/events/update/{id}', EventController::class, 'update', [AuthMiddleware::class, $roleMiddleware], $idConstraint, 'events.update'),
    route('DELETE', '/ems/events/delete/{id}', EventController::class, 'delete', [AuthMiddleware::class, $roleMiddleware], $idConstraint, 'events.delete'),
    route('GET', '/ems/events/search', EventController::class, 'search', [AuthMiddleware::class], [], 'events.search'),

    // attendees 
    // route('GET', '/ems/events/registration', AttendeeController::class, 'showRegistrationForm', [AuthMiddleware::class], [], 'events.showRegistrationForm'),
    route('POST', '/ems/events/registration', AttendeeController::class, 'registerAttendee', [AuthMiddleware::class], [], 'events.registerAttendee'),
    route('GET', '/ems/events/check/{id}', AttendeeController::class, 'checkRegistration', [AuthMiddleware::class, $roleMiddleware], $idConstraint, 'events.checkRegistration'),

    route('GET', '/ems/events/report/{id}', AttendeeController::class, 'generateReport', [AuthMiddleware::class, $adminMiddleware], $idConstraint, 'events.generateReport'),
    route('POST', '/ems/attendees/delete/{id}', AttendeeController::class, 'deleteAttendee', [AuthMiddleware::class, $adminMiddleware], $idConstraint, 'events.deleteAttendee'),

    // API Routes
    route('GET', '/ems/api/events/{id}', EventController::class, 'getEventDetails', [], $idConstraint, 'events.getEventDetails'),
];