<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Event;
use App\Models\User;
use App\Controllers\BaseController;
use PDO;


class DashboardController extends BaseController
{
    private User $userModel;
    private Event $eventModel;
    private AuthHelper $authHelper;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userModel = new User($this->pdo);
        $this->eventModel = new Event($this->pdo);
        $this->authHelper = new AuthHelper();
    }

    public function index()
    {
        $userId = $this->authHelper->getUserId();
        $user = $this->userModel->getUserById($userId);

        if (!$user) {
            die("User not found");
        }

        $userCount = $this->userModel->getUserCount();
        $reportCount = $this->userModel->getUserCount();
        $eventCount = $this->eventModel->countAllEvents();
        $attendeeCount = $this->userModel->getUserCount();

        $this->renderView('dashboard', [
            'title' => 'Dashboard',
            'styles' => [BASE_URL . 'public/assets/css/dashboard-layout.css'],
            'scripts' => [],
            'user' => $user,
            'userCount' => $userCount,
            'reportCount' => $reportCount,
            'eventCount' => $eventCount,
            'attendeeCount' => $attendeeCount,
            'userId' => $user['id'],
            'userFullName' => $user['fullname'],
            'userEmail' => $user['email'],
            'userRole' => $user['role'],
            'userCreatedAt' => $user['created_at'],
            'userUpdatedAt' => $user['updated_at'],
        ], 'layouts/dashboard_layout');
    }
}
