<?php

namespace App\Controllers;

use App\Models\Event;
use PDO;

class HomeController extends BaseController
{
    private Event $eventModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->eventModel = new Event($this->pdo);
    }

    public function index()
    {
        $events = $this->eventModel->getAllEvents(3);

        $this->renderView('home', [
            'title' => 'EMS',
            'styles' => [BASE_URL . 'public/assets/css/home.css'],
            'scripts' => [],
            'events' => $events
        ], 'layouts/site_layout');
    }
}
