<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "unauthorized"]);
    exit;
}

// Simple session timeout (30 minutes)
if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > 1800)) {
    session_unset();
    session_destroy();
    echo json_encode(["status" => "unauthorized"]);
    exit;
}

// Update last activity time
$_SESSION["last_activity"] = time();
echo json_encode(["status" => "authorized"]);
?>
