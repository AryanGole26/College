<?php
// includes/header.php
$current_role = $_SESSION['role'] ?? null;
$current_name = $_SESSION['name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Care Hospital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="/hospital-appointment-system/assets/css/custom.css">
    <script src="https://unpkg.com/three@0.128.0/build/three.min.js"></script>
</head>
<body class="bg-[#0a0e1a] min-h-screen">

<nav class="dark-glass border-b border-slate-800/50 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <a href="/hospital-appointment-system/index.php" class="flex items-center gap-2 group">
                <span class="text-2xl group-hover:scale-110 transition-transform inline-block">🏥</span>
                <span class="text-xl font-bold font-display gradient-text">City Care Hospital</span>
            </a>

            <div class="flex items-center gap-6">
                <?php if ($current_role === 'PATIENT'): ?>
                    <a href="/hospital-appointment-system/patient/dashboard.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Dashboard</a>
                    <a href="/hospital-appointment-system/patient/book_appointment.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Book Appointment</a>
                    <a href="/hospital-appointment-system/patient/my_appointments.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">My Appointments</a>
                    <a href="/hospital-appointment-system/patient/profile.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Profile</a>
                <?php elseif ($current_role === 'DOCTOR'): ?>
                    <a href="/hospital-appointment-system/doctor/dashboard.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Dashboard</a>
                    <a href="/hospital-appointment-system/doctor/schedule.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Schedule</a>
                <?php elseif ($current_role === 'ADMIN'): ?>
                    <a href="/hospital-appointment-system/admin/dashboard.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Dashboard</a>
                    <a href="/hospital-appointment-system/admin/manage_doctors.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Doctors</a>
                    <a href="/hospital-appointment-system/admin/manage_patients.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Patients</a>
                    <a href="/hospital-appointment-system/admin/manage_departments.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Departments</a>
                    <a href="/hospital-appointment-system/admin/reports.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Reports</a>
                <?php endif; ?>

                <?php if ($current_role): ?>
                    <div class="flex items-center gap-3 pl-6 border-l border-slate-700">
                        <span class="text-sm text-slate-400">Hi, <span class="font-semibold text-slate-200"><?php echo htmlspecialchars($current_name); ?></span></span>
                        <a href="/hospital-appointment-system/auth/logout.php"
                           class="text-sm bg-red-500/10 text-red-400 hover:bg-red-500/20 px-3 py-1.5 rounded-full font-medium transition-colors">
                            Logout
                        </a>
                    </div>
                <?php else: ?>
                    <a href="/hospital-appointment-system/auth/login.php" class="text-slate-300 hover:text-blue-400 font-medium text-sm link-underline">Login</a>
                    <a href="/hospital-appointment-system/auth/register.php"
                       class="btn-shine bg-gradient-to-r from-blue-600 to-purple-600 hover:shadow-lg hover:shadow-blue-900/50 text-white px-5 py-2 rounded-full font-medium text-sm transition-all">
                        Register
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">