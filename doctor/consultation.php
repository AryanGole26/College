<?php
$allowed_roles = ['DOCTOR'];
require_once '../includes/auth_check.php';

$doctor_id = $_SESSION['user_id'];
$appointment_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT a.*, p.name AS patient_name, p.mobile, p.gender, p.dob
    FROM appointments a JOIN patients p ON a.patient_id = p.user_id
    WHERE a.id = ? AND a.doctor_id = ? AND a.status = 'CONFIRMED'
");
$stmt->bind_param("ii", $appointment_id, $doctor_id);
$stmt->execute();
$appt = $stmt->get_result()->fetch_assoc();

if (!$appt) {
    header("Location: schedule.php");
    exit();
}

$error = $_SESSION['consult_error'] ?? '';
unset($_SESSION['consult_error']);

require_once '../includes/header.php';
?>

<div class="mb-6">
    <a href="schedule.php" class="text-sm text-blue-600 hover:underline">&larr; Back to Schedule</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-2xl">
    <h1 class="text-xl font-bold text-slate-800 mb-1">Complete Consultation</h1>
    <p class="text-sm text-slate-500 mb-6">
        Patient: <span class="font-medium text-slate-700"><?php echo htmlspecialchars($appt['patient_name']); ?></span> &middot;
        <?php echo date('d M Y', strtotime($appt['appointment_date'])); ?>, <?php echo htmlspecialchars($appt['time_slot']); ?>
    </p>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="consultation_action.php" method="POST" enctype="multipart/form-data" class="space-y-5">
        <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Consultation Notes / Remarks *</label>
            <textarea name="doctor_remarks" rows="4" required placeholder="Diagnosis, observations, advice..."
                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Prescription (optional PDF/image)</label>
            <input type="file" name="prescription" accept=".pdf,.jpg,.jpeg,.png"
                class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 file:font-medium hover:file:bg-blue-100">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Follow-up Date (optional)</label>
            <input type="date" name="follow_up_date" min="<?php echo date('Y-m-d'); ?>"
                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">
            Mark as Completed
        </button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>