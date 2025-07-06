<?php
session_start();
$msg = '';


//Simple password authentication for demonstration purposes 
//This should be replaced with a proper database connection and user validation in production
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
   $user = $_POST['username'];
   $password = $_POST['password'];

   //For now, we are using hardcoded values for demonstration purposes
   if ($user === 'admin' && $password === 'test123') {
      $msg = "You have entered correct username and password";
      $_SESSION['user_id'] = 1; // replace this with actual user ID from DB later
      $_SESSION['username'] = $user;
      header('Location: index.php');
      exit();
   } else {
      $msg = "You have entered wrong Password";
   }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Gym Management Login</title>
</head>

<body class="font-[Montserrat] m-0 p-0">

   <div class="flex w-full h-full overflow-hidden m-0 flex">


        <div class="flex-1 relative">
            <img src="assets/img/gym-bg.jpg" alt="Gym Background" class="w-full h-full object-cover" />
            <div class="absolute top-1/5 left-10 text-white">
                <h1 class="mb-5 text-6xl font-bold">MANAGE</h1>
                <h1 class="mb-5 text-6xl font-bold">MOTIVATE</h1>
                <h1 class="mb-5 text-6xl font-bold">MAINTAIN</h1>
            </div>
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

                <div class="text-center mb-5">
                    <a href="#" class="text-gray-400 text-xs no-underline">Forgot Password?</a>
                </div>

                <button type="submit" name="login"
                    class="w-full p-2 bg-purple-800 text-white border-none rounded font-bold cursor-pointer">
                    SIGN IN
                </button>

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
