<?php
// config/db.php
// Central database connection file — required by every page that needs data.

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";          // default XAMPP MySQL password is empty
$DB_NAME = "hospital_appointment_db";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Ensure proper character encoding
$conn->set_charset("utf8mb4");

// Start session on every page that includes this file (needed for login state)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>