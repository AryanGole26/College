<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

$flash = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$doctors = $conn->query("
    SELECT d.*, u.email, u.is_active, dep.name AS dept_name
    FROM doctors d
    JOIN users u ON d.user_id = u.id
    JOIN departments dep ON d.department_id = dep.id
    ORDER BY d.name ASC
");

require_once '../includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Manage Doctors</h1>
    <a href="add_doctor.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">+ Add Doctor</a>
</div>

<?php if ($flash): ?><div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-sm"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr class="text-left text-slate-500">
                <th class="px-6 py-3 font-medium">Name</th>
                <th class="px-6 py-3 font-medium">Department</th>
                <th class="px-6 py-3 font-medium">Email</th>
                <th class="px-6 py-3 font-medium">Fee</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($d = $doctors->fetch_assoc()): ?>
            <tr class="border-t border-slate-100">
                <td class="px-6 py-3 font-medium text-slate-800">Dr. <?php echo htmlspecialchars($d['name']); ?></td>
                <td class="px-6 py-3 text-slate-600"><?php echo htmlspecialchars($d['dept_name']); ?></td>
                <td class="px-6 py-3 text-slate-600"><?php echo htmlspecialchars($d['email']); ?></td>
                <td class="px-6 py-3 text-slate-600">₹<?php echo number_format((float)$d['fee'], 2); ?></td>
                <td class="px-6 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?php echo $d['is_active'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'; ?>">
                        <?php echo $d['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td class="px-6 py-3">
                    <div class="flex gap-3">
                        <a href="edit_doctor.php?id=<?php echo $d['user_id']; ?>" class="text-xs text-slate-600 hover:underline font-medium">Edit</a>
                        <form action="doctor_toggle_status.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $d['user_id']; ?>">
                            <button type="submit" class="text-xs text-blue-600 hover:underline font-medium">
                                <?php echo $d['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>