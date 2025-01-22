<?php

require_once 'config/config.php';

$pageTitle = 'Login';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

require_once 'pages/home/header.php';

$loginError = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>


<main class="container main-content">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Login</h2>
                    <p class="card-text text-center mb-4">Please enter your credentials to login.</p>

                    <!-- Show login error (if any) -->
                    <?php if (isset($_SESSION['login_error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['login_error']); ?>
                        </div>
                        <?php unset($_SESSION['login_error']); ?>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form method="POST" action="actions/login.php" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email"
                                required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password" required>
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once 'pages/home/footer.php';
?>