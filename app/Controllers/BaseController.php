<?php

namespace App\Controllers;

use PDO;
use App\Utils\Logger;

class BaseController
{
    protected PDO $pdo;
    protected Logger $logger;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->logger = new Logger(__DIR__ . '/logs/debug.log');

    }

    /**
     * Load a view and return its output as a string.
     *
     * @param string $viewPath The path to the view file.
     * @param array $data Data to pass to the view.
     * @return string The rendered view content.
     * @throws \RuntimeException If the view file does not exist.
     */
    protected function loadView(string $viewPath, array $data = []): string
    {
        $viewFile = __DIR__ . "/../Views/{$viewPath}.php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file '{$viewPath}' not found.");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        return ob_get_clean();
    }

    /**
     * Load a layout and render the page.
     *
     * @param string $layoutPath The path to the layout file.
     * @param array $data Data to pass to the layout.
     * @throws \RuntimeException If the layout file does not exist.
     */
    protected function loadLayout(string $layoutPath, array $data = []): void
    {
        $layoutFile = __DIR__ . "/../Views/{$layoutPath}.php";

        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout file '{$layoutPath}' not found.");
        }

        extract($data, EXTR_SKIP);
        require $layoutFile;
    }

    /**
     * Render a view within a layout.
     *
     * @param string $view The view to render.
     * @param array $data Data to pass to the view.
     * @param string $layout The layout to use (default: 'layouts/site_layout').
     */
    protected function renderView(string $view, array $data = [], string $layout = 'layouts/site_layout'): void
    {
        try {
            $data['content'] = $this->loadView($view, $data);
            $this->loadLayout($layout, $data);
        } catch (\RuntimeException $e) {
            $this->logger->error("View/Layout Error: " . $e->getMessage());
            $this->sendJsonResponse(['error' => 'An error occurred while rendering the page.'], 500);
        }
    }

    /**
     * Send a JSON response.
     *
     * @param mixed $data The data to encode as JSON.
     * @param int $statusCode The HTTP status code (default: 200).
     */
    protected function sendJsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get request data (for POST/PUT requests).
     *
     * @return array The decoded JSON request data.
     */
    protected function getRequestData(): array
    {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }


    /**
     * Generate a URL-friendly slug from a name.
     *
     * @param string $name The input string.
     * @return string The generated slug.
     */
    protected function generateSlugFromName(string $name): string
    {
        $slug = preg_replace('/[^a-zA-Z0-9]+/', '-', $name);
        $slug = strtolower($slug);
        return trim($slug, '-');
    }

    /**
     * Check if the request is an AJAX request.
     *
     * @return bool True if the request is AJAX, false otherwise.
     */
    protected function isAjaxRequest(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Check if the user is logged in.
     *
     * @return bool True if the user is logged in, false otherwise.
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Start a session if not already started.
     */
    protected function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Log in a user and set session variables.
     *
     * @param array $user The user data (must include 'id', 'email', 'fullname', and 'role').
     */
    protected function loginUser(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
    }

    /**
     * Redirect to the dashboard if the user is already authenticated.
     */
    protected function redirectIfAuthenticated(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect(BASE_URL . 'dashboard');
        }
    }

    /**
     * Redirect to a specified URL.
     *
     * @param string $url The URL to redirect to.
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }


    protected function renderError(string $title, string $message): void
    {
        $this->renderView('errors/error', compact('title', 'message'));
    }
}
