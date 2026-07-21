<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

$departments = $conn->query("SELECT id, name FROM departments ORDER BY name ASC");

$errors = $_SESSION['doctor_errors'] ?? [];
unset($_SESSION['doctor_errors']);

require_once '../includes/header.php';
?>

<div class="mb-6">
    <a href="manage_doctors.php" class="text-sm text-blue-600 hover:underline">&larr; Back to Doctors</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-2xl">
    <h1 class="text-xl font-bold text-slate-800 mb-6">Add New Doctor</h1>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm">
            <ul class="list-disc list-inside space-y-1">
                <?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="add_doctor_action.php" method="POST" class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address *</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Temporary Password *</label>
                <input type="password" name="password" required minlength="6" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Department *</label>
                <select name="department_id" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Select</option>
                    <?php while ($dep = $departments->fetch_assoc()): ?>
                        <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Qualification *</label>
                <input type="text" name="qualification" required placeholder="e.g. MBBS, MD" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Experience (years) *</label>
                <input type="number" name="experience" required min="0" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Consultation Fee (₹) *</label>
                <input type="number" name="fee" required min="0" step="0.01" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Contact Number</label>
                <input type="tel" name="contact_number" pattern="[0-9]{10}" maxlength="10" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Available Days *</label>
            <div class="flex flex-wrap gap-3">
                <?php foreach (['MON','TUE','WED','THU','FRI','SAT','SUN'] as $day): ?>
                    <label class="flex items-center gap-1.5 text-sm bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                        <input type="checkbox" name="available_days[]" value="<?php echo $day; ?>"> <?php echo $day; ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Available Time Slots *</label>
            <input type="text" name="available_time_slots" required placeholder="e.g. 09:00-12:00,14:00-17:00"
                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            <p class="text-xs text-slate-400 mt-1">Comma-separated, format HH:MM-HH:MM</p>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors">Create Doctor Account</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>