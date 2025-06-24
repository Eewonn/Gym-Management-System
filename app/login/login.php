<html lang = "en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../../assets/styles.css">
   <title>Login</title>
</head>
<body>
   <?php
      $msg = '';
      $users = ['user'=>"test"];

      if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
         $user = $_POST['username'];
         $password = $_POST['password'];

         if ($password === $users['user']) {
            $msg = "You have entered correct username and password";
         } else {
            $msg = "You have entered wrong Password";
         }
      }
   ?>

   <!-- Front End -->
   <h2>Enter Username and Password</h2> 

   <br/><br/>
   <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
      <div>
         <label for="username">Username: </label>
         <input type="text" name="username" id="name">
      </div>
      <div>
         <label for="password">Password: </label>
         <input type="password" name="password" id="password">
      </div>
      <section>
         <button type="submit" name="login">Login</button>
      </section>
   </form>  
   
   <!-- Print "entered password correctly or  not" -->
   <h3><?php echo $msg; ?></h3>  

   <!-- <p> 
      <a href = "logout.php" tite = "Logout">Click here to clean Session.</a>
   </p> -->
</body>
</html>