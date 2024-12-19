<?php
session_start(); // Start the session to manage session variables
// session_unset(); // Unset all session variables
session_destroy(); // Destroy the session
header("Location: index.php"); // Redirect to index.php
exit(); // Ensure no further code is executed
?>