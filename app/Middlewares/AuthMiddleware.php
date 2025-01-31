<?php

namespace App\Middlewares;

use App\Helpers\AuthHelper;

class AuthMiddleware
{
    public function handle($request, $next)
    {
        $authHelper = new AuthHelper();

        if (!$authHelper->isLoggedIn()) {
            header("Location: /ems/login");
            exit;
        }

        return $next($request);
    }
}