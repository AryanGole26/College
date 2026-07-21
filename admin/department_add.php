<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: manage_departments.php"); exit(); }

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if (empty($name)) {
    $_SESSION['flash_error'] = "Department name is required.";
    header("Location: manage_departments.php");
    exit();
}

$stmt = $conn->prepare("INSERT INTO departments (name, description) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $description);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = "Department added successfully.";
} else {
    $_SESSION['flash_error'] = "A department with this name already exists.";
}
$stmt->close();

header("Location: manage_departments.php");
exit();
?>