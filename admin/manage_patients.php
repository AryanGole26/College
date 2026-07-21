<?php
$allowed_roles = ['ADMIN'];
require_once '../includes/auth_check.php';

$flash = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$patients = $conn->query("
    SELECT p.*, u.email, u.is_active
    FROM patients p JOIN users u ON p.user_id = u.id
    ORDER BY p.name ASC
");

require_once '../includes/header.php';
?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Manage Patients</h1>
</div>

<?php if ($flash): ?><div class="bg-green-50 border border-green-200 text-green-700 rounded-lg p-4 mb-6 text-sm"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50">
            <tr class="text-left text-slate-500">
                <th class="px-6 py-3 font-medium">Name</th>
                <th class="px-6 py-3 font-medium">Email</th>
                <th class="px-6 py-3 font-medium">Mobile</th>
                <th class="px-6 py-3 font-medium">Blood Group</th>
                <th class="px-6 py-3 font-medium">Status</th>
                <th class="px-6 py-3 font-medium">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($p = $patients->fetch_assoc()): ?>
            <tr class="border-t border-slate-100">
                <td class="px-6 py-3 font-medium text-slate-800"><?php echo htmlspecialchars($p['name']); ?></td>
                <td class="px-6 py-3 text-slate-600"><?php echo htmlspecialchars($p['email']); ?></td>
                <td class="px-6 py-3 text-slate-600"><?php echo htmlspecialchars($p['mobile']); ?></td>
                <td class="px-6 py-3 text-slate-600"><?php echo htmlspecialchars($p['blood_group'] ?: '-'); ?></td>
                <td class="px-6 py-3">
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?php echo $p['is_active'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'; ?>">
                        <?php echo $p['is_active'] ? 'Active' : 'Inactive'; ?>
                    </span>
                </td>
                <td class="px-6 py-3">
                    <form action="patient_toggle_status.php" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $p['user_id']; ?>">
                        <button type="submit" class="text-xs text-blue-600 hover:underline font-medium">
                            <?php echo $p['is_active'] ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer.php'; ?>