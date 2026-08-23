<?php

session_start();

// Clear session data
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect to frontend login page
header("Location: ../Frontend/login.html");
exit;

?>