<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/dashboardstyles.css">
    <link rel="icon" type="image/png" href="./assets/img/logo.jpg">
    <link href="./assets/css/output.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Gym Management System</title>
</head>
<body>
    <div class="container">
        <!-- Left Panel: Sidebar -->
        <?php include 'app/includes/sidebar.php'; ?>

        <!-- Right Panel: Content -->
        <div class="right-panel-content">
            <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

                $allowed_pages = ['dashboard', 'members', 'payments', 'staff_attendance', 'training_schedule'];

                if (in_array($page, $allowed_pages)) {
                    include 'app/pages/' . $page . '.php';
                } else {
                    echo "<h2>Page not found</h2>";
                }
            ?>
        </div>
    </div>
</body>
</html>
