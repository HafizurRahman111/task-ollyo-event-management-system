<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- Bootstrap CDN for faster loading -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">

    <!-- Dynamic CSS for specific page -->
    <link rel="stylesheet" href="<?= BASE_URL; ?>public/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Global Styles */
        body {
            background-color: #f0f2f5;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar Styling */
        .navbar {
            background-color:rgb(24, 48, 92) !important;
            color: white;
            padding: 10px 20px;
            height: 50px;
            border-bottom: 1px solid #ddd;
        }

        .navbar .navbar-brand {
            color: white;
            font-weight: bold;
            font-size: 1.3rem;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: 20px;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #343a40;
            padding-top: 20px;
            box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .sidebar .user-info {
            color: white;
            padding: 20px;
            background-color: #444;
            text-align: center;
            border-bottom: 1px solid #666;
        }

        .sidebar .user-info p {
            margin: 0;
            font-size: 1rem;
        }

        .sidebar .user-info .user-name {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .sidebar .nav-link {
            font-size: 1.1rem;
            color: white;
            padding: 10px 20px;
            border-bottom: 1px solid #555;
        }

        .sidebar .nav-link.active {
            background-color: #0062cc;
            color: #fff;
            font-weight: bold;
        }

        /* Main Content Area */
        .main-content {
            margin-top: 60px;
            /* Adjust for navbar height */
            margin-left: 250px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            flex-grow: 1;
            min-height: calc(100vh - 100px);
            /* Adjust for navbar and footer */
            overflow: auto;
        }

        /* Footer Styling */
        .footer {
            background-color: #222;
            color: white;
            text-align: center;
            width: 100%;
            height: 40px;
            margin-top: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                width: 100%;
                height: 100%;
                left: -250px;
                top: 0;
                background-color: #343a40;
                z-index: 999;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
                padding: 10px;
            }

            .navbar {
                padding: 10px;
                height: 40px;
            }
        }

        /* Optional: Animated Hamburger Icon */
        .navbar-toggler-icon {
            background-color: white;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <main class="container-fluid d-flex flex-column">
        <!-- Sidebar for large screens -->
        <aside class="sidebar">
            <div class="user-info">
                <p class="user-name"><?= isset($userFullName) ? $userFullName : 'User Name'; ?></p>
                <p><?= isset($userEmail) ? $userEmail : 'user@example.com'; ?></p>
                <p class="user-role"><?= isset($userRole) ? $userRole : 'User Type'; ?></p>
            </div>
            <?php include 'sidebar.php'; ?>
        </aside>

        <!-- Content Wrapper with main content area -->
        <div class="content-wrapper">
            <div class="main-content">
                <?php echo isset($content) ? $content : ''; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <?php include 'footer.php'; ?>
    </footer>

    <!-- JS & Dynamic Script Loading -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="<?= BASE_URL; ?>public/assets/js/dashboard.js"></script>

    <!-- Add page-specific JavaScript if needed -->
    <?php if (isset($pageSpecificJs)): ?>
        <script src="<?= BASE_URL . $pageSpecificJs; ?>"></script>
    <?php endif; ?>
</body>

</html>