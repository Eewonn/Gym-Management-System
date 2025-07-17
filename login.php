<?php
session_start();

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$msg = '';

// If user is already logged in, redirect to index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Include DB connection
require_once __DIR__ . '/db/db.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // CSRF token check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $msg = "Invalid request. Please try again.";
    } else {
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Rate limiting check
        $ip = $_SERVER['REMOTE_ADDR'];
        $current_time = time();

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }

        // Filter old attempts beyond 15 minutes
        $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($timestamp) use ($current_time) {
            return ($current_time - $timestamp) < 900; // 15 minutes = 900 seconds
        });

        if (count($_SESSION['login_attempts']) >= 5) {
            $msg = "Too many login attempts. Please try again later.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $account = $stmt->fetch();

            if ($account && password_verify($password, $account['password'])) {
                // Success: clear attempts and regenerate session ID
                unset($_SESSION['login_attempts']);
                session_regenerate_id(true);

                $_SESSION['user_id'] = $account['user_id'];
                $_SESSION['username'] = $account['username'];

                header('Location: index.php');
                exit();
            } else {
                // Failed login
                $_SESSION['login_attempts'][] = $current_time;
                $msg = "Invalid username or password.";
            }
        }
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <link rel="icon" type="image/png" href="./assets/img/logo.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
    <title>Gym Management Login</title>
</head>

<body class="font-[Montserrat] m-0 p-0">

   <div class="flex w-full h-full overflow-hidden m-0 flex">


        <div class="flex-1 relative flex justify-center items-center">
            <img src="assets/img/gym-bg.jpg" alt="Gym Background" class="absolute inset-0 w-full h-full object-cover" />
            <div class="relative z-10 flex flex-col justify-center items-left text-white">
            <h1 class="mb-5 text-6xl font-bold">MANAGE</h1>
            <h1 class="mb-5 text-6xl font-bold">MOTIVATE</h1>
            <h1 class="mb-5 text-6xl font-bold">MAINTAIN</h1>
            </div>
            <div class="absolute inset-0 bg-black opacity-40"></div>
        </div>

        <!-- Right Panel -->
        <div class="flex-1 bg-[#2A2A2A] text-white p-10 flex flex-col justify-center items-center">

            <div class="flex justify-center items-center mb-5">
                <div class="bg-white w-24 h-24 flex justify-center items-center rounded overflow-hidden shadow">
                    <img src="assets/img/logo.jpg" alt="Gym Logo" class="object-contain max-w-full max-h-full" />
                </div>
            </div>

            <div class="text-center text-2xl mb-12 leading-tight w-full">
                <h2>WELCOME</h2>
                <h2>ADMIN</h2>
            </div>

            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off" class="w-full max-w-[300px] mt-5">

                <div class="mb-4">
                    <input type="text" name="username" placeholder="Email" required autocomplete="off"
                        class="w-full p-4 bg-[#030303] border-none rounded text-[14px] text-[#FAFCFCBA] placeholder-[#FAFCFCBA] focus:outline-none focus:bg-[#030303] focus:text-[#FAFCFCBA]">
                </div>

                <div class="mb-4">
                    <input type="password" name="password" placeholder="Password" required autocomplete="new-password"
                        class="w-full p-4 bg-[#030303] border-none rounded text-[14px] text-[#FAFCFCBA] placeholder-[#FAFCFCBA] focus:outline-none focus:bg-[#030303] focus:text-[#FAFCFCBA]">
                </div>
                
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <button type="submit" name="login"
                    class="w-full p-2 bg-purple-800 text-white border-none rounded font-bold cursor-pointer">
                    SIGN IN
                </button>
                <div class="mt-4 text-center">
                    <a href="register.php" class="w-full inline-block p-2 bg-gray-700 text-white border-none rounded font-bold cursor-pointer hover:bg-gray-800">
                        CREATE ACCOUNT
                    </a>
                </div>
                

            </form>

            <div id="livedate" class="absolute bottom-5 right-10 text-xs text-white mt-0"></div>
        </div>

    </div>

    <script>
        function updateDateTime() {
            fetch('./app/includes/date.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('livedate').innerHTML = data;
                });
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);
    </script>

</body>

</html>
