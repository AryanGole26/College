<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: manage_doctors.php"); exit(); }

$errors = [];
$user_id = (int)($_POST['user_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$department_id = (int)($_POST['department_id'] ?? 0);
$qualification = trim($_POST['qualification'] ?? '');
$experience = (int)($_POST['experience'] ?? 0);
$fee = (float)($_POST['fee'] ?? 0);
$contact_number = trim($_POST['contact_number'] ?? '');
$available_days = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : '';
$available_time_slots = trim($_POST['available_time_slots'] ?? '');
$new_password = $_POST['new_password'] ?? '';

if (empty($name)) $errors[] = "Name is required.";
if ($department_id <= 0) $errors[] = "Please select a department.";
if (empty($qualification)) $errors[] = "Qualification is required.";
if (empty($available_days)) $errors[] = "Select at least one available day.";
if (empty($available_time_slots)) $errors[] = "Available time slots are required.";
if (!empty($new_password) && strlen($new_password) < 6) $errors[] = "New password must be at least 6 characters.";

if (!empty($errors)) {
    $_SESSION['doctor_errors'] = $errors;
    header("Location: edit_doctor.php?id=$user_id");
    exit();
}

$stmt = $conn->prepare("UPDATE doctors SET department_id=?, name=?, qualification=?, experience=?, fee=?, available_days=?, available_time_slots=?, contact_number=? WHERE user_id=?");
$stmt->bind_param("issidsssi", $department_id, $name, $qualification, $experience, $fee, $available_days, $available_time_slots, $contact_number, $user_id);
$stmt->execute();
$stmt->close();

if (!empty($new_password)) {
    $hashed = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=? AND role='DOCTOR'");
    $stmt->bind_param("si", $hashed, $user_id);
    $stmt->execute();
    $stmt->close();
}

$_SESSION['flash_success'] = "Doctor details updated successfully.";
header("Location: manage_doctors.php");
exit();
?>