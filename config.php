<?php
session_start();

$host = "localhost";
$user = "root";       // Sesuaikan dengan user database hosting
$pass = "";           // Sesuaikan dengan pass database hosting
$db   = "spx_logbook";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["status" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

$conn->set_charset("utf8mb4");
?>