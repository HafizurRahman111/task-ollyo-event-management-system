<!-- sidebar.php -->
<div class="position-sticky pt-3">
    <div class="sidebar-header">

        <button class="btn btn-sm btn-light d-md-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebar-nav" aria-controls="sidebar-nav" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="fas fa-bars"></span>
        </button>
    </div>
    <ul class="nav flex-column ms-auto" id="sidebar-nav">
        <li class="nav-item">
            <a class="nav-link <?php echo isset($active) && $active == 'dashboard' ? 'active' : ''; ?>"
                href="<?= BASE_URL; ?>dashboard">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo isset($active) && in_array($active, ['users', 'roles', 'permissions']) ? 'active' : ''; ?>"
                href="#" id="submenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-users"></i> Users & Roles
            </a>
            <ul class="dropdown-menu" aria-labelledby="submenuDropdown">
                <li><a class="dropdown-item" href="<?= BASE_URL; ?>users">Manage Users</a></li>
                <li><a class="dropdown-item" href="#">Manage Roles</a></li>
                <li><a class="dropdown-item" href="#">Manage Permissions</a></li>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isset($active) && $active == 'events' ? 'active' : ''; ?>"
                href="<?= BASE_URL; ?>events">
                <i class="fa-solid fa-calendar"></i> Events
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isset($active) && $active == 'settings' ? 'active' : ''; ?>"
                href="<?= BASE_URL; ?>settings">
                <i class="fa-solid fa-gear"></i> Settings
            </a>
        </li>
    </ul>
</div>