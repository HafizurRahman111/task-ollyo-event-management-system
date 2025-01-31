<!-- navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm fixed-top">
    <div class="container-fluid">
        <!-- Brand/Logo -->
        <a class="navbar-brand" href="<?= BASE_URL; ?>">Dashboard</a>

        <!-- Hamburger Menu for Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- User Avatar (Optional) -->
                        <span class="me-2">
                            <i class="fas fa-user-circle fa-lg"></i> <!-- Font Awesome User Icon -->
                        </span>
                        <!-- User Name -->
                        <span class="text-truncate" style="max-width: 150px;">
                            <?= htmlspecialchars($userFullName ?? 'Guest', ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </a>
                    <!-- Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?= BASE_URL; ?>profile"><i
                                    class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL; ?>settings"><i
                                    class="fas fa-cog me-2"></i>Settings</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?= BASE_URL; ?>auth/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>