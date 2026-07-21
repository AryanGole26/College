<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profile.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
$name = trim($_POST['name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$emergency_contact = trim($_POST['emergency_contact'] ?? '');
$blood_group = $_POST['blood_group'] ?? '';
$address = trim($_POST['address'] ?? '');
$medical_history = trim($_POST['medical_history'] ?? '');
$allergies = trim($_POST['allergies'] ?? '');

if (empty($name) || !preg_match('/^[0-9]{10}$/', $mobile)) {
    $_SESSION['flash_error'] = "Name and a valid 10-digit mobile number are required.";
    header("Location: profile.php");
    exit();
}

// Handle optional photo upload
$photo_filename = null;
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed_ext)) {
        $photo_filename = 'patient_' . $patient_id . '_' . time() . '.' . $ext;
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], __DIR__ . '/../uploads/profile_photos/' . $photo_filename);
    }
}

if ($photo_filename) {
    $stmt = $conn->prepare("UPDATE patients SET name=?, mobile=?, emergency_contact=?, blood_group=?, address=?, medical_history=?, allergies=?, profile_photo=? WHERE user_id=?");
    $stmt->bind_param("ssssssssi", $name, $mobile, $emergency_contact, $blood_group, $address, $medical_history, $allergies, $photo_filename, $patient_id);
} else {
    $stmt = $conn->prepare("UPDATE patients SET name=?, mobile=?, emergency_contact=?, blood_group=?, address=?, medical_history=?, allergies=? WHERE user_id=?");
    $stmt->bind_param("sssssssi", $name, $mobile, $emergency_contact, $blood_group, $address, $medical_history, $allergies, $patient_id);
}
$stmt->execute();
$stmt->close();

// Keep session name in sync with nav bar display
$_SESSION['name'] = $name;

$_SESSION['flash_success'] = "Profile updated successfully.";
header("Location: profile.php");
exit();
?>