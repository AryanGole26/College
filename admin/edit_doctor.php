<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

$user_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT d.*, u.email FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$doctor) {
    header("Location: manage_doctors.php");
    exit();
}

$departments = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");
$selected_days = explode(',', $doctor['available_days']);

$errors = $_SESSION['doctor_errors'] ?? [];
unset($_SESSION['doctor_errors']);

require_once '../includes/header.php';
?>

<div class="mb-6">
    <a href="manage_doctors.php" class="text-sm text-blue-600 hover:underline">&larr; Back to Doctors</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-2xl">
    <h1 class="text-xl font-bold text-slate-800 mb-1">Edit Doctor</h1>
    <p class="text-sm text-slate-500 mb-6">Dr. <?php echo htmlspecialchars($doctor['name']); ?> &middot; <?php echo htmlspecialchars($doctor['email']); ?></p>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">
            <ul class="list-disc list-inside space-y-1">
                <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="edit_doctor_action.php" method="POST" class="space-y-5">
        <input type="hidden" name="user_id" value="<?php echo $doctor['user_id']; ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($doctor['name']); ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Department *</label>
                <select name="department_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <?php while ($dep = $departments->fetch_assoc()): ?>
                        <option value="<?php echo $dep['id']; ?>" <?php echo $dep['id'] == $doctor['department_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dep['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Qualification *</label>
                <input type="text" name="qualification" required value="<?php echo htmlspecialchars($doctor['qualification']); ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Experience (years) *</label>
                <input type="number" name="experience" required min="0" value="<?php echo (int)$doctor['experience']; ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Consultation Fee (₹) *</label>
                <input type="number" name="fee" required min="0" step="0.01" value="<?php echo htmlspecialchars($doctor['fee']); ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Contact Number</label>
                <input type="tel" name="contact_number" pattern="[0-9]{10}" maxlength="10" value="<?php echo htmlspecialchars($doctor['contact_number']); ?>"
                    class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Available Days *</label>
            <div class="flex flex-wrap gap-3">
                <?php foreach (['MON','TUE','WED','THU','FRI','SAT','SUN'] as $day): ?>
                    <label class="flex items-center gap-1.5 text-sm bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                        <input type="checkbox" name="available_days[]" value="<?php echo $day; ?>" <?php echo in_array($day, $selected_days) ? 'checked' : ''; ?>> <?php echo $day; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Available Time Slots *</label>
            <input type="text" name="available_time_slots" required value="<?php echo htmlspecialchars($doctor['available_time_slots']); ?>"
                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <p class="text-xs text-slate-400 mt-1">Comma-separated, format HH:MM-HH:MM</p>
        </div>

        <div class="border-t border-slate-200 pt-5">
            <p class="text-sm font-medium text-slate-700 mb-3">Reset Password (optional)</p>
            <input type="password" name="new_password" minlength="6" placeholder="Leave blank to keep current password"
                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">Save Changes</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>