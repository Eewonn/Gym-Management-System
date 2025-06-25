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
   <link rel="stylesheet" href="./assets/css/loginstyles.css">
    <title>Gym Management Login</title>
</head>

<body>
    

    <!-- Front End -->
    <div class="login-container">

         <div class="left-panel">
            <img src="assets/img/gym-bg.jpg" alt="Gym Background" class="background-image" />
            <div class="overlay-text">
               <h1>MANAGE</h1>
               <h1>MOTIVATE</h1>
               <h1>MAINTAIN</h1>
            </div>
        </div>

        <div class="right-panel">
            <div class="logo">
               <div class="logo-box">
                  <img src="assets/img/logo.jpg" alt="Gym Logo" />
               </div>
            </div>

            <div>
                  <div class="welcome-box">
                    <h2>WELCOME</h2>
                    <h2>ADMIN</h2>
                  </div>
               <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" autocomplete="off">
                  <div class="input-group">
                     <input type="text" name="username" placeholder="Email" required autocomplete="off">
                  </div>
                  <div class="input-group">
                     <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
                  </div>
                  <div class="forgot-password">
                     <a href="#">Forgot Password?</a>
                  </div>
                  <button type="submit" name="login" class="sign-in-btn">SIGN IN</button>
               </form>


               <div class="datetime">
                  <?php
                     echo date('l jS \of F Y');
                  ?>
               </div>   
            </div>

            </div>
        </div>


        
    </div>
</body>

</html>