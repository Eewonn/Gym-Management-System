<?php
session_start();
$msg = '';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/db/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $user = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$user, $password]);
    $account = $stmt->fetch();

    if ($account) {
        $_SESSION['user_id'] = $account['user_id'];
        $_SESSION['username'] = $account['username'];
        header('Location: index.php');
        exit();
    } else {
        $msg = "Invalid username or password";
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="./assets/img/logo.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
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
