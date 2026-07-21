<?php
$allowed_roles = ['DOCTOR'];
require_once '../includes/auth_check.php';

$doctor_id = $_SESSION['user_id'];
$tab = $_GET['tab'] ?? 'pending';

$status_map = [
    'pending'   => "('PENDING')",
    'confirmed' => "('CONFIRMED')",
    'completed' => "('COMPLETED')",
    'cancelled' => "('CANCELLED')"
];
$status_filter = $status_map[$tab] ?? $status_map['pending'];

$stmt = $conn->prepare("
    SELECT a.*, p.name AS patient_name, p.mobile, p.gender, p.dob, p.blood_group, p.medical_history, p.allergies, f.rating, f.comment AS feedback_comment
    FROM appointments a JOIN patients p ON a.patient_id = p.user_id
    LEFT JOIN feedback f ON f.appointment_id = a.id
    WHERE a.doctor_id = ? AND a.status IN $status_filter
    ORDER BY a.appointment_date ASC, a.time_slot ASC
");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments = $stmt->get_result();

$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

require_once '../includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">My Schedule</h1>
</div>

<?php if ($flash_success): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-sm"><?php echo htmlspecialchars($flash_success); ?></div>
<?php endif; ?>

<div class="flex gap-2 mb-6 border-b border-slate-200">
    <?php foreach (['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label): ?>
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

    <?php while ($a = $appointments->fetch_assoc()): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex flex-col sm:flex-row justify-between sm:items-start gap-4">
                <div class="flex-1">
                    <p class="font-semibold text-slate-800"><?php echo htmlspecialchars($a['patient_name']); ?></p>
                    <p class="text-sm text-slate-600 mt-1">
                        📅 <?php echo date('d M Y', strtotime($a['appointment_date'])); ?> &nbsp;⏰ <?php echo htmlspecialchars($a['time_slot']); ?>
                    </p>
                    <p class="text-sm text-slate-500 mt-1">Reason: <?php echo htmlspecialchars($a['reason']); ?></p>

                    <?php if ($a['report_path']): ?>
                        <a href="../<?php echo htmlspecialchars($a['report_path']); ?>" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 inline-block">📎 View uploaded report</a>
                    <?php endif; ?>

                    <details class="mt-3">
                        <summary class="text-xs font-semibold text-slate-500 cursor-pointer uppercase">Patient Details</summary>
                        <div class="mt-2 text-sm text-slate-600 space-y-1 bg-slate-50 rounded-lg p-3">
                            <p>Mobile: <?php echo htmlspecialchars($a['mobile']); ?></p>
                            <p>Gender: <?php echo htmlspecialchars($a['gender']); ?> &middot; DOB: <?php echo htmlspecialchars($a['dob']); ?></p>
                            <p>Blood Group: <?php echo htmlspecialchars($a['blood_group'] ?: 'N/A'); ?></p>
                            <p>Medical History: <?php echo htmlspecialchars($a['medical_history'] ?: 'None'); ?></p>
                            <p>Allergies: <?php echo htmlspecialchars($a['allergies'] ?: 'None'); ?></p>
                        </div>
                    </details>

                    <?php if ($a['status'] === 'COMPLETED'): ?>
                        <div class="mt-3 bg-blue-500/10 rounded-lg p-3">
                            <p class="text-xs font-semibold text-slate-400 uppercase mb-1">Your Remarks</p>
                            <p class="text-sm text-slate-300"><?php echo htmlspecialchars($a['doctor_remarks'] ?: 'No remarks added.'); ?></p>
                        </div>
                        <?php if ($a['rating']): ?>
                            <div class="mt-2 bg-yellow-500/10 rounded-lg p-3">
                                <div class="flex items-center gap-1 mb-1">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <span class="<?php echo $s <= $a['rating'] ? 'text-yellow-400' : 'text-slate-700'; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                                <?php if ($a['feedback_comment']): ?>
                                    <p class="text-xs text-slate-400 italic">"<?php echo htmlspecialchars($a['feedback_comment']); ?>"</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="flex flex-col items-end gap-2 shrink-0">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        <?php
                        echo match($a['status']) {
                            'CONFIRMED' => 'bg-green-100 text-green-700',
                            'PENDING' => 'bg-yellow-100 text-yellow-700',
                            'CANCELLED' => 'bg-red-100 text-red-700',
                            'COMPLETED' => 'bg-blue-100 text-blue-700',
                            default => 'bg-slate-100 text-slate-700'
                        };
                        ?>">
                        <?php echo htmlspecialchars($a['status']); ?>
                    </span>

                    <?php if ($a['status'] === 'PENDING'): ?>
                        <div class="flex gap-2">
                            <form action="appointment_action.php" method="POST">
                                <input type="hidden" name="appointment_id" value="<?php echo $a['id']; ?>">
                                <input type="hidden" name="action" value="accept">
                                <button type="submit" class="text-xs bg-green-600 hover:bg-green-700 text-white font-medium px-3 py-1.5 rounded-lg">Accept</button>
                            </form>
                            <form action="appointment_action.php" method="POST">
                                <input type="hidden" name="appointment_id" value="<?php echo $a['id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-medium px-3 py-1.5 rounded-lg">Reject</button>
                            </form>
                        </div>
                    <?php elseif ($a['status'] === 'CONFIRMED'): ?>
                        <a href="consultation.php?id=<?php echo $a['id']; ?>" class="text-xs bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded-lg">Complete Consultation</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php require_once '../includes/footer.php'; ?>