<?php
$allowed_roles = ['PATIENT'];
require_once '../includes/auth_check.php';

$patient_id = $_SESSION['user_id'];
$appointment_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT a.id, a.appointment_date, d.name AS doctor_name,
           (SELECT COUNT(*) FROM feedback f WHERE f.appointment_id = a.id) AS already_reviewed
    FROM appointments a JOIN doctors d ON a.doctor_id = d.user_id
    WHERE a.id = ? AND a.patient_id = ? AND a.status = 'COMPLETED'
");
$stmt->bind_param("ii", $appointment_id, $patient_id);
$stmt->execute();
$appt = $stmt->get_result()->fetch_assoc();

if (!$appt || $appt['already_reviewed'] > 0) {
    header("Location: my_appointments.php?tab=completed");
    exit();
}

require_once '../includes/header.php';
?>

<div class="mb-6">
    <a href="my_appointments.php?tab=completed" class="text-sm text-blue-400 hover:underline">&larr; Back to Appointments</a>
</div>

<div class="dark-card rounded-2xl p-6 max-w-xl">
    <h1 class="text-xl font-display font-bold text-white mb-1">Rate Your Visit</h1>
    <p class="text-sm text-slate-400 mb-6">
        Dr. <?php echo htmlspecialchars($appt['doctor_name']); ?> &middot; <?php echo date('d M Y', strtotime($appt['appointment_date'])); ?>
    </p>

    <form action="feedback_action.php" method="POST" class="space-y-5">
        <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Your Rating *</label>
            <div class="flex gap-2 text-3xl" id="starPicker">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="<?php echo $i; ?>" required class="hidden peer">
                        <span class="star text-slate-600 peer-checked:text-yellow-400 hover:text-yellow-300 transition-colors">★</span>
                    </label>
                <?php endfor; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Comments (optional)</label>
            <textarea name="comment" rows="4" placeholder="Share your experience..."
                class="w-full px-4 py-2.5 bg-slate-900/60 border border-slate-700 text-white placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
        </div>

        <button type="submit" class="btn-shine w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 rounded-xl transition-all">
            Submit Feedback
        </button>
    </form>
</div>

<script>
document.querySelectorAll('#starPicker input').forEach(input => {
    input.addEventListener('change', () => {
        document.querySelectorAll('#starPicker .star').forEach((s, i) => {
            s.classList.toggle('text-yellow-400', i < input.value);
            s.classList.toggle('text-slate-600', i >= input.value);
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>