<?php

session_start();

// Define the root directory to use for includes and set the base URL
define('BASE_URL', '/ems/');  // Update this to match your actual base URL
define('ROOT_PATH', dirname(__DIR__) . '/ems');

// Adjust security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; img-src 'self' data:;");
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: geolocation=(), microphone=()'); // Customize as needed

// Autoload Controllers and other necessary classes
spl_autoload_register(function ($className) {
    $classPath = ROOT_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($classPath)) {
        require_once $classPath;
    } else {
        // Handle file not found
        http_response_code(500);
        echo "500 - Internal Server Error";
        exit;
    }
});

// Include the database connection file
require_once ROOT_PATH . '/config/database.php';

// Initialize the logger
require_once ROOT_PATH . '/app/Helpers/Logger.php';
use App\Helpers\Logger;

$logger = new Logger(__DIR__ . '/logs/debug.log');

// Initialize the PDO database connection
try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
} catch (PDOException $e) {
    // Handle database connection error
    $logger->log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    echo "500 - Internal Server Error";
    exit;
}

// Include the routes file located in the /config folder
$routesFile = ROOT_PATH . '/config/routes.php';
if (file_exists($routesFile)) {
    $routes = include $routesFile;
} else {
    $logger->log("Routes configuration file not found.");
    http_response_code(500);
    echo "500 - Internal Server Error";
    exit;
}

// Get the requested URI and sanitize it
$requestUri = filter_var(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), FILTER_SANITIZE_URL);
$requestUri = rtrim(str_replace(BASE_URL, '', $requestUri), '/');  // Remove the base URL and trailing slash

// Log the requested URL
$logger->log("Requested URL: " . $requestUri);

$routeFound = false;

// Function to match route with dynamic parameters
function matchRoute($routeUri, $requestUri)
{
    // Split the URI into parts
    $routeParts = explode('/', $routeUri);
    $requestParts = explode('/', $requestUri);

    // If the number of parts don't match, it's not a match
    if (count($routeParts) != count($requestParts)) {
        return false;
    }

    // Check each part
    foreach ($routeParts as $index => $part) {
        // If part is a placeholder (like {id}), skip matching
        if (preg_match('/^{.*}$/', $part)) {
            continue;
        }

        if ($part !== $requestParts[$index]) {
            return false;
        }
    }

    return true;
}

// Loop through all routes to find a match
foreach ($routes['/ems'] as $routeUri => $route) {
    if (matchRoute($routeUri, $requestUri)) {
        $routeFound = true;
        $controllerName = $route['controller'];
        $action = $route['action'];

        // Check if the controller class exists
        if (class_exists($controllerName)) {
            // Pass the PDO instance when creating the controller
            $controller = new $controllerName($pdo);

            // Check if the controller method exists
            if (method_exists($controller, $action)) {
                // Call the controller's action method
                call_user_func([$controller, $action]);
            } else {
                // 404 - Method Not Found
                $logger->log("404 Method Not Found - Requested URL: " . $_SERVER['REQUEST_URI']);
                http_response_code(404);
                echo "404 - Method Not Found";
            }
        } else {
            // 404 - Controller Not Found
            $logger->log("404 Controller Not Found - Requested URL: " . $_SERVER['REQUEST_URI']);
            http_response_code(404);
            echo "404 - Controller Not Found";
        }

        break;
    }
}

// If no route found, display a 404 error page
if (!$routeFound) {
    $logger->log("404 Not Found - Requested URL: " . $_SERVER['REQUEST_URI']);
    http_response_code(404);
    echo "404 - Page Not Found";
}
