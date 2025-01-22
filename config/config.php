<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
// $host = $_SERVER['HTTP_HOST'];
// $base_path = '/your_project_folder/'; 

// define('BASE_URL', $protocol . $host . $base_path);
define('BASE_URL', 'http://localhost/task-ollyo-event-management-system/');

?>