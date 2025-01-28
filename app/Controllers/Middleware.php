<?php

class Middleware
{
    public static function authOnly()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function roleBasedAccess($allowedRoles)
    {
        self::authOnly();

        if (!in_array($_SESSION['user_role'], $allowedRoles)) {
            http_response_code(403);
            echo "403 - Forbidden: You do not have access to this page.";
            exit;
        }
    }
}
