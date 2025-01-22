<?php
require_once 'config/dbConnection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
        $_SESSION['login_error'] = 'Invalid email or password.';
        header('Location: ../public/login.php');
        exit();
    }

    try {
        $pdo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: ../pages/dashboard/dashboard.php');
            exit();
        } else {
            $_SESSION['login_error'] = 'Incorrect email or password.';
            header('Location: ../login.php');
            exit();
        }
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        $_SESSION['login_error'] = 'An error occurred. Please try again later.';
        header('Location: ../login.php');
        exit();
    }
} else {
    header('Location: ../login.php');
    exit();
}
