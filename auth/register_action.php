<?php
// auth/register_action.php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.php");
    exit();
}

$errors = [];

// Collect + trim inputs
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$gender = $_POST['gender'] ?? '';
$dob = $_POST['dob'] ?? '';
$mobile = trim($_POST['mobile'] ?? '');
$address = trim($_POST['address'] ?? '');
$emergency_contact = trim($_POST['emergency_contact'] ?? '');
$blood_group = $_POST['blood_group'] ?? '';
$medical_history = trim($_POST['medical_history'] ?? '');
$allergies = trim($_POST['allergies'] ?? '');

// ---- Validation ----
if (empty($name)) $errors[] = "Full name is required.";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
if (!in_array($gender, ['MALE', 'FEMALE', 'OTHER'])) $errors[] = "Please select a valid gender.";
if (empty($dob)) $errors[] = "Date of birth is required.";
if (empty($mobile) || !preg_match('/^[0-9]{10}$/', $mobile)) $errors[] = "Mobile number must be exactly 10 digits.";
if (!empty($emergency_contact) && !preg_match('/^[0-9]{10}$/', $emergency_contact)) $errors[] = "Emergency contact must be exactly 10 digits.";

// Check if email already exists
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "An account with this email already exists.";
    }
    $stmt->close();
}

// If validation failed, send back to form with errors + old values
if (!empty($errors)) {
    $_SESSION['register_errors'] = $errors;
    $_SESSION['register_old'] = [
        'name' => $name, 'email' => $email, 'gender' => $gender, 'dob' => $dob,
        'mobile' => $mobile, 'address' => $address, 'emergency_contact' => $emergency_contact,
        'blood_group' => $blood_group, 'medical_history' => $medical_history, 'allergies' => $allergies
    ];
    header("Location: register.php");
    exit();
}

// ---- Insert into DB (users + patients, using a transaction) ----
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$conn->begin_transaction();

try {
    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'PATIENT')");
    $stmt->bind_param("ss", $email, $hashed_password);
    $stmt->execute();
    $user_id = $conn->insert_id;
    $stmt->close();

    // Insert into patients table
    $stmt = $conn->prepare("INSERT INTO patients 
        (user_id, name, gender, dob, mobile, address, emergency_contact, blood_group, medical_history, allergies) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isssssssss",
        $user_id, $name, $gender, $dob, $mobile, $address, $emergency_contact, $blood_group, $medical_history, $allergies
    );
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    // Auto-login after successful registration
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = 'PATIENT';
    $_SESSION['name'] = $name;

    header("Location: ../patient/dashboard.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['register_errors'] = ["Registration failed. Please try again."];
    $_SESSION['register_old'] = $_POST;
    header("Location: register.php");
    exit();
}
?>