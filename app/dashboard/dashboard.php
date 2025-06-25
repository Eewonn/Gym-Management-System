<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management Dashboard</title>
</head>
<body>
    <h1>Gym Management Dashboard</h1>
    
    <h2>Navigation</h2>
    <ul>
        <li><a href="../members/members.php">Members</a></li>
        <li><a href="../payments/payments.php">Payments</a></li>
        <li><a href="../reports/reports.php">Reports</a></li>
        <li><a href="../staff_attendance/staff_attendance.php">Staff Attendance</a></li>
        <li><a href="../training_schedule/training_schedule.php">Training Schedule</a></li>
    </ul>

    <form action="../login/logout.php" method="post">
        <button type="submit">Logout</button>
    </form>
</body>
</html>