<?php

use App\Utils\Logger;

session_start();


// Define constants
define('BASE_URL', '/ems/');
define('ROOT_PATH', dirname(__DIR__) . '/ems');

// Adjust security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; font-src 'self' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; img-src 'self' data:;");
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: geolocation=(), microphone=()');

// Autoload Controllers and other necessary classes
spl_autoload_register(function ($className) {
    $classPath = ROOT_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($classPath)) {
        require_once $classPath;
    } else {
        http_response_code(500);
        echo "500 - Internal Server Error";
        exit;
    }
});

require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/Helpers/Logger.php';

$logger = new Logger(ROOT_PATH . '/logs/debug.log');

try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();
} catch (PDOException $e) {
    $logger->error("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    echo "500 - Internal Server Error";
    exit;
}

// Include the routes file
$routesFile = ROOT_PATH . '/config/routes.php';
if (file_exists($routesFile)) {
    $routes = include $routesFile;
} else {
    $logger->error("Routes configuration file not found.");
    http_response_code(500);
    echo "500 - Internal Server Error";
    exit;
}

$requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$requestUri = str_replace(BASE_URL, '', $requestUri);

// $logger->info("Requested URL: " . $requestUri);

// Match route with dynamic parameters
function matchRoute($routePattern, $requestUri)
{
    $routeParts = explode('/', trim($routePattern, '/'));
    $requestParts = explode('/', trim($requestUri, '/'));

    if (count($routeParts) !== count($requestParts)) {
        return false;
    }

    $params = [];
    foreach ($routeParts as $index => $part) {
        if (preg_match('/^{(.*)}$/', $part, $matches)) {
            $params[$matches[1]] = $requestParts[$index];
        } elseif ($part !== $requestParts[$index]) {
            return false;
        }
    }
    return $params;
}

// Route Matching & Execution
$routeFound = false;
foreach ($routes as $route) {
    $params = matchRoute($route['path'], $requestUri);
    if ($params !== false) {
        $routeFound = true;
        $controllerName = $route['controller'];
        $action = $route['action'];

        if (class_exists($controllerName) && method_exists($controllerName, $action)) {
            $controller = new $controllerName($pdo);

            // Execute Middleware Chain
            $middlewares = $route['middlewares'] ?? [];
            $next = function () use ($controller, $action, $params) {
                call_user_func_array([$controller, $action], $params);
            };

            foreach (array_reverse($middlewares) as $middleware) {
                $middlewareInstance = is_string($middleware) ? new $middleware : $middleware;
                $next = function ($request) use ($middlewareInstance, $next) {
                    return $middlewareInstance->handle($request, $next);
                };
            }

            // Execute the final action
            $next(null);
        } else {
            $logger->error("404 - Controller/Method Not Found: $controllerName@$action");
            http_response_code(404);
            exit("404 - Not Found");
        }

        break;
    }
}

// Handle unmatched routes
if (!$routeFound) {
    $logger->error("404 - Page Not Found: $requestUri");
    http_response_code(404);
    exit("404 - Page Not Found");
}
