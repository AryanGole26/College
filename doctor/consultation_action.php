<?php
$allowed_roles = ['DOCTOR'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: schedule.php");
    exit();
}

$doctor_id = $_SESSION['user_id'];
$appointment_id = (int)($_POST['appointment_id'] ?? 0);
$doctor_remarks = trim($_POST['doctor_remarks'] ?? '');
$follow_up_date = !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null;

if (empty($doctor_remarks)) {
    $_SESSION['consult_error'] = "Consultation notes are required.";
    header("Location: consultation.php?id=$appointment_id");
    exit();
}

$prescription_path = null;
if (isset($_FILES['prescription']) && $_FILES['prescription']['error'] === UPLOAD_ERR_OK) {
    $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['prescription']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed_ext)) {
        $unique_name = 'prescription_' . $appointment_id . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['prescription']['tmp_name'], __DIR__ . '/../uploads/prescriptions/' . $unique_name);
        $prescription_path = 'uploads/prescriptions/' . $unique_name;
    }
}

if ($prescription_path) {
    $stmt = $conn->prepare("UPDATE appointments SET status='COMPLETED', doctor_remarks=?, prescription_path=?, follow_up_date=? WHERE id=? AND doctor_id=?");
    $stmt->bind_param("sssii", $doctor_remarks, $prescription_path, $follow_up_date, $appointment_id, $doctor_id);
} else {
    $stmt = $conn->prepare("UPDATE appointments SET status='COMPLETED', doctor_remarks=?, follow_up_date=? WHERE id=? AND doctor_id=?");
    $stmt->bind_param("ssii", $doctor_remarks, $follow_up_date, $appointment_id, $doctor_id);
}
$stmt->execute();
$stmt->close();

$_SESSION['flash_success'] = "Consultation marked as completed.";
header("Location: schedule.php?tab=completed");
exit();
?>