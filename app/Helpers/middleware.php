<?php

function middleware($role = null)
{
    require_once 'auth.php';

    if (!isLoggedIn()) {
        header('Location: /login');
        exit;
    }

    if ($role && $_SESSION['role'] !== $role) {
        http_response_code(403);
        echo "403 - Forbidden: You don't have access to this resource.";
        exit;
    }
}
