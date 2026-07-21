<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';
require_once '../includes/dept_theme.php';

$patient_id = $_SESSION['user_id'];
$tab = $_GET['tab'] ?? 'upcoming';

$status_map = [
    'upcoming'  => "('PENDING','CONFIRMED')",
    'completed' => "('COMPLETED')",
    'cancelled' => "('CANCELLED')"
];
$status_filter = $status_map[$tab] ?? $status_map['upcoming'];

$sql = "
    SELECT a.*, d.name AS doctor_name, dep.name AS dept_name, f.rating AS my_rating
    FROM appointments a
    JOIN doctors d ON a.doctor_id = d.user_id
    JOIN departments dep ON d.department_id = dep.id
    LEFT JOIN feedback f ON f.appointment_id = a.id
    WHERE a.patient_id = ? AND a.status IN $status_filter
    ORDER BY a.appointment_date DESC, a.time_slot DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$appointments = $stmt->get_result();

$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);
$flash_error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

require_once '../includes/header.php';
?>

<div class="mb-6" data-aos="fade-up">
    <h1 class="text-3xl font-display font-bold text-slate-800">My Appointments</h1>
</div>

<?php if ($flash_success): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 mb-6 text-sm"><?php echo htmlspecialchars($flash_success); ?></div>
<?php endif; ?>
<?php if ($flash_error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6 text-sm"><?php echo htmlspecialchars($flash_error); ?></div>
<?php endif; ?>

<div class="flex gap-2 mb-6 border-b border-slate-200">
    <?php foreach (['upcoming' => 'Upcoming', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label): ?>
        <a href="?tab=<?php echo $key; ?>"
           class="px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors
                  <?php echo $tab === $key ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'; ?>">
            <?php echo $label; ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="space-y-4">
    <?php if ($appointments->num_rows === 0): ?>
        <p class="text-slate-500 text-sm">No appointments in this category.</p>
    <?php endif; ?>

    <?php $i = 0; while ($appt = $appointments->fetch_assoc()): $dt = dept_theme($appt['dept_name']); ?>
        <div class="hover-lift bg-white rounded-2xl shadow-sm border border-slate-200 p-6" data-aos="fade-up" data-aos-delay="<?php echo min($i * 60, 240); ?>">
            <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-4">
                <div class="flex gap-4 flex-1">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br <?php echo $dt['gradient']; ?> flex items-center justify-center text-lg text-white shadow-md shrink-0">
                        <?php echo $dt['icon']; ?>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">Dr. <?php echo htmlspecialchars($appt['doctor_name']); ?></p>
                        <p class="text-sm <?php echo $dt['text']; ?> font-medium"><?php echo htmlspecialchars($appt['dept_name']); ?></p>
                        <p class="text-sm text-slate-600 mt-1">
                            📅 <?php echo date('d M Y', strtotime($appt['appointment_date'])); ?> &nbsp;⏰ <?php echo htmlspecialchars($appt['time_slot']); ?>
                        </p>
                        <p class="text-sm text-slate-500 mt-2">Reason: <?php echo htmlspecialchars($appt['reason']); ?></p>
                        <?php if ($appt['payment_method']): ?>
                            <p class="text-xs mt-1">
                                <span class="text-slate-500"><?php echo $appt['payment_method']; ?> &middot; ₹<?php echo number_format((float)$appt['amount'], 2); ?> &middot;</span>
                                <span class="<?php echo $appt['payment_status'] === 'PAID' ? 'text-green-400' : 'text-yellow-400'; ?> font-medium">
                                    <?php echo $appt['payment_status'] === 'PAID' ? 'Paid' : ($appt['payment_status'] === 'PENDING_CONFIRMATION' ? 'Payment Pending' : 'Pay at Hospital'); ?>
                                </span>
                            </p>
                        <?php endif; ?>

                        <?php if ($appt['report_path']): ?>
                            <a href="../<?php echo htmlspecialchars($appt['report_path']); ?>" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">📎 View uploaded report</a>
                        <?php endif; ?>

                        <?php if ($tab === 'completed'): ?>
                            <div class="mt-3 <?php echo $dt['bg']; ?> rounded-xl p-3">
                                <p class="text-xs font-semibold text-slate-500 uppercase mb-1">Doctor's Remarks</p>
                                <p class="text-sm text-slate-700"><?php echo $appt['doctor_remarks'] ? htmlspecialchars($appt['doctor_remarks']) : 'No remarks added.'; ?></p>
                                <?php if ($appt['prescription_path']): ?>
                                    <a href="../<?php echo htmlspecialchars($appt['prescription_path']); ?>" target="_blank" class="text-xs text-blue-600 hover:underline mt-2 inline-block">📄 Download Prescription</a>
                                <?php endif; ?>
                                <?php if ($appt['follow_up_date']): ?>
                                    <p class="text-xs text-slate-500 mt-2">Follow-up recommended: <?php echo date('d M Y', strtotime($appt['follow_up_date'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if ($appt['my_rating']): ?>
                            <div class="mt-3 flex items-center gap-1 text-sm">
                                <span class="text-slate-500">Your rating:</span>
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <span class="<?php echo $s <= $appt['my_rating'] ? 'text-yellow-400' : 'text-slate-700'; ?>">★</span>
                                <?php endfor; ?>
                            </div>
                        <?php else: ?>
                            <a href="feedback_submit.php?id=<?php echo $appt['id']; ?>" class="inline-block mt-3 text-xs bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 px-3 py-1.5 rounded-lg font-medium">⭐ Rate this visit</a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        <?php
                        echo match($appt['status']) {
                            'CONFIRMED' => 'bg-green-100 text-green-700',
                            'PENDING' => 'bg-yellow-100 text-yellow-700',
                            'CANCELLED' => 'bg-red-100 text-red-700',
                            'COMPLETED' => 'bg-blue-100 text-blue-700',
                            default => 'bg-slate-100 text-slate-700'
                        };
                        ?>">
                        <?php echo htmlspecialchars($appt['status']); ?>
                    </span>

                    <?php if (in_array($appt['status'], ['PENDING', 'CONFIRMED'])): ?>
                        <form action="appointment_cancel.php" method="POST" onsubmit="return confirm('Cancel this appointment?');">
                            <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                            <button type="submit" class="text-xs text-red-600 hover:underline font-medium">Cancel</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php $i++; endwhile; ?>
</div>

<?php require_once '../includes/footer.php'; ?>