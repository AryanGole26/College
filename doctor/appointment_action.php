<?php
$allowed_roles = ['DOCTOR'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: schedule.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$appointment_id = (int)($_POST['appointment_id'] ?? 0);
$action = $_POST['action'] ?? '';

$new_status = null;
if ($action === 'accept') $new_status = 'CONFIRMED';
if ($action === 'reject') $new_status = 'CANCELLED';

if ($new_status) {
    $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ? AND status = 'PENDING'");
    $stmt->bind_param("sii", $new_status, $appointment_id, $doctor_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['flash_success'] = "Appointment " . ($action === 'accept' ? 'accepted' : 'rejected') . " successfully.";
}

header("Location: schedule.php?tab=pending");
exit();
?>