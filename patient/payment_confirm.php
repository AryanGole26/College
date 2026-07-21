<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: my_appointments.php"); exit(); }

$patient_id = $_SESSION['user_id'];
$appointment_id = (int)($_POST['appointment_id'] ?? 0);

$stmt = $conn->prepare("UPDATE appointments SET payment_status = 'PAID' WHERE id = ? AND patient_id = ? AND payment_method = 'UPI'");
$stmt->bind_param("ii", $appointment_id, $patient_id);
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = "Payment confirmed! Your appointment request has been submitted.";
header("Location: my_appointments.php");
exit();
?>