<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "Please enter both email and password.";
    header("Location: login.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, email, password, role, is_active FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: login.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

if (!password_verify($password, $user['password'])) {
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: login.php");
    exit();
}

if (!$user['is_active']) {
    $_SESSION['login_error'] = "Your account has been deactivated. Please contact the hospital admin.";
    header("Location: login.php");
    exit();
}

$name = '';
if ($user['role'] === 'PATIENT') {
    $stmt = $conn->prepare("SELECT name FROM patients WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
} elseif ($user['role'] === 'DOCTOR') {
    $stmt = $conn->prepare("SELECT name FROM doctors WHERE user_id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
} else {
    $name = 'Administrator';
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];
$_SESSION['name'] = $name;

switch ($user['role']) {
    case 'ADMIN':
        header("Location: ../admin/dashboard.php");
        break;
    case 'DOCTOR':
        header("Location: ../doctor/dashboard.php");
        break;
    case 'PATIENT':
        header("Location: ../patient/dashboard.php");
        break;
    default:
        header("Location: login.php");
}
exit();
?>