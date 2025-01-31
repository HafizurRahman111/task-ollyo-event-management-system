<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\EventController;
use App\Controllers\HomeController;
use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;

/**
 * Helper function to define routes 
 */
function route(string $method, string $path, string $controller, string $action, array $middlewares = [], array $parameters = [], string $name = null)
{
    return compact('method', 'path', 'controller', 'action', 'middlewares', 'parameters', 'name');
}

$roleMiddleware = new RoleMiddleware(['admin', 'user']);
$adminMiddleware = new RoleMiddleware(['admin']);

return [
    // Home Route 
    route('GET', '/ems', HomeController::class, 'index', [], [], 'home'),

    // Authentication Routes
    route('GET', '/ems/login', AuthController::class, 'login', [], [], 'login'),
    route('GET', '/ems/register', AuthController::class, 'register', [], [], 'register'),
    route('POST', '/ems/logout', AuthController::class, 'logout', [AuthMiddleware::class], [], 'logout'),

    // Dashboard Route
    route('GET', '/ems/dashboard', DashboardController::class, 'index', [AuthMiddleware::class, $roleMiddleware], [], 'dashboard.index'),

    // Event Routes
    route('GET', '/ems/events', EventController::class, 'index', [AuthMiddleware::class], [], 'events.index'),
    route('POST', '/ems/events/create', EventController::class, 'create', [AuthMiddleware::class, $roleMiddleware], [], 'events.create'),
    route('GET', '/ems/events/view/{id}', EventController::class, 'show', [AuthMiddleware::class, $roleMiddleware], ['id' => '[0-9]+'], 'events.show'),
    route('GET', '/ems/events/edit/{id}', EventController::class, 'edit', [AuthMiddleware::class, $roleMiddleware], ['id' => '[0-9]+'], 'events.edit'),
    route('POST', '/ems/events/update/{id}', EventController::class, 'update', [AuthMiddleware::class, $roleMiddleware], ['id' => '[0-9]+'], 'events.edit'),
    route('DELETE', '/ems/events/delete/{id}', EventController::class, 'delete', [AuthMiddleware::class, $roleMiddleware], ['id' => '[0-9]+'], 'events.delete'),
    route('GET', '/ems/events/search', EventController::class, 'create', [AuthMiddleware::class], [], 'events.search'),

    route('POST', '/ems/events/registration', EventController::class, 'registerAttendee', [AuthMiddleware::class], [], 'events.registerAttendee'),

    route('GET', '/ems/events/report/{id}', EventController::class, 'generateReport', [AuthMiddleware::class, $adminMiddleware], ['id' => '[0-9]+'], 'events.generateReport'),
    route('GET', '/ems/attendees/delete/{id}', EventController::class, 'deleteAttendee', [AuthMiddleware::class, $adminMiddleware], ['id' => '[0-9]+'], 'events.deleteAttendee'),

];