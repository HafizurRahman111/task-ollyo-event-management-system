<?php

namespace App\Helpers;

use App\Models\User;

class AuthHelper
{
    // Check if the user is logged in
    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    // Check if the logged-in user is an admin
    public function isAdmin(): bool
    {
        return $this->isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    // Check if the logged-in user is a regular user
    public function isUser(): bool
    {
        return $this->isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'user';
    }

    // Get the logged-in user's ID
    public function getUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    // Retrieve the User object for the logged-in user
    public function getUser(): ?User
    {
        if ($this->isLoggedIn()) {
            $userId = $this->getUserId();
            return User::find($userId); // Assuming User::find fetches a user by ID
        }
        return null;
    }

    // Ensure that the user is logged in, otherwise redirect to the login page
    public function requireLogin(string $redirectUrl = '/login'): void
    {
        if (!$this->isLoggedIn()) {
            header("Location: $redirectUrl");
            exit;
        }
    }

    // Ensure that the logged-in user is an admin, otherwise redirect to the homepage
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
