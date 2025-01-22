<?php

$pageTitle = isset($_GET['pageTitle']) ? htmlspecialchars($_GET['pageTitle']) : 'Event Management';
$currentAction = $_GET['action'] ?? 'home';

function isActive($action, $currentAction)
{
    return $action === $currentAction ? 'active' : '';
}

require_once __DIR__ . '.../config/config.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" rel="stylesheet" />
    <link href="assets/css/home.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <i class="fa-solid fa-calendar-alt"></i> Event Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('home', $currentAction); ?>" href="<?= BASE_URL ?>">
                            <i class="fa-solid fa-home me-2"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('login', $currentAction); ?>" href="<?= BASE_URL ?>login.php">
                            <i class="fa-solid fa-sign-in-alt me-2"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('register', $currentAction); ?>"
                            href="<?= BASE_URL ?>register.php">
                            <i class="fa-solid fa-user-plus me-2"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>