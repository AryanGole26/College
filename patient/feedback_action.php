<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: my_appointments.php"); exit(); }

$patient_id = $_SESSION['user_id'];
$appointment_id = (int)($_POST['appointment_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($rating < 1 || $rating > 5) {
    header("Location: feedback_submit.php?id=$appointment_id");
    exit();
}

$stmt = $conn->prepare("SELECT doctor_id FROM appointments WHERE id = ? AND patient_id = ? AND status = 'COMPLETED'");
$stmt->bind_param("ii", $appointment_id, $patient_id);
$stmt->execute();
$appt = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appt) {
    header("Location: my_appointments.php");
    exit();
}

$stmt = $conn->prepare("INSERT INTO feedback (appointment_id, patient_id, doctor_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiis", $appointment_id, $patient_id, $appt['doctor_id'], $rating, $comment);
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = "Thank you for your feedback!";
header("Location: my_appointments.php?tab=completed");
exit();
?>