<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: manage_patients.php"); exit(); }

$user_id = (int)($_POST['user_id'] ?? 0);
$stmt = $conn->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ? AND role = 'PATIENT'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = "Patient status updated.";
header("Location: manage_patients.php");
exit();
?>