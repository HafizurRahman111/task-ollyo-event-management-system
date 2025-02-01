<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use App\Controllers\BaseController;
use PDO;

class DashboardController extends BaseController
{
    private User $userModel;
    private Event $eventModel;
    private Attendee $attendeeModel;
    private AuthHelper $authHelper;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userModel = new User($this->pdo);
        $this->eventModel = new Event($this->pdo);
        $this->attendeeModel = new Attendee($this->pdo);
        $this->authHelper = new AuthHelper();
    }

    public function index()
    {
        // current user id
        $userId = $this->authHelper->getUserId();

        // fetch the user details
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            $this->sendJsonResponse(['error' => "Record not found in table: users"], 404);
            return;
        }

        $userCount = $this->userModel->getUserCount();
        $eventCount = $this->eventModel->getEventCount();
        $attendeeCount = $this->attendeeModel->getAttendeeCount();

        $this->renderView('dashboard', [
            'title' => 'Dashboard',
            'styles' => [BASE_URL . 'public/assets/css/dashboard-layout.css'],
            'scripts' => [],
            'user' => $user,
            'userCount' => $userCount,
            'eventCount' => $eventCount,
            'attendeeCount' => $attendeeCount,
            'userId' => $user['id'],
            'userEmail' => $user['email'],
            'userRole' => $user['role'],
            'userCreatedAt' => $user['created_at'],
            'userUpdatedAt' => $user['updated_at'],
        ], 'layouts/dashboard_layout');
    }
}
