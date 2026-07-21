<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: book_appointment.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$doctor_id = (int)($_POST['doctor_id'] ?? 0);
$appointment_date = $_POST['appointment_date'] ?? '';
$time_slot = trim($_POST['time_slot'] ?? '');
$reason = trim($_POST['reason'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';

if (!in_array($payment_method, ['CASH', 'CARD', 'UPI'])) {
    $_SESSION['booking_error'] = "Please select a payment method.";
    header("Location: book_appointment.php?doctor_id=$doctor_id");
    exit();
}

// Validation
if ($doctor_id <= 0 || empty($appointment_date) || empty($time_slot) || empty($reason)) {
    $_SESSION['booking_error'] = "Please fill in all required fields.";
    header("Location: book_appointment.php?doctor_id=$doctor_id");
    exit();
}

if (strtotime($appointment_date) < strtotime(date('Y-m-d'))) {
    $_SESSION['booking_error'] = "Appointment date cannot be in the past.";
    header("Location: book_appointment.php?doctor_id=$doctor_id");
    exit();
}

// Check doctor's department - General Medicine allows multiple patients per slot
$stmt = $conn->prepare("
    SELECT dep.name FROM doctors d JOIN departments dep ON d.department_id = dep.id WHERE d.user_id = ?
");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$dept_name = $stmt->get_result()->fetch_assoc()['name'] ?? '';
$stmt->close();

$is_general_medicine = (stripos($dept_name, 'general') !== false);

// Prevent double-booking the same doctor/date/slot (skip this check for General Medicine)
if (!$is_general_medicine) {
    $stmt = $conn->prepare("SELECT id FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND time_slot = ? AND status IN ('PENDING','CONFIRMED')");
    $stmt->bind_param("iss", $doctor_id, $appointment_date, $time_slot);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['booking_error'] = "This slot is already booked. Please choose a different time.";
        header("Location: book_appointment.php?doctor_id=$doctor_id");
        exit();
    }
    $stmt->close();
}

// Handle optional file upload
$report_path = null;
if (isset($_FILES['report']) && $_FILES['report']['error'] === UPLOAD_ERR_OK) {
    $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['report']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        $_SESSION['booking_error'] = "Report must be a PDF, JPG, or PNG file.";
        header("Location: book_appointment.php?doctor_id=$doctor_id");
        exit();
    }

    $unique_name = 'report_' . $patient_id . '_' . time() . '_' . uniqid() . '.' . $ext;
    $upload_dir = __DIR__ . '/../uploads/reports/';
    $destination = $upload_dir . $unique_name;

    if (move_uploaded_file($_FILES['report']['tmp_name'], $destination)) {
        $report_path = 'uploads/reports/' . $unique_name;
    }
}

// Fetch doctor's fee for the amount
$stmt = $conn->prepare("SELECT fee FROM doctors WHERE user_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$fee = $stmt->get_result()->fetch_assoc()['fee'] ?? 0;
$stmt->close();

$payment_status = ($payment_method === 'UPI') ? 'PENDING_CONFIRMATION' : 'UNPAID';

// Insert appointment
$stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, time_slot, reason, status, report_path, payment_method, payment_status, amount) VALUES (?, ?, ?, ?, ?, 'PENDING', ?, ?, ?, ?)");
$stmt->bind_param("iissssssd", $patient_id, $doctor_id, $appointment_date, $time_slot, $reason, $report_path, $payment_method, $payment_status, $fee);
$stmt->execute();
$new_appointment_id = $conn->insert_id;
$stmt->close();

if ($payment_method === 'UPI') {
    header("Location: payment_qr.php?id=$new_appointment_id");
    exit();
}

$_SESSION['flash_success'] = "Appointment requested successfully! You'll be notified once the doctor confirms it.";
header("Location: my_appointments.php");
exit();
?>