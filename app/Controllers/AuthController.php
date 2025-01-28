<?php

namespace App\Controllers;

use App\Models\User;
use PDO;
use PDOException;
use App\Helpers\Logger;

class AuthController extends BaseController
{
    private PDO $pdo;
    private $logger;

    private User $userModel;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($this->pdo);

        $this->logger = new Logger(__DIR__ . '/logs/debug.log');
    }

    public function login(): void
    {
        $this->startSession();
        $successMessage = $_SESSION['successMessage'] ?? '';
        unset($_SESSION['successMessage']);

        if ($this->isLoggedIn()) {
            $this->redirect('/ems/dashboard');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize inputs
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);

            // Validate inputs
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Please enter a valid email address.";
            }

            if (empty($password) || strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }

            if (empty($errors)) {
                try {
                    // Retrieve user from database
                    $user = $this->userModel->getUserByEmail($email);

                    if (!$user || !password_verify($password, $user['password'])) {
                        $errors[] = "Invalid email or password.";
                    } else {
                        $this->loginUser($user);

                        // Handle AJAX request
                        if ($this->isAjaxRequest()) {
                            $this->sendJsonResponse([
                                'success' => true,
                                'redirect_url' => '/ems/dashboard',
                            ]);
                            return;
                        }

                        $this->redirect('/ems/dashboard');
                    }
                } catch (PDOException $e) {
                    $errors[] = "A database error occurred. Please try again later.";
                    $this->logger->log("Database Error in Login: " . $e->getMessage());
                }
            }
        }

        // Handle response
        if ($this->isAjaxRequest()) {
            $this->sendJsonResponse([
                'success' => false,
                'errors' => $errors,
            ]);
        } else {
            $this->renderView('auth/login', [
                'title' => 'Login',
                'errors' => $errors,
                'successMessage' => $successMessage,
                'styles' => [BASE_URL . 'public/assets/css/form.css'],
                'scripts' => [],
            ]);
        }
    }

    public function register(): void
    {
        $this->startSession();

        $errors = [];
        $successMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize inputs
            $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
            $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

            // Validate inputs
            if (empty($fullname) || strlen($fullname) < 3 || strlen($fullname) > 250) {
                $errors[] = "Full name must be between 3 and 250 characters.";
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Please enter a valid email address.";
            }

            if (empty($password) || strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }

            if ($password !== $confirmPassword) {
                $errors[] = "Passwords do not match.";
            }

            if (empty($errors) && $this->userModel->emailExists($email)) {
                $errors[] = "Email is already registered.";
            }

            if (empty($errors)) {
                try {
                    $userSaved = $this->userModel->registerUser($fullname, $email, $password);

                    if ($userSaved) {
                        $_SESSION['successMessage'] = "Registration successful! You can now log in.";
                        echo json_encode(['success' => true]);
                        return;
                    } else {
                        $errors[] = 'Registration failed. Please try again.';
                    }
                } catch (PDOException $e) {
                    $errors[] = "A database error occurred. Please try again later.";
                    $this->logger->log("Database Error in Registration: " . $e->getMessage());
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    $this->logger->log("Registration Error: " . $e->getMessage());
                }
            }

            echo json_encode(['success' => false, 'errors' => $errors]);
        } else {
            $this->renderView('auth/register', [
                'title' => 'Register',
                'errors' => $errors,
                'successMessage' => $successMessage,
                'styles' => [BASE_URL . 'public/assets/css/form.css'],
                'scripts' => [],
            ]);
        }
    }


    public function logout(): void
    {
        $this->startSession();
        $this->logoutUser();
        $this->redirect('/ems/home');
    }

    private function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true;
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function loginUser(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['logged_in'] = true;
    }

    private function logoutUser(): void
    {
        session_unset();
        session_destroy();
    }

    private function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    private function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private function sendJsonResponse(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    private function renderView(string $view, array $data = []): void
    {
        $data['content'] = $this->loadView($view, $data);
        $this->loadLayout('layouts/site_layout', $data);
    }
}
