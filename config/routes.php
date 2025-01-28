<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\EventsController;
use App\Controllers\HomeController;
use App\Controllers\AttendeeController;

return [
    // Base prefix
    '/ems' => [
        // Home route
        // One-layer route
        '' => [
            'controller' => HomeController::class,
            'action' => 'index',
        ],

        // Authentication routes
        // Two-layer route (auth/login)
        'auth/login' => [
            'controller' => AuthController::class,
            'action' => 'login',
        ],
        'auth/register' => [
            'controller' => AuthController::class,
            'action' => 'register',
        ],
        'auth/logout' => [
            'controller' => AuthController::class,
            'action' => 'logout',
        ],
        'dashboard' => [
            'controller' => DashboardController::class,
            'action' => 'index',
            'middleware' => ['auth'],
        ],

        // Dashboard routes (with middleware)
        // 'dashboard' => [
        //         'controller' => DashboardController::class,
        //         'action' => 'index',
        //         'middleware' => ['auth'],
        //     ],
        // 'events' => [
        //     'index' => [
        //         'controller' => EventsController::class,
        //         'action' => 'index',
        //         'middleware' => ['auth'],
        //     ],
        //     'create' => [
        //         'controller' => EventsController::class,
        //         'action' => 'create',
        //         'middleware' => ['auth'],
        //     ],
        //     '{id}' => [
        //         'controller' => EventsController::class,
        //         'action' => 'show',
        //         'parameters' => ['id'],
        //         'middleware' => ['auth'],
        //     ],
        //     '{id}/edit' => [
        //         'controller' => EventsController::class,
        //         'action' => 'edit',
        //         'parameters' => ['id'],
        //         'middleware' => ['auth'],
        //     ],
        //     '{id}/delete' => [
        //         'controller' => EventsController::class,
        //         'action' => 'delete',
        //         'parameters' => ['id'],
        //         'middleware' => ['auth'],
        //     ],
        // ],
        // 'attendees' => [
        //     'index' => [
        //         'controller' => AttendeeController::class,
        //         'action' => 'index',
        //         'middleware' => ['auth'],
        //     ],
        // ]
    ],

    // Admin routes (middleware restricted to admins)
    // 'admin' => [
    //     'index' => [
    //         'controller' => AdminController::class,
    //         'action' => 'index',
    //         'middleware' => ['auth', 'admin'],
    //     ],
    // ]

];
