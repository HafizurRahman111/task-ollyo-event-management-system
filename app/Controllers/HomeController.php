<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $authHelper = new AuthHelper();

        if ($authHelper->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }

        $events = $this->getEvents();

        $data = [
            'title' => 'Welcome to Event Management System',
            'styles' => [
                BASE_URL . 'public/assets/css/home.css',
            ],
            'scripts' => [],
            'content' => $this->loadView('home', ['events' => $events]),
        ];

        // echo '<pre>';
        // print_r($data['content']);
        // echo '</pre>';

        $this->loadLayout('layouts/site_layout', $data);

    }

    /**
     * Fetch event data.
     *
     * @return array
     */
    public function getEvents()
    {
        return [
            [
                'id' => 1,
                'name' => 'Event 1',
                'description' => 'Description of Event 1.',
                'max_capacity' => 500,
                'start_time' => '2025-02-01 10:00:00',
                'end_time' => '2025-02-01 18:00:00',
            ],
            [
                'id' => 2,
                'name' => 'Event 2',
                'description' => 'Description of Event 2.',
                'max_capacity' => 300,
                'start_time' => '2025-02-05 09:00:00',
                'end_time' => '2025-02-05 17:00:00',
            ],
            [
                'id' => 3,
                'name' => 'Event 3',
                'description' => 'Description of Event 3.',
                'max_capacity' => 250,
                'start_time' => '2025-02-10 08:00:00',
                'end_time' => '2025-02-10 16:00:00',
            ],
            // [
            //     'id' => 4,
            //     'name' => 'Event 4',
            //     'description' => 'Description of Event 4.',
            //     'max_capacity' => 400,
            //     'start_time' => '2025-02-12 11:00:00',
            //     'end_time' => '2025-02-12 19:00:00',
            // ],
            // [
            //     'id' => 5,
            //     'name' => 'Event 5',
            //     'description' => 'Description of Event 5.',
            //     'max_capacity' => 150,
            //     'start_time' => '2025-02-15 14:00:00',
            //     'end_time' => '2025-02-15 22:00:00',
            // ],
            // [
            //     'id' => 6,
            //     'name' => 'Event 6',
            //     'description' => 'Description of Event 6.',
            //     'max_capacity' => 350,
            //     'start_time' => '2025-02-20 13:00:00',
            //     'end_time' => '2025-02-20 21:00:00',
            // ],
        ];
    }

}