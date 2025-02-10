<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    echo json_encode(["status" => "unauthorized"]);
    exit();
} else {
    echo json_encode(["status" => "authorized"]);
}
?>
