<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

$flash = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

$departments = $conn->query("
    SELECT d.*, (SELECT COUNT(*) FROM doctors WHERE department_id = d.id) AS doctor_count
    FROM departments d ORDER BY d.name ASC
");

require_once '../includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Manage Departments</h1>
</div>

<?php if ($flash): ?><div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-sm"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>
<?php if ($error): ?><div class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 mb-6 text-sm"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h2 class="font-semibold text-slate-800 mb-4">All Departments</h2>
        <div class="space-y-3">
            <?php while ($d = $departments->fetch_assoc()): ?>
                <div class="flex justify-between items-center bg-slate-50 rounded-xl p-4">
                    <div>
                        <p class="font-medium text-slate-800"><?php echo htmlspecialchars($d['name']); ?></p>
                        <p class="text-xs text-slate-500"><?php echo (int)$d['doctor_count']; ?> doctor(s) assigned</p>
                    </div>
                    <form action="department_delete.php" method="POST" onsubmit="return confirm('Delete this department? This is only possible if no doctors are assigned.');">
                        <input type="hidden" name="department_id" value="<?php echo $d['id']; ?>">
                        <button type="submit" class="text-xs text-red-600 hover:underline font-medium">Delete</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 h-fit">
        <h2 class="font-semibold text-slate-800 mb-4">Add New Department</h2>
        <form action="department_add.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Department Name *</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors">Add Department</button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>