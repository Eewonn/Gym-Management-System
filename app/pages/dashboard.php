<?php

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$name = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Gym Management Dashboard</title>
</head>
<body>
    <h1 class="text-4xl font-bold">Dashboard</h1>
    <p>Welcome Back, <?php echo htmlspecialchars($name)?>!</p>

    <?php include 'app/includes/card.php'; ?>

    <?php include 'app/includes/membershipgrowth.php'; ?>

    

</body>
</html>
