<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my_appointments.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$appointment_id = (int)($_POST['appointment_id'] ?? 0);

// Only allow cancelling your own PENDING/CONFIRMED appointments
$stmt = $conn->prepare("UPDATE appointments SET status = 'CANCELLED' WHERE id = ? AND patient_id = ? AND status IN ('PENDING','CONFIRMED')");
$stmt->bind_param("ii", $appointment_id, $patient_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['flash_success'] = "Appointment cancelled successfully.";
} else {
    $_SESSION['flash_error'] = "Unable to cancel this appointment.";
}
$stmt->close();

header("Location: my_appointments.php");
exit();
?>