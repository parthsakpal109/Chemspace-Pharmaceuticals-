<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.html");
    exit;
}

// Simple session timeout (30 minutes)
if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: admin_login.html");
    exit;
}

// Update last activity time
$_SESSION["last_activity"] = time();
?>
