<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Ensure any session cookies are deleted
setcookie(session_name(), '', time() - 3600);

// Redirect to the login page
header("Location: ../../../index.html");
exit;
?>
