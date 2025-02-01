<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= htmlspecialchars($title ?? 'EMS Dashboard', ENT_QUOTES, 'UTF-8'); ?></title>

    <!-- css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f4f7fc;
            font-family: 'Roboto', sans-serif;
            margin: 0;
        }

        .navbar {
            background: linear-gradient(135deg, #18305c, #343a40);
            color: white;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            height: 60px;
            width: 100%;
            z-index: 1000;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.4rem;
        }

        .menu-toggle {
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            position: fixed;
            top: 60px;
            left: 0;
            width: 240px;
            height: calc(100vh - 60px);
            background: linear-gradient(188deg, #162a3f, #111e35);
            color: white;
            transition: width 0.3s ease;
            overflow-y: auto;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar.expanded {
            width: 60px;
        }

        .sidebar .nav-link {
            padding: 12px 20px;
            color: white;
            display: flex;
            align-items: center;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
        }

        .sidebar.expanded .nav-link i {
            margin-right: 0;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        .sidebar .nav-link.active {
            background-color: rgba(46, 130, 219, 0.31);
            color: #007bff !important;
            font-weight: bold;
            border-radius: 5px;
        }

        .sidebar .nav-text {
            display: inline;
        }

        .sidebar.expanded .nav-text {
            display: none;
        }

        .nav-link.active i {
            color: #007bff !important;

        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
            transition: margin-left 0.3s ease;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 70px;
        }

        .sidebar.expanded+.main-content {
            margin-left: 80px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            /* / transform: translateY(-5px); */
        }

        .footer {
            height: 40px;
            background: rgb(219, 218, 218);
            text-align: center;
            padding: 10px;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            margin-top: auto;
        }

        .role-section {
            display: flex;
            align-items: center;
            color: white;
        }

        .role-section i {
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
                transform: translateX(-250px);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>

    <!-- page-specific css -->
    <?php if (!empty($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($style, ENT_QUOTES, 'UTF-8'); ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>

<body>
    <?php
    $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
    $userFullName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
    $currentPage = basename($_SERVER['REQUEST_URI']);
    ?>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <span class="navbar-brand text-white">
                <a class="nav-link" href="<?php echo BASE_URL . 'dashboard'; ?>">
                    EMS Dashboard
                </a>
            </span>
            <span class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></span>

            <div class="dropdown ms-auto">
                <span class="user-dropdown dropdown-toggle text-white d-flex align-items-center" id="userDropdown"
                    data-bs-toggle="dropdown">
                    <?= htmlspecialchars($userFullName, ENT_QUOTES, 'UTF-8'); ?>
                    <img src="<?= htmlspecialchars($userAvatar ?? BASE_URL . 'public/assets/images/user-sample.jpg', ENT_QUOTES, 'UTF-8'); ?>"
                        alt="User Avatar" class="rounded-circle ms-2" width="35" height="35">
                </span>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL . 'profile'; ?>">Profile</a></li>
                    <!-- <li><a class="dropdown-item" href="#">Settings</a></li> -->
                    <li><a class="dropdown-item" href="<?php echo BASE_URL . 'logout'; ?>">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'profile' ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL . 'profile'; ?>"> <i class="fas fa-user-circle"></i>
                    <span class="nav-text">
                        <?= $userRole; ?>
                    </span>
                </a>
            </li>
            <hr />
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL . 'dashboard'; ?>"><i class="fas fa-home"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <?php if ($userRole === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $currentPage === 'users' ? 'active' : ''; ?>"
                        href="<?php echo BASE_URL . 'users'; ?>"><i class="fas fa-users"></i> <span
                            class="nav-text">Users</span>
                    </a>
                </li>
                <hr />
            <?php else: ?>

            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'events' ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL . 'events'; ?>"><i class="fas fa-calendar-alt"></i> <span
                        class="nav-text">Events</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo $currentPage === 'registration' ? 'active' : ''; ?>"
                    href="<?php echo BASE_URL . 'events/registration'; ?>"><i class="fa fa-registered"></i> <span
                        class="nav-text">Attendee Registration</span>
                </a>
            </li>
        </ul>
    </aside>

    <div class="main-content">
        <h2><?= htmlspecialchars($title ?? 'Dashboard', ENT_QUOTES, 'UTF-8'); ?></h2>
        <div class="card">
            <div class="card-body">
                <?= $content ?? 'Main content...'; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> <a href="<?php echo BASE_URL; ?>">EMS</a>. All rights reserved. </p>
    </footer>

    <!-- js -->
    <script>
        document.getElementById('menuToggle').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('expanded');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- page-specific js -->
    <?php if (!empty($scripts)): ?>
        <?php foreach ($scripts as $script): ?>
            <script src="<?= htmlspecialchars($script, ENT_QUOTES, 'UTF-8'); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>