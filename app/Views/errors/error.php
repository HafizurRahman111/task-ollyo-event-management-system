<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - <?= $title ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/error.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .error-container {
            background: #ffffff;
            padding: 40px 60px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-size: 36px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #333;
            margin-bottom: 30px;
        }

        .btn {
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .error-icon {
            font-size: 50px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-size: 14px;
            color: #007bff;
            text-decoration: none;
            text-align: center;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1><?= $title ?></h1>
        <p><?= $message ?></p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE_URL ?>dashboard" class="btn btn-primary">Go to Dashboard</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>" class="btn btn-secondary">Go Back to Home</a>
        <?php endif; ?>

        <div class="back-link">
            <a href="<?= BASE_URL ?>">Return to Homepage</a>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>

</html>