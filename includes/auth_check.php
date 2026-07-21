<?php
// includes/auth_check.php
// Include this at the very top of any protected page.
// Usage: require_once '../includes/auth_check.php';
// Optionally restrict to specific roles by setting $allowed_roles before including this file:
//   $allowed_roles = ['PATIENT'];
//   require_once '../includes/auth_check.php';

require_once __DIR__ . '/../config/db.php';

// Not logged in at all -> send to login
if (!isset($_SESSION['user_id'])) {
    header("Location: /hospital-appointment-system/auth/login.php");
    exit();
}

// If the calling page defined $allowed_roles, enforce it
if (isset($allowed_roles) && is_array($allowed_roles)) {
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        // Logged in, but wrong role -> send back to their own dashboard
        header("Location: /hospital-appointment-system/" . strtolower($_SESSION['role']) . "/dashboard.php");
        exit();
    }
}
?>