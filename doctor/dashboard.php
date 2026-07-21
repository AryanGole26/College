<?php
$allowed_roles = ['DOCTOR'];
require_once '../includes/auth_check.php';

$doctor_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND appointment_date = CURDATE() AND status IN ('PENDING','CONFIRMED')");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$stmt->bind_result($today_count);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'PENDING'");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$stmt->bind_result($pending_count);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ? AND status = 'COMPLETED'");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$stmt->bind_result($completed_count);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("
    SELECT a.id, a.time_slot, a.reason, a.status, p.name AS patient_name
    FROM appointments a JOIN patients p ON a.patient_id = p.user_id
    WHERE a.doctor_id = ? AND a.appointment_date = CURDATE() AND a.status IN ('PENDING','CONFIRMED')
    ORDER BY a.time_slot ASC
");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$today_appts = $stmt->get_result();

require_once '../includes/header.php';
?>

<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Welcome, Dr. <?php echo htmlspecialchars($_SESSION['name']); ?></h1>
    <p class="text-slate-500 mt-1">Here's your schedule overview</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <p class="text-sm text-slate-500 font-medium">Today's Appointments</p>
        <p class="text-3xl font-bold text-blue-600 mt-2"><?php echo $today_count; ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <p class="text-sm text-slate-500 font-medium">Pending Requests</p>
        <p class="text-3xl font-bold text-yellow-600 mt-2"><?php echo $pending_count; ?></p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <p class="text-sm text-slate-500 font-medium">Completed Consultations</p>
        <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $completed_count; ?></p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-slate-800">Today's Timeline</h2>
        <a href="schedule.php" class="text-sm text-blue-600 font-medium hover:underline">View Full Schedule &rarr;</a>
    </div>
    <?php if ($today_appts->num_rows === 0): ?>
        <p class="text-slate-500 text-sm">No appointments scheduled for today.</p>
    <?php endif; ?>
    <div class="space-y-3">
        <?php while ($a = $today_appts->fetch_assoc()): ?>
            <div class="flex justify-between items-center bg-slate-50 rounded-xl p-4">
                <div>
                    <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($a['patient_name']); ?></p>
                    <p class="text-sm text-slate-500"><?php echo htmlspecialchars($a['reason']); ?></p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($a['time_slot']); ?></p>
                    <span class="text-xs font-semibold <?php echo $a['status'] === 'CONFIRMED' ? 'text-green-600' : 'text-yellow-600'; ?>"><?php echo $a['status']; ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>