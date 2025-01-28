<?php

class EventController
{
    public function index()
    {
        require_once '../app/Helpers/middleware.php';

        // Only logged-in users can access
        middleware();

        // Logic to fetch events
        require_once '../app/Views/events/list.php';
    }

    public function create()
    {
        require_once '../app/Helpers/middleware.php';

        // Only admins can access event creation
        middleware('admin');

        require_once '../app/Views/events/create.php';
    }
}
