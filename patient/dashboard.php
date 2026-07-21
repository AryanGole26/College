<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';
require_once '../includes/dept_theme.php';

$patient_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status IN ('PENDING','CONFIRMED') AND appointment_date >= CURDATE()");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($upcoming_count);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status = 'COMPLETED'");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$stmt->bind_result($completed_count);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("
    SELECT a.appointment_date, a.time_slot, a.status, d.name AS doctor_name, dep.name AS dept_name
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.user_id
    JOIN departments dep ON d.department_id = dep.id
    WHERE a.patient_id = ? AND a.status IN ('PENDING','CONFIRMED') AND a.appointment_date >= CURDATE()
    ORDER BY a.appointment_date ASC, a.time_slot ASC
    LIMIT 1
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$next_appt = $stmt->get_result()->fetch_assoc();
$stmt->close();

$next_theme = $next_appt ? dept_theme($next_appt['dept_name']) : dept_theme('general');

$banner_title = "Welcome back, " . ($_SESSION['name'] ?? 'Patient');
$banner_subtitle = "Here's an overview of your health appointments";
$banner_photo = "https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=1600&q=80";
$banner_icon = "🩺";
require_once '../includes/header.php';
?>

<?php require_once '../includes/page_banner.php'; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="hover-lift bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden" data-aos="fade-up" data-aos-delay="0">
        <div class="absolute -right-4 -top-4 text-6xl opacity-10">📅</div>
        <p class="text-sm text-slate-500 font-medium relative z-10">Upcoming Appointments</p>
        <p class="text-4xl font-display font-bold text-blue-600 mt-2 relative z-10"><?php echo $upcoming_count; ?></p>
    </div>
    <div class="hover-lift bg-white rounded-2xl shadow-sm border border-slate-200 p-6 relative overflow-hidden" data-aos="fade-up" data-aos-delay="100">
        <div class="absolute -right-4 -top-4 text-6xl opacity-10">✅</div>
        <p class="text-sm text-slate-500 font-medium relative z-10">Completed Visits</p>
        <p class="text-4xl font-display font-bold text-green-600 mt-2 relative z-10"><?php echo $completed_count; ?></p>
    </div>
    <div class="hover-lift bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-lg p-6 flex flex-col justify-between relative overflow-hidden" data-aos="fade-up" data-aos-delay="200">
        <div class="blob w-32 h-32 bg-white top-0 right-0 opacity-20"></div>
        <p class="text-sm text-blue-100 font-medium relative z-10">Need Care?</p>
        <a href="book_appointment.php" class="btn-shine mt-2 inline-block bg-white text-blue-700 text-sm font-semibold px-4 py-2.5 rounded-xl text-center transition-all hover:scale-105 relative z-10">
            + Book Appointment
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6" data-aos="fade-up">
    <h2 class="text-lg font-display font-semibold text-slate-800 mb-4">Your Next Appointment</h2>
    <?php if ($next_appt): ?>
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 <?php echo $next_theme['bg']; ?> rounded-2xl p-5 ring-1 <?php echo $next_theme['ring']; ?>">
<div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 <?php echo $next_theme['bg']; ?> rounded-2xl p-5 ring-1 <?php echo $next_theme['ring']; ?>">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br <?php echo $next_theme['gradient']; ?> flex items-center justify-center text-xl shadow-md">
                    <?php echo $next_theme['icon']; ?>
                </div>
                <div>
                    <p class="font-semibold text-white">Dr. <?php echo htmlspecialchars($next_appt['doctor_name']); ?></p>
                    <p class="text-sm <?php echo $next_theme['text']; ?> font-medium"><?php echo htmlspecialchars($next_appt['dept_name']); ?></p>
                    <p class="text-sm text-slate-400 mt-1">
                        📅 <?php echo date('d M Y', strtotime($next_appt['appointment_date'])); ?> &nbsp;⏰ <?php echo htmlspecialchars($next_appt['time_slot']); ?>
                    </p>
                </div>
            </div>
            <span class="self-start sm:self-center px-3 py-1 rounded-full text-xs font-semibold
                <?php echo $next_appt['status'] === 'CONFIRMED' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                <?php echo htmlspecialchars($next_appt['status']); ?>
            </span>
        </div>
    <?php else: ?>
        <p class="text-slate-500 text-sm">You have no upcoming appointments. <a href="book_appointment.php" class="text-blue-600 font-medium hover:underline">Book one now</a>.</p>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>