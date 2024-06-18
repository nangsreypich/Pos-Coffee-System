<?php
session_start();

// Clear the session
session_unset();
session_destroy();

// Clear the cookies
setcookie("username", "", time() - 3600, "/");
setcookie("user_type", "", time() - 3600, "/");

// Redirect to login page
header("Location: ../view/index1.php");
exit();
?>
