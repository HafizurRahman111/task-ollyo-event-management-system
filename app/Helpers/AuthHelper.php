<?php

namespace App\Helpers;

use App\Models\User;

class AuthHelper
{
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(): bool
    {
        return $this->isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function isUser(): bool
    {
        return $this->isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'user';
    }

    public function getUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    public function getUser(): ?User
    {
        if ($this->isLoggedIn()) {
            $userId = $this->getUserId();
            return User::find($userId);
        }
        return null;
    }

    public function requireLogin(string $redirectUrl = '/login'): void
    {
        if (!$this->isLoggedIn()) {
            header("Location: $redirectUrl");
            exit;
        }
    }

    // Redirect if the user is not an admin
    public function requireAdmin(string $redirectUrl = '/'): void
    {
        if (!$this->isAdmin()) {
            header("Location: $redirectUrl");
            exit;
        }
    }

    // Helper to regenerate session ID to prevent session fixation attacks
    public function regenerateSession(): void
    {
        session_regenerate_id(true); // Regenerates the session ID
    }
}
