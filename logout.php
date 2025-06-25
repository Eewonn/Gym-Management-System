<?php
session_start();

// Unset all session variables
session_unset();
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>