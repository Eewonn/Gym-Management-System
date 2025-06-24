<?php
session_start();

// Check if the user is logged in and is user_id 1 (admin)
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1) {
    // User is logged in, redirect to dashboard
    header('Location: app/dashboard/dashboard.php');
    exit();
} else {
    // User is not logged in, redirect to login page
    header('Location: app/login/login.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
</head>
<body>
    
</body>
</html>