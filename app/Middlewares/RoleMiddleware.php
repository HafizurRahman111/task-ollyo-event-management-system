<?php

namespace App\Middlewares;

use App\Helpers\AuthHelper;

class RoleMiddleware
{
    private $roles;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function handle($request, $next)
    {
        $authHelper = new AuthHelper();

        if (!$authHelper->isLoggedIn()) {
            header("Location: /ems/login");
            exit;
        }

        if (in_array($this->getUserRole(), $this->roles)) {
            return $next();
        }

        header("Location: " . BASE_URL . "login");
        exit;
    }


    private function getUserRole()
    {
        return $_SESSION['user_role'] ?? null;
    }
}
