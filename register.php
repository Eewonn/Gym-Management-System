<?php
session_start();
$msg = '';

require_once __DIR__ . '/db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $msg = "Username already exists!";
    } else {
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $password]);

        // Redirect to login
        header('Location: login.php?registered=true');
        exit();
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Gym Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="./assets/css/output.css" rel="stylesheet">
</head>

<body class="font-[Montserrat] bg-[#2A2A2A] flex justify-center items-center min-h-screen text-white">
    <div class="bg-[#1e1e1e] p-10 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6">Create an Account</h2>

        <?php if ($msg): ?>
            <div class="mb-4 p-3 bg-red-700 text-white text-sm rounded text-center font-medium font-bold">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" autocomplete="off">
            <div class="mb-4">
                <input type="text" name="username" placeholder="Email" required
                    class="w-full p-3 rounded bg-[#030303] text-[#FAFCFCBA] placeholder-[#FAFCFCBA] focus:outline-none">
            </div>
            <div class="mb-6">
                <input type="password" name="password" placeholder="Password" required
                    class="w-full p-3 rounded bg-[#030303] text-[#FAFCFCBA] placeholder-[#FAFCFCBA] focus:outline-none">
            </div>
            <button type="submit" name="register"
                class="w-full bg-[#800080] p-3 rounded text-white font-bold hover:bg-purple-900">
                REGISTER
            </button>
            <div class="text-center mt-4">
                <a href="login.php" class="text-gray-400 text-sm hover:underline">Already have an account? Login</a>
            </div>
        </form>
    </div>
</body>

</html>
