<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\User;
use PDO;
use Exception;

class UserController extends BaseController
{
    private User $userModel;
    private AuthHelper $authHelper;


    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userModel = new User($this->pdo);
        $this->authHelper = new AuthHelper();
    }

    public function index(): void
    {
        try {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
            $search = htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8');
            $sort = $_GET['sort'] ?? 'id';
            $order = $_GET['order'] ?? 'asc';

            $usersData = $this->userModel->getUsers($page, 5, $search, $sort, $order);
            $users = $usersData['users'];
            $totalPages = $usersData['totalPages'];
            $totalResults = $usersData['totalResults'];

            if ($this->isAjaxRequest()) {
                $this->sendJsonResponse([
                    'status' => 'success',
                    'data' => [
                        'users' => $users,
                        'totalPages' => $totalPages,
                        'totalResults' => $totalResults,
                        'showingResults' => count($users),
                    ],
                ]);
                return;
            }

            $this->renderView('users/list', [
                'title' => 'Users',
                'users' => $users,
                'totalPages' => $totalPages,
                'totalResults' => $totalResults,
                'currentPage' => $page,
                'search' => $search,
                'sort' => $sort,
                'order' => $order,
                'showingResults' => count($users),
            ], 'layouts/dashboard_layout');
        } catch (\Exception $e) {
            $this->logger->error("Error in UserController: " . $e->getMessage());

            if ($this->isAjaxRequest()) {
                $this->sendJsonResponse([
                    'status' => 'error',
                    'message' => 'Failed to fetch users. Please try again later.',
                ], 500);
            } else {
                $this->renderError('Server Error', 'An error occurred while fetching users.');
            }
        }
    }

    public function profile(): void
    {
        try {
            $userId = $_SESSION['user_id'];

            if (!$userId) {
                $this->renderError('Unauthorized', 'You are not logged in.');
                return;
            }

            $user = $this->userModel->getUserById($userId);

            if (!$user) {
                $this->renderError('User Not Found', 'The requested user does not exist.');
                return;
            }

            $this->renderView('users/profile', [
                'title' => 'User Profile',
                'user' => $user,
            ], 'layouts/dashboard_layout');
        } catch (\Exception $e) {
            $this->logger->error("Error in UserController::profile: " . $e->getMessage());

            $this->renderError('Server Error', 'An error occurred while fetching the user profile.');
        }
    }


}


