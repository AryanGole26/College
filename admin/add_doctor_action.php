<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: add_doctor.php"); exit(); }

$errors = [];
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$department_id = (int)($_POST['department_id'] ?? 0);
$qualification = trim($_POST['qualification'] ?? '');
$experience = (int)($_POST['experience'] ?? 0);
$fee = (float)($_POST['fee'] ?? 0);
$contact_number = trim($_POST['contact_number'] ?? '');
$available_days = isset($_POST['available_days']) ? implode(',', $_POST['available_days']) : '';
$available_time_slots = trim($_POST['available_time_slots'] ?? '');

if (empty($name)) $errors[] = "Name is required.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
if ($department_id <= 0) $errors[] = "Please select a department.";
if (empty($qualification)) $errors[] = "Qualification is required.";
if (empty($available_days)) $errors[] = "Select at least one available day.";
if (empty($available_time_slots)) $errors[] = "Available time slots are required.";

if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) $errors[] = "An account with this email already exists.";
    $stmt->close();
}

if (!empty($errors)) {
    $_SESSION['doctor_errors'] = $errors;
    header("Location: add_doctor.php");
    exit();
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'DOCTOR')");
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $user_id = $conn->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO doctors (user_id, department_id, name, qualification, experience, fee, available_days, available_time_slots, contact_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissidsss", $user_id, $department_id, $name, $qualification, $experience, $fee, $available_days, $available_time_slots, $contact_number);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    $_SESSION['flash_success'] = "Doctor account created successfully.";
    header("Location: manage_doctors.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['doctor_errors'] = ["Failed to create doctor account. Please try again."];
    header("Location: add_doctor.php");
    exit();
}
?>