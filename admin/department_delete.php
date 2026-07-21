<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: manage_departments.php"); exit(); }

$department_id = (int)($_POST['department_id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
$stmt->bind_param("i", $department_id);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = "Department deleted.";
} else {
    $_SESSION['flash_error'] = "Cannot delete — doctors are still assigned to this department.";
}
$stmt->close();

header("Location: manage_departments.php");
exit();
?>