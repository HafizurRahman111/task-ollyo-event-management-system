<?php

namespace App\Controllers;

use App\Models\User;
use PDO;
use PDOException;

class AuthController extends BaseController
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->userModel = new User($this->pdo);
    }

    public function login(): void
    {
        $this->startSession();

        $this->redirectIfAuthenticated();

        $successMessage = $_SESSION['successMessage'] ?? '';
        unset($_SESSION['successMessage']);

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Please enter a valid email address.";
            }

            if (empty($password) || strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }

            if (empty($errors)) {
                try {
                    $user = $this->userModel->getUserByEmail($email);

                    if (!$user || !password_verify($password, $user['password'])) {
                        $errors[] = "Invalid email or password.";
                    } else {
                        $this->loginUser($user);

                        if ($this->isAjaxRequest()) {
                            $this->sendJsonResponse([
                                'success' => true,
                                'redirect_url' => BASE_URL . 'dashboard',
                            ]);
                            return;
                        }

                        $this->redirect(BASE_URL . 'dashboard');
                    }
                } catch (PDOException $e) {
                    $errors[] = "A database error occurred. Please try again later.";
                    $this->logger->error("Database Error in Login: " . $e->getMessage());
                }
            }
        }

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

        $this->redirectIfAuthenticated();

        $errors = [];
        $successMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT);
            $confirmPassword = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

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
                    $this->logger->error("Database Error in Registration: " . $e->getMessage());
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                    $this->logger->error("Registration Error: " . $e->getMessage());
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
        $this->redirect(BASE_URL . 'login');
    }

    private function logoutUser(): void
    {
        session_unset();
        session_destroy();
    }

}
