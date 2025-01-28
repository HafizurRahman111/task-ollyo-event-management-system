<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\User;
use App\Controllers\BaseController;
use PDO;
use App\Helpers\Logger;

class DashboardController extends BaseController
{
    private PDO $pdo;
    private Logger $logger;
    private User $userModel;
    private AuthHelper $authHelper;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($this->pdo);
        $this->authHelper = new AuthHelper();
        $this->logger = new Logger(__DIR__ . '/../logs/debug.log'); // Adjusted path to logs directory
    }

    public function index()
    {
        if (!$this->authHelper->isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }

        $userId = $this->authHelper->getUserId();
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            // Handle error: user data not found
            die("User not found");
        }

        $eventCount = $this->userModel->getUserCount(); // replace 'yourModel' with the actual model name
        $attendeeCount = $this->userModel->getUserCount(); // replace 'yourModel' with the actual model name
        $userCount = $this->userModel->getUserCount(); // replace 'yourModel' with the actual model name


        $data = [
            'title' => 'Dashboard',
            'styles' => [BASE_URL . 'public/assets/css/dashboard-layout.css'],
            'scripts' => [],
            'content' => $this->loadView('dashboard', ['user' => $user]),
            'eventCount' => $eventCount,
            'attendeeCount' => $attendeeCount,
            'userCount' => $userCount,
            'userId' => $user['id'],            // Pass user id
            'userFullName' => $user['fullname'],  // Pass user fullname
            'userEmail' => $user['email'],        // Pass user email
            'userRole' => $user['role'],          // Pass user role
            'userCreatedAt' => $user['created_at'],  // Pass user created_at
            'userUpdatedAt' => $user['updated_at'],
        ];

        $this->loadLayout('layouts/dashboard_layout', $data);
    }




    public function getEventCount()
    {
        return $this->db->table('events')->countAllResults();
    }

    public function getAttendeeCount()
    {
        return $this->db->table('attendees')->countAllResults();
    }

    public function getUserCount()
    {
        return $this->db->table('users')->countAllResults();
    }

}
